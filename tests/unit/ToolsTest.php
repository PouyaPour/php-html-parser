<?php


use PHPHtmlParser\Utility\Tools;
use PHPUnit\Framework\TestCase;

class ToolsTest extends TestCase
{
    /**
     * @dataProvider additionProviderForTestGetHostName
     * @param $a
     * @param $expected
     */
    public function testGetHostName($a, $expected)
    {
        $this->assertSame(Tools::getHostName($a), $expected);
    }

    public function additionProviderForTestGetHostName()
    {
        return [
            'url from a inner page'  => ['https://www.yjc.ir/fa/news/4037755/096440-%D8%B4%D9%85%D8%A7%D8%B1%D9%87-%D8%AC%D8%AF%D9%8A%D8%AF-%D9%85%D8%B1%D9%83%D8%B2-%D8%AA%D9%85%D8%A7%D8%B3-%D8%A7%D9%8A%D8%B1%D8%A7%D9%86-%D8%AE%D9%88%D8%AF%D8%B1%D9%88', 'https://www.yjc.ir'],
            'home page' => ['https://github.com', 'https://github.com'],
            'sub domain' => ['https://phpunit.readthedocs.io/en/9.3/writing-tests-for-phpunit.html', 'https://phpunit.readthedocs.io']
        ];
    }

    /**
     * @dataProvider additionProviderForTestCountSubStr
     * @param $strings
     * @param $phrase
     * @param $result
     */
    public function testCountSubStr($strings, $phrase, $result)
    {
        $this->assertSame(Tools::countSubStr($strings, $phrase), $result);
    }

    public function additionProviderForTestCountSubStr()
    {
        return [
                'persian language'  => [
                        'لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ، و با استفاده از طراحان گرافیک است، چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که لازم است، و برای شرایط فعلی تکنولوژی مورد نیاز، و کاربردهای متنوع با هدف بهبود ابزارهای کاربردی می باشد، کتابهای زیادی در شصت و سه درصد گذشته حال و آینده، شناخت فراوان جامعه و متخصصان را می طلبد، تا با نرم افزارها شناخت بیشتری را برای طراحان رایانه ای علی الخصوص طراحان خلاقی، و فرهنگ پیشرو در زبان فارسی ایجاد کرد، در این صورت می توان امید داشت که تمام و دشواری موجود در ارائه راهکارها، و شرایط سخت تایپ به پایان کرد و زمان مورد نیاز شامل حروفچینی دستاوردهای اصلی، و جوابگوی سوالات پیوسته اهل دنیای موجود طراحی کرد اساسا مورد استفاده قرار گیرد.',
                        'کرد',
                        3
                    ],
                'english language' => [
                        'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Egestas purus viverra accumsan in nisl nisi. Arcu cursus vitae congue mauris rhoncus aenean vel elit scelerisque. In egestas erat imperdiet sed euismod nisi porta lorem mollis. Morbi tristique senectus et netus. Mattis pellentesque id nibh tortor id aliquet lectus proin. Sapien faucibus et molestie ac feugiat sed lectus vestibulum. Ullamcorper velit sed ullamcorper morbi tincidunt ornare massa eget. Dictum varius duis at consectetur lorem. Nisi vitae suscipit tellus mauris a diam maecenas sed enim. Velit ut tortor pretium viverra suspendisse potenti nullam. Et molestie ac feugiat sed lectus. Non nisi est sit amet facilisis magna. Dignissim diam quis enim lobortis scelerisque fermentum. Odio ut enim blandit volutpat maecenas volutpat. Ornare lectus sit amet est placerat in egestas erat. Nisi vitae suscipit tellus mauris a diam maecenas sed. Placerat duis ultricies lacus sed turpis tincidunt id aliquet.',
                        'dolor',
                        2
                ],
                'false english language' => [
                        'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua',
                        'pouya',
                        0
                ]
        ];
    }
}
