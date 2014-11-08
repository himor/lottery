<?php

/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 10/23/14
 * Time: 3:16 AM
 */
class LotteryRemote
{
    private static $data     = [];
    private static $instance = null;
    private static $url      = 'http://data.ny.gov/resource/5xaw-6ayf.json';

    private function __construct()
    {
        if (empty(self::$data)) {
            self::retrieve();
            self::normalize();
        }
    }

    /**
     * Make cURL call
     */
    private static function retrieve()
    {
        // is cURL installed yet?
        if (!function_exists('curl_init')) {
            die('Sorry cURL is not installed!');
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$url);
        curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
        // Include header in result? (0 = yes, 1 = no)
        curl_setopt($ch, CURLOPT_HEADER, false);
        // Should cURL return or print out the data? (true = return, false = print)
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Timeout in seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        // Download the given URL, and return output
        $output = curl_exec($ch);
        // Close the cURL resource, and free system resources
        curl_close($ch);

        self::$data = json_decode($output, true);
    }

    /**
     * Normalize data
     *
     * @throws Exception
     */
    private static function normalize()
    {
        if (empty(self::$data) || !is_array(self::$data)) {
            throw new Exception('Data must be retrieved first');
        }

        foreach (self::$data as $key => $value) {
            if ($value['draw_date'] < '2013-10-22T00:00:00') {
                unset(self::$data[$key]);
                continue;
            }
            self::$data[$key] = [
                'draw_date'       => date('Y-m-d', strtotime($value['draw_date'])),
                'winning_numbers' => array_map('intval', explode(' ', $value['winning_numbers'])),
                'mega_ball'       => (int)$value['mega_ball'],
            ];
        }
    }

    /**
     * Returns data
     *
     * @return array
     * @throws Exception
     */
    public static function getData()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        if (empty(self::$data) || !is_array(self::$data)) {
            throw new Exception('Data must be retrieved first');
        }

        return self::$data;
    }


} 