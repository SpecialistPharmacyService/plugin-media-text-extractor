<?php

namespace makeandship\mediatextextractor;

class Constants
{
    // plugin
    const VERSION    = '1.0.0';
    const DB_VERSION = 1;

    const OPTION_STATUS         = 'media_text_extractor_status';
    const OPTION_ACF_FIELD_NAME = 'media_text_extractor_acf_field';

    const EXTRACTOR_TIKA = "tika";

    const DEFAULT_FILES_PER_PAGE = 100;

    // no instantiation
    public function __construct()
    {
    }
}