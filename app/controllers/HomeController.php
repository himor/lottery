<?php

class HomeController extends BaseController
{
    const DEFAULT_COUNT = 30;

    protected $layout = 'layout.layout';

    /**
     * Set statistical info about last sets
     *
     * @return mixed
     * @throws Exception
     */
    public function statAction()
    {
        $set = $this->getDataset();

        $aSet = [
            BaseAnalyzer::AN_FREQUENCY => BaseAnalyzer::getInstance(BaseAnalyzer::AN_FREQUENCY),
        ];

        foreach ($aSet as $name => $analyzer) {
            $cache = $this->getCacheSet($set, $name);
            if ($cache) {
                $aRes[$name] = $cache;
                $analyzer->storeResult($cache, $set);
            } else {
                $aRes[$name] = $analyzer->analyze($set);
                $this->setCacheSet($set, $name, $aRes[$name], $analyzer->canExpire ? 3600 : 0);
            }
        }

        return View::make('stat', [
            'set'       => $set,
            'fromCache' => $this->fromCache,
            'aResults'  => $aRes,
            'analyzers' => $aSet,
        ]);
    }

    /**
     * Generate a number of sets
     * and run analysis on them
     *
     * @return mixed
     * @throws Exception
     */
    public function setAction()
    {
        $data  = Input::all();
        $count = isset($data['count']) ? (int)$data['count'] : self::DEFAULT_COUNT;
        $set   = $this->getDataset();

        $aSet = [
            BaseAnalyzer::AN_FREQUENCY => BaseAnalyzer::getInstance(BaseAnalyzer::AN_FREQUENCY),
            BaseAnalyzer::AN_RANGE     => BaseAnalyzer::getInstance(BaseAnalyzer::AN_RANGE),
            BaseAnalyzer::AN_DISTANCE  => BaseAnalyzer::getInstance(BaseAnalyzer::AN_DISTANCE),
        ];

        foreach ($aSet as $name => $analyzer) {
            $cache = $this->getCacheSet($set, $name);
            if ($cache) {
                $aRes[$name] = $cache;
                $analyzer->storeResult($cache, $set);
            } else {
                $aRes[$name] = $analyzer->analyze($set);
                $this->setCacheSet($set, $name, $aRes[$name], $analyzer->canExpire ? 3600 : 0);
            }
        }

        $built = [];
        for ($i = 0; $i < $count; $i++) {
            Builder::generate();
            $b = Builder::getNumbers(true);
            $built[] = $b;
        }

        return View::make('set', [
            'count'     => $count,
            'set'       => $built,
            'fromCache' => $this->fromCache,
            'aResults'  => $aRes,
            'analyzers' => $aSet,
        ]);
    }

    /**
     * Generate a best number of sets
     * and run analysis on them
     *
     * @return mixed
     * @throws Exception
     */
    public function bestAction()
    {
        set_time_limit(120);
        $data  = Input::all();
        $count = isset($data['count']) ? (int)$data['count'] : 9999;
        $set   = $this->getDataset();

        $aSet = [
            BaseAnalyzer::AN_FREQUENCY => BaseAnalyzer::getInstance(BaseAnalyzer::AN_FREQUENCY),
            BaseAnalyzer::AN_RANGE     => BaseAnalyzer::getInstance(BaseAnalyzer::AN_RANGE),
            BaseAnalyzer::AN_DISTANCE  => BaseAnalyzer::getInstance(BaseAnalyzer::AN_DISTANCE),
        ];

        foreach ($aSet as $name => $analyzer) {
            $cache = $this->getCacheSet($set, $name);
            if ($cache) {
                $aRes[$name] = $cache;
                $analyzer->storeResult($cache, $set);
            } else {
                $aRes[$name] = $analyzer->analyze($set);
                $this->setCacheSet($set, $name, $aRes[$name], $analyzer->canExpire ? 3600 : 0);
            }
        }

        $built      = [];
        $predefined = [];
        for ($i = 0; $i < $count; $i++) {
            Builder::generate();
            $bset = Builder::getNumbers(true);
            // if ($aSet[BaseAnalyzer::AN_RANGE]->checkSet($bset)) continue;
            $res = $aSet[BaseAnalyzer::AN_DISTANCE]->checkSet($bset);
            if ($res['total'] > 0) {
                $built[]                             = array_merge($bset, ['total' => $res['total']]);
                $predefined[md5(json_encode($bset))] = $res;
            }
        }

        usort($built, function ($a, $b) {
            return $a['total'] > $b['total'] ? -1 : 1;
        });

        $built = array_slice($built, 0, 10);

        return View::make('set', [
            'count'      => $count,
            'set'        => $built,
            'fromCache'  => $this->fromCache,
            'aResults'   => $aRes,
            'analyzers'  => $aSet,
            'predefined' => $predefined,
        ]);
    }

    public function testAction()
    {
        $data = [
            0 => [
                'winning_numbers' => [1, 2, 3, 4, 5],
                'mega_ball'       => 6
            ],
            1 => [
                'winning_numbers' => [2, 3, 5, 7, 9],
                'mega_ball'       => 4
            ],
            2 => [
                'winning_numbers' => [1, 6, 7, 8, 9],
                'mega_ball'       => 2
            ],
            3 => [
                'winning_numbers' => [1, 4, 5, 8, 9],
                'mega_ball'       => 4
            ]
        ];

        $a = BaseAnalyzer::getInstance(BaseAnalyzer::AN_DISTANCE)->analyze($data);
        $b = BaseAnalyzer::getInstance(BaseAnalyzer::AN_DISTANCE)->checkSet(
            [
                'set' => [2, 4, 6, 8, 9],
                'mb'  => 2
            ]
        );

        echo "<pre>";
        var_dump($b);
        die;
    }

    public function checkAction()
    {
        $data = Input::get('set', null);
        $set  = $this->getDataset();

        $aSet = [
            BaseAnalyzer::AN_FREQUENCY => BaseAnalyzer::getInstance(BaseAnalyzer::AN_FREQUENCY),
            BaseAnalyzer::AN_RANGE     => BaseAnalyzer::getInstance(BaseAnalyzer::AN_RANGE),
            BaseAnalyzer::AN_DISTANCE  => BaseAnalyzer::getInstance(BaseAnalyzer::AN_DISTANCE),
        ];

        foreach ($aSet as $name => $analyzer) {
            $cache = $this->getCacheSet($set, $name);
            if ($cache) {
                $aRes[$name] = $cache;
                $analyzer->storeResult($cache, $set);
            } else {
                $aRes[$name] = $analyzer->analyze($set);
                $this->setCacheSet($set, $name, $aRes[$name], $analyzer->canExpire ? 3600 : 0);
            }
        }

        return View::make('check', [
            'data'      => $data,
            'set'       => [Builder::convertInput($data)],
            'fromCache' => $this->fromCache,
            'aResults'  => $aRes,
            'analyzers' => $aSet,
        ]);
    }
}
