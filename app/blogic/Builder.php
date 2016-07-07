<?php

/**
 * Created by PhpStorm.
 * User: Mike Gordo <mgordo@live.com>
 * Date: 10/22/14
 * Time: 11:32 PM
 */
class Builder
{
    private static $numbers  = [];
    private static $instance = null;

    private function __construct()
    {
    }

    /**
     * Build random set
     */
    private static function populate()
    {
        self::$numbers['set'] = [];
        self::$numbers['mb']  = 0;
        for ($i = 1; $i < 6; $i++) {
            $n = mt_rand(1, 75);
            while (in_array($n, self::$numbers['set'])) {
                $n = mt_rand(1, 75);
            }
            self::$numbers['set'][] = $n;
        }

        sort(self::$numbers['set']);

        self::$numbers['mb'] = mt_rand(1, 15);
    }

    /**
     * @param bool $raw
     * @return array|string
     */
    public static function getNumbers($raw = false)
    {
        $output = '';
        if ($raw) {
            return self::$numbers;
        }

        foreach (self::$numbers['set'] as $number) {
            $output .= $number . ' ';
        }

        $output .= self::$numbers['mb'];

        return $output;
    }

    /**
     * Regenerate set
     */
    public static function generate()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        self::populate();
    }

    public static function convertInput($set)
    {
        if (!is_array($set)) {
            return [];
        }

        $return = ['set' => [], 'mb' => 0];

        $return['set'][1] = (int) $set[0];
        $return['set'][2] = (int) $set[1];
        $return['set'][3] = (int) $set[2];
        $return['set'][4] = (int) $set[3];
        $return['set'][5] = (int) $set[4];
        $return['mb']     = (int) $set[5];

        sort($return['set']);
        self::$numbers = $return;

        return $return;
    }
} 