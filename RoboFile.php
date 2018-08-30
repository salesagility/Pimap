<?php
/*********************************************************************************
 * Pimap is a PHP IMAP library developed by SalesAgility Ltd.
 * Copyright (C) 2018 SalesAgility Ltd.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *********************************************************************************/

require_once __DIR__ . '/vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use \SalesAgility\Imap\ManagerFactory;
use \SalesAgility\Stream\Connection;

class RoboFile extends \Robo\Tasks
{
    /**
     * Connect to a real imap server and view messages
     * @param array $opt
     * @throws ErrorException
     * @throws \SalesAgility\Imap\Token\TokenException
     */
    public function runEmailClient($opt = [
        'address' => '',
        'port' => '',
        'tls' => '',
        'user' => '',
        'password' => ''
    ])
    {
        $log = new Logger('name');
        $log->pushHandler(new StreamHandler(__DIR__ . '/tests/_output/functional_tests.log', Logger::DEBUG));
        $log->info('Robo task: runTests');

        $opt['address'] = $this->askIfEmpty($opt['address'], 'address', '127.0.0.1');
        $opt['port'] = $this->askIfEmpty($opt['port'], 'port', '143');
        $opt['tls'] = $this->confirmIfEmpty($opt['tls'], 'tls');
        $opt['user'] = $this->askIfEmpty($opt['user'], 'user', 'user');
        $opt['password'] = $this->askIfEmpty($opt['password'], 'password', '');


        $log->info(var_export($opt, true));


        $log = new Logger('name');
        $log->pushHandler(new StreamHandler(__DIR__ . '/tests/_output/functional_tests.log', Logger::DEBUG));
        $log->info('loading global bootstrap file');
        $managerFactory = ManagerFactory::instance();
        $manager = $managerFactory->PimapManager();
        $manager->setLogger($log);
        $manager->transporter()->connection()->setLogger($log);
        $address = $opt['address'];
        $port = $opt['port'];
        $tls = $opt['tls'];
        $user = $opt['user'];
        $password = $opt['password'];


        $this->say('Connecting to '. "tcp://$address:$port");

        $manager->transporter()->connection()->setConnectionString("tcp://$address:$port");

        if($tls == 'false') {
            $manager->transporter()->connection()->disableEncryption();
        } else {
            $manager->transporter()->connection()->enableEncryption();
        }


        $manager->transporter()->connection()->connect();
        $serverGreeting = $manager->transporter()->connection()->readMessage();
        $this->writeln($serverGreeting);
        $this->writeln('Sending login details');
        $loginResponse = $manager->run($manager->command()->login()->user($user)->password($password));
        $this->writeln($loginResponse->message()->toString());
        $this->emailClient($manager);

    }

    /**
     * Connect to a real imap server and view messages
     * @param array $opt
     * @throws ErrorException
     * @throws \SalesAgility\Imap\Token\TokenException
     * @throws \SalesAgility\Imap\ImapException
     */
    public function runPhpImapExtensionEmailClient($opt = [
        'address' => '',
        'port' => '',
        'tls' => '',
        'user' => '',
        'password' => ''
    ])
    {
        $log = new Logger('name');
        $log->pushHandler(new StreamHandler(__DIR__ . '/tests/_output/functional_tests.log', Logger::DEBUG));
        $log->info('Robo task: runTests');

        $opt['address'] = $this->askIfEmpty($opt['address'], 'address', '127.0.0.1');
        $opt['port'] = $this->askIfEmpty($opt['port'], 'port', '143');
        $opt['tls'] = $this->confirmIfEmpty($opt['tls'], 'tls');
        $opt['user'] = $this->askIfEmpty($opt['user'], 'user', 'user');
        $opt['password'] = $this->askIfEmpty($opt['password'], 'password', '');

        $log->info(var_export($opt, true));

        $log = new Logger('name');
        $log->pushHandler(new StreamHandler(__DIR__ . '/tests/_output/functional_tests.log', Logger::DEBUG));
        $log->info('loading global bootstrap file');
        $managerFactory = ManagerFactory::instance();
        $manager = $managerFactory->PhpImapExtensionManager();
        $manager->setLogger($log);
        $address = $opt['address'];
        $port = $opt['port'];
        $tls = $opt['tls'];
        $user = $opt['user'];
        $password = $opt['password'];


        $this->say('Connecting to '. "tcp://$address:$port");

        $manager->transporter()->connection()->setConnectionString("tcp://$address:$port");

        if($tls == 'false') {
            $manager->transporter()->connection()->disableEncryption();
        } else {
            $manager->transporter()->connection()->enableEncryption();
        }


        $manager->transporter()->connection()->connect();
        $this->writeln('Sending login details');
        $loginResponse = $manager->run($manager->command()->login()->user($user)->password($password));
        $this->writeln($loginResponse->message()->toString());

        $this->emailClient($manager);
    }


