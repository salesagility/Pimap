### Installation
It is recommended that you use [composer](https://getcomposer.org/) to manage the library.

```php
composer require SalesAgility/pimap
```

### Set Up Connection Manager And Transporter
Before we can communicate with an IMAP server, we need to set up a [manager](02_Managers.md) in order to create a connection to the IMAP server.

```php
try {
    $manager = ImapManagerFactory::instance()->PimapManager();
    $transporter = $manager->transporter();
    $connection = $transporter->connection();
    $connection = $transporter->transporter()->connection()->enableEncryption();
    $connection->setConnectionString('tcp://imap.emailservice.com:993/');
} catch (Exception $e) {
    // report error
    die($e->getMessage());
}
```

> **Note:** the _setConnectionString_ format is _tcp:/hostname:portnumber/_

### Send Login Command
Once we have established a connection with the IMAP server, we need to send the login credentials to authenticate with the IMAP server.

```php
$user = 'username@emailservice.com';
$password = 'secret';

$loginCommand = $manager->command()->login()->user($user)->password($pass)->build();
$response = $manager->run($loginCommand);
```

To create a command, use the _$manager->command()_ to get a new instance of a [command builder](03_Commands.md).

> **Note:** you must call the _->build()_ method at the end of command, before passing it to the _$manager->run($command)_

### Select Mailbox Command
In order to get access the email messages, we need to tell the IMAP server which mailbox our messages belong to.
```php
$selectCommand = $manager->command()->select('INBOX')->build();
$mailbox = $manager->run($selectCommand);
```

This returns a [mailbox](04_Responses.md) response. You can use the response to find out the information about the mailbox

```php
echo  "Total messages:". $mailbox->exists() .PHP_EOL;
echo  "Recent messages:". $mailbox->recent() .PHP_EOL;
```


### Fetch Multiple Messages Command
Now that we have selected the mailbox, we can begin fetch the messages inside the mailbox. Typically, you will want to display a list of messages to the user. Lets get the email headers.

```php
$fetchCommand = $manager->command()
                ->fetchRange(1, 5)
                ->uids()
                ->flags()
                ->header()
                ->build();
                
$messages = $manager->run($fetchCommand);

foreach ($messages as $message) {
    // Handle header
    echo "UID:". $message->uid() ." ";
    echo "Number:". $message->number() ." ";
    echo "Seen:". (($message->flags()->isSeen()) ? "Y":"N") ." ";
    echo "Date:". $message->header()->date()->format("r") ." ";
    echo "To:". $message->header()->to() ." ";
    echo "From:". $message->header()->from() ." ";
    echo "Subject:". $message->header()->subject() ." ";
    echo "Attachments:". (($message->body->structure()->hasAttachments()) ? "Y":"N") ." ";
    echo PHP_EOL;
}
```

> **Tip:** The _->header()_ method also includes the body structure of a message.

### Fetch Single Message Command
Typically the user will want to view a single email at a time. We already have the headers, all we need is the body of the message.

```php
$fetchCommand = $manager->command()
                    ->fetch(1)
                    ->body()
                    ->build();
                    
$messages = $manager->run($fetchCommand);

foreach($messages as $message) {
    // Check to see body is available
    if(   $message->body()->structure()->plainTextBodyExists()
       || $message->body()->structure()->htmlBodyExists()) {
         
         // Get Attachments
         foreach($message->body->attachments as $attachment) {
            if($attachment->isInline()) {
                // handle embedded content
                $embeddedContent = 'data:'.trim($attachment->structure()->type()).';base64,'.base64_encode($attachment->content());
                $cid = 'cid:'.$attachment->contentId();
                $message->body()->offsetSet('html' str_replace($cid, $encodedContent, $message->body()->html()));
            }
            
            // Get file properties
            $name = $attachment->structure()->name();
            $type = $attachment->structure()->type();
            $size = $attachment->structure()->size();
            
            // Get the file contents
            if ($attachment->hasContent()) {
                $content = $attachment->content();
            }
            
            // Handle attachment
         }
         
         // Get the body or the content of the message
         echo "html:" . $message->body()->html() . PHP_EOL;
    }
}
```

### Disconnect from the server
Now that we have what we need, to disconnect we must send the log out command before we disconnect.

```php
if ($manager->transporter()->connection()->isConnected()) {
    $logoutResponse = $manager->run($manager->command()->logout());
    $manager->transporter()->connection()->disconnect();
}
```

> **Note:** you must use the disconnect method after issuing a LOGOUT command
