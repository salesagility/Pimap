# PHP Internet Message Access Protocol  (Pimap)
A PHP Library for communication with IMAP Servers.

[![License: GPL v3](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![Build Status](https://travis-ci.org/SalesAgility/Pimap.svg?branch=master)](https://travis-ci.org/SalesAgility/Pimap)
[![codecov](https://codecov.io/gh/SalesAgility/Pimap/branch/master/graph/badge.svg)](https://codecov.io/gh/SalesAgility/Pimap)
[![Total Downloads](https://poser.pugx.org/SalesAgility/Pimap/downloads)](https://packagist.org/packages/SalesAgility/Pimap)


### Rationale
When communication with an IMAP email server involves a high amount of latency,
it helps to be able to hand craft commands, so that we can reduce the quantity of commands being sent to the server. The PHP IMAP extension
does not offer such control of over the transport layer of the IMAP protocol. This library replaces the PHP IMAP extension
and provides full control over the entire process.

### Features
- Fewer combined calls
- Decrease latency
- Command builder
- IMAP Connection Manager 
- IMAP Response Interpreters
- Pipeline for caching commands and responses
- PSR 3 logger aware
- Mock objects for automated testing

### Compliant
- [RFC 3501](https://tools.ietf.org/html/rfc3501) IMAP version 4rev1
- [RFC 2822](https://tools.ietf.org/html/rfc2822) Internet Message Format
- [RFC 2045](https://tools.ietf.org/html/rfc2045) Multipurpose Internet Mail Extensions


### Dependencies
Dependencies are managed by [composer](https://getcomposer.org/). Please see the composer.json file for a complete list of dependencies.

### Contributing to the Pimap project
Please read and sign the following [contributor agreement][cont_agrmt]

[cont_agrmt]: https://www.clahub.com/agreements/salesagility/Pimap

The Contributor Agreement only needs to be signed once for all pull requests and contributions. 

Once signed and confirmed, any pull requests will be considered for inclusion in the Pimap project.

#### Security

We take security seriously here at SalesAgility so if you have discovered a security risk report it by
emailing security@suitecrm.com. This will be delivered to the product team who handle security issues.
Please don't disclose security bugs publicly until they have been handled by the security team.

Your email will be acknowledged within 24 hours during the business week (Mon - Fri), and you’ll receive a more
detailed response to your email within 72 hours during the business week (Mon - Fri) indicating the next steps in
handling your report.

### Add to your project (Composer)

```bash
composer require "salesagility/pimap"
```

### Getting Help
All the documentation is situated in the docs directory. The documentation in this project is written in mark down and generated using [Daux.io](https://daux.io/).

#### Generate Documentation
To build the documentation

```bash
cd /path/to/pimap/
./vendor/bin/daux
```

open the generated _static/index.html_ file.

#### Host Documentation
When updating the documentation, it helps to be able to see a live copy of your changes. Daux.io provides a built in web server.

```bash
cd /path/to/pimap/
./vendor/bin/daux serve
```

this command will output something similar to:
```bash
Daux development server started on http://localhost:8085/
```

in this case you can access  **http://localhost:8085/** in your web browser.