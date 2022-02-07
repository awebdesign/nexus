<?php
/*
 * Created on Fri Dec 20 2019 by DaRock
 *
 * Aweb Design
 * https://www.awebdesign.ro
 *
 */

use Aweb\Nexus\Request;

if (!function_exists('isCompatible')) {
    /**
     * isCompatible - Checks if nexus is compatible or not with you OpenCart version
     *
     * @return boolean
     */
    function isCompatible()
    {
        return (isOc23() || isOc3());
    }
}

if (!function_exists('isOc23')) {
    function isOc23()
    {
        return version_compare(VERSION, '2.3.0.0', '>=');
    }
}

if (!function_exists('isOc3')) {
    function isOc3()
    {
        return version_compare(VERSION, '3.0.0.0') >= 0;
    }
}

if (!function_exists('getToken')) {
    /**
     * getToken - return user token for logged in admin
     *
     * @return string
     */
    function getToken()
    {
        return Request::get(getTokenKey());
    }
}

if (!function_exists('getTokenKey')) {
    /**
     * getTokenKey - return user token key for admin
     *
     * @return string
     */
    function getTokenKey()
    {
        return (isOc3() ? 'user_token' : 'token');
    }
}
