<?php


class DataParserTest extends \Codeception\Test\Unit
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
     * 测试转化为xml
     */
    public function testArrayToXml()
    {
        $data = [
            'name'  => 'dayu',
            'age'   => 26,
            'fee'   => 3000,
            'num'   => '123456',
        ];

        $this->specify('正确的数组转化为xml', function () use ($data) {
            $xml = \Payment\Utils\DataParser::toXml($data);

            $this->assertXmlStringEqualsXmlString('<xml><name><![CDATA[dayu]]></name><age>26</age><fee>3000</fee><num>123456</num></xml>', $xml);
        });

        $this->specify('非数组参数，转化xml返回false', function () {
            $xml = \Payment\Utils\DataParser::toXml('123');

            $this->assertFalse($xml, '参数错误，返回false');
        });
    }

    /**
     * xml 转化为数组
     */
    public function testXmlToArray()
    {
        $xml = '<xml><name><![CDATA[dayu]]></name><age>26</age><fee>3000</fee><num>123456</num></xml>';

        $this->specify('错误的xml将返回fals', function () {
            $array = \Payment\Utils\DataParser::toArray('this is not xml');

            $this->assertFalse($array, '错误的xml应该返回false');
        });

        $this->specify('正确的xml返回一个数组', function () use ($xml) {
            $array = \Payment\Utils\DataParser::toArray($xml);

            $this->assertArrayHasKey('name', $array);
            $this->assertArrayHasKey('age', $array);
            $this->assertArrayHasKey('fee', $array);
            $this->assertArrayHasKey('num', $array);
        });
    }
}