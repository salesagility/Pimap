The pipeline is used to cache the commands and responses. It is up to you to choose how you wish to 
persist the pipeline. This library does not provide any persistence mechanisms. It only provides the
objects to cache. With the pipeline in conjunction with the provided interpreters you can also passively detect
details about server capability, mailboxes, and messages.  All tags are automatically managed by the pipeline.

### Pipe
A Pipe is a container for all pipeline caching activity.

**Pimap Manager Supports:**
- **addResponse($response)** - Add a response from the IMAP server.
- **getResponse()** - get the response from the  IMAP server.
- **getTag()** - get the tag used for the command.
- **getCommand()** - get the command used.
- **buildCommand()** - build the command string.
- **addTokenList(TokenList $tokenList)** - add result from the tokenizer.
- **isTokenized()** - has the response been tokenized.
- **tokenList()** - get the result from the tokenizer.
- **addLexemeList(LexemeList $lexemeList)** - add result from the lexeme interpreter.
- **isLexemized()** - has the response been broken up into lexemes.
- **lexemeList()** - get the result from the lexeme interpreter.
- **addParsed($parsedContent)** - add the parsed Result/Message/List.
- **parsed()** - get  the parsed Result/Message/List.

**Php Imap Extension Manager Supports:**
- **addResponse($response)** - Add a response from the IMAP server.
- **getResponse()** - get the response from the  IMAP server.
- **getTag()** - get the tag used for the command.
- **getCommand()** - get the command used.
- **addParsed($parsedContent)** - add the parsed Result/Message/List.
- **parsed()** - get  the parsed Result/Message/List.

### Pipeline
A Pipeline is a list of Pipes.

**Methods:**
- **add(CommandBuildInterface $command)** - create a pipe from a command.
- **pipes()** - get the iterator for the list of pipes.
- **getLastPipe()** - the last pipe which was created.
- **pipeByCommand($command)** - find a pipe based on the command.
- **mergePipeline($pipeline)** - merge an other pipeline into the current pipeline.


### Session Caching
Caching the pipeline in the users session is a relatively simple method to persist a temporary pipeline between HTTP requests. You may want to define this caching mechanism using the following methods.

```php
use SalesAgility\Imap\Pipeline\PipelineInterface;

/**
 * restores pipeline from cache
 * @param string $cacheId - identifer of the connection eg tcp://hostname:port/username
 * @returns null|PipelineInterface  pipeline object that has been cache
 */
function restoreCachePipeLine($cacheId)
{
    return unserialize($_SESSION[$connectionString]);
}

/**
 * saves pipeline to cache
  * @param string $cacheId - identifer of the connection eg tcp://hostname:port/username
  * @param PipelineInterface $pipeline - pipeline object to cache
  * @returns PipelineInterface
 */
function cachePipeline($connectionString, PipelineInterface $pipeline)
{
    $_SESSION[$connectionString] = serialize($pipeline);
}
```

Then you can choose to use the cache copy of the response.
```php
use SalesAgility\Imap\Manager\ManagerInterface;

/**
 * Example of how to use session caching
 * @param ManagerInterface $manager
 * @return MessageList
 */
function fetchList(ManagerInterface $manager)
{
    $cachedPipeline = restoreCachePipeLine('tcp://imap.emailservice.com:993/username', $manager->pipeline());
    $fetchCommand = $manager->command()
                    ->fetchRange(1, 5)
                    ->header()
                    ->flags()
                    ->build();
                    
    // is cached version of reponse available?
    $cachedPipe = $cachedPipeline->pipeByCommand($command)
    if(!empty($cachedPipe) {
        // use cached response
        return $cachedPipe->parsed();
    } else {
        // run command and get the response from the server
        return $manager->run($fetchCommand);
    }
    
    cachePipeline('tcp://imap.emailservice.com:993/username', $manager->pipeline());
}
```