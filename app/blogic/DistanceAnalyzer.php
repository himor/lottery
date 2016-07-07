<?php

/**
 * Created by PhpStorm.
 * User: Mike
 */
class DistanceAnalyzer extends BaseAnalyzer implements Analysable
{
    public $requireReason = true;

    public $errorOnNonemptyCheckSet = false;

    public $canExpire = true;

    const MAX_DEPTH = 100;

    const MAX_NUMBER = 75;
    const MAX_MEGA   = 15;

    /**
     * Distance analysis
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function analyze(array $data)
    {
        if (empty($data) || !is_array($data)) {
            throw new Exception('Data must be set first');
        }

        $return = [
            'numbers' => array_fill(1, self::MAX_NUMBER, array_fill(1, self::MAX_DEPTH, 0)),
            'mb'      => array_fill(1, self::MAX_MEGA, array_fill(1, self::MAX_DEPTH, 0)),
        ];

        /* calc distances */
        for ($i = 1; $i <= self::MAX_NUMBER; $i++) {
            $step = 1;
            while ($step <= self::MAX_DEPTH) {
                foreach ($data as $key => $value) {
                    if ($key >= count($data) - $step) continue;
                    $numbers = $value['winning_numbers'];
                    if (!in_array($i, $numbers)) continue;
                    $anotherSet     = $data[$key + $step];
                    $anotherNumbers = $anotherSet['winning_numbers'];
                    if (in_array($i, $anotherNumbers)) {
                        $return['numbers'][$i][$step] += 1;
                    }
                }
                $step++;
            }
        }

        for ($i = 1; $i <= self::MAX_MEGA; $i++) {
            $step = 1;
            while ($step <= self::MAX_DEPTH) {
                foreach ($data as $key => $value) {
                    if ($key >= count($data) - $step) continue;
                    $number = $value['mega_ball'];
                    if ($i != $number) continue;
                    $anotherSet    = $data[$key + $step];
                    $anotherNumber = $anotherSet['mega_ball'];
                    if ($i == $anotherNumber) {
                        $return['mb'][$i][$step] += 1;
                    }
                }
                $step++;
            }
        }

        $max = count($data);

        foreach ($return['numbers'] as $key => $value) {
            foreach ($value as $dist => $ocur) {
                $return['numbers'][$key][$dist] = $return['numbers'][$key][$dist] / $max;
                if ($return['numbers'][$key][$dist] == 0)
                    $return['numbers'][$key][$dist] = 1 / self::MAX_NUMBER / $max;

            }
        }

        foreach ($return['mb'] as $key => $value) {
            foreach ($value as $dist => $ocur) {
                $return['mb'][$key][$dist] = $return['mb'][$key][$dist] / $max;
                if ($return['mb'][$key][$dist] == 0)
                    $return['mb'][$key][$dist] = 1 / self::MAX_MEGA / $max;
            }
        }

        $this->result[__CLASS__]              = $return;
        $this->result[__CLASS__ . '_initial'] = $data;

        return $return;
    }

    /**
     * @param array $set 5 + 1 numbers
     *
     * @return array of probabilities
     */
    public function checkSet(array $set)
    {
        $a    = $this->result[__CLASS__];
        $data = $this->result[__CLASS__ . '_initial'];

        $return = [
            'numbers' => array_fill(1, self::MAX_NUMBER, array_fill(1, self::MAX_DEPTH, 0)),
            'mb'      => array_fill(1, self::MAX_MEGA, array_fill(1, self::MAX_DEPTH, 0)),
        ];

        /* calc distances */
        foreach ($set['set'] as $i) {
            $step   = 1;
            $key    = 0;
            $maxRec = 0;
            while ($step <= self::MAX_DEPTH) {
                if ($maxRec++ > 1000) {
                    $step++;
                    continue;
                }
                if ($key >= count($data) - $step) continue;
                $anotherSet     = $data[$key + $step];
                $anotherNumbers = $anotherSet['winning_numbers'];
                if (in_array($i, $anotherNumbers)) {
                    $return['numbers'][$i][$step]++;
                }
                $step++;
            }
        }

        $i      = $set['mb'];
        $step   = 1;
        $key    = 0;
        $maxRec = 0;
        while ($step <= self::MAX_DEPTH) {
            if ($maxRec++ > 1000) {
                $step++;
                continue;
            }
            if ($key >= count($data) - $step) continue;
            $anotherSet    = $data[$key + $step];
            $anotherNumber = $anotherSet['mega_ball'];
            if ($i == $anotherNumber) {
                $return['mb'][$i][$step]++;
            }
            $step++;
        }

        $return_ = $return;
        $return  = [];

        foreach ($set['set'] as $i) {
            $return['numbers'][$i] = $return_['numbers'][$i];
        }

        $return['mb'][$set['mb']] = $return_['mb'][$set['mb']];

        /* calc probability */
        foreach ($return['numbers'] as $key => $value) {
            foreach ($value as $dist => $ocur) {
                $return['numbers'][$key][$dist] *= $a['numbers'][$key][$dist];
            }
        }
        foreach ($return['mb'] as $key => $value) {
            foreach ($value as $dist => $ocur) {
                $return['mb'][$key][$dist] *= $a['mb'][$key][$dist];
            }
        }

        /* calc probability for positions */
        foreach ($set['set'] as $pos => $i) {
            $normal[$pos + 1] = 0;
            foreach ($return['numbers'][$i] as $v)
                $normal[$pos + 1] += $v;
        }

        $normal['mb'] = 0;
        foreach ($return['mb'][$set['mb']] as $v)
            $normal['mb'] += $v;

        $total = 1;
        foreach ($normal as $n)
            $total *= $n;

        foreach ($normal as $key => $n) {
            $normal[$key] = number_format($n, 6); 
        }

        $normal['total'] = number_format($total, 12);

        return $normal;
    }

    public function storeResult($result, $data = null)
    {
        $this->result[__CLASS__] = $result;
        if ($data) {
            $this->result[__CLASS__ . '_initial'] = $data;
        }
    }

}