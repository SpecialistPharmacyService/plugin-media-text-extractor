<?php

namespace makeandship\mediatextextractor;

use makeandship\mediatextextractor\Constants;
use makeandship\mediatextextractor\extractors\TextExtractorFactory;
use makeandship\mediatextextractor\settings\SettingsManager;

class ExtractionManager
{
    public function __construct()
    {
        $this->media_manager            = new MediaManager();
        $this->extracted_text_acf_field = SettingsManager::get_instance()->get(Constants::OPTION_ACF_FIELD_NAME);
    }

    public function extract($fresh)
    {
        Util::debug('ExtractionManager#extract', 'exit');

        $status = SettingsManager::get_instance()->get(Constants::OPTION_STATUS);

        if ($fresh || (!isset($status) || empty($status))) {
            $status = $this->media_manager->initialise_status();

            // store initial state
            SettingsManager::get_instance()->set(Constants::OPTION_STATUS, $status);
        }

        // find the next site to index (or next page in a site to index)
        $page = $status['page'];
        $per  = Constants::DEFAULT_FILES_PER_PAGE;

        // gather files (capture time)
        $before = microtime(true);

        $files = $this->media_manager->get_files(null, $page, $per);

        $after       = microtime(true);
        $search_time = ($after - $before) . " sec";
        Util::debug('ExtractionManager#extract', 'Gathered posts: ' . $search_time);

        // extract files (capture time)
        $before = microtime(true);

        $count = $this->extract_text_from_files($files);

        $after       = microtime(true);
        $search_time = ($after - $before) . " sec";
        Util::debug('ExtractionManager#extract', 'Extracted text: ' . $search_time);

        Util::debug('ExtractionManager#extract', 'exit');
    }

    public function extract_text_from_files($files)
    {
        $count  = 0;
        $errors = array();

        if ($files) {
            $extractor_name = SettingsManager::get_instance()->get_extractor_name();
            if ($extractor_name) {
                $factory   = new TextExtractorFactory();
                $extractor = $factory->get($extractor_name);

                foreach ($files as $file) {
                    $filepath = $this->get_filepath($file);
                    if ($filepath) {
                        try {
                            $text   = $extractor->extract($filepath);
                            $result = $this->save_extracted_text($file, $text);
                            if ($result) {
                                $count++;
                            }
                        } catch (Exception $error) {
                            $errors[] = $error;
                        }
                    }
                }
            }
        }

        return array(
            'errors' => $errors,
            'count'  => $count,
        );
    }

    public function get_filepath($file)
    {
        if ($file && is_array($file)) {
            $filepath = Util::safely_get_attribute($file, 'filepath');
            return $filepath;
        }

        return null;
    }

    public function save_extracted_text($file, $text)
    {
        if ($file && $text) {
            $id   = Util::safely_get_attribute($file, 'ID');
            $name = $this->extracted_text_acf_field;
            if ($id && $name) {
                $result = update_field($name, $text, $id);
                return $result;
            }
        }

        return false;
    }
}