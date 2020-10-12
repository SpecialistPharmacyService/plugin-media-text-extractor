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

        $response = $this->extract_text_from_files($files);
        $count    = Util::safely_get_attribute($response, 'count');

        $after       = microtime(true);
        $search_time = ($after - $before) . " sec";
        Util::debug('ExtractionManager#extract', 'Extracted text: ' . $search_time);

        // update count
        $status['count'] = $status['count'] + intval($count);

        if ($status['count'] >= $status['total']) {
            $status['completed'] = true;
        } else {
            // only update page if we're not complete
            $status['page'] = $page + 1;
        }

        SettingsManager::get_instance()->set(Constants::OPTION_STATUS, $status);

        Util::debug('ExtractionManager#extract', 'exit');

        return $status;
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
                            $text = $extractor->extract($filepath);
                            // false is a failure or matches existing value hence can't infer error from false
                            $result = $this->save_extracted_text($file, $text);
                        } catch (Exception $error) {
                            $errors[] = $error;
                        }
                    }

                    // increement count either way - log failed saves
                    $count++;
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