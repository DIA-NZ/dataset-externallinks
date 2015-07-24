<?php

class LinkParser {

    public static function find_links($string) {

        $pattern = '/\b(?:http:|https:)(?:[^\s,."!?]|[,.!?](?!\s))+/';
        preg_match_all($pattern, $string, $matches);

        return $matches[0];
    }
}