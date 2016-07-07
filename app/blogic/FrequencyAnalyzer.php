<?php

/**
 * Created by PhpStorm.
 * How often is this number on this position?
 *
 * User: Mike
 * Date: 10/23/14
 * Time: 4:38 AM
 */
class FrequencyAnalyzer extends BaseAnalyzer implements Analysable
{
    public $requireReason = true;

    public $canExpire = true;

    /**
     * Frequency analysis
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
            'numbers' => array_fill(1, 75, [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0]),
            'mb'      => array_fill(1, 15, 0),
        ];

        foreach ($data as $key => $value) {
            $numbers = $value['winning_numbers'];
            $mb = $value['mega_ball'];
            foreach ($numbers as $pos => $number) {
                $return['numbers'][$number][$pos + 1]++;
            }
            $return['mb'][$mb]++;
        }

        $max = count($data);

        foreach ($return['numbers'] as $key => $value) {
            foreach ($value as $pos => $z) {
                $return['numbers'][$key][$pos] = $return['numbers'][$key][$pos] / $max * 100;
            }
        }

        foreach ($return['mb'] as $key => $value) {
            $return['mb'][$key] = $return['mb'][$key] / $max * 100;
        }

        $this->result[__CLASS__] = $return;

        return $return;
    }

    /**
     * @param array $set 5 + 1 numbers
     *
     * @return array of wrong spots
     */
    public function checkSet(array $set)
    {
        $a = $this->result[__CLASS__];
        $normal = [];
        foreach ($set['set'] as $pos_ => $number) {
            $pos = $pos_ + 1;
            $q = $a['numbers'][$number];
            $right = $q[$pos];
            foreach ($q as $qq) {
                if ($qq > $right) {
                    $normal[] = $pos_;
                }
            }
        }
        return $normal;
    }

    public function storeResult($result, $data = null)
    {
        $this->result[__CLASS__] = $result;
    }

}