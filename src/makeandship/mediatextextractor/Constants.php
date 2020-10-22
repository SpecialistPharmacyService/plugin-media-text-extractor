<?php

namespace makeandship\mediatextextractor;

class Constants
{
    // plugin
    const VERSION    = '1.0.1';
    const DB_VERSION = 1;

    const OPTION_STATUS         = 'media_text_extractor_status';
    const OPTION_ACF_FIELD_NAME = 'media_text_extractor_acf_field';

    const EXTRACTOR_TIKA = "tika";

    const DEFAULT_FILES_PER_PAGE = 50;

    const STATUS_PUBLISH = 'publish';
    const STATUS_PRIVATE = 'private';
    const POST_STATUSES  = [Constants::STATUS_PUBLISH, Constants::STATUS_PRIVATE];

    // no instantiation
    public function __construct()
    {
    }
}