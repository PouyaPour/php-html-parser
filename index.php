<?php


use PHPHtmlParser\Crawl\WebPageProcessor;

require_once __DIR__ . '/vendor/autoload.php';

$data = WebPageProcessor::onePageProcessed('https://www.bambin.ir');
dump($data);
