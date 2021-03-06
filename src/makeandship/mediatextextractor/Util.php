<?php

namespace makeandship\mediatextextractor;

class Util
{
    /**
     * Call wordpress apply filters using the plugin prefix
     *
     * e.g. prepare_query will become mediatextextractor/prepare_query
     */
    public static function apply_filters()
    {
        $args    = func_get_args();
        $args[0] = 'mediatextextractor/' . $args[0];

        return call_user_func_array('apply_filters', $args);
    }

    /**
     * Call wordpress apply filters using the plugin prefix
     *
     * e.g. search_exception will become mediatextextractor/search_exception
     */
    public static function do_action()
    {
        $args    = func_get_args();
        $args[0] = 'mediatextextractor/' . $args[0];

        return call_user_func_array('do_action', $args);
    }

    /**
     * Retrieve the value from an array item, or object attribute
     * returning null if the attribute is missing or the value is null
     *
     * @param array the array or object
     * @param attribute the name of the attribute to extract
     * @return the value or the attribute or null if missing
     */
    public static function safely_get_attribute($array, $attribute)
    {
        if (is_array($array)) {
            if (isset($array) && isset($attribute) && $array && $attribute) {
                if (array_key_exists($attribute, $array)) {
                    return $array[$attribute];
                }
            }
        } elseif (is_object($array)) {
            if (isset($array) && isset($attribute) && $array && $attribute) {
                if (property_exists($array, $attribute)) {
                    return $array->{$attribute};
                }
            }
        }
        return null;
    }

    public static function is_array_associative($array)
    {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }

    public static function is_array_sequential($array)
    {
        return !Util::is_array_associative($array);
    }

    public static function debug($method, $message)
    {
        if (is_object($message)) {
            $message = print_r($message, true);
        }

        $log = "MediaTextExtractor " . $method . ": " . $message;
        if (WP_DEBUG) {
            error_log($log);
        }
    }
}