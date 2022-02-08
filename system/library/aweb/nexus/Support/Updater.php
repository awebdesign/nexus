<?php


namespace Aweb\Nexus\Support;

use Exception;
use ErrorException;
use Aweb\Nexus\Cache;
use Aweb\Nexus\Support\Arr;
use ZipArchive;
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

        $version_available = Arr::get($latest_version, 'version');

        if ($version_available > $this->getCurrentVersion()) {
            return $latest_version;
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
        $latest_version = $this->isNewVersionAvailable();
        if(empty($latest_version['assets']) || !is_array($latest_version['assets']))
        {
            throw new Exception('There is no update available!');
        }

        $filtered = Arr::where($latest_version['assets'], function($value) use ($latest_version) {
            if(!empty($value['browser_download_url']) && !empty($value['name']) && $value['name'] == 'v' . $latest_version['version'] . '.zip') {
                return true;
            }
        });
        $asset = reset($filtered);

        if(empty($asset))
        {
            throw new Exception('There is no valid package ready for download!');
        }

        try {
            $packageFile = $this->prepareDownload();

            $this->download($asset['browser_download_url'], $packageFile);

            $this->checkzip($packageFile);

            $this->delete_dir(DIR_SYSTEM . 'library/aweb/nexus');

            $this->unzip($packageFile);
        } catch(ErrorException $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Chceck update package integrity
     *
     * @param [type] $update_file
     * @return void
     */
    private function checkzip($update_file)
    {
        try {
            $zip = new ZipArchive;
        } catch(Exception $e) {
            throw new ErrorException("PHP Zip extension not installed! " . $e->getMessage());
        }

        $update_file = realpath($update_file);

        if(!file_exists($update_file)) {
            throw new ErrorException("Missing file {$update_file} for extraction!");
        }

        // ZipArchive::CHECKCONS will enforce additional consistency checks
        $res = $zip->open($update_file, ZipArchive::CHECKCONS);
        if(!$res) {
            throw new ErrorException('Error opening update file');
        }

        if($res === ZipArchive::ER_OPEN) {
            throw new ErrorException('Can\'t open update package');
        }elseif($res === ZipArchive::ER_READ) {
            throw new ErrorException('Can\'t read the update package');
        }elseif($res === ZipArchive::ER_NOZIP) {
            throw new ErrorException('The update package is not a zip archive');
        }elseif($res === ZipArchive::ER_INCONS) {
            throw new ErrorException('Update package consistency check failed');
        }elseif($res === ZipArchive::ER_CRC) {
            throw new ErrorException('Update package checksum failed');
        }
    }

    /**
     * Unzip update package
     *
     * @param string $update_file
     * @return void
     */
    private function unzip($update_file)
    {
        try {
            $zip = new ZipArchive;
        } catch(Exception $e) {
            throw new ErrorException("PHP Zip extension not installed! " . $e->getMessage());
        }

        $update_file = realpath($update_file);

        if(!file_exists($update_file)) {
            throw new ErrorException("Missing file {$update_file} for extraction!");
        }

        if ($zip->open($update_file) === true) {
            if (!$zip->extractTo(DIR_SYSTEM . '../')) {
                throw new ErrorException("Zip file extraction failed!");
            }
            $zip->close();

            $this->clean_tmp_files();
        } else {
            throw new ErrorException("Update package is corrupted!");
        }
    }

    /**
     * Download update package method
     *
     * @param string $url
     * @param string $file_path
     * @return mixed
     */
    private function download($url, $file_path)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $resource = fopen($file_path, "w+");
        curl_setopt($ch, CURLOPT_FILE, $resource);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);

        $response = curl_exec($ch);

        //check is there was a CURL error
        if (curl_errno($ch)) {
            $error = curl_error($ch);

            curl_close($ch);

            throw new ErrorException($error);
        } elseif (empty($response)) { //check for empty response
            throw new ErrorException("Could not download the update");
        }

        curl_close($ch);

        return $response;
    }

    /**
     * Prepare temp folder for downloading the update package
     *
     * @return void
     */
    private function prepareDownload()
    {
        $tmp_dir = @ini_get('upload_tmp_dir');
        if (!$tmp_dir) {
            $tmp_dir = @sys_get_temp_dir();
            if (!$tmp_dir) {
                $tmp_dir = DIR_UPLOAD;
            }
        }

        if (!is_writable($tmp_dir)) {
            throw new ErrorException("Download directory is not writable {$tmp_dir}");

            return false;
        }

        $tmp_dir = rtrim($tmp_dir, '/') . '/update/';
        $this->tmp_dir = $tmp_dir;

        $this->clean_tmp_files();

        if (!is_dir($tmp_dir)) {
            mkdir($tmp_dir);
            fopen($tmp_dir . 'index.html', 'w');
        }

        if (file_exists($tmp_dir . 'nexus.zip')) {
            @unlink($tmp_dir . 'nexus.zip');
        }

        return $tmp_dir . 'nexus.zip'; // Local Zip File Path
    }

    /**
     * Clean temporary download folder
     *
     * @return void
     */
    private function clean_tmp_files()
    {
        if (is_dir($this->tmp_dir)) {
            if (@!$this->delete_dir($this->tmp_dir)) {
                @rename($this->tmp_dir, $this->tmp_dir . 'delete_this_' . uniqid());
            }
        }
    }

    /**
     * Recursive delete method
     *
     * @param string $dirPath
     * @return bool
     */
    private function delete_dir($dirPath)
    {
        if (!is_dir($dirPath)) {
            throw new ErrorException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->delete_dir($file);
            } else {
                unlink($file);
            }
        }
        if (rmdir($dirPath)) {
            return true;
        }

        return false;
    }
}