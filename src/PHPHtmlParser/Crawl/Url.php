<?php
/**
 * Created by PhpStorm.
 * User: trty
 * Date: 29/07/2017
 * Time: 11:00 PM
 */

namespace PHPHtmlParser\Crawl;


use Exception;
use PHPHtmlParser\Utility\Tools;

class Url
{
    /** @var string  */
    private $homeAddress;
    /** @var string  */
    private $url;
    /** @var string  */
    private $parent;
    /** @var string  */
    private $anchorText;
    /** @var string  */
    private $fabricUrl;
    /** @var int  */
    private $depth;
    /** @var int  */
    private $status;
    /** @var string|null|bool  */
    private $homeAddressExternalLink=NULL;

    /**
     * UrlInfo constructor.
     * @param array $urlInfo
     * @throws Exception
     */
    public function __construct(array $urlInfo)
    {
        if(array_key_exists('homeAddress', $urlInfo))
            $this->setHomeAddress($urlInfo['homeAddress']);
        if(array_key_exists('url', $urlInfo))
            $this->setUrl($urlInfo['url']);
        if(array_key_exists('parent', $urlInfo))
            $this->setParent($urlInfo['parent']);
        if(array_key_exists('anchorText', $urlInfo))
            $this->setAnchorText($urlInfo['anchorText']);
        if(array_key_exists('fabricUrl', $urlInfo))
            $this->setFabricUrl($urlInfo['fabricUrl']);
        if(array_key_exists('depth', $urlInfo))
            $this->setDepth($urlInfo['depth']);
        if(array_key_exists('status', $urlInfo))
            $this->SetStatus($urlInfo['status']);
        if(array_key_exists('homeAddressIsExternalLink', $urlInfo)) {
            $this->setHomeAddressExternalLink($urlInfo['homeAddressIsExternalLink']);
        }
    }


    /**
     * @param string $homeAddress
     * @param string $url
     * @param string $parent
     * @param string $anchorText
     * @param string $fabricUrl
     * @param int $depth
     * @param null $status
     * @param bool $homeAddressExternalLink
     * @return Url
     * @throws Exception
     */
    public static function createUrlInfo(string $homeAddress, string $url, string $parent, string $anchorText, string $fabricUrl, int $depth, $status=NULL,
                                         $homeAddressExternalLink=false)
    {
        $result=array();
        $result['homeAddress'] = $homeAddress;
        $result['url'] = $url;
        $result['parent'] = $parent;
        $result['anchorText'] = $anchorText;
        $result['fabricUrl'] = $fabricUrl;
        $result['depth'] = $depth;
        $result['status']=$status;
        $result['homeAddressIsExternalLink']=$homeAddressExternalLink;
        return new Url($result);
    }

    /**
     * @param string $url
     * @return Url
     * @throws Exception
     */
    public static function createUrlInfoWithUrl(string $url)
    {
        $result=array();
        $result['homeAddress'] = Tools::getHostName($url);
        $result['url'] = $url;
        $result['parent'] = $url;
        $result['anchorText'] = 'home';
        $result['fabricUrl'] = $url;
        $result['depth'] = 0;
        $result['status']=200;
        $result['homeAddressIsExternalLink']=null;
        return new Url($result);
    }

    /**
     * @param array $urlInfoArray
     * @return array
     * @throws Exception
     */
    public static function arrayDataToArrayUrlInfo(array $urlInfoArray)
    {
        $arrayUrlInfo=array();
        foreach ($urlInfoArray as $urlInfo){
            $arrayUrlInfo[]=new Url($urlInfo);
        }
        return $arrayUrlInfo;
    }

    /**
     * @param string $url
     * @return string
     * @throws Exception
     */
    public static function correctUrl(string $url)
    {
        $url_parser=parse_url($url);
        if(array_key_exists('scheme', $url_parser) && array_key_exists('host', $url_parser)) {
            if (array_key_exists('path', $url_parser) && array_key_exists('query', $url_parser)) {
                $url_end = $url_parser['scheme'] . '://' . $url_parser['host'] . $url_parser['path'] . '?' . $url_parser['query'];
            } elseif (array_key_exists('path', $url_parser)) {
                $url_end = $url_parser['scheme'] . '://' . $url_parser['host'] . $url_parser['path'];
            } elseif (array_key_exists('query', $url_parser)) {
                $url_end = $url_parser['scheme'] . '://' . $url_parser['host'] . '?' . $url_parser['query'];
            } else {
                $url_end = $url_parser['scheme'] . '://' . $url_parser['host'];
            }
            #$url_end = rtrim($url_end, "/");
            $url_end = Url::convertToUTF8($url_end);
            return $url_end;
        }

        $url_end = Url::convertToUTF8($url);
        return $url_end;

    }