    /**
     * @param \SalesAgility\Imap\Manager\PimapManager | \SalesAgility\Imap\Manager\PhpImapExtensionManager $manager
     * @throws ErrorException
     * @throws \SalesAgility\Imap\ImapException
     * @throws \SalesAgility\Imap\Token\TokenException
     */
    private function emailClient( $manager)
    {
        $mailbox = 'INBOX';
        $this->writeln('Selecting Mailbox: '. $mailbox);
        /** @var \SalesAgility\Imap\Response\Mailbox $selectMailBox */
        $selectMailBox = $manager->run($manager->command()->select($mailbox));


        $this->writeln('Fetching 20 Emails:');
        /** @var \SalesAgility\Imap\Response\MessageList $messages */
        $messages = $manager->run($manager->command()->fetchRange(1, 20)->flags()->uids()->header()->build());

        while(1) {
            // Display List View
            foreach ($messages as $message) {
                $line  = '';
                $line .= 'Message: ';
                $line .= $message->number();
                $line .= "\t";
                $line .= 'From: ';
                $line .= $message->header()->from()[0] ;
                $line .= "\t";
                $line .= 'Subject: ';
                $line .= $message->header()->subject();
                $line .= "\t\t";
                $line .= 'Attachments: ';
                $line .= $message->body()->structure()->attachmentsExists() ? '[A]' : '[X]';
                $line .= "\t\t";
                $line .= 'Date: ';
                $line .= $message->header()->date()->format(DateTime::RFC2822);
                $this->writeln($line);
            }
            $messageInput = $this->ask('Select message number (just press enter to exit):');

            if(empty(trim($messageInput)) || trim($messageInput) == 0) {
                break;
            } else {
                // Display Detail View
                /** @var \SalesAgility\Imap\Response\MessageList $messageSelected */
                $messageSelected = $manager->run($manager->command()->fetch($messageInput)->flags()->uids()->header()->body()->build());
                $line = '';
                $line .= 'Message Number: ' . $messageSelected->offsetGet(0)->number() . PHP_EOL;
                $line .= 'From: ' . $messageSelected->offsetGet(0)->header()->from()[0] . PHP_EOL;
                $line .= 'Subject: ' . $messageSelected->offsetGet(0)->header()->subject() . PHP_EOL;
                $line .= 'Message: ' . PHP_EOL . $messageSelected->offsetGet(0)->body()->text() . PHP_EOL;
                $line .= 'Attachments: ';
                $line .= $messageSelected->offsetGet(0)->body()->structure()->attachmentsExists() ? '[A]' : '[X]';
                $line .= PHP_EOL;
                if($messageSelected->offsetGet(0)->body()->structure()->attachmentsExists()) {
                    $line .= PHP_EOL;
                    $attachments = $messageSelected->offsetGet(0)->body()->attachments();
                    foreach ($attachments as $attachment) {
                        $line .= $attachment->structure()->name();
                        $line .= ' ('. $attachment->structure()->type().') ';
                        $line .= $attachment->structure()->size();
                        $line .= PHP_EOL;
                    }
                }

                $line .= 'Date: ' . $messageSelected->offsetGet(0)->header()->date()->format(DateTime::RFC2822) . PHP_EOL;
                $this->writeln($line);

                $messageInput = $this->ask('Press enter to see list view:');
            }
        };

        $this->writeln('Logging out');
        $logoutResponse = $manager->run($manager->command()->logout());
        $this->writeln($logoutResponse->message()->toString());
        $manager->transporter()->connection()->disconnect();

    }

