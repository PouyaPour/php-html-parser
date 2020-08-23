<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 21/05/2017
 * Time: 02:21 PM
 */

namespace PHPHtmlParser\Parser;
use PHPHtmlParser\Crawl\Url;
use voku\helper\HtmlDomParser;

define('MAX_FILE_SIZE', 6000000);
class HtmlParser
{
    private $url;
    private $urlBeforeRedirect;
    private $anchorText;
    private $parent;
    private $host;
    private $html;
    private $status;
    private $headers;
    private $text=array();
    private $HtmlDomParser;
    private $crawlDuplicates;
    private $data=array();
    private $furtherInformation=array();
    private $homeAddress;
    private $depth;
    private $urlInfo;
    private $redirects = [];
    private $baseTag;
    private $headerContentType;


    /**
     * HtmlDomParserLocal constructor.
     * @param string $html
     * @param int $status
     * @param array $headers
     * @param string $text
     * @param array $furtherInformation
     * @param Url $urlInfo
     * @param int $size
     * @param bool $crawlDuplicates
     * @param array $redirect
     * @throws \Exception
     */
    public function __construct(string $html, int $status, array $headers, string $text,
                                array $furtherInformation, Url $urlInfo,
                                int $size, $crawlDuplicates=false, array $redirect)
    {
        $this->urlBeforeRedirect = $urlInfo->getUrl();
        $counter = count($redirect);
        if($counter>0) {
            $urlInfo->setUrl($redirect[$counter - 1]['originalUrl']);
        }
        /** 
         * set status 
         */
        $urlInfo->setStatus($status);
        /**
         * set is url  external link
         */
        if($urlInfo->isUrlExternalLink()){
            $urlInfo->setHomeAddressExternalLink(true);
        }else{
            $urlInfo->setHomeAddressExternalLink(false);
        }
        $this->urlInfo=$urlInfo;
        $this->html = Url::convertToUTF8($html);
        if (empty($this->html) OR !is_string($this->html)) {
            $this->html='<html></html>';
        }
        $this->url=$urlInfo->getUrl();
        $this->anchorText=$urlInfo->getAnchorText();
        $this->parent=$urlInfo->getParent();
        $this->depth=$urlInfo->getDepth();
        $this->status=$status;
        $this->headers=$headers;
        $this->text=Url::convertToUTF8($text);
        $this->redirects = $redirect;
        $this->furtherInformation=$furtherInformation;
        $this->data['urlInfo']=$this->urlInfo->getUrlInfoArray();
        $this->data['html']=$this->html;
        $this->data['url']=$this->url;
        $this->data['anchorText']=$this->anchorText;
        $this->data['parent']=$this->parent;
        $this->data['status']=$this->status;
        $this->data['headers']=$this->headers;
        $this->data['depth']=$this->depth;
        $this->data['urlBeforeRedirect']=$this->urlBeforeRedirect;
        $this->data['redirect']=$this->redirects;
        $this->HtmlDomParser=HtmlDomParser::str_get_html($this->html);
        if(is_bool($this->HtmlDomParser) AND $this->HtmlDomParser === false){
            $this->HtmlDomParser=HtmlDomParser::str_get_html('<html></html>');
        }
        $this->crawlDuplicates=$crawlDuplicates;
        $parse = parse_url($this->url);
        if(array_key_exists('host', $parse)) {
            $this->host = $parse['host'];
        }else{
            $this->host = $this->url;
        }
        $this->extractBaseTag();
        $this->data['baseTag']=$this->baseTag;
        $this->extractHeaderContentType();
        $this->data['headerContentType']=$this->headerContentType;
        $this->homeAddress=$urlInfo->getHomeAddress();
    }

    /**
     * @param string $attribute
     * @return array
     */
    private function extractData(string $attribute)
    {
        $data=array();
        if(!is_bool($this->HtmlDomParser)) {
            foreach ($this->HtmlDomParser->find($attribute) as $e) {
                $value = $e->plaintext;
                $value = trim($value);
                if ($value != '') {
                    $data[] = $value;
                }
            }
        }
        return $data;
    }

    /**
     * @return bool|mixed|null|string
     */
    private function extractBaseTag()
    {
        if(!is_bool($this->HtmlDomParser)) {
            foreach ($this->HtmlDomParser->find('base') as $e) {
                if($e->parent->tag ===strtolower('head')){
                    $this->baseTag = $e->getAttribute('href');
                    return $e->getAttribute('href');
                }
            }
        }
        $this->baseTag = null;
        return null;
    }

    private function extractHeaderContentType()
    {
        if (array_key_exists('Content-Type', $this->headers)) {
            if (is_array($this->headers['Content-Type'])) {
                $this->headerContentType = $this->headers['Content-Type'][0];
            }else{
                $this->headerContentType = $this->headers['Content-Type'];
            }
        }else {
            $this->headerContentType = null;
        }

    }

