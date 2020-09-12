<?php


use PHPHtmlParser\Utility\Tools;
use PHPUnit\Framework\TestCase;

class ToolsTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     * @param $a
     * @param $expected
     */
    public function testGetHostName($a, $expected)
    {
        $this->assertSame(Tools::getHostName($a), $expected);
    }

    public function additionProvider()
    {
        return [
            'url from a inner page'  => ['https://www.yjc.ir/fa/news/4037755/096440-%D8%B4%D9%85%D8%A7%D8%B1%D9%87-%D8%AC%D8%AF%D9%8A%D8%AF-%D9%85%D8%B1%D9%83%D8%B2-%D8%AA%D9%85%D8%A7%D8%B3-%D8%A7%D9%8A%D8%B1%D8%A7%D9%86-%D8%AE%D9%88%D8%AF%D8%B1%D9%88', 'https://www.yjc.ir'],
            'home page' => ['https://github.com', 'https://github.com'],
            'sub domain' => ['https://phpunit.readthedocs.io/en/9.3/writing-tests-for-phpunit.html', 'https://phpunit.readthedocs.io']
        ];
    }
}
