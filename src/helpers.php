<?php

use EasySlugger\Slugger;

if (! function_exists('slugify')) {
    /**
     * Generate slug
     *
     * @param  string  $text
     * @return string
     */
    function slugify($text)
    {
        return Slugger::slugify($text);
    }
}

if (! function_exists('uniqueSlugify')) {
    /**
     * Generate unique slug
     *
     * @param  string  $text
     * @return string
     */
    function uniqueSlugify($text)
    {
        return Slugger::uniqueSlugify($text);
    }
}
