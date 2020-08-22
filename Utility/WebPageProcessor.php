<?php
/**
 * Created by PhpStorm.
 * User: trty
 * Date: 21/05/2017
 * Time: 10:50 PM
 */

namespace Seosazi\Utility;



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
     * @param UrlInfo $urlInfo
     * @return mixed
     * @throws \Exception
     */
    public static function getAllUrlInAddress(UrlInfo $urlInfo)
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
     * @param $urlInfo UrlInfo
     * @return null|HtmlDomParserLocal
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pageHtmlDomParser($urlInfo)
    {
        if($this->getClintCrawler()) {
            $http_url_info = UrlLoader::getHttpWithClint($this->clintCrawler, $urlInfo->getUrl());
            $html = $http_url_info['html'];
            $status = $http_url_info['status'];
            $headers = $http_url_info['headers'];
            $text = $http_url_info['text'];
            $furtherInformation = $http_url_info['furtherInformation'];
            $size = $http_url_info['size'];
            $redirect = $http_url_info['redirect'];
            return new HtmlDomParserLocal($html, $status, $headers, $text,
                $furtherInformation, $urlInfo, $size, null, $redirect);
        }else{
            return $this->zeroPageDomParser($urlInfo);
        }
    }


    /**
     * @param UrlInfo $urlInfo
     * @return HtmlDomParserLocal
     * @throws \Exception
     */
    public function zeroPageDomParser(UrlInfo $urlInfo)
    {
        $html='<html></html>';
        $status=800;
        $headers=array();
        $text='';
        $furtherInformation=array();
        $size = 0;
        return new HtmlDomParserLocal($html, $status, $headers, $text, $furtherInformation, $urlInfo, $size,null, []);
    }


    /**
     * @param $urlInfo UrlInfo
     * @return array
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function onePageProcessed($urlInfo)
    {
        $data=NULL;
        if($urlInfo->isUrlExternalLink()){
            $data=new WebPageProcessor($urlInfo->getHomeAddressExternalLink());
        }else {
            $data = new WebPageProcessor($urlInfo->getHomeAddress());
        }
        $websiteHtmlDomParser=$data->pageHtmlDomParser($urlInfo);
        return $websiteHtmlDomParser->getData();
    }

    /**
     * @param UrlInfo $urlInfo
     * @return array
     * @throws \Exception
     */
    public static function createZeroPage(UrlInfo $urlInfo)
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