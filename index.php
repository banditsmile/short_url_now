<?php
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
        }

        if (method_exists($this, $code)) {
            $this->$code();
        }
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
    }

    protected function redirect($code)
    {
        $url = apcu_fetch($code);
        if (!empty($url)) {
            header("Location: {$url}", false, 301);
        }
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
    }

    public function add( )
    {
        $url = $_SERVER['QUERY_STRING'];
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
}

(new short_url_now())->index();