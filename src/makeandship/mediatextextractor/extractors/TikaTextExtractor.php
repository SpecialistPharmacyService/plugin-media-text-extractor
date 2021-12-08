<?php

namespace makeandship\mediatextextractor\extractors;

use makeandship\logging\Log;
use makeandship\mediatextextractor\Constants;

class TikaTextExtractor extends TextExtractor
{
    public function extract($url)
    {
        if ($url) {
            $rows = array();

            $env_command  = getenv(Constants::ENV_TIKA_COMMAND);
            $tika_command = $env_command ? $env_command : "/usr/local/bin/tika";
            $command      = $tika_command . " --text " . $url . " | awk NF 2>&1";

            Log::debug('TikaTextExtractor#extract: command: ' . $command);
            exec($command, $rows);
            if ($rows) {
                $text = implode("\n", $rows);
                return $text;
            }
        }
        return null;
    }
}
