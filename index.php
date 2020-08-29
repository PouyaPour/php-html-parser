<?php

require_once "vendor/autoload.php";


$data = \PHPHtmlParser\Crawl\WebPageProcessor::onePageProcessed('https://www.vcp.ir');
dump($data->getInternalLinks());
