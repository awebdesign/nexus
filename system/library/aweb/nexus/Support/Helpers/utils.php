<?php

use Aweb\Nexus\Session;

if (! function_exists('_error')) {
    /**
     * Echo error of key given
     *
     * @param string $key
     * @return void
     */
    function _error(string $key)
    {
        $err = Session::get('_errors.'.$key);
        if ($err) {
            echo '<span class="text-danger">'.$err.'</span>';
        }
    }
}

if (! function_exists('_alerts')) {
    /**
     * Will show a bootstrap alert element containing: success, warning or errors by default
     *
     * @param string $key "success" | "warning" | "errors" | if is null show all
     * @return void
     */
    function _alerts(string $key = null, $default = null)
    {
        $keys = is_null($key) ? ['success', 'warning', 'errors'] : [$key];

        foreach($keys as $key)
        {
            $alerts = (array) Session::get('_'.$key, $default);
            if ($alerts) {
                $class = $key === 'errors' ? 'danger' : $key;
                echo '<div class="alert alert-'.$class.'">
                    <ul>';
                    foreach ($alerts as $a) {
                        echo "<li>$a</li>";
                    }
                echo '</ul>
                </div>';
            }
        }
    }
}