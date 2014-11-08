<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/30/14
 * Time: 5:55 PM
 */
interface Analysable
{
    public function analyze(array $data);
    public function checkSet(array $set);

} 