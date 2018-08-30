
### Overview

```text
+-----------------------+
|                       |
|        Client         |
|                       |
+-----------------------+
            ^
            |
            V
+-----------------------+
|                       |
|    Protocol Manager   |
|                       |
+-----------------------+
            ^            
            |            
            V            
+-----------------------+
|                       |
|  Message Transporter  |
|                       |
+-----------------------+
            ^
            |
            V
+-----------------------+  
|                       |
|   Connection Manager  |
|                       |
+-----------------------+
            ^
            |
            V
+-----------------------+
|                       |
|       Imap Server     |
|                       |
+-----------------------+
```
#### Protocol Manager
- Manages the connection manager
- Manages the imap protocol requests and responses
- Manages the evaluation of commands and responses

#### Message Transporter
- Maintain the protocol link between the client and the server
- Relays protocol messages to the Connection Manager and Protocol Manager
- Builds the server responses
- Determines what constitutes as a the end of file for the protocol 

#### Connection Manager
- Manages connection configuration
- Manages network socket
- Manages network level encryption SSL/TLS
- Transmits client messages
- Waits for server response messages


### Processes
#### Get Manager via Factory
```
|Client             |Command Builder |Manager         |Pipeline        |Transporter     |Connection      
|                   |                |                |                |                |
|         +<----------------------------------------------------------------------------|
|         |         |                |                |                |                |
|         +<-----------------------------------------------------------|                |
|         |         |                |                |                |                |
|         +<------------------------------------------|                |                |
|         |         |                |                |                |                |
|         V         |                |                |                |                |
|-1->Factory------------------------>|                |                |                |
|                   |                |--------------->|                |                |
|                   |                |-------------------------------->|                |
|                   |                |                |                |<---------------|
|<-2---------------------------------|<--------------------------------|                |
```

#### Configure Connection
```
|Client             |Command Builder |Manager         |Pipeline        |Transporter     |Connection
|<--1------------------------------------------------------------------|                |
|                   |                |                |                |                |
|<--2------------------------------------------------------------------|<---------------|
|                   |                |                |                |                |
|---3---------------------------------------------------------------------------------->|
```

#### Command Builder
- Provides a set of interfaces to build imap commands.
- Uses PHPDoc to be IDE Friendly
- Builds / Optimises the commands sent via the protocol manager to the server

### Pipeline
- Caches the messages which have been transmitted to the server
- Caches the messages which have been received from the server
- Caches the data structures used to create commands and parse responses
- Manages the tags in the requests and responses
- Cache last for the life time of user http request

#### Running commands

```
|Client             |Command Builder |Manager         |Pipeline        |Transporter     |Connection
|                   |                |                |                |                |
|<--1---------------|                |                |                |                |
|                   |                |                |                |                |
|---2------------------------------->|                |                |                |
|                   |                |-3------------->|                |                |
|                   |                |                |                |                |
|                   |                |-4------------------------------>|                |
|                   |                |                |                |                |
|                   |                |                |                |-5------------->|
|                   |                |                |                |                |
|                   |                |                |                |<-6-------------|
|                   |                |<--7-----------------------------|                |
|                   |                |---8----------->|                |                |
|                   |                |
|                   |                |                |Intepreter      |Lexemizer       |Tokenizer
|                   |                |---9----------->|                |                |
|                   |                |                |-10------------>|                |
|                   |                |                |                |--11----------->|
|                   |                |                |                |                |
|                   |                |                |                |<-12------------|
|                   |                |                |<-13------------|                |
|                   |                |<-14------------|                |                |
|                   |                |                
|                   |                |                |Pipeline
|                   |                |--15----------->|
|<--16-------------------------------|                |
```

#### Command Validator
- Validates the structure of the command builder

#### Token Parser (Tokenizer)
- Low Level Parser
- Iterates through the response messages and produces a RFC 2882 Token List

A Token List is a collection of Tokens.

#### Tokens
- store the positions of where a token starts and ends in a request/response
- store the type of token
- provide an easy to read interface to determine the type of token
- Types may be whitespace, folding whitespace, special, group, optional

#### Lexeme Interpreter (Lexemizer)
- Creates a lexeme list from a token list RFC 822/2822 rules 
- Provides the initial level of message interpretation
- Reduces protocol noise by removing folding space

A Lexeme List is an collection of lexemes

#### Lexemes
- Store a collection of Tokens
- Adds higher level token types to the tokens
- Types may include Keyword, Number, Text, Whitespace, Group, Optional, CText

#### Interpreter
- Creates a Response or a List Of Responses from the Lexeme List
- Provides the final level of interpretation.