<?php

namespace Aweb\Nexus;

class Route
{
    private function __construct() {}

    const ADMIN = 1;
    const CATALOG = 0;

    /**
     * call this->url-link() with upgraded options, cross OpenCart version and auto added token on Admin
     *
     * @param string $name
     * @param array $qs
     * @param self $type [Route::ADMIN or Route::CATALOG]
     * @return string
     */
    public static function link(string $name, array $qs = [], $type = null): string
    {
        if (is_null($type)) {
            // check if admin or catalog
            if (defined('DIR_CATALOG')) {
                $type = self::ADMIN;
            } else {
                $type = self::CATALOG;
            }
        }

        if ($type === self::ADMIN) {
            if (version_compare(VERSION, '3.0', '<')) {
                $qs['token'] = Session::get('token');
            } else {
                $qs['user_token'] = Session::get('user_token');
            }
        }

        $url = Nexus::getRegistry('url')->link($name, http_build_query($qs), true);
        if ($type === self::CATALOG) {
            $host = Request::getHost();
            $url = str_replace($host.'/admin/', $host.'/', $url); // more exact replacement
        }

        return html_entity_decode($url);
    }
}