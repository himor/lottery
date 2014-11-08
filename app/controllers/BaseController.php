<?php

class BaseController extends Controller
{
    protected $fromCache = false;

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

    private function getRedis()
    {
        $redis = Redis::connection();
        return $redis;
    }

    public function getDataset($forceRemote = false)
    {
        $redis   = $this->getRedis();
        $setName = 'set:' . date('YmdH', time());
        $set     = $redis->get($setName);

        $cache = null;
        if ($set == null || $forceRemote) {
            $set = LotteryRemote::getData();
            $redis->set($setName, json_encode($set));
            $redis->expire($setName, 3600);
            $this->fromCache = false;
        } else {
            $set             = json_decode($set, true);
            $this->fromCache = true;
        }
        return $set;
    }

    public function getCacheSet($set, $analysisType = 'default')
    {
        if (is_array($set)) {
            $set = json_encode($set);
        }

        $set = md5($set);

        $redis   = $this->getRedis();
        $setName = 'cset:' . $set . ':' . $analysisType . ':' . date('YmdH', time());

        $return = $redis->get($setName);
        if ($return) {
            $return = json_decode($return, true);
        }

        return $return;
    }

    public function setCacheSet($set, $analysisType = 'default', $value = [], $expires = 3600)
    {
        if (is_array($set)) {
            $set = json_encode($set);
        }

        if (is_array($value)) {
            $value = json_encode($value);
        }

        $set = md5($set);

        $redis   = $this->getRedis();
        $setName = 'cset:' . $set . ':' . $analysisType . ':' . date('YmdH', time());

        $redis->set($setName, $value);
        if ($expires) {
            $redis->expire($setName, $expires);
        }

        return true;
    }

    /**
     * Check set analysis in cache. if not found - analyze and cache.
     * Takes too much time.
     *
     * @param            $set
     * @param Analysable $analyzer
     * @param string     $analysisType
     * @param int        $expires
     * @return mixed
     */
    public function checkSet($set, Analysable $analyzer, $analysisType = 'default', $expires = 3600)
    {
        $cache = $this->getCacheSet($set, $analysisType);
        if ($cache !== null) {
            return $cache;
        } else {
            $result = $analyzer->checkSet($set);
            $this->setCacheSet($set, $analysisType, $result, $expires);
            return $result;
        }
    }

}
