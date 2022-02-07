<?php

namespace Aweb\Nexus;

class Url
{
    private function __construct() {}

    const ADMIN = 2;
    const CATALOG = 3;

    /**
     * call this->url-link() with upgraded options, cross OpenCart version and auto added token on Admin
     *
     * @param string $name oc route name
     * @param array $params | query string params
     * @param int|bool $force when true|1, if script is on Admin, compose a catalog route, or when on catalog, compose Admin Route
     * @return string
     */
    public static function route(string $name, array $params = [], $force = null): string
    {
        if (defined('DIR_CATALOG')) {
            $type = self::ADMIN;
        } else {
            $type = self::CATALOG;
        }

        // if $force is true, switch them
        if ($force === true || $force === 1) {
            $type = $type === self::ADMIN ? self::CATALOG : self::ADMIN;
        }
        elseif ($force) {
            $type = $force;
        }

        if ($type === self::ADMIN) {
            $params[getTokenKey()] = Session::get(getTokenKey());
        }

        $url = Nexus::getRegistry('url')->link($name, http_build_query($params), true);
        if ($type === self::CATALOG) {
            $host = Request::getHost();
            $url = str_replace($host.'/admin/', $host.'/', $url); // more exact replacement
        }

        return html_entity_decode($url);
    }

    /**
     * Compose route from given params then redirect to it.
     *
     * @param string $name oc route name
     * @param array $params query strings
     * @param integer $code redirect code, default 302
     * @param int|bool $force when true|1, if script is on Admin, compose a catalog route, or when on catalog, compose Admin Route
     * @return void
     */
    public static function redirectTo(string $name, array $params = [], $code = 302, $force = null)
    {
        $url = self::route($name, $params, $force);
        Nexus::getRegistry('response')->redirect($url, $code);
    }

    public static function redirect(string $url, $code = 302)
    {
        Nexus::getRegistry('response')->redirect($url, $code);
    }
}