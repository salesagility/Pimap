### Rationale
When communication with an IMAP email server involves a high amount of latency, you will want to reduce the commands being sent to the server. The php imap extension does not offer such control of over the transport layer of the IMAP protocol. This library replaces the php imap extension
and provides full control over the entire process.

### Features
- Fewer combined calls
- Decrease latency
- Command builder
- IMAP Manager 
- IMAP Response Interpreters
- Pipeline for caching commands and responses
- PSR 3 logger aware
- Mock objects for automated testing

### Dependencies
Dependencies are manages by [composer](https://getcomposer.org/). Please see the composer.json file for a complete list of dependencies.

### Compliant
- [RFC 3501](https://tools.ietf.org/html/rfc3501) IMAP version 4rev1
- [RFC 2822](https://tools.ietf.org/html/rfc2822) Internet Message Format
- [RFC 2045](https://tools.ietf.org/html/rfc2045) Multipurpose Internet Mail Extensions