<?php

class LinkParser {

    public static function find_links($string) {

        $pattern = '/\b(?:http:|https:)(?:[^\s,."!?]|[,.!?](?!\s))+/i'; //TODO: make http/https non-compulsory, and factor in things such as parameters following the main domain STOP
        preg_match_all($pattern, $string, $matches);

        return $matches[0];
    }
}