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
/**
 * Php imap extension
 * @see \SalesAgility\Imap\Stream\PhpImpExtensionConnection
 * @see http://php.net/manual/en/ref.imap.php
 */

namespace SalesAgility\Imap\Stream;

/**
 * @param $mailbox
 * @param $username
 * @param $password
 * @param int $options
 * @param int $n_retries
 * @param null $params
 * @return \stdClass
 */
function imap_open($mailbox, $username, $password, $options = 0, $n_retries = 0, $params = NULL)
{
    if(array_key_exists('mock_imap_open', $GLOBALS)) {
        return $GLOBALS['mock_imap_open'];
    }

    return new \stdClass();
}

/**
 * @param $imap_stream
 * @return bool
 */
function imap_ping($imap_stream)
{
    if(array_key_exists('mock_imap_ping', $GLOBALS)) {
        return $GLOBALS['mock_imap_ping'];
    }

    return true;
}

/**
 * @param $imap_stream
 * @param $mailbox
 * @param int $options
 * @param int $n_retries
 * @return bool
 */
function imap_reopen($imap_stream, $mailbox, $options = 0, $n_retries = 0)
{
    if(array_key_exists('mock_imap_reopen', $GLOBALS)) {
        return $GLOBALS['mock_imap_reopen'];
    }

    return true;
}

/**
 * @return array
 */
function imap_errors()
{
    if(array_key_exists('mock_imap_errors', $GLOBALS)) {
        return $GLOBALS['mock_imap_errors'];
    }

    return array();
}

/**
 * @param $imap_stream
 * @return int
 */
function imap_num_msg($imap_stream)
{
    if(array_key_exists('mock_imap_num_msg', $GLOBALS)) {
        return $GLOBALS['mock_imap_num_msg'];
    }

    return 0;
}

/**
 * @param $imap_stream
 * @return int
 */
function imap_num_recent($imap_stream)
{
    if(array_key_exists('mock_imap_num_recent', $GLOBALS)) {
        return $GLOBALS['mock_imap_num_recent'];
    }

    return 0;
}


/**
 * @return array|mixed
 */
function imap_fetch_overview()
{
    if(array_key_exists('mock_imap_fetch_overview', $GLOBALS)) {
        return array_pop($GLOBALS['mock_imap_fetch_overview']);
    }

    return new \stdClass();
}
/**
 * @return array|mixed
 */
function imap_fetchstructure()
{
    if(array_key_exists('mock_imap_fetchstructure', $GLOBALS)) {
        return array_pop($GLOBALS['mock_imap_fetchstructure']);
    }

    return new \stdClass();
}


/**
 * @return array|mixed
 */
function imap_fetchbody()
{
    if(array_key_exists('mock_imap_fetchbody', $GLOBALS)) {
        return array_pop($GLOBALS['mock_imap_fetchbody']);
    }

    return new \stdClass();
}

/**
 * @return array|mixed
 */
function imap_body()
{
    if(array_key_exists('mock_imap_body', $GLOBALS)) {
        return array_pop($GLOBALS['mock_imap_body']);
    }

    return new \stdClass();
}


/**
 * @return array|mixed
 */
function imap_search($imap_stream, $criteria, $options = SE_FREE, $charset = NULL)
{
    if(array_key_exists('mock_imap_search', $GLOBALS)) {
        return $GLOBALS['mock_imap_search'];
    }

    return array();
}

function imap_setflag_full()
{
    if(array_key_exists('mock_imap_setflag_full', $GLOBALS)) {
        return $GLOBALS['mock_imap_setflag_full'];
    }

    return false;
}

function imap_clearflag_full()
{
    if(array_key_exists('mock_imap_clearflag_full', $GLOBALS)) {
        return $GLOBALS['mock_imap_clearflag_full'];
    }

    return false;
}


function imap_mail_copy()
{
    if(array_key_exists('mock_imap_mail_copy', $GLOBALS)) {
        return $GLOBALS['mock_imap_mail_copy'];
    }

    return false;
}


function imap_list()
{
    if(array_key_exists('mock_imap_list', $GLOBALS)) {
        return $GLOBALS['mock_imap_list'];
    }

    return array();
}

function imap_lsub()
{
    if(array_key_exists('mock_imap_lsub', $GLOBALS)) {
        return $GLOBALS['mock_imap_lsub'];
    }

    return array();
}

function imap_check()
{
    if(array_key_exists('mock_imap_check', $GLOBALS)) {
        return $GLOBALS['mock_imap_check'];
    }

    return true;
}

function imap_delete()
{
    if(array_key_exists('mock_imap_delete', $GLOBALS)) {
        return $GLOBALS['mock_imap_delete'];
    }

    return true;
}

function imap_renamemailbox()
{
    if(array_key_exists('mock_imap_renamemailbox', $GLOBALS)) {
        return $GLOBALS['mock_imap_renamemailbox'];
    }

    return true;
}

function imap_createmailbox()
{
    if(array_key_exists('mock_imap_createmailbox', $GLOBALS)) {
        return $GLOBALS['mock_imap_createmailbox'];
    }

    return true;
}

function imap_subscribe()
{
    if(array_key_exists('mock_imap_subscribe', $GLOBALS)) {
        return $GLOBALS['mock_imap_subscribe'];
    }

    return true;
}

function imap_unsubscribe()
{
    if(array_key_exists('mock_imap_unsubscribe', $GLOBALS)) {
        return $GLOBALS['mock_imap_unsubscribe'];
    }

    return true;
}

function imap_expunge()
{
    if(array_key_exists('mock_imap_expunge', $GLOBALS)) {
        return $GLOBALS['mock_imap_expunge'];
    }

    return true;
}

function imap_append()
{
    if(array_key_exists('mock_imap_append', $GLOBALS)) {
        return $GLOBALS['mock_imap_append'];
    }

    return true;
}


function imap_last_error() {
    if(array_key_exists('mock_imap_last_error', $GLOBALS)) {
        return $GLOBALS['mock_imap_last_error'];
    }

    return '';
}

/**
 * @param $imap_stream
 * @param int $flag
 * @return bool
 * @see http://php.net/manual/en/function.imap-close.php
 */
function imap_close($imap_stream, $flag = 0)
{
    if(array_key_exists('mock_imap_close', $GLOBALS)) {
        return $GLOBALS['mock_imap_close'];
    }

    return true;
}
