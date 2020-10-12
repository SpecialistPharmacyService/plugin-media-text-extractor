<?php

namespace makeandship\mediatextextractor\extractors;

use makeandship\mediatextextractor\Constants;

class TextExtractorFactory
{
    public function __construct()
    {

    }

    public function get($name)
    {
        switch ($name) {
            case Constants::EXTRACTOR_TIKA:
                return new TikaTextExtractor();
        }

        return null;
    }
}