    /**
     * @param string $relativeUrl
     * @param string $currentUrl
     * @param string|null $baseUrl
     * @return bool|string
     * @throws Exception
     */
    public static function completeEditUrl(string $relativeUrl, string $currentUrl, string $baseUrl=null )
    {
        #dump('before=>      ' . $relativeUrl);
        $relativeUrl = html_entity_decode($relativeUrl);
        #dump('after html_entity_decode=>      ' .$relativeUrl);
        $url = static::absoluteUrl($relativeUrl, $currentUrl, $baseUrl);
        #dump('after absoluteUrl=>      ' .$relativeUrl);
        if($url) {
            $url = Url::correctUrl($url);
            return $url;
        }else{
            return false;
        }
    }

    /**
     * @param string $lastUrl
     * @return string
     * @throws Exception
     */
    public static function editCrawlUrl(string $lastUrl)
    {
        $url = '';
        $url_parser=parse_url($lastUrl);
        if (array_key_exists('scheme', $url_parser) && array_key_exists('host', $url_parser)) {
            if (array_key_exists('path', $url_parser) && array_key_exists('query', $url_parser)) {
                $url = $url_parser['scheme'] . '://' . $url_parser['host'] . $url_parser['path'] . '?' . $url_parser['query'];
            } elseif (array_key_exists('path', $url_parser)) {
                $url = $url_parser['scheme'] . '://' . $url_parser['host'] . $url_parser['path'];
            } elseif (array_key_exists('query', $url_parser)) {
                $url = $url_parser['scheme'] . '://' . $url_parser['host'] . '?' . $url_parser['query'];
            } else {
                $url = $url_parser['scheme'] . '://' . $url_parser['host'];
            }
        }else{
            if (array_key_exists('path', $url_parser) && array_key_exists('query', $url_parser)) {
                $url = $url_parser['path'] . '?' . $url_parser['query'];
            } elseif (array_key_exists('path', $url_parser)) {
                $url = $url_parser['path'];
            } elseif (array_key_exists('query', $url_parser)) {
                $url = '?' . $url_parser['query'];
            }
        }

        $url = Url::convertToUTF8($url);
        return $url;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getHomeAddress()
    {
        if(empty($this->homeAddress))
            throw new Exception('home address is empty.');
        return $this->homeAddress;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getUrl()
    {
        if(empty($this->url))
            throw new Exception('url is empty.');
        return $this->url;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getParent()
    {
        if(empty($this->parent))
            throw new Exception('parent address is empty.');
        return $this->parent;
    }

    /**
     * @return string
     */
    public function getAnchorText()
    {
        return $this->anchorText;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getFabricUrl()
    {
        return $this->fabricUrl;
    }

    /**
     * @return bool|int
     */
    public function getDepth()
    {
        if(empty($this->depth))
            return false;
        return $this->depth;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getUrlInfoArray()
    {
        $result=array();
        $result['homeAddress'] = $this->getHomeAddress();
        $result['url'] = $this->getUrl();
        $result['parent'] = $this->getParent();
        $result['anchorText'] = $this->getAnchorText();
        $result['fabricUrl'] = $this->getFabricUrl();
        $result['depth'] = $this->getDepth();
        $result['status']=$this->getStatus();
        $result['homeAddressIsExternalLink']=$this->getHomeAddressExternalLink();
        return $result;
    }

    /**
     * @param array $urlsInfo
     * @return bool
     * @throws Exception
     */
    public function isUrlInArray(array $urlsInfo)
    {
        /** @var array $urlsInfo */
        /** @var Url $urlInfo */
        foreach ($urlsInfo as $urlInfo) {
            if ($urlInfo->getUrl() === $this->getUrl()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $urlsInfo
     * @return bool|int
     * @throws Exception
     */
    public function getIndexUrlInArray(array $urlsInfo)
    {
        /** @var array $urlsInfo */
        /** @var Url $urlInfo */
        foreach ($urlsInfo as $key => $urlInfo) {
            if ($urlInfo->getUrl() === $this->getUrl()) {
                /** @var integer $key */
                return $key;
            }
        }
        return false;
    }

    /**
     *
     */
    public function incrementDepth()
    {
        $this->depth++;
    }

    /**
     * @param string $parent
     * @throws Exception
     */
    public function setParent(string $parent)
    {
        $this->parent = $parent;
    }


    /**
     * @return bool
     */
    private function isUrlCorrect()
    {
        $url = parse_url($this->url);
        if(!is_array($url))
            return false;
        if (!array_key_exists('host', $url))
            return false;
        return true;
    }

    /**
     * @param string $url
     * @return bool
     */
    public static function isUrl(string $url)
    {
        $url = parse_url($url);
        if (!array_key_exists('host', $url)) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     * @throws Exception
     */
    private function isParentUrlInSite()
    {
        $parseHomeAddress = parse_url($this->getHomeAddress());
        $parseUrl = parse_url($this->getParent());
        if (array_key_exists('host', $parseHomeAddress) && array_key_exists('host', $parseUrl)) {
            if (strpos($parseHomeAddress['host'], $parseUrl['host']) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isUrlExternalLink()
    {
        if($this->isUrlInternalLink())
            return false;
        if($this->isCorrect())
            return true;
        return false;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isUrlInternalLink()
    {
        $parseHomeAddress = parse_url($this->getHomeAddress());
        $parseUrl = parse_url($this->getUrl());
        if (array_key_exists('host', $parseHomeAddress) && array_key_exists('host', $parseUrl)) {
            if (strpos($parseHomeAddress['host'], $parseUrl['host']) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * this function use edit home address from crawl external link
     * @throws Exception
     */
    public function createHomeAddressExternalLink()
    {
        $parser=parse_url($this->getUrl());
        $this->setHomeAddressExternalLink($parser['scheme'] . '://' . $parser['host']);        
    }

    /**
     * @param $header
     * @return bool|string
     */
    public static function ContentTypePage($header)
    {
        if(is_array($header)) {
            if (array_key_exists("Content-Type", $header))
                return $header["Content-Type"];
            if (isset($header[0])) {
                $header = $header[0];
                if (array_key_exists("Content-Type", $header))
                    return $header["Content-Type"];
            }
        }
        return false;
    }

    /**
     * @param mixed $url
     * @throws Exception
     */
    public function setUrl($url)
    {
        if(empty($url)){
            throw new Exception('url is empty');
        }
        if(!is_string($url)){
            throw new Exception('url must be string');
        }
        $this->url = self::correctUrl($url);
    }

    /**
     * @param string $anchorText
     * @throws Exception
     */
    public function setAnchorText(string $anchorText)
    {
        $this->anchorText = $anchorText;
    }

    /**
     * @param string $fabricUrl
     * @throws Exception
     */
    public function setFabricUrl(string $fabricUrl)
    {
        $this->fabricUrl = $fabricUrl;
    }

    /**
     * @param int $depth
     * @throws Exception
     */
    public function setDepth(int $depth)
    {
        $this->depth = $depth;
    }

    /**
     * @param array $headers
     * @return bool|string
     */
    public function HttpResponseCode(array $headers)
    {
        return substr($headers[0], 9, 3);
    }

    public static function isHtmlPage($headers)
    {
        $ContentType = self::ContentTypePage($headers);
        if($ContentType) {
            $ContentType = $headers["Content-Type"];
            if (is_array($ContentType)) {
                if (Tools::countSubStr($ContentType[0], 'text/html')===0)
                    return false;
            } else {
                if (Tools::countSubStr($headers["Content-Type"], 'text/html')===0)
                    return false;
            }
            return true;
        }else{
            return false;
        }
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isCorrect()
    {
        //check url is ok
        if(!$this->isUrlCorrect())
            return false;


        //check parent is inner link
        if(!$this->isParentUrlInSite())
            return false;
        
        return true;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isCorrectForCrawl()
    {
        //check url is ok
        if(!$this->isUrlCorrect())
            return false;

        //check inner link
        if(!$this->isParentUrlInSite())
            return false;

        #$headers=get_headers($this->url, 1);
        $headers=$this->getHeaders();
        #var_dump($headers);
        //check link is webPage not link css, image, ...
        if(!self::isHtmlPage($headers))
            return false;
        /*if($this->HttpResponseCode($headers)!==200)
            return false;*/
        return true;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param $status
     * @throws Exception
     */
    public function setStatus($status)
    {
        if(!(is_int($status) OR is_null($status) ) ) {
            throw new Exception('Status must be int or null');
        }
        $this->status = $status;
    }

    /**
     * @param string $string
     * @return string
     * @throws Exception
     */
    public static function convertToUTF8(string $string)
    {
       return iconv('UTF-8', 'UTF-8//IGNORE',$string);
    }

    /**
     * @param mixed $homeAddress
     * @throws Exception
     */
    public function setHomeAddress($homeAddress)
    {
        if(empty($homeAddress)){
            throw new Exception('home address is empty');
        }
        if(!is_string($homeAddress)){
            throw new Exception('home address must string');
        }
        $this->homeAddress = Url::correctUrl($homeAddress);
    }

    /**
     * @return bool|string|null
     */
    public function getHomeAddressExternalLink()
    {
        return $this->homeAddressExternalLink;
    }

    /**
     * @param bool|string|null $homeAddressExternalLink
     * @throws Exception
     */
    public function setHomeAddressExternalLink($homeAddressExternalLink)
    {
        if(!(is_string($homeAddressExternalLink) OR is_NULL($homeAddressExternalLink) )){
            throw new Exception('Home address external link must string or NULL or bool');
        }
        if(is_string($homeAddressExternalLink)){
            $this->homeAddressExternalLink = Url::correctUrl($homeAddressExternalLink);
        }else {
            $this->homeAddressExternalLink = $homeAddressExternalLink;
        }
    }



    public function getHeaders()
    {
//        $clint = SeosaziFramework::makeFreeProcessor($this->homeAddress);
//        $resource = SeosaziFramework::getHttpWithClint($clint, $this->url);
//        #var_dump($resource);
        $resource = WebPageProcessor::onePageProcessed($this);
        return $resource->getHeader();
    }

    /**
     * @param string $url
     * @return string
     * @throws Exception
     */
    public static function getHomeAddressWithRedirect(string $url){

        $url = self::correctUrl($url);
        $result = [];
        $result['homeAddress'] = $url;
        $result['url'] = $url;
        $result['parent'] = $url;
        $result['anchorText'] = 'master';
        $result['fabricUrl'] = '/';
        $result['depth'] = 0;
        $result['status'] = 200;
        $result['homeAddressIsExternalLink'] = NULL;
        $urlInfo = new Url($result);
        $headers = $urlInfo->getHeaders();
        if(array_key_exists('X-Guzzle-Redirect-History', $headers)){
            $redirect = $headers['X-Guzzle-Redirect-History'];
            $counter = count($redirect);
            $url =$redirect[$counter-1];
        }
        $url_parser=parse_url($url);
        if($url_parser AND array_key_exists('scheme', $url_parser) AND array_key_exists('host', $url_parser)) {
            $url_end = $url_parser['scheme'] . '://' . $url_parser['host'];
            return $url_end;
        }
        throw new Exception('url is uncorrected');

    }


    /**
     * @param string $url
     * @return mixed
     * @throws Exception
     */
    public static function getUrlRedirect(string $url){

        $url = self::correctUrl($url);
        $urlInfo = Url::createUrlInfoWithUrl($url);
        $headers = $urlInfo->getHeaders();
        if(array_key_exists('X-Guzzle-Redirect-History', $headers)){
            $redirect = $headers['X-Guzzle-Redirect-History'];
            $counter = count($redirect);
            return $redirect[$counter-1];
        }
    }


    /**
     * @param string $url1
     * @param string $url2
     * @return bool
     * @throws Exception
     */
    public static function isTwoUrlSameHost(string $url1, string $url2)
    {
        $parseUrl1 = parse_url($url1);
        $parseUrl2 = parse_url($url2);
        if (array_key_exists('host', $parseUrl1) && array_key_exists('host', $parseUrl2)) {
            if (strpos($parseUrl1['host'], $parseUrl2['host']) !== false) {
                return true;
            }
        }

        return false;
    }

    private static function onlySitePath($url) {
        $url = preg_replace('/(^https?:\/\/.+?\/)(.*)$/i', '$1', $url);
        return rtrim($url, '/');
    }

    // Get the path with last directory
    // http://example.com/some/fake/path/page.html => http://example.com/some/fake/path/
    public static function upToLastDir($url) {
//        $url = preg_replace('/\/([^\/]+\.[^\/]+)$/i', '', $url);
        $url = preg_replace('/\/([^\/]+)$/i', '', $url);
        return rtrim($url, '/') . '/';
    }

    public static function absoluteUrl($relativeUrl, $currentUrl, $baseUrl=null) {
        $addIfRemoveDomain = '';
        $scheme = '';
        $baseUrl_Parser = parse_url($baseUrl);
        if($baseUrl === null) {
            $resultC=$currentUrl;
            $currentUrl_parser = parse_url($currentUrl);
            $addIfRemoveDomain = $currentUrl_parser['host'] . '/';
            $scheme = $currentUrl_parser['scheme'];
        }elseif(array_key_exists('scheme', $baseUrl_Parser)){
//            $resultC = $baseUrl . '://';
            $resultC = $baseUrl ;
            $addIfRemoveDomain = $baseUrl_Parser['host']. '/';
            $scheme = $baseUrl_Parser['scheme'];
        }else{
            $currentUrl_parser = parse_url($currentUrl);
            $resultC = $currentUrl_parser['scheme'] . '://' . $currentUrl_parser['host'] . $baseUrl;
            $addIfRemoveDomain = $currentUrl_parser['host']. '/';
            $scheme = $currentUrl_parser['scheme'];
        }
        #dump($resultC);
        // Skip converting if the relative url like http://... or android-app://... etc.
        if (preg_match('/[a-z0-9-]{1,}(:\/\/)/i', $relativeUrl)) {
            if(preg_match('/services:\/\//i', $relativeUrl))
                return null;
            if(preg_match('/whatsapp:\/\//i', $relativeUrl))
                return null;
            if(preg_match('/tel:/i', $relativeUrl))
                return null;
            return $relativeUrl;
        }
        // Treat path as invalid if it is like javascript:... etc.
        if (preg_match('/^[a-zA-Z]{0,}:[^\/]{0,1}/i', $relativeUrl)) {
            return NULL;
        }
        // Convert //www.google.com to http://www.google.com
        if(substr($relativeUrl, 0, 2) == '//') {
            return 'http:' . $relativeUrl;
        }
        // If the path is a fragment or query string,
        // it will be appended to the base url
        if(substr($relativeUrl, 0, 1) == '#' || substr($relativeUrl, 0, 1) == '?') {
            return $resultC . $relativeUrl;
        }
        // Treat paths with doc root, i.e, /about
        if(substr($relativeUrl, 0, 1) == '/') {
            return static::onlySitePath($resultC) . $relativeUrl;
        }
        // For paths like ./foo, it will be appended to the furthest directory
        if(substr($relativeUrl, 0, 2) == './') {
            return static::uptoLastDir($resultC) . substr($relativeUrl, 2);
        }
        // Convert paths like ../foo or ../../bar
        if(substr($relativeUrl, 0, 3) == '../') {
            $rel = $relativeUrl;
            $base = static::uptoLastDir($resultC);
            while(substr($rel, 0, 3) == '../') {
                $base = preg_replace('/\/([^\/]+\/)$/i', '/', $base);
                $rel = substr($rel, 3);
            }
            if ($base === ($scheme . '://')) {
                $base .= $addIfRemoveDomain;
            } elseif ($base===($scheme. ':/')) {
                $base .= '/' . $addIfRemoveDomain;
            }
            return $base . $rel;
        }
        if (empty($relativeUrl)) {
            return $currentUrl;
        }
        // else
        return static::uptoLastDir($resultC) . $relativeUrl;
    }

    /**
     * @param $url
     * @return string
     */
    public static function getHostNameWithoutScheme($url)
    {
        $editUrl = $url;
        if(preg_match('/^http/i', $url)){
            $editUrl = $url;
        }else{
            $editUrl = 'http://' . $url;
        }
        $url_parser=parse_url($editUrl);
        $urlEnd='';
        if(array_key_exists('scheme', $url_parser) && array_key_exists('host', $url_parser)) {
            $urlEnd= $url_parser['host'];
        }else{
            $urlEnd = $url;
        }
        $urlEnd=rtrim($urlEnd, "/");
        return $urlEnd;
    }

    public static function getHostNameWithoutSchemeAndWww($url)
    {
        $hostNameWithoutScheme = static::getHostNameWithoutScheme($url);
        if(preg_match('/^www\./i', $hostNameWithoutScheme)){
            return substr($hostNameWithoutScheme, 4);
        }else{
            return $hostNameWithoutScheme;
        }
    }

    /**
     * @param string $str
     * @return string
     */
    public static function urlEncode(string $str)
    {
        $convert = '';
        $arr1 = str_split($str);
        foreach ($arr1 as $key => $value) {
            if (mb_detect_encoding($value) == "ASCII") $convert .= $value; else $convert .= urlencode($value);
        }
        return $convert;
    }
}