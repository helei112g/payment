<?php


class CurlTest extends \Codeception\Test\Unit
{
    use \Codeception\Specify;

    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * 测试的对象
     * @var \Payment\Utils\Curl
     */
    protected $curl;

    protected function _before()
    {
        $this->curl = new \Payment\Utils\Curl();
    }

    protected function _after()
    {
        unset($this->curl);
    }

    /**
     * 测试get 方法
     */
    public function testGet()
    {
        $url = 'https://helei112g.github.io/';

        $this->specify("测试 get 请求 成功", function() use ($url) {
            $ret = $this->curl->get($url);

            $this->assertEquals('0', $ret['error']);
        });

        $this->specify("测试 get 请求 失败", function() use ($url) {
            $ret = $this->curl->get($url . 'xxxxxxxx');

            $this->assertEquals('1', $ret['error']);
        });
    }

    /**
     * 测试POST请求
     */
    public function testPost()
    {
        $url = 'http://www.tiyushe.com/';
        $data = [
            'name'  => 'helei',
            'age'   => '26',
        ];

        $this->specify("测试 post 请求 成功", function() use ($data, $url) {
            $ret = $this->curl->post($data)->submit($url);

            $this->assertEquals('0', $ret['error']);
        });

        $this->specify("测试 post 请求 失败", function() use ($data, $url) {
            $ret = $this->curl->post($data)->submit($url . 'xxxxxxxx');

            $this->assertEquals('1', $ret['error']);
        });
    }
}