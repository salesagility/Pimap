
To create a command, use the _$manager->command()_ to get a new instance of a command builder.

```php
$command = $manager->command()->command()->commandArgument()->build());
```
The command build will provide you with a set of methods which you can chain together to build the command. Use the _->build()_ method to finalise the command.

If you have an Integrated Development Environment (IDE). You can use your IDE with auto-complete to assist you to make the right choices.

> **Note:** you must call the _->build()_ method at the end of command, before passing it to the _$manager->run($command)_ method

**Table Of Contents**
[TOC]

### Verify Command Support

Pimap offers multiple managers, which may not support some commands. There is a command builder for each manager. A command builder
implements a set of interfaces which indicates which commands are supported.

```php
use SalesAgility\Imap\CommandBuilder\Commands;
if($manager->command() instanceof Commands\StartTlsCommandInterface) {
    // Command is supported
    $manager->run($manager->command()->startTLS()->build());
}
```

### Raw Commands
When you need to create a custom command to support an IMAP extension you can use the _->raw()_ method.


```php

$response = $manager->command()->raw('THREAD ORDEREDSUBJECT UTF-8 SINCE 5-MAR-2000')->build(); 
```

The manager will return a [Response](04_Responses.md) from raw command . To start interpreting the response you can use the _->include()_ 
method to get the untagged responses which was received from the server.

```php
$responses = $response->included();
$interpreter = new CustomInterpreter()
$response = $interpreter->parse($responses);
```

> **Note:** The manager will not attempt to interpret the response of a raw command

