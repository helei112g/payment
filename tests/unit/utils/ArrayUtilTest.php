<?php


class ArrayUtilTest extends \Codeception\Test\Unit
{
    use \Codeception\Specify;

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    /**
     * 测试拼接数组
     */
    public function testLink()
    {
        $data = [
            'name'  => 'helei',
            'age'   => '30',
        ];
        $this->specify('测试用 & 链接字符串', function () use ($data) {
            $str = \Payment\Utils\ArrayUtil::createLinkstring($data);

            $this->assertStringEndsWith('30', $str, '结尾不是预期的 30');

            $this->assertEquals('name=helei&age=30', $str, '期望结果是： name=helei&age=30');
        });
    }

    /**
     * 测试排序
     */
    public function testSort()
    {
        $this->specify('字母型 key 进行字典排序', function () {
            $data = [
                'd' => 1,
                'a' => 2,
                't' => 3,
            ];

            $arr = \Payment\Utils\ArrayUtil::arraySort($data);
            $this->assertEquals([
                'a' => 2,
                'd' => 1,
                't' => 3
            ], $arr);
        });

        $this->specify('数值型 key 进行字典排序', function () {
            $data = [
                '6' => 'aaa',
                '154' => 'bb',
                '3' => 'ccc',
            ];

            $arr = \Payment\Utils\ArrayUtil::arraySort($data);
            $this->assertEquals([
                '3' => 'ccc',
                '6' => 'aaa',
                '154' => 'bb'
            ], $arr);
        });
    }
}