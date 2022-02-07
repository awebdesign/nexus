<?php


namespace Aweb\Nexus\Support;

use Exception;
use Aweb\Nexus\Cache;
use Aweb\Nexus\Support\Arr;

class Updater
{
    private $version;

    public function __construct() {
        $this->checkVersion();
    }

    public function isNewVersionAvailable() {
        $latest_version = Cache::get('nexus_updater');

        if (empty($latest_version) || (!empty($latest_version['last_version_check_date']) && $latest_version['last_version_check_date'] < date('Y-m-d'))) {
            $latest_version = $this->getLatestVersion();
        }

        if(empty($latest_version)) {
            throw new Exception('Error checking last release!');
        }

        $version_available = Arr::get($latest_version, 'version');

        if ($version_available > $this->getCurrentVersion()) {
            return $version_available;
        }

        return false;
    }

    /**
     * Load current version from composer.json
     *
     * @return void
     */
    public function checkVersion() {
        $composerLocation  = realpath(DIR_SYSTEM . 'library/aweb/nexus/composer.json');
        if(!file_exists($composerLocation)) {
            throw new Exception('Nexus is corrupted! composer.json is missing from Nexus');
        }

        $composerData = file_get_contents($composerLocation);
        $data = json_decode($composerData);

        $this->version = $data->version;
    }

    /**
     * Return package version
     *
     * @return string
     */
    public function getCurrentVersion() {
        return $this->version;
    }

    /**
     * Get GitHu'sb last release
     *
     * @return array
     */
    private function getLatestVersion() {

        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://api.github.com/repos/awebdesign/nexus/releases/latest');
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_USERAGENT, "curl");

            ob_start();
            curl_exec($curl);
            curl_close($curl);
            $lines = ob_get_contents();
            ob_end_clean();
            $json = json_decode($lines, true);

            if (!$json || !isset($json['tag_name'])) {
                return null;
            }

            $latest_version = $json['tag_name'];

            if((substr($latest_version, 0, 1) == 'v')) {
                $json['last_version_check_date'] = date("Y-m-d");
                $json['version'] = substr($latest_version, 1);

                Cache::set('nexus_updater', $json);

                return $json;
            }
        } catch (Exception $e) {}

        return null;
    }

    public function doUpdate()
    {
        //TODO:
        //get last version info
        //download last version
        //delete aweb/nexus folder
        //extract to opencart route the new fodler contents
        //if there are errors throw exceptions else return true for success

        throw new Exception('Not ready yet');
    }
}