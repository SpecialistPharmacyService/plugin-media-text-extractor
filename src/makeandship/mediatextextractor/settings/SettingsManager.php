<?php

namespace makeandship\mediatextextractor\settings;

use makeandship\mediatextextractor\Constants;
use makeandship\mediatextextractor\Util;

class SettingsManager
{
    protected static $instance = null;

    // can't be instantiated externally
    protected function __construct()
    {
    }
    protected function __clone()
    {
    } // no clone

    public static function get_instance()
    {
        if (SettingsManager::$instance === null) {
            SettingsManager::$instance = new SettingsManager();
            SettingsManager::$instance->initialize();
        }
        return SettingsManager::$instance;
    }

    protected function initialize()
    {
        $this->get_settings(true);
    }

    /**
     * Get the current configuration.  Configuration values
     * are cached.  Use the $fresh parameter to get an updated
     * set
     *
     * @param $fresh - true to get updated values
     * @return array of settings
     */
    public function get_settings($fresh = false)
    {
        if (!isset($this->settings) || $fresh) {
            $this->settings = array();

            $this->settings[Constants::OPTION_ACF_FIELD_NAME] = $this->get_option(Constants::OPTION_ACF_FIELD_NAME);
            $this->settings[Constants::OPTION_STATUS]         = $this->get_option(Constants::OPTION_STATUS);
        }

        return $this->settings;
    }

    public function get($name)
    {
        $settings = $this->get_settings();

        return Util::safely_get_attribute($settings, $name);
    }

    public function set($name, $value)
    {
        if ($this->valid_setting($name)) {
            $this->set_option($name, $value);

            if ($this->settings) {
                $this->settings[$name] = $value;
            }
        }
    }

    private function valid_setting($name)
    {
        if ($name) {
            if (in_array($name, [
                Constants::OPTION_ACF_FIELD_NAME,
                Constants::OPTION_STATUS,
            ])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve an option for a given key.  If this is a
     * network installation, finds a network site option otherwise
     * finds a local site option
     *
     * @param $key the name of the option
     *
     * @return the option value
     */
    public function get_option($key)
    {
        if (is_multisite()) {
            return get_site_option($key);
        } else {
            return get_option($key);
        }
    }

    /**
     * Set an option for a given key.  If this is a
     * network installation, sets a network site option otherwise
     * sets a local site option
     *
     * @param $key the name of the option
     * @param $value to store
     */
    public function set_option($key, $value)
    {
        if (is_multisite()) {
            return update_site_option($key, $value);
        } else {
            return update_option($key, $value);
        }
    }

    /**
     * Get a value from a config where that config contains optional
     * constants, environment variables (or environment variable files)
     */
    public function get_option_from_config($config)
    {
        if ($config) {
            $const_settings = Util::safely_get_attribute($config, 'const');
            if ($const_settings && is_array($const_settings) && count($const_settings) > 0) {
                foreach ($const_settings as $const_setting) {
                    if (defined($const_setting)) {
                        $value = constant($const_setting);
                        if ($value) {
                            return $value;
                        }
                    }
                }
            }

            $env_settings = Util::safely_get_attribute($config, 'env');
            if ($env_settings && is_array($env_settings) && count($env_settings) > 0) {
                foreach ($env_settings as $env_setting) {
                    // get the value
                    $value = getenv($env_setting);
                    if ($value) {
                        return $value;
                    }

                    // get the value from a file in the value
                    $filename_env = $env_setting . '_FILE';
                    $filename     = getenv($filename_env);
                    if (file_exists($filename)) {
                        $value = file_get_contents($filename);
                        if ($value) {
                            return $value;
                        }
                    }
                }
            }
        }

        return null;
    }

    public function is_extraction_supported()
    {
        $extractor = $this->get_extractor_name();
        if ($extractor) {
            return true;
        }

        return false;
    }

    public function get_extractor_name()
    {
        if ($this->is_tika_supported()) {
            return 'tika';
        }

        return null;
    }

    public function is_tika_supported()
    {
        exec("/usr/local/bin/tika --version 2>&1", $rows);
        if ($rows) {
            foreach ($rows as $row) {
                $matches = array();
                preg_match('/Apache Tika (\d+\.\d+\.\d+)/', $row, $matches, PREG_OFFSET_CAPTURE);

                $version = is_array($matches) && count($matches) > 0 ? $matches[1][0] : null;
                if ($version) {
                    return true;
                }
            }
        }

        return false;
    }
}