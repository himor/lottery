<?php

/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 10/23/14
 * Time: 4:38 AM
 */
class RangeAnalyzer extends BaseAnalyzer implements Analysable
{
    public $canExpire = true;

    /**
     * Range analysis
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
            1 => ['min' => 76, 'max' => 0],
            2 => ['min' => 76, 'max' => 0],
            3 => ['min' => 76, 'max' => 0],
            4 => ['min' => 76, 'max' => 0],
            5 => ['min' => 76, 'max' => 0],
        ];

        foreach ($data as $key => $value) {
            $numbers = $value['winning_numbers'];
            foreach ($numbers as $pos => $number) {
                if ($return[$pos + 1]['min'] > $number)
                    $return[$pos + 1]['min'] = $number;
                if ($return[$pos + 1]['max'] < $number)
                    $return[$pos + 1]['max'] = $number;
            }
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
            if ($a[$pos]['min'] > $number || $a[$pos]['max'] < $number)
                $normal[] = $pos_;
        }
        return $normal;
    }

    public function storeResult($result, $data = null)
    {
        $this->result[__CLASS__] = $result;
    }

}