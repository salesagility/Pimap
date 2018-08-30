Pimap can use different implementations of the IMAP protocol. An adapter (called a manager) is provided for each implementation. 

**ManagerInterface Methods:**
- **setTransporter(CommandTransporterInterface $connection)** - set the transporter.
- **transporter()** - get the transporter.
- **command()** - get an instance of the corresponding command builder.
- **run(CommandBuildArgumentsInterface $command)** - run command and get response.

### Pimap Manager
The Pimap Manager is a [stream socket client](http://php.net/manual/en/function.stream-socket-client.php) which provides you the ability to control the transport layer of the IMAP protocol. The Pimap manager lets for dig into the inner workings of the IMAP protocol. So that you can choose exactly what is communicated with the IMAP server. You can use this manager to help speed up your IMAP client.

#### Example
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

### Php Imap Extension Manager
The Php Imap Extension Manager provides an adapter to php imap extension that comes with PHP. This manager was added to provide backwards compatibility with older email servers. You can use it with the pipeline for caching purposes, however, this manager does not give you
any control over the commands sent ot the IMAP server.

#### Example
```php
try {
    $manager = ImapManagerFactory::instance()->PhpImapExtensionManager();
    $transporter = $manager->transporter();
    $connection = $transporter->connection();
    $connection = $transporter->transporter()->connection()->enableEncryption();
    $connection->setConnectionString('tcp://imap.emailservice.com:993/');
} catch (Exception $e) {
    // report error
    die($e->getMessage());
}
```