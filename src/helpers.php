<?php

use EasySlugger\Slugger;

if (!function_exists('slugify')) {
    /**
     * Generate slug.
     *
     * @param string $text
     *
     * @return string
     */
    function slugify($text)
    {
        return Slugger::slugify($text);
    }
}

if (!function_exists('uniqueSlugify')) {
    /**
     * Generate unique slug.
     *
     * @param string $text
     *
     * @return string
     */
    function uniqueSlugify($text)
    {
        return Slugger::uniqueSlugify($text);
    }
}

if (!function_exists('transcrud')) {
    /**
     * Translate string but remove file key if translation not found.
     *
     * @param string $text
     *
     * @return string
     */
    function transcrud($text)
    {
        return str_replace('anavel-crud::models.', '', trans('anavel-crud::models.'.$text));
    }
}