    /**
     * @return array
     * @throws \Exception
     */
    private function extractCanonical()
    {
        $data=array();
        if(!is_bool($this->HtmlDomParser)) {
            foreach ($this->HtmlDomParser->find('link') as $e) {
                if ('canonical' === $e->getAttribute('rel')) {
                    $value = $e->getAttribute('href');
                    $value = trim($value);
                    if ($value != '') {
                        $Canonical = array();
                        $Canonical['url'] = $this->checkUrl($value);
                        $Canonical['parentUrl'] = $e->parentNode()->tag;
                        $data[] = $Canonical;
                    }
                }
            }
        }
        return $data;
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function extractImageAttribute()
    {
        $data=array();
        if(!is_bool($this->HtmlDomParser)) {
            foreach ($this->HtmlDomParser->find('img') as $e) {
                $value = $e->getAttribute('src');
                $value = trim($value);
                if (empty($value)) {
                    $value = $e->getAttribute('data-src');
                    $value = trim($value);
                }
                $value = Url::convertToUTF8($value);
                $value = Url::completeEditUrl($value, $this->url, $this->baseTag);
                $ImageAttribute = array();
                $ImageAttribute['src'] = $value;
                $ImageAttribute['alt'] = trim($e->getAttribute('alt'));
                $data[] = $ImageAttribute;
            }
        }
        return $data;
    }

    /**
     * @param string $attribute
     * @return array
     */
    private function extractMetaHttpEquiv(string $attribute)
    {
        $data=array();
        if(!is_bool($this->HtmlDomParser)) {
            foreach ($this->HtmlDomParser->find('meta') as $e) {
                if ($attribute === strtolower($e->getAttribute('http-equiv'))) {
                    $value = $e->getAttribute('content');
                    $value = trim($value);
                    if ($value != '') {
                        $data[] = $value;
                    }
                }
            }
        }
        return $data;
    }

    /**
     * @param $attribute
     * @return array
     */
    private function extractMetaTag($attribute)
    {
        $data=array();
        if(!is_bool($this->HtmlDomParser)) {
            foreach ($this->HtmlDomParser->find('meta') as $e) {
                if ($attribute === strtolower($e->getAttribute('name'))) {
                    $value = $e->getAttribute('content');
                    $value = trim($value);
                    if ($value != '') {
                        $data[] = $value;
                    }
                }
            }
        }
        return $data;
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function extractLinks()
    {
        $data=array();
        if(!is_bool($this->HtmlDomParser)) {
            foreach ($this->HtmlDomParser->find('a') as $e) {
                $value = $e->href;
                $value = trim($value);
                $value = Url::convertToUTF8($value);
                $realUrl = $value;
                #$value = $this->checkUrl($value);
                $value = Url::completeEditUrl($value, $this->url, $this->baseTag);
                if($value) {
                    $text = $e->getAttribute('alt');
                    $text = $e->plaintext;
                    $url = Url::createUrlInfo($this->homeAddress, $value, $this->url, $text, $realUrl, ($this->depth + 1));
                    if ($url->isUrlExternalLink()) {
                        $url->createHomeAddressExternalLink();
                    }
                    $data[] = $url->getUrlInfoArray();
                }
            }
            foreach ($this->HtmlDomParser->find('link') as $e) {
                if(strtolower($e->getAttribute('rel'))==='stylesheet') {
                    $value = $e->href;
                    $value = trim($value);
                    $value = Url::convertToUTF8($value);
                    $realUrl = $value;
                    $value = Url::completeEditUrl($value, $this->url, $this->baseTag);
                    if($value) {
                        $text = $e->plaintext;
                        $url = Url::createUrlInfo($this->homeAddress, $value, $this->url, $text, $realUrl, ($this->depth + 1));
                        if ($url->isUrlExternalLink()) {
                            $url->createHomeAddressExternalLink();
                        }
                        $data[] = $url->getUrlInfoArray();
                    }
                }
            }
            foreach ($this->HtmlDomParser->find('script') as $e) {
                $value = $e->src;
                $value = trim($value);
                $value = Url::convertToUTF8($value);
                $realUrl = $value;
                $value = Url::completeEditUrl($value, $this->url, $this->baseTag);
                if($value) {
                    $text = $e->plaintext;
                    $url = Url::createUrlInfo($this->homeAddress, $value, $this->url, $text, $realUrl, ($this->depth + 1));
                    if ($url->isUrlExternalLink()) {
                        $url->createHomeAddressExternalLink();
                    }
                    $data[] = $url->getUrlInfoArray();
                }
            }
            foreach ($this->HtmlDomParser->find('img') as $e) {
                $value = $e->src;
                $value = trim($value);
                $value = Url::convertToUTF8($value);
                $realUrl = $value;
                $value = Url::completeEditUrl($value, $this->url, $this->baseTag);
                if($value) {
                    $text = $e->plaintext;
                    $url = Url::createUrlInfo($this->homeAddress, $value, $this->url, $text, $realUrl, ($this->depth + 1));
                    if ($url->isUrlExternalLink()) {
                        $url->createHomeAddressExternalLink();
                    }
                    $data[] = $url->getUrlInfoArray();
                }
            }
        }
        return $data;
    }


    /**
     * @return array
     * @throws \Exception
     */
    private function extractInnerLinks()
    {
        $data=array();
        $links=$this->getLinks();
        if($links) {
            foreach ($links as $urlArray) {
                $url = new Url($urlArray);
                if ($url->isUrlInternalLink()) {
                    $data[] = $url->getUrlInfoArray();
                }
            }
        }
        return $data;

    }

    /**
     * @return array
     * @throws \Exception
     */
    private function extractExternalLinks()
    {
        $data=array();
        $links=$this->getLinks();
        if($links) {
            foreach ($links as $urlArray) {
                $url = new Url($urlArray);
                if ($url->isUrlExternalLink()) {
                    $data[] = $url->getUrlInfoArray();
                }
            }
        }
        return $data;
    }

    /**
     * @param string $href
     * @return string
     * @throws \Exception
     */
    private function checkUrl(string $href)
    {
        $href = html_entity_decode($href);
        $originalHref = $href;
        if (0 !== strpos($href, 'http')) {
            if(is_array(parse_url($href))) {
                if (array_key_exists('path', parse_url($href))) {
                    $href = parse_url($href)['path'];
                    if(is_array(parse_url($originalHref))) {
                        if (array_key_exists('query', parse_url($originalHref))) {
                            $href .= '?' . parse_url($originalHref)['query'];
                        }
                    }
                }
            }
            #$path = '/' . ltrim($href, '/');
            $path = '/' . $href;
            if (extension_loaded('http')) {
                $href = http_build_url($this->url, array('path' => $path));
            } else {
                $parts = parse_url($this->url);
                $href = $parts['scheme'] . '://';
                if (isset($parts['user']) && isset($parts['pass'])) {
                    $href .= $parts['user'] . ':' . $parts['pass'] . '@';
                }
                $href .= $parts['host'];
                if (isset($parts['port'])) {
                    $href .= ':' . $parts['port'];
                }
                $href .= $path;
            }
        }
        #$href = strtolower($href);
        $href= Url::convertToUTF8($href);
        return $href;
    }
    
    private function title()
    {
        if (empty($this->extractData('title'))) {
            $this->data['title'] = $this->extractMetaTag('title');
        }else {
            $this->data['title'] = $this->extractData('title');
        }
    }

    private function body()
    {
        $this->data['body'] = $this->extractData('body');
    }

    private function keywords()
    {
        $this->data['keywords'] = $this->extractMetaTag('keywords');
    }

    private function description()
    {
        $this->data['description'] = $this->extractMetaTag('description');
    }

    private function metaRobots()
    {
        $this->data['metaRobots'] = $this->extractMetaTag('robots');
    }

    private function tagP()
    {
        $this->data['p'] = $this->extractData('p');
    }

    private function canonical()
    {
        $this->data['canonical']=$this->extractCanonical();
    }

    private function span()
    {
        $this->data['span'] = $this->extractData('span');
    }

    private function tagH1()
    {
        $this->data['H1'] = $this->extractData('H1');
    }

    private function tagH2()
    {
        $this->data['H2'] = $this->extractData('H2');
    }

    private function tagH3()
    {
        $this->data['H3'] = $this->extractData('H3');
    }

    private function tagH4()
    {
        $this->data['H4'] = $this->extractData('H4');
    }

    private function tagH5()
    {
        $this->data['H5'] = $this->extractData('H5');
    }

    private function tagH6()
    {
        $this->data['H6'] = $this->extractData('H6');
    }

    private function tagLi()
    {
        $this->data['li'] = $this->extractData('li');
    }

    private function tagA()
    {
        $this->data['a'] = $this->extractData('a');
    }

    /**
     * @throws \Exception
     */
    private function links()
    {
        $this->data['links'] = $this->extractLinks();
    }

    /**
     * @throws \Exception
     */
    private function innerLinks()
    {
        $this->data['innerLinks'] = $this->extractInnerLinks();
    }

    /**
     * @throws \Exception
     */
    private function externalLinks()
    {
        $this->data['externalLinks'] = $this->extractExternalLinks();
    }
    private function baseTag()
    {
        $this->data['baseTag'] = $this->extractBaseTag();
    }

    /**
     * @throws \Exception
     */
    private function getAttributeHtml()
    {
        $this->body();
        $this->baseTag();
        $this->title();
        $this->text();
        $this->keywords();
        $this->description();
        $this->canonical();
        $this->pageSize();
        $this->metaRefresh();
        $this->metaViewport();
        $this->metaRobots();
        $this->metaRevisitAfter();
        $this->metaAbstract();
        $this->metaAuthor();
        $this->metaContact();
        $this->metaCopyright();
        $this->metaDistribution();
        $this->metaExpires();
        $this->metaGenerator();
        $this->metaGooglebot();
        $this->metaLanguage();
        $this->metaRating();
        $this->metaCacheControl();
        $this->metaContentType();
        $this->metaSetCookie();
        $this->metaResourceType();
        $this->tagP();
        $this->span();
        $this->tagH1();
        $this->tagH2();
        $this->tagH3();
        $this->tagH4();
        $this->tagH5();
        $this->tagH6();
        $this->tagLi();
        $this->ImageAttribute();
        $this->tagA();
        $this->links();
        $this->innerLinks();
        $this->externalLinks();
    }

    /**
     * @return PageFeatures
     * @throws \Exception
     */
    public function getData()
    {
        $this->getAttributeHtml();
        return PageFeatures::create($this->data);
//        return $this->data;
        #return new ProcessedPage($this->data);
    }

    public function getImageAttribute()
    {
        $this->ImageAttribute();
        return $this->data['ImageAttribute'];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getLinks()
    {
        $this->links();
        if(empty($this->data['links']))
            return false;
        return $this->data['links'];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getInnerLinks()
    {
        $this->innerLinks();
        return $this->data['innerLinks'];
    }

    /**
     * @return bool
     */
    public function getCrawlDuplicates()
    {
        return $this->crawlDuplicates;
    }

    /**
     * @param bool $crawlDuplicates
     */
    public function setCrawlDuplicates($crawlDuplicates)
    {
        $this->crawlDuplicates = $crawlDuplicates;
    }

    private function metaRefresh()
    {
        $this->data['metaRefresh'] = $this->extractMetaHttpEquiv('refresh');
    }

    private function metaViewport()
    {
        $this->data['metaViewport'] = $this->extractMetaTag('viewport');
    }

    private function metaRevisitAfter()
    {
        $this->data['metaRevisitAfter'] = $this->extractMetaTag('revisit-after');
    }

    private function metaAbstract()
    {
        $this->data['metaAbstract'] = $this->extractMetaTag('abstract');
    }

    private function metaAuthor()
    {
        $this->data['metaAuthor'] = $this->extractMetaTag('author');
    }

    private function metaContact()
    {
        $this->data['metaContact'] = $this->extractMetaTag('contact');
    }

    private function metaCopyright()
    {
        $this->data['metaCopyright'] = $this->extractMetaTag('copyright');
    }

    private function metaDistribution()
    {
        $this->data['metaDistribution'] = $this->extractMetaTag('distribution');
    }

    private function metaExpires()
    {
        $this->data['metaExpires'] = $this->extractMetaTag('expires');
    }

    private function metaGenerator()
    {
        $this->data['metaGenerator'] = $this->extractMetaTag('generator');
    }

    private function metaGooglebot()
    {
        $this->data['metaGooglebot'] = $this->extractMetaTag('googlebot');
    }

    private function metaLanguage()
    {
        $this->data['metaLanguage'] = $this->extractMetaTag('language');
    }

    private function metaRating()
    {
        $this->data['metaRating'] = $this->extractMetaTag('rating');
    }

    private function metaCacheControl()
    {
        $this->data['metaCacheControl'] = $this->extractMetaHttpEquiv('cache-control');
    }

    private function metaContentType()
    {
        $this->data['metaContentType'] = $this->extractMetaHttpEquiv('content-type');
    }

    private function metaSetCookie()
    {
        $this->data['metaSetCookie'] = $this->extractMetaHttpEquiv('set-cookie');
    }

    private function metaResourceType()
    {
        $this->data['metaResourceType'] = $this->extractMetaHttpEquiv('resource-type');
    }

    private function pageSize()
    {
        if(array_key_exists('size_download', $this->furtherInformation)) {
            $this->data['pageSize'] = round(($this->furtherInformation['size_download']) / 1024.0, 1);
        }else{
            $this->data['pageSize'] =0;
        }
    }

    private function ImageAttribute()
    {
        $this->data['ImageAttribute'] = $this->extractImageAttribute();
    }

    private function text()
    {
        $data=array();
        foreach($this->HtmlDomParser->find('body') as $e) {
            $value = $e->text();
            $value=trim($value);
            if ($value!=''){
                $data[]=$value;
            }
        }
        $this->text=$data;
        $this->data['text']=$this->text;
    }
}