    /**
     * Print out the command and responses for use of test data
     * @param array $opt
     * @throws ErrorException
     * @throws \SalesAgility\Imap\Token\TokenException
     */
    public function generateTestData($opt = [
        'address' => '',
        'port' => '',
        'tls' => '',
        'user' => '',
        'password' => ''
    ])
    {
        $log = new Logger('name');
        $log->pushHandler(new StreamHandler(__DIR__ . '/tests/_output/functional_tests.log', Logger::DEBUG));
        $log->info('Robo task: runTests');

        $opt['address'] = $this->askIfEmpty($opt['address'], 'address', '127.0.0.1');
        $opt['port'] = $this->askIfEmpty($opt['port'], 'port', '143');
        $opt['tls'] = $this->confirmIfEmpty($opt['tls'], 'tls');
        $opt['user'] = $this->askIfEmpty($opt['user'], 'user', 'user');
        $opt['password'] = $this->askIfEmpty($opt['password'], 'password', '');

        $managerFactory = ManagerFactory::instance();
        $manager = $managerFactory->PimapManager();
        $manager->transporter()->connection()->setLogger($log);
        $address = $opt['address'];
        $port = $opt['port'];
        $tls = $opt['tls'];
        $user = $opt['user'];
        $password = $opt['password'];


        $this->say('Connecting to '. "tcp://$address:$port");

        $manager->transporter()->connection()->setConnectionString("tcp://$address:$port");

        if($tls == 'false') {
            $manager->transporter()->connection()->disableEncryption();
        } else {
            $manager->transporter()->connection()->enableEncryption();
        }


        $manager->transporter()->connection()->connect();
        $serverGreeting = $manager->transporter()->connection()->readMessage();
        $this->writeln($serverGreeting);
        $this->writeln('Sending login details');
        $loginResponse = $manager->run($manager->command()->login()->user($user)->password($password));
        $this->writeln($loginResponse->message());
        $this->writeln('Selecting Mailbox: INBOX');
        $selectMailBox = $manager->run($manager->command()->select('INBOX'));
        $this->writeln($selectMailBox->message());


        $this->writeln('Fetching 20 Emails:');
        /** @var \SalesAgility\Imap\Response\MessageList $messages */
        $messages = $manager->run($manager->command()->raw('FETCH 2672:2682 (UID FLAGS BODY[HEADER] BODYSTRUCTURE BODY[TEXT])'));
        $response = $manager->pipeline()->getLastPipe()->getResponse();
        $this->writeln('Output response: '.__DIR__.'/tests/_output/testdata.log');
        file_put_contents(__DIR__ . '/tests/_output/testdata.log', $response);



        $logoutResponse = $manager->run($manager->command()->logout());
        $this->writeln($logoutResponse->message());
        $manager->transporter()->connection()->disconnect();

    }


    public function addCopyright($opt = ['directory' => ''])
    {
        $copyright = file_get_contents(__DIR__.'/COPYRIGHT');
        $opt['directory'] = $this->askIfEmpty($opt['directory'], 'Directory', '.');
        $directory = new RecursiveDirectoryIterator(realpath($opt['directory']));
        $iterator = new RecursiveIteratorIterator($directory);
        $regex = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
        foreach ($regex as $files) {
            foreach ($files as $file) {
                $contents = file_get_contents($file);
                $replaced = preg_replace('/^<\?php/i', "<?php\n".$copyright, $contents);
                $isReplaced = file_put_contents($file, $replaced);
                if ($isReplaced !== false) {
                    $this->writeln('Replaced '. $file);
                } else {
                    $this->writeln('Error writing to  '. $file);
                }
            }
        }
    }

    private function askIfEmpty($option, $text, $default) {
        if(empty($option)) {
            return $this->askDefault($text, $default);
        }
        return $option;
    }

    private function confirmIfEmpty($option, $text) {
        if(empty($option)) {
            return (string)$this->confirm($text);
        }
        return $option;
    }
}