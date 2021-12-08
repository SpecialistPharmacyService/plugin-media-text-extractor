<?php
/*
Plugin Name: Media Text Extractor
Plugin URI:  https://www/makeandship.com/blog/plugin-media-text-extractor
Description: Extract text from media files
Version:     1.0.5
Author:      Make and Ship Limited
Author URI:  https://www.makeandship.com/
License:     MIT
License URI: https://opensource.org/licenses/MIT
 */
// exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

require_once dirname(__FILE__) . '/src/makeandship/mediatextextractor/Constants.php';
require_once dirname(__FILE__) . '/src/makeandship/mediatextextractor/ExtractionManager.php';
require_once dirname(__FILE__) . '/src/makeandship/mediatextextractor/MediaManager.php';
require_once dirname(__FILE__) . '/src/makeandship/mediatextextractor/MediaTextExtractorManager.php';
require_once dirname(__FILE__) . '/src/makeandship/mediatextextractor/Util.php';
require_once dirname(__FILE__) . '/src/makeandship/mediatextextractor/admin/HtmlUtils.php';
require_once dirname(__FILE__) . '/src/makeandship/mediatextextractor/admin/UserInterfaceManager.php';
require_once dirname(__FILE__) . '/src/makeandship/mediatextextractor/extractors/TextExtractor.php';
require_once dirname(__FILE__) . '/src/makeandship/mediatextextractor/extractors/TextExtractorFactory.php';
require_once dirname(__FILE__) . '/src/makeandship/mediatextextractor/extractors/TikaTextExtractor.php';
require_once dirname(__FILE__) . '/src/makeandship/mediatextextractor/settings/SettingsHelper.php';
require_once dirname(__FILE__) . '/src/makeandship/mediatextextractor/settings/SettingsManager.php';

use makeandship\mediatextextractor\Constants;
use makeandship\mediatextextractor\MediaTextExtractorManager;

// check if class already exists
if (!class_exists('MediaTextExtractorPlugin')):

    require_once __DIR__ . '/vendor/autoload.php';

    class MediaTextExtractorPlugin
{

        /*
         *  __construct
         *
         *  This function will setup the class functionality
         *
         *  @type    function
         *  @date    17/02/2016
         *  @since    1.0.0
         *
         *  @param    n/a
         *  @return    n/a
         */

        public function __construct()
    {
            // vars
            $this->settings = array(
                'version' => Constants::VERSION,
                'url'     => plugin_dir_url(__FILE__),
                'path'    => plugin_dir_path(__FILE__),
            );

            $this->manager = new MediaTextExtractorManager();

            // activation
            register_activation_hook(__FILE__, array($this->manager, 'activate'));
            register_deactivation_hook(__FILE__, array($this->manager, 'deactivate'));

        }

    }

    // initialize
    new MediaTextExtractorPlugin();

// class_exists check
endif;
