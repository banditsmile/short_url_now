<?php
class short_url_now
{

    //短url长度
    protected $len = 6;
    //短url生命周期
    protected $ttl = 86400;
    //
    protected $dbFile = 'short_url.db';

    public function index()
    {
        $this->sqlite();
    }

    public function add($url, $user='nobody')
    {
        $urlHash = md5($url);
        $db = new SQLite3($this->dbFile);
    }

    public function sqlite()
    {
        $db = new SQLite3($this->dbFile);
        $db->exec("INSERT INTO foo (bar) VALUES ('This is a test')");

        $result = $db->query('SELECT bar FROM foo');
        var_dump($result->fetchArray());
    }

    public function install()
    {
        $db = new SQLite3($this->dbFile);
        $tableSql = 'CREATE TABLE main.url (
                  id         INTEGER PRIMARY KEY  AUTOINCREMENT NOT NULL,
                  code       CHAR(20)                           NOT NULL,
                  hash       CHAR(35)                           NOT NULL,
                  url        VARCHAR(255)                       NOT NULL,
                  start_time INTEGER                            NOT NULL,
                  end_time   INTEGER                            NOT NULL
                );';

        $db->exec($tableSql);
    }

}

(new short_url_now())->index();