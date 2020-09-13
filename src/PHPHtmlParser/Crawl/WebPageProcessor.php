<?php
/**
 * Created by PhpStorm.
 * User: trty
 * Date: 21/05/2017
 * Time: 10:50 PM
 */

namespace PHPHtmlParser\Crawl;



use GuzzleHttp\Exception\GuzzleException;
use PHPHtmlParser\Parser\HtmlParser;
use PHPHtmlParser\Parser\PageFeatures;

class WebPageProcessor
{
    private $websiteUrl;
    private $scheme;
    private $host;
    private $clintCrawler;

    /**
     * WebPageProcessor constructor.
     * @param $websiteUrl
     * @throws \Exception
     */
    function __construct($websiteUrl){
        $this->websiteUrl=$websiteUrl;
        $this->scheme=parse_url($this->websiteUrl, PHP_URL_SCHEME);
        $this->host=parse_url($this->websiteUrl, PHP_URL_HOST);
        $this->clintCrawler=UrlLoader::initialize($this->websiteUrl);
    }


    /**
     * @param Url $urlInfo
     * @return mixed
     * @throws \Exception
     */
    private static function getAllUrlInAddress(Url $urlInfo)
    {
        if($urlInfo->isCorrectForCrawl()) {
            $data = NULL;
            if ($urlInfo->isUrlExternalLink()) {
                $data = new WebPageProcessor($urlInfo->getHomeAddressExternalLink());
            } else {
                $data = new WebPageProcessor($urlInfo->getHomeAddress());
            }
            $final = $data->pageHtmlDomParser($urlInfo);
            return $final->getLinks();
        }
        return false;
    }


    /**
     * @param $urlInfo Url
     * @return null|HtmlParser
     * @throws \Exception
     * @throws GuzzleException
     */
    private function pageHtmlDomParser($urlInfo)
    {
        $urlClass = Url::createUrlInfoWithUrl($urlInfo);
        if($this->getClintCrawler()) {
            $http_url_info = UrlLoader::getHttpWithClint($this->clintCrawler, $urlInfo);
            $html = $http_url_info['html'];
            $status = $http_url_info['status'];
            $headers = $http_url_info['headers'];
            $text = $http_url_info['text'];
            $furtherInformation = $http_url_info['furtherInformation'];
            $redirect = $http_url_info['redirect'];
            return new HtmlParser($html, $status, $headers, $text,
                $furtherInformation, $urlClass, null, $redirect);
        }else{
            return $this->zeroPageDomParser($urlClass);
        }
    }


    /**
     * @param Url $urlInfo
     * @return HtmlParser
     * @throws \Exception
     */
    private function zeroPageDomParser(Url $urlInfo)
    {
        $html='<html></html>';
        $status=800;
        $headers=array();
        $text='';
        $furtherInformation=array();
        return new HtmlParser($html, $status, $headers, $text, $furtherInformation, $urlInfo,null, []);
    }


    /**
     * @param $urlInfo
     * @return PageFeatures
     * @throws Exception
     * @throws GuzzleException
     */
    public static function onePageProcessed($urlInfo)
    {
        $data=NULL;
        $data = new WebPageProcessor($urlInfo);
        $websiteHtmlDomParser=$data->pageHtmlDomParser($urlInfo);
        return $websiteHtmlDomParser->getData();
    }

    /**
     * @param Url $urlInfo
     * @return array
     * @throws \Exception
     */
    private static function createZeroPage(Url $urlInfo)
    {
        $data=NULL;
        if($urlInfo->isUrlExternalLink()){
            $data=new WebPageProcessor($urlInfo->getHomeAddressExternalLink());
        }else {
            $data = new WebPageProcessor($urlInfo->getHomeAddress());
        }
        $websiteHtmlDomParser=$data->zeroPageDomParser($urlInfo);
        return $websiteHtmlDomParser->getData();
    }

    /**
     * @return bool|\GuzzleHttp\Client
     */
    public function getClintCrawler()
    {
        return $this->clintCrawler;
    }
}