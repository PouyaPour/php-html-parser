<?php

require_once "vendor/autoload.php";


$data = \PHPHtmlParser\Crawl\WebPageProcessor::onePageProcessed('https://www.vcp.ir');
$data->getUrlInfo()->getUrl();
// if anchor text exists
$data->getUrlInfo()->getAnchorText();
$data->getUrlInfo()->getFabricUrl();
$data->getUrlInfo()->getHomeAddress();
// if parent exists
$data->getUrlInfo()->getParent();
//get all information in array
$data->getUrlInfo()->getUrlInfoArray();
