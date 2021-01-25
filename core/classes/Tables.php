<?php

namespace Synclogic\Classes;

class Tables
{
    public static function getAll()
    {
        global $wpdb;
        $prefix = $wpdb->prefix;

        $tables = [
            'questions' => $prefix . 'slmoderator_questions',
            'favorites' => $prefix . 'slmoderator_favorites',
            'califications' => $prefix . 'slmoderator_califications'
        ];

        return $tables;
    }

    public static function get($table)
    {
        return self::getAll()[$table];
    }
}
