<?php

/**
 * Class short_url_now
 */
class short_url_now
{

    //短url长度
    protected $len = 6;
    //短url生命周期
    protected $ttl = 86400;
    //当前请求地址信息
    protected $urlInfo;

    public function index()
    {
        $this->urlInfo = parse_url($_SERVER['REQUEST_URI']);
        $code = ltrim($this->urlInfo['path'], '/');
        if (is_numeric($code)) {
            $this->redirect($code);
        } elseif (method_exists($this, $code)) {
            $this->$code();
        } else {
            header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
        }
        die();
    }

    /**
     * 跳转到索引地址
     *
     * @param $code
     */
    protected function redirect($code)
    {
        $url = apcu_fetch($code);
        if (!empty($url)) {
            header("Location: {$url}", true, 308);
            return;
        }
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
        return;
    }

    /**
     * 添加短地址
     *
     */
    public function add()
    {
        $url = $_SERVER['QUERY_STRING'];
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            header("Bad Request", true, 401);
            return;
        }
        if (strlen($url)>500) {
            header("Request Entity Too Large", true, 413);
            return;
        }
        $code = $this->code();
        while (apcu_add($code, $url, $this->ttl) ===false) {
            $code = $this->code();
        }
        echo  $code;
    }

    /**
     * 由短地址获取原来地址
     *
     * @param $code
     *
     * @return mixed
     */
    protected function get($code)
    {
        return apcu_fetch($code);
    }

    /**
     * 生成便于记忆的短地址
     *
     * @return string
     */
    protected function code()
    {
        $min= 100000;
        $max = 999999;
        $code = rand($min, $max);
        return (string)$code;
    }

    protected function stat()
    {
        $info =apcu_sma_info();
        echo json_encode($info);
    }

    protected function all()
    {
        $data = apcu_cache_info();
        $data['memory'] = memory_get_usage(true);
        $data['memory_peak'] = memory_get_peak_usage(true);
        echo json_encode($data);
    }
}

(new short_url_now())->index();