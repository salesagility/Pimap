IMAP has a set of common responses for each command. These responses are defined in the [RFC 3501](https://tools.ietf.org/html/rfc3501).

**Table Of Contents**

[TOC]

## Response
A Response object represents is the foundation of all of the responses. It holds both the tagged and untagged responses which 
were received from an IMAP server. Typically,  an IMAP server will send a OK/BAD/NO response. BAD/NO Responses are thrown
as exceptions. However, a Response object will typically be a OK response.

**Methods:**
- **status()** - status code sent from server eg OK / BAD / NO
- **included()** - the non tagged responses which are included
- **message()** -  the tagged response

## List Containers
When Pimap has to supply a list of objects, it will use one of the following containers. All list containers implement
the ArrayAccess interface. Which allows you to access it's elements like an array.

### Capability
A Capability is an object which implements the array access interface. Each offset or element represents a capability which is supported by the IMAP server

**Methods:**
- offsetExists($capability) - is capability supported?

### MailboxList
A MailboxList is an object which implements the array access interface. Each offset or element represents a mailbox (also known as a folder or a filter)

### MessageList
A MailboxList is an object which implements the array access interface. Each offset or element represents a message. 


## Response Containers

When Pimap has to supply a specialisation of a response, it will use one of the following containers. All Response containers implement the ArrayAccess interface. Which allows you to access it's elements like an array.


### Mailbox
A Mailbox is an object which represents a mailbox (also known as a folder or a filter). 

**Methods:**
- **hierarchy()** - The hierarchy delimiter is a character used to delimit levels of hierarchy in a mailbox name.  A client can use it to create child
mailboxes, and to search higher or lower levels of naming hierarchy.  All children of a top-level hierarchy node MUST use the same separator character.  A NIL hierarchy delimiter means
that no hierarchy exists; the name is a "flat" name.
- **name()** - The name of the mailbox
- **attributes()**
    - _Noinferiors_ - It is not possible for any child levels of hierarchy to exist under this name; no child levels exist now and none can be created in the future.
    - _Noselect_ - It is not possible to use this name as a selectable mailbox.
    - _Marked_ - The mailbox has been marked "interesting" by the server; the mailbox probably contains messages that have been added since the last time the mailbox was selected.
    - _Unmarked_ - The mailbox does not contain any additional messages since the last time the mailbox was selected.
- **flags()**  - Defined flags in the mailbox.
- **exists()** - The number of messages in the mailbox.
- **recent()** - The number of messages with the \Recent flag set.
- **unseen()** - The message sequence number of the first unseen message in the mailbox.
- **uidValidity()** - The unique identifier validity value.
- **uidNext()** - The next unique identifier value.


### Message
A Message is used to represent a IMAP message (also known as an email). The information kept in an email message may change depending on which command was executed.

**Required Methods:**
- **number()** - the index or position in the mailbox.
- **uid()** - the unique identifier of a message.
- **hasHeader()** - has the header been included in the message
- **hasBody()** - has body been included in the message

**Optional Methods:** may return null or an empty container.
- **flags()** - the flags of the message
- **header()** - the message header
- **body()** - the message body

#### MessageFlags
A MessageFlags is a container which contains a list of flags.

**Methods:**
- **isSeen()** - has Message been read
- **isAnswered()** - has been Message answered
- **isFlagged()** - is "flagged" for urgent/special attention
- **isDeleted()** - is "deleted" for removal later by the EXPUNGE command
- **isDraft()** -  has Message not completed composition (marked as a draft).
- **isRecent()** - is "recently" arrived in this mailbox.


#### MessageHeader
A MessageHeader represents information about the email.

**Methods:**
- **date()** - The origination date specifies the date and time at which the creator of the message indicated that the message was complete and ready to enter the mail delivery system.
- **to()** - contains the address(es) of the primary recipient(s) of the message.
- **from()** - where the email originates from.
- **replyTo()** - where the email originates from / an email to reply to.
- **cc()** - "carbon copy" - contains the addresses of the primary recipient(s) of the message.
- **bcc()** - "blind carbon copy" -  contains addresses of recipients of the message whose addresses are not to be revealed to other recipients of the message.
- **subject()** - brief description of what the email is about.
- **messageId()** - contains a single unique message identifier.

#### MessageBody
A MessageHeader represents the content of email.

**Methods:**
- **structure()** - get the body structure or information about the body.
- **html()** - get the html part of the body.
- **text()** - get the plain text  part of the body.
- **attachments()** - get the attachments or embedded content of the body.

#### MessageBodyStructure
A MessageHeader represents information about the content of email.

**Methods:**
- **htmlBodyExists()** - does message contains a html part?
- **plainTextBodyExists()** - does message contain a plain text part?
- **attachmentsExists()** - does message contain attachments or embedded content?

#### MessageAttachment
A MessageHeader represents an attachment or the mime parts of email. This also includes content which is emedded in the
message text or html.

**Attachment Methods:**
- **structure()** - get the information about the attachment.
- **hasContent()** - has the attachments content been included? 
- **content()** -  get attachments content been included.

**Embedded Content Methods:**
- **isInline()** - is the attachment embedded content?
- **contentId()** - get the content id (cid:) which is referenced in the text or html of the body

#### MessageAttachmentStructure
A MessageHeader represents information about an attachment included of email.

**Methods:**
- **type()** - get the mime type of the content. Eg text/plain, text/html, image/jpeg etc...
- **name()** - get the file name of the content
- **size()** - get the size (in bytes) of the content