### IMAP version 4rev1 Commands
Pimap current has support for [RFC 3501](https://tools.ietf.org/html/rfc3501) IMAP version 4rev1 commands.

#### LOGIN
Once a connection established, you will need send the login credentials to authenticate with the IMAP server.

**Usage**:

```php
$user = 'username@emailservice.com';
$password = 'secret';

$loginCommand = $manager->command()->login()->user($user)->password($pass)->build());
$response = $manager->run($loginCommand);
```

**Returns**: [Response](04_Responses.md)

#### CAPABILITY
The CAPABILITY command requests a listing of capabilities that the server supports.

**Usage**:

```php
$response = $manager->command->capability()->build()
```

**Returns**: [Response](04_Responses.md)

> **Note:** capability may be detect passively in the Server Greeting, and Responses for LOGIN, EXAMINE and SELECT commands
> **Note:** Php Imap Extension Manager does not support the capability command

#### LIST
The LIST command returns a subset of names from the complete set of all names available to the client.

**Usage**:

```php
$mailboxList = $manager->command->listMailbox("", "*")->build());
```

**Returns**: MailboxList

#### SELECT
The SELECT command selects a mailbox so that messages in the mailbox can be accessed.

**Usage**:

```php
$mailbox = $manager->command->select("INBOX")->build());
```

**Returns**: [Mailbox](04_Responses.md)

#### EXAMINE
The EXAMINE command is identical to SELECT and returns the same output. It is used to get the most recent information
about the mailbox.

**Usage**:

```php
$mailbox = $manager->command->examine("INBOX")->build());
```

**Returns**: [Mailbox](04_Responses.md)

#### UID
The UID command takes as its arguments a COPY, FETCH, or STORE command with arguments
appropriate for the associated command.  However, the numbers in
the sequence set argument are unique identifiers instead of
message sequence numbers.  Sequence set ranges are permitted, but
there is no guarantee that unique identifiers will be contiguous.


**Usage**:

```php
$messageList = $manager->command->uid()->fetch(1)->flags()->build());
```

**Returns**: The object for the command as its arguments eg [MessageList](04_Responses.md) / [Response](04_Responses.md)


> **Note:** some imap servers do not support UIDs

#### FETCH

The FETCH command retrieves data associated with a message in the mailbox.

**Usage**:

```php
$fetchCommand = $manager->command()
                ->fetchRange(1, 5)
                ->header()
                ->flags()
                ->build();
                
$messageList = $manager->run($fetchCommand);
```

**Returns**: [MessageList](04_Responses.md)

**Methods**:
- **fetch($message)** - fetch a single message
- **fetchRange($messageFrom, $messageTo)** - fetch messages from a contiguous range
- **fetchSequence(MailboxList)** - fetch messages from a non-contiguous range. eg the output from SEARCH command.
- **header()** - include header and body structure of a message.
- **body()** - include the text and attachments of a message. Body must be run separately from the header.
- **uids()** - include the uid of a message 
- **flags()** - include the flags of a message. eg seen, answered 

#### SEARCH
The SEARCH command searches the mailbox for messages that match the given searching criteria.

**Usage**:

```php
$searchCommand = $manager->command()
                ->search()
                ->searchUnseen()
                ->searchOr()
                ->searchRecent()
                ->build();
                
$messageList = $manager->run($searchCommand);
```

**Returns**: [MessageList](04_Responses.md) 

> **Note:** The Messages return only contain uids / message numbers

> **Tip:** Use the _$manager->command()->fetchRange($messageList)_ to fetch message(s)

**Methods**:
- **withRange($messageFrom, $messageTo)** - search with in a contiguous range of messages
- **withSequence([MessageList](04_Responses.md) $messages)** - search with a non-contiguous range of messages
- **searchAll()** - All messages in the mailbox
- **searchAnswered()** - Messages with the \Answered flag set
- **searchBcc($string)** -  Messages that contain the specified string in the envelope structure's BCC field
- **searchBefore(\DateTime $dateTime)** -  Messages whose internal date (disregarding time and timezone) is earlier than the specified date.
- **searchBody($string)** -  Messages that contain the specified string in the body of the message
- **searchCc($string)** - Messages that contain the specified string in the envelope structure's CC field
- **searchDeleted()** - Messages with the \Deleted flag set
- **searchDraft()** -  Messages with the \Draft flag set
- **searchFlagged()** - Messages with the \Flagged flag set
- **searchFrom($string)** - Messages that contain the specified string in the envelope structure's FROM field
- **searchHeader($name, $string)** - Messages that have a header with the specified field-name (as defined in [RFC-2822](https://tools.ietf.org/html/rfc2822)) and that contains the specified string in the text of the header (what comes after the colon)
- **searchKeyword($flag)** - Messages with the specified keyword flag set
- **searchLarger($n)** -  Messages with an [RFC-2822](https://tools.ietf.org/html/rfc2822) size larger than the specified number of octets
- **searchNew()** -  Messages that have the \Recent flag set but not the \Seen flag
- **searchNot()** - Messages that do not match the specified search key
- **searchOld()** -  Messages that do not have the \Recent flag set
- **searchOn(\DateTime $date)** - Messages whose internal date (disregarding time and timezone) is within the specified date
- **searchOr()** - Messages that match either search key before and after
- **searchRecent()** - Messages that have the \Recent flag set
- **searchSeen()** - Messages that have the \Seen flag set
- **searchSentBefore(\DateTime $date)** - Messages whose [RFC-2822](https://tools.ietf.org/html/rfc2822) Date: header (disregarding time and timezone) is earlier than the specified date
- **searchSentOn(\DateTime $date)** - Messages whose [RFC-2822](https://tools.ietf.org/html/rfc2822) Date: header (disregarding time and timezone) is within the specified date.
- **searchSentSince(\DateTime $date)** - Messages whose [RFC-2822](https://tools.ietf.org/html/rfc2822) Date: header (disregarding time and timezone) is within or later than the specified date.
- **searchSince(\DateTime $date)** - Messages whose internal date (disregarding time and timezone) is within or later than the specified date
- **searchSmaller($n)** - Messages with an [RFC-2822](https://tools.ietf.org/html/rfc2822) size smaller than the specified number of octets
- **searchSubject($string)** - Messages that contain the specified string in the envelope structure's SUBJECT field
- **searchText($string)** -  Messages that contain the specified string in the header or body of the message
- **searchTo($string)** - Messages that contain the specified string in the envelope structure's TO field
- **searchUid($string)** -  Messages with unique identifiers corresponding to the specified unique identifier set. Sequence set ranges are permitted
- **searchUnanswered()** - Messages that do not have the \Answered flag set
- **searchUndeleted()** - Messages that do not have the \Deleted flag set
- **searchUndraft()** - Messages that do not have the \Draft flag set
- **searchUnflagged()** - Messages that do not have the \Flagged flag set
- **searchUnkeyword($keyword)** - Messages that do not have the specified keyword flag set
- **searchUnseen()** - Messages that do not have the \Seen flag set


#### LOGOUT
The LOGOUT command informs the server that the client is done with the connection

**Usage**:

```php
if ($manager->transporter()->connection()->isConnected()) {
    $logoutResponse = $manager->run($manager->command()->logout()->build());
    $manager->transporter()->connection()->disconnect();
}
```

**Returns**: [Response](04_Responses.md) 

> **Note:** you must use the disconnect method after issuing a LOGOUT command

#### NOOP
Since any command can return a status update as untagged data, the
NOOP command can be used as a periodic poll for new messages or
message status updates during a period of inactivity (this is the
preferred method to do this).  The NOOP command can also be used
to reset any inactivity autologout timer on the server


**Usage**:

```php
$response = $manager->run($manager->command()->noop()->build());
```

**Returns**: [Response](04_Responses.md) 


#### CHECK
The CHECK command requests a checkpoint of the currently selected
mailbox.  A checkpoint refers to any implementation-dependent
housekeeping associated with the mailbox (e.g., resolving the
server's in-memory state of the mailbox with the state on its
disk) that is not normally executed as part of each command.  A
checkpoint MAY take a non-instantaneous amount of real time to
complete.  If a server implementation has no such housekeeping
considerations, CHECK is equivalent to NOOP


**Usage**:

```php
$response = $manager->run($manager->command()->check()->build());
```

**Returns**: [Response](04_Responses.md) 

#### APPEND
The APPEND command appends the literal argument as a new message
to the end of the specified destination mailbox.  This argument
SHOULD be in the format of an [RFC-2822](https://tools.ietf.org/html/rfc2822) message.

**Usage**:

```php
// Rfc 2822 Message Truncated to keep the mark down formatting
// to see an example of $rawMessage: https://tools.ietf.org/html/rfc3501#page-46
$response = $manager->run($manager->command()->append($rawMessage)->build());
```

**Returns**: [Response](04_Responses.md) 


#### CLOSE
The CLOSE command permanently removes all messages that have the
\Deleted flag set from the currently selected mailbox, and returns
to the authenticated state from the selected state


**Usage**:

```php
$response = $manager->run($manager->command()->close()->build());
```

**Returns**: [Response](04_Responses.md) 


#### COPY
The COPY command copies the specified message(s) to the end of the
specified destination mailbox.

**Usage**:

```php
$response = $manager->run($manager->command()->copy()->withRange(1,2)->toMailbox('Invoices')->build());
```

**Returns**: [Response](04_Responses.md)

#### CREATE
The CREATE command creates a mailbox with the given name.

**Usage**:

```php
$response = $manager->run($manager->command()->create('INVOICES')->build());
```

**Returns**: [Response](04_Responses.md)

#### EXAMINE
The EXAMINE command is identical to SELECT and returns the same
output; however, the selected mailbox is identified as read-only.
No changes to the permanent state of the mailbox.

**Usage**:

```php
$response = $manager->run($manager->command()->examine('INVOICES')->build());
```

**Returns**: [Response](04_Responses.md)


#### DELETE

The DELETE command permanently removes the mailbox with the given name.

**Usage**:

```php
$response = $manager->run($manager->command()->delete('INVOICES')->build());
```

**Returns**: [Response](04_Responses.md)

#### EXPUNGE
The EXPUNGE command permanently removes all messages that have the
\Deleted flag set from the currently selected mailbox.


**Usage**:

```php
$response = $manager->run($manager->command()->expunge()->build());
```

**Returns**: [Response](04_Responses.md)

#### LSUB
The LSUB command returns a subset of names from the set of names
that the user has declared as being "active" or "subscribed".

**Usage**:

```php
$response = $manager->run($manager->command()->lsub("#news.", "comp.mail.*")->build());
```

**Returns**: MailboxList

#### RENAME
The RENAME command changes the name of a mailbox.

**Usage**:

```php
$response = $manager->run($manager->command()->rename('INVOICES, 'QUOTES')->build());
```

**Returns**: [Response](04_Responses.md)


#### STATUS
The STATUS command requests the status of the indicated mailbox.

**Usage**:

```php
$response = $manager->run($manager->command()->status()->build());
```

**Returns**: [Mailbox](04_Responses.md)

#### STORE
The STORE command alters data associated with a message in the mailbox. The new value of the data associated is returned as if a FETCH COMMAND is run.

**Usage**:

```php
$response = $manager->run($manager->command()->store()->withMessage(1)->addFlag('Answered')->build());
```

**Returns**: [Response](04_Responses.md)

**Methods**:
- **withMessage($message)** - set the flags of a single message
- **withRange($messageFrom, $messageTo)** - set the flags or a contiguous set of messages
- **replaceFlag($flag)** -  Replace the flags for the message (other than \Recent) with the argument
- **addFlag($flag)** -  Add the argument to the flags for the message
- **removeFlag($flag)** - Remove the argument from the flags for the message


#### SUBSCRIBE

The SUBSCRIBE command adds the specified mailbox name to the
server's set of "active" or "subscribed" mailboxes as returned by
the LSUB command.

**Usage**:

```php
$response = $manager->run($manager->command()->subscribe('INVOICES')->build());
```

**Returns**: [Response](04_Responses.md)
    

#### UNSUBSCRIBE

The UNSUBSCRIBE command removes the specified mailbox name from
the server's set of "active" or "subscribed" mailboxes as returned
by the LSUB command

**Usage**:

```php
$response = $manager->run($manager->command()->unsubscribe('INVOICES')->build());
```

**Returns**: [Response](04_Responses.md)
      
### RFC 2177 - IDLE Command

The Internet Message Access Protocol requires a client to
poll the server for changes to the selected mailbox.  It's often more desirable to have the server transmit
updates to the client in real time. The IDLE command which is defined in 
[RFC 2177](https://tools.ietf.org/html/rfc2177) enabled the client to receive updates in real time. 

The IDLE has a different process from the other supported commands.


```php
while(true) {
    $response = $manager->run($manager->command()->idle()->build());
    $message = $manager->run($manager->command()->fetch($response->number())->build());
    // import message
}
```

**Returns**: [Mailbox](04_Responses.md)

### Not Supported

Some commands are currently not supported:
- STARTTLS for port 143  is not supported because most IMAP servers use port 993 via a TLS.
- AUTHENTICATE - LOGIN over TLS connection is more secure that SASL AUTHENTICATE
- [RFC 5256](https://tools.ietf.org/html/rfc5256) SORT - Support the SORT extension IMAP servers is limited
