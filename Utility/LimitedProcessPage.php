<?php
/**
 * Created by PhpStorm.
 * User: trty
 * Date: 22/09/2017
 * Time: 10:50 AM
 */

namespace Seosazi\Utility;


use Exception;
use MongoDB\BSON\ObjectID;
use Seosazi\Tools\PageReference;

class LimitedProcessPage
{
    private $title;
    private $description;
    /** @var  int */
    private $crawlJobID;
    /** @var  ObjectID */
    private $attributeID;
    private $url;
    private $status;
    private $header;
    /* @var $urlInfo UrlInfo*/
    private $urlInfo;
    /** @var int */
    private $depth;
    /** @var array */
    private $h1Tag;
    /** @var array */
    private $bodyText;
    /** @var array */
    private $imageAlt;
    /** @var string */
    private $canonical;
    /** @var string */
    private $html;
    /** @var string */
    private $parent;
    /** @var array */
    private $externalLinks;


    /**
     * @param array $attribute
     * @return LimitedProcessPage
     * @throws Exception
     */
    public static function create(array $attribute)
    {
        $processPage = new LimitedProcessPage();
        $processPage->setTitle($attribute['title']);
        $processPage->setH1Tag($attribute['H1']);
        $processPage->setDescription($attribute['description']);
        $processPage->setHeader($attribute['headers']);
        $processPage->setStatus($attribute['status']);
        $processPage->setHtml($attribute['html']);
        $processPage->setImageAlt($attribute['ImageAttribute']);
        $processPage->setExternalLinks($attribute['externalLinks']);
        return $processPage;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getTitle()
    {
        if($this->title === null){
            throw new Exception('Title is not selected.');
        }
        return $this->title;
    }

    /**
     * @param string $title
     * @throws Exception
     */
    public function setTitle($title)
    {
        if($title === null){
            throw new Exception('Title selected is NULL.');
        }
        if (is_array($title)) {
            if (empty($title)) {
                $this->title = '';
            }elseif (!is_string($title[0]) AND !empty($title[0])) {
                throw new Exception('Title selected is not string.');
            }else {
                $this->title = $title[0];
            }
        }else {
            if (!is_string($title) AND !empty($title) ) {
                throw new Exception('Title selected is not string.');
            }
            $this->title = $title;
        }
    }

    /**
     * @param string $canonical
     * @throws Exception
     */
    public function setCanonical($canonical)
    {
        if($canonical === null){
//            throw new Exception('canonical selected is NULL.');
            $canonical = '';
        }
        if (is_array($canonical)) {
            if (empty($canonical)) {
                $this->canonical = '';
            }else {
                $this->canonical = $canonical[0]['url'];
            }
        }else {
//            if (!is_string($canonical) AND !empty($canonical) ) {
//                throw new Exception('canonical selected is not string.');
//            }
            $this->canonical = $canonical;
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getDescription()
    {
        if($this->description === null){
            throw new Exception('Description is not selected.');
        }
        
        return $this->description;
    }

    /**
     * @param string $description
     * @throws Exception
     */
    public function setDescription($description)
    {
        if($description === null){
            throw new Exception('Description selected is NULL.');
        }
        if (is_array($description)) {
            if (empty($description)) {
                $this->description = '';
            }elseif (!is_string($description[0]) AND !empty($description[0])) {
                throw new Exception('Title selected is not string.');
            }else {
                $this->description = $description[0];
            }
        }else {
            if (!is_string($description) AND !empty($description)) {
                throw new Exception('Title selected is not string.');
            }
            $this->description = $description;
        }
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getCrawlJobID()
    {
        if($this->crawlJobID === null){
            throw new Exception('Id is not selected.');
        }
        return $this->crawlJobID;
    }

    /**
     * @param ObjectID $crawlJobID
     * @throws Exception
     */
    public function setCrawlJobID($crawlJobID)
    {
        if($crawlJobID === null){
            throw new Exception('Id selected is NULL.');
        }
        $this->crawlJobID = $crawlJobID;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getUrl()
    {
        if($this->url === null){
            throw new Exception('Url is not selected.');
        }
        return $this->url;
    }

    /**
     * @param string $url
     * @throws Exception
     */
    public function setUrl($url)
    {
        if($url === null){
            throw new Exception('Url selected is NULL.');
        }
        if(!is_string($url)){
            throw new Exception('Url selected is not string.');
        }
        $this->url = $url;
    }

    /**
     * @return PageReference
     * @throws Exception
     */
    public function getPageReference()
    {
        if($this->title === null){
            throw new Exception('Title is not selected.');
        }
        if($this->url === null){
            throw new Exception('Url is not selected.');
        }
        if($this->crawlJobID === null){
            throw new Exception('crawlJobResultID is not selected.');
        }
        if($this->attributeID === null){
            throw new Exception('attributeID is not selected.');
        }
        return new PageReference($this->url, $this->title, $this->crawlJobID, $this->attributeID);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getArrayDataPageReference()
    {
        if($this->title === null){
            throw new Exception('Title is not selected.');
        }
        if($this->url === null){
            throw new Exception('Url is not selected.');
        }
        if($this->crawlJobID === null){
            throw new Exception('crawlJobID is not selected.');
        }
        if($this->attributeID === null){
            throw new Exception('attributeID is not selected.');
        }
        $result = [];
        $result['title'] = $this->title;
        $result['url'] = $this->url;
        $result['crawlJobID'] = $this->getCrawlJobID();
        $result['attributeID'] = $this->getAttributeID();
        return $result;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getStatus()
    {
        if($this->status === null){
            throw new Exception('status is not selected.');
        }
        return $this->status;
    }

    /**
     * @param mixed $status
     * @throws Exception
     */
    public function setStatus($status)
    {
        if($status === null){
            throw new Exception('status selected is NULL.');
        }
        if(!is_int($status)){
            throw new Exception('status selected is not int.');
        }
        $this->status = $status;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getHeader()
    {
        if($this->header === null){
            throw new Exception('header is not selected.');
        }   
        return $this->header;
    }

    /**
     * @param mixed $header
     * @throws Exception
     */
    public function setHeader($header)
    {
        if($header === null){
            throw new Exception('header selected is NULL.');
        }
        $this->header = $header;
    }

    /**
     * @return bool
     */
    public function isPage()
    {
        return UrlInfo::isPageUrl($this->header);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getUrlInfo()
    {
        if($this->urlInfo === null){
            throw new Exception('urlInfo is not selected.');
        }
        return $this->urlInfo;
    }

    /**
     * @param mixed $urlInfo
     * @throws Exception
     */
    public function setUrlInfo($urlInfo)
    {
        if($urlInfo === null){
            throw new Exception('urlInfo selected is NULL.');
        }
        if(!is_array($urlInfo)){
            throw new Exception('urlInfo Must be Array.');
        }
        $this->urlInfo = new UrlInfo($urlInfo);
    }

    /**
     *
     */
    public function isInnerPage()
    {
        if($this->urlInfo->getHomeAddressExternalLink() === false OR $this->urlInfo->getHomeAddressExternalLink() === NULL)
            return true;
        return false;
    }

    /**
     * @return ObjectID
     */
    public function getAttributeID(): ObjectID
    {
        return $this->attributeID;
    }

    /**
     * @param ObjectID $attributeID
     */
    public function setAttributeID(ObjectID $attributeID)
    {
        $this->attributeID = $attributeID;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getDepth()
    {
        if($this->depth === null){
            throw new Exception('depth is not selected.');
        }
        return $this->depth;
    }

    /**
     * @param int $depth
     * @throws Exception
     */
    public function setDepth(int $depth)
    {
        if($depth === null){
            throw new Exception('depth selected is NULL.');
        }
        if(!is_int($depth)) {
            throw new Exception('depth selected is not int.');
        }
        $this->depth = $depth;
    }

    /**
     * @return array
     */
    public function getH1Tag()
    {
        return $this->h1Tag;
    }

    /**
     * @param array $h1Tag
     */
    public function setH1Tag(array $h1Tag)
    {
        $this->h1Tag = $h1Tag;
    }

    /**
     * @return array
     */
    public function getBodyText()
    {
        return $this->bodyText;
    }

    /**
     * @param array $bodyText
     */
    public function setBodyText(array $bodyText)
    {
        $this->bodyText = $bodyText;
    }

    /**
     * @return array
     */
    public function getImageAlt()
    {
        return $this->imageAlt;
    }

    /**
     * @param array $imageAlt
     */
    public function setImageAlt(array $imageAlt)
    {
        $this->imageAlt = $imageAlt;
    }

    /**
     * @return string
     */
    public function getCanonical()
    {
        return $this->canonical;
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @param string $html
     */
    public function setHtml(string $html)
    {
        $this->html = $html;
    }

    /**
     * @return string
     */
    public function getParent(): string
    {
        return $this->parent;
    }

    /**
     * @param string $parent
     */
    public function setParent(string $parent)
    {
        if($parent === null){
            $this->parent = '';
        }else{
            $this->parent = $parent;
        }

    }

    /**
     * @return array
     */
    public function getExternalLinks(): array
    {
        return $this->externalLinks;
    }

    /**
     * @param array|null $externalLinks
     */
    public function setExternalLinks($externalLinks)
    {
        if(is_null($externalLinks)){
            $this->externalLinks = [];
        }else {
            $this->externalLinks = $externalLinks;
        }
    }
}