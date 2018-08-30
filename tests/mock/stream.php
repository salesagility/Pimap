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
 * Used to mock stream calls
 * @see \SalesAgility\Stream\Connection
 * @see http://php.net/manual/en/ref.stream.php
 */
namespace SalesAgility\Stream;

function stream_socket_client()
{
    if(array_key_exists('mock_stream_socket_client', $GLOBALS)) {
        return $GLOBALS['mock_stream_socket_client'];
    }

    return new \stdClass();
}

function stream_socket_enable_crypto()
{
    if(array_key_exists('mock_stream_socket_enable_crypto_exception', $GLOBALS)) {
        throw $GLOBALS['mock_stream_socket_enable_crypto_exception'];
    }

    if(array_key_exists('mock_stream_socket_enable_crypto', $GLOBALS)) {
        return $GLOBALS['mock_stream_socket_enable_crypto'];
    }

    return false;
}

function fclose()
{
    if(array_key_exists('mock_fclose', $GLOBALS)) {
        return $GLOBALS['mock_fclose'];
    }

    return false;
}

function fwrite()
{
    if(array_key_exists('mock_fwrite', $GLOBALS)) {
        return $GLOBALS['mock_fwrite'];
    }

    return false;
}

function fgets()
{
    if(array_key_exists('mock_fgets', $GLOBALS)) {
        return $GLOBALS['mock_fgets'];
    }

    return false;
}

function feof()
{
    if(array_key_exists('mock_feof', $GLOBALS)) {
        return $GLOBALS['mock_feof'];
    }

    return false;
}