<?php

namespace makeandship\mediatextextractor\extractors;

abstract class TextExtractor
{
    abstract protected function extract($url);
}