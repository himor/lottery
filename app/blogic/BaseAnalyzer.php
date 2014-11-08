<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/30/14
 * Time: 5:57 PM
 */
class BaseAnalyzer
{
    /**
     * Storage for temp analysis results
     * @var array
     */
    protected $result = [];

    /**
     * Do we need to show additional values? (like distribution)
     * @var bool
     */
    public $requireReason = false;

    public $errorOnNonemptyCheckSet = true;

    /**
     * Do we need to expire this type of analysis in cache?
     * @var bool
     */
    public $canExpire = false;

    const AN_FREQUENCY = 'frequency';
    const AN_RANGE     = 'range';
    const AN_DISTANCE  = 'distance';

    public static $engines = [
        self::AN_FREQUENCY => 'frequency',
        self::AN_RANGE     => 'range',
        self::AN_DISTANCE  => 'distance'
    ];

    private static $instance = [];

    public static function getInstance($name)
    {
        if (!$name) {
            throw new Exception('Name expected, "' . $name . '" given');
        }

        $name = ucfirst($name);

        if (!isset(self::$instance[$name])) {
            $className             = $name . 'Analyzer';
            self::$instance[$name] = new $className;
        }

        return self::$instance[$name];
    }

}