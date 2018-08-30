<?php

namespace SalesAgility\Utility;

use Psr\Log\AbstractLogger;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * /**
 * PSR-3 Compliant logger
 * Class PimapLogger
 * @see http://www.php-fig.org/psr/psr-3/
 */
class PimapLogger extends AbstractLogger implements LoggerInterface
{
    /**
     * @param LogLevel|string $level
     * @param string $message eg 'hello {user}'
     * @param array $context eg array(user => 'joe')
     * @throws InvalidArgumentException
     */
    public function log($level, $message, array $context = array())
    {
        $message = $this->interpolate($message, $context);
        switch ($level) {
            case LogLevel::EMERGENCY:
                /** @noinspection PhpUndefinedMethodInspection */
                $this->php_error_log('[EMERGENCY] ' . $message);
                break;
            case LogLevel::ALERT:
                /** @noinspection PhpUndefinedMethodInspection */
                $this->php_error_log('[ALERT] ' . $message);
                break;
            case LogLevel::CRITICAL:
                /** @noinspection PhpUndefinedMethodInspection */
                $this->php_error_log('[CRITICAL] ' . $message);
                break;
            case LogLevel::ERROR:
                /** @noinspection PhpUndefinedMethodInspection */
                $this->php_error_log('[ERROR] ' . $message);
                break;
            case LogLevel::WARNING:
                /** @noinspection PhpUndefinedMethodInspection */
                $this->php_error_log('[WARNING] ' . $message);
                break;
            case LogLevel::NOTICE:
                /** @noinspection PhpUndefinedMethodInspection */
                $this->php_error_log('[NOTICE] ' . $message);
                break;
            case LogLevel::INFO:
                /** @noinspection PhpUndefinedMethodInspection */
                $this->php_error_log('[INFO] ' . $message);
                break;
            case LogLevel::DEBUG:
                /** @noinspection PhpUndefinedMethodInspection */
                $this->php_error_log('[DEBUG] ' . $message);
                break;
            default:
                throw new \InvalidArgumentException('Invalid Log Level');
        }
    }

    /**
     * build a replacement array with braces around the context keys
     * @param $message
     * @param array $context
     * @return string
     */
    private function interpolate($message, array $context = array())
    {
        $replace = array();

        if (empty($context)) {
            return $message;
        }

        foreach ($context as $key => $val) {
            // check that the value can be casted to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        return strtr($message, $replace);
    }

    /**
     * @param string $message
     */
    protected function php_error_log($message)
    {
        error_log($message);
    }
}
