<?php
/**
 * Created by PhpStorm.
 * User: trty
 * Date: 22/09/2017
 * Time: 10:50 AM
 */

namespace PHPHtmlParser\Parser;


use Exception;
use PHPHtmlParser\Crawl\Url;
use Seosazi\Tools\PageReference;

class PageFeatures
{
    private $title;
    private $description;
    private $url;
    private $status;
    private $header;
    /* @var $urlInfo Url*/
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
    /** @var array */
    private $innerLinks;
    /** @var array */
    private $links;


    /**
     * @param array $attribute
     * @return PageFeatures
     * @throws Exception
     */
    public static function create(array $attribute)
    {
        $processPage = new PageFeatures();
        $processPage->setTitle($attribute['title']);
        $processPage->setH1Tag($attribute['H1']);
        $processPage->setDescription($attribute['description']);
        $processPage->setHeader($attribute['headers']);
        $processPage->setStatus($attribute['status']);
        $processPage->setHtml($attribute['html']);
        $processPage->setImageAlt($attribute['ImageAttribute']);
        $processPage->setUrlInfo($attribute['urlInfo']);
        $processPage->setParent($attribute['parent']);
        $processPage->setCanonical($attribute['canonical']);
        $processPage->setBodyText($attribute['body']);
        $processPage->setLinks($attribute['links']);
        $processPage->setInnerLinks($attribute['innerLinks']);
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
            $this->title = false;
        }
        if (is_array($title)) {
            if (empty($title)) {
                $this->title = false;
            }elseif (!is_string($title[0]) AND !empty($title[0])) {
                $this->title = false;
            }else {
                $this->title = $title[0];
            }
        }else {
            if (!is_string($title) AND !empty($title) ) {
                $this->title = false;
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
            $canonical = false;
        }
        if (is_array($canonical)) {
            if (empty($canonical)) {
                $this->canonical = false;
            }else {
                $this->canonical = $canonical[0]['url'];
            }
        }else {
            $this->canonical = $canonical;
        }
    }

    public function getCanonical()
    {
        if($this->canonical === null){
            throw new Exception('canonical is not selected.');
        }

        return $this->canonical;
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
            $this->description = false;
        }
        if (is_array($description)) {
            if (empty($description)) {
                $this->description = '';
            }elseif (!is_string($description[0]) AND !empty($description[0])) {
                $this->description = false;
            }else {
                $this->description = $description[0];
            }
        }else {
            if (!is_string($description) AND !empty($description)) {
                $this->description = false;
            }
            $this->description = $description;
        }
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
            $this->status = false;
        }
        if(!is_int($status)){
            $this->status = false;
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
            $this->header = false;
        }
        $this->header = $header;
    }

    /**
     * @return bool
     */
    public function isPage()
    {
        return Url::isHtmlPage($this->header);
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
            $this->urlInfo = false;
        }
        if(!is_array($urlInfo)){
            $this->urlInfo = false;
        }
        $this->urlInfo = new Url($urlInfo);
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
     */
    public function setDepth($depth)
    {
        if($depth === null){
            $this->depth = false;
        }
        if(!is_int($depth)) {
            $this->depth = false;
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
        if($bodyText === null){
            $this->bodyText = false;
        }
        if (is_array($bodyText)) {
            if (empty($bodyText)) {
                $this->bodyText =false;
            }elseif (!is_string($bodyText[0]) AND !empty($bodyText[0])) {
                $this->bodyText = false;
            }else {
                $this->bodyText = $bodyText[0];
            }
        }else {
            if (!is_string($bodyText) AND !empty($bodyText)) {
                $this->bodyText = false;
            }
            $this->bodyText = $bodyText;
        }
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
    public function getParent()
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
    public function getExternalLinks()
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

    /**
     * @return array
     */
    public function getInnerLinks()
    {
        return $this->innerLinks;
    }

    /**
     * @param array $innerLinks
     */
    public function setInnerLinks($innerLinks)
    {
        if(is_null($innerLinks)){
            $this->innerLinks = [];
        }else {
            $this->innerLinks = $innerLinks;
        }
    }

    /**
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @param array $links
     */
    public function setLinks($links)
    {
        if(is_null($links)){
            $this->links = [];
        }else {
            $this->links = $links;
        }
    }
}