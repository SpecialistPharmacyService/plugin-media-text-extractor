<?php

namespace makeandship\mediatextextractor;

class Constants
{
    // plugin
    const VERSION    = '1.0.2';
    const DB_VERSION = 1;

    const OPTION_STATUS                    = 'media_text_extractor_status';
    const OPTION_ACF_FIELD_NAME            = 'media_text_extractor_acf_field';
    const OPTION_ACF_DATE_FIELD_NAME       = 'media_text_extractor_acf_date_field';
    const OPTION_ACF_POST_OR_ATTACHMENT_ID = 'media_text_extractor_id';

    const EXTRACTOR_TIKA = "tika";

    const ENV_TIKA_COMMAND = 'TIKA_COMMAND';

    const DEFAULT_FILES_PER_PAGE = 10;

    const STATUS_PUBLISH = 'publish';
    const STATUS_PRIVATE = 'private';
    const POST_STATUSES  = [Constants::STATUS_PUBLISH, Constants::STATUS_PRIVATE];

    // no instantiation
    public function __construct()
    {
    }
}
