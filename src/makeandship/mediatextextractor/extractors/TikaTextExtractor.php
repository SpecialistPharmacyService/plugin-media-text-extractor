<?php

namespace makeandship\mediatextextractor\extractors;

use makeandship\mediatextextractor\Util;

class TikaTextExtractor extends TextExtractor
{
    public function extract($filepath)
    {
        if ($filepath) {
            $rows    = array();
            $command = "/usr/local/bin/tika --text " . $filepath . " | awk NF 2>&1";
            Util::debug("TikaTextExtractor#extract", $command);
            exec($command, $rows);
            if ($rows) {
                $text = implode("\n", $rows);
                return $text;
            }
        }
        return null;
    }
}