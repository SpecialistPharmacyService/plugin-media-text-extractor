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
        $this->extracted_date_acf_field = SettingsManager::get_instance()->get(Constants::OPTION_ACF_DATE_FIELD_NAME);
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

    public function extract_text_from_file($file)
    {
        if ($file) {
            if (is_array($file) && Util::is_array_sequential($file)) {
                return $this->extract_text_from_files($file);
            } else {
                return $this->extract_text_from_files([$file]);
            }
        }

        return array(
            'errors' => array(),
            'count'  => 0,
        );
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
                            $has_run_extraction = $this->has_extracted_text($file);
                            if (!$has_run_extraction) {
                                $text = $extractor->extract($filepath);
                                // false is a failure or matches existing value hence can't infer error from false
                                $result = $this->save_extracted_text($file, $text);
                            }
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

    public function has_extracted_text($file)
    {
        if ($file) {
            $id = Util::safely_get_attribute($file, 'ID');

            $date_name = $this->extracted_date_acf_field;
            if ($id && $date_name) {
                $date = get_field($date_name, $id);
                if ($date) {
                    return true;
                }
            }
        }

        return false;
    }

    public function save_extracted_text($file, $text)
    {
        if ($file && $text) {
            $id        = Util::safely_get_attribute($file, 'ID');
            $name      = $this->extracted_text_acf_field;
            $date_name = $this->extracted_date_acf_field;

            if ($id && $name && $date_name) {
                $date_result = update_field($date_name, date('Y-m-d H:i:s'), $id);
                $result      = update_field($name, $text, $id);

                return $result;
            }
        }

        return false;
    }
}