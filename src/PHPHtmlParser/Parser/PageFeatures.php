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


class PageFeatures
{
    private $title;
    private $description;
    private $url;
    private $status;
    private $header;
    private $urlInfo;
    private $depth;
    private $h1Tag;
    private $bodyText;
    private $imageAlt;
    private $canonical;
    private $html;
    private $parent;
    private $externalLinks;
    private $internalLinks;
    private $links;
    private $urlBeforeRedirect;
    private $redirect;
    private $baseTag;
    private $headerContentType;
    private $keywords;
    private $pTag = [];
    private $spanTag = [];
    private $h2Tag = [];
    private $h3Tag = [];
    private $h4Tag = [];
    private $h5Tag = [];
    private $h6Tag = [];
    private $liElement = [];
    private $anchorTag = [];
    private $entireData = [];


    /**
     * @param array $attribute
     * @return PageFeatures
     * @throws Exception
     */
    public static function create(array $attribute)
    {
//        dump($attribute);
        $processPage = new PageFeatures();
        $processPage->setUrlInfo($attribute['urlInfo']);
        $processPage->setHtml($attribute['html']);
        $processPage->setUrl($attribute['url']);
        $processPage->setParent($attribute['parent']);
        $processPage->setStatus($attribute['status']);
        $processPage->setHeader($attribute['headers']);
        $processPage->setDepth($attribute['depth']);
        $processPage->setUrlBeforeRedirect($attribute['urlBeforeRedirect']);
        $processPage->setRedirect($attribute['redirect']);
        $processPage->setBaseTag($attribute['baseTag']);
        $processPage->setHeaderContentType($attribute['headerContentType']);
        $processPage->setBodyText($attribute['body']);
        $processPage->setTitle($attribute['title']);
        $processPage->setKeywords($attribute['keywords']);
        $processPage->setDescription($attribute['description']);
        $processPage->setCanonical($attribute['canonical']);
        $processPage->setPTag($attribute['p']);
        $processPage->setSpanTag($attribute['span']);
        $processPage->setH1Tag($attribute['H1']);
        $processPage->setH2Tag($attribute['H2']);
        $processPage->setH3Tag($attribute['H3']);
        $processPage->setH4Tag($attribute['H4']);
        $processPage->setH5Tag($attribute['H5']);
        $processPage->setH6Tag($attribute['H6']);
        $processPage->setLiElement($attribute['li']);
        $processPage->setImageAlt($attribute['ImageAttribute']);
        $processPage->setAnchorTag($attribute['a']);
        $processPage->setLinks($attribute['links']);
        $processPage->setInternalLinks($attribute['innerLinks']);
        $processPage->setExternalLinks($attribute['externalLinks']);
        $processPage->setEntireData($attribute);
        return $processPage;
    }


    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param $title
     * @throws Exception
     */
    public function setTitle($title)
    {
        if($title === null){
            $this->title = false;
        }elseif (is_array($title)) {
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
            }else {
                $this->title = $title;
            }
        }
    }

    /**
     * @param $canonical
     */
    public function setCanonical($canonical)
    {
        if($canonical === null){
            $this->canonical = false;
        }elseif (is_array($canonical)) {
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
        return $this->canonical;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        if($description === null){
            $this->description = false;
        }elseif (is_array($description)) {
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
            }else {
                $this->description = $description;
            }
        }
    }


    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        if($url === null){
            $this->url = false;
        }elseif (is_array($url)) {
            if (empty($url)) {
                $this->url = false;
            }else {
                $this->url = $url[0]['url'];
            }
        }else {
            $this->url = $url;
        }
    }


    public function getStatus()
    {
        return $this->status;
    }


    public function setStatus($status)
    {
        if($status === null){
            $this->status = false;
        }elseif(!is_int($status)){
            $this->status = false;
        }else {
            $this->status = $status;
        }
    }


    public function getHeader()
    {
        return $this->header;
    }

    public function setHeader($header)
    {
        if($header === null){
            $this->header = false;
        }else {
            $this->header = $header;
        }
    }

    /**
     * @return bool
     */
    public function isPage()
    {
        return Url::isHtmlPage($this->header);
    }


    public function getUrlInfo()
    {
        return $this->urlInfo;
    }

    public function setUrlInfo($urlInfo)
    {
        if($urlInfo === null){
            $this->urlInfo = false;
        }elseif(!is_array($urlInfo)){
            $this->urlInfo = false;
        }else{
            $this->urlInfo = new Url($urlInfo);
        }
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

    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * @param $depth
     */
    public function setDepth($depth)
    {
        if($depth === null){
            $this->depth = false;
        }elseif(!is_int($depth)) {
            $this->depth = false;
        }else {
            $this->depth = $depth;
        }
    }


    public function getH1Tag()
    {
        return $this->h1Tag;
    }

    /**
     * @param $h1Tag
     */
    public function setH1Tag($h1Tag)
    {
        if($h1Tag === null){
            $this->h1Tag = false;
        }elseif(is_array($h1Tag)) {
            if (empty($h1Tag)) {
                $this->h1Tag =false;
            }else {
                $this->h1Tag = $h1Tag;
            }
        }else {
            $this->h1Tag = $h1Tag;
        }
    }


    public function getBodyText()
    {
        return $this->bodyText;
    }

    /**
     * @param $bodyText
     */
    public function setBodyText($bodyText)
    {
        if($bodyText === null){
            $this->bodyText = false;
        }elseif (is_array($bodyText)) {
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
            }else {
                $this->bodyText = $bodyText;
            }
        }
    }


    public function getImageAlt()
    {
        return $this->imageAlt;
    }

    /**
     * @param $imageAlt
     */
    public function setImageAlt($imageAlt)
    {
        if($imageAlt === null){
            $this->imageAlt = false;
        }elseif(is_array($imageAlt)) {
            if (empty($imageAlt)) {
                $this->imageAlt =false;
            }else {
                $this->imageAlt = $imageAlt;
            }
        }else {
            $this->imageAlt = $imageAlt;
        }
    }

    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @param $html
     */
    public function setHtml($html)
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
     * @param $parent
     */
    public function setParent($parent)
    {
        if($parent === null){
            $this->parent = '';
        }else{
            $this->parent = $parent;
        }

    }

    public function getExternalLinks()
    {
        return $this->externalLinks;
    }

    public function setExternalLinks($externalLinks)
    {
        if($externalLinks === null){
            $this->externalLinks = false;
        }elseif(is_array($externalLinks)) {
            if (empty($externalLinks)) {
                $this->externalLinks =false;
            }else {
                $lastLinks = [];
                foreach ($externalLinks as $link) {
                    $lastLinks[] = new Url($link);
                }
                $this->externalLinks = $lastLinks;
            }
        }else {
            $this->externalLinks = false;
        }
    }


    public function getInternalLinks()
    {
        return $this->internalLinks;
    }

    public function setInternalLinks($internalLinks)
    {
        if($internalLinks === null){
            $this->internalLinks = false;
        }elseif(is_array($internalLinks)) {
            if (empty($internalLinks)) {
                $this->internalLinks =false;
            }else {
                $lastLinks = [];
                foreach ($internalLinks as $link) {
                    $lastLinks[] = new Url($link);
                }
                $this->internalLinks = $lastLinks;
            }
        }else {
            $this->internalLinks = false;
        }
    }

    public function getLinks()
    {
        return $this->links;
    }


    public function setLinks($links)
    {
        if($links === null){
            $this->links = false;
        }elseif(is_array($links)) {
            if (empty($links)) {
                $this->links =false;
            }else {
                $lastLinks = [];
                foreach ($links as $link) {
                    $lastLinks[] = new Url($link);
                }
                $this->links = $lastLinks;
            }
        }else {
            $this->links = false;
        }
    }

    public function setUrlBeforeRedirect($urlBeforeRedirect)
    {
        if($urlBeforeRedirect === null){
            $this->urlBeforeRedirect = false;
        }elseif(is_array($urlBeforeRedirect)) {
            if (empty($urlBeforeRedirect)) {
                $this->urlBeforeRedirect =false;
            }elseif (!is_string($urlBeforeRedirect[0]) AND !empty($urlBeforeRedirect[0])) {
                $this->urlBeforeRedirect = false;
            }else {
                $this->urlBeforeRedirect = $urlBeforeRedirect[0];
            }
        }else {
            if (!is_string($urlBeforeRedirect) AND !empty($urlBeforeRedirect)) {
                $this->urlBeforeRedirect = false;
            }else {
                $this->urlBeforeRedirect = $urlBeforeRedirect;
            }
        }
    }


    public function getUrlBeforeRedirect()
    {
        return $this->urlBeforeRedirect;
    }


    public function getRedirect()
    {
        return $this->redirect;
    }


    public function setRedirect($redirect)
    {
        if($redirect === null){
            $this->redirect = false;
        }elseif(is_array($redirect)) {
            if (empty($redirect)) {
                $this->redirect =false;
            }else {
                $this->redirect = $redirect;
            }
        }else {
            $this->redirect = $redirect;
        }
    }


    public function getBaseTag()
    {
        return $this->baseTag;
    }

    /**
     * @param $baseTag
     */
    public function setBaseTag($baseTag)
    {
        if($baseTag === null){
            $this->baseTag = false;
        }elseif(is_array($baseTag)) {
            if (empty($baseTag)) {
                $this->baseTag =false;
            }else {
                $this->baseTag = $baseTag;
            }
        }else {
            $this->baseTag = $baseTag;
        }
    }

    /**
     * @return mixed
     */
    public function getHeaderContentType()
    {
        return $this->headerContentType;
    }

    /**
     * @param $headerContentType
     */
    public function setHeaderContentType($headerContentType)
    {
        if($headerContentType === null){
            $this->headerContentType = false;
        }elseif(is_array($headerContentType)) {
            if (empty($headerContentType)) {
                $this->headerContentType =false;
            }else {
                $this->headerContentType = $headerContentType;
            }
        }else {
            $this->headerContentType = $headerContentType;
        }
    }

    /**
     * @return mixed
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * @param mixed $keywords
     */
    public function setKeywords($keywords)
    {
        if($keywords === null){
            $this->keywords = false;
        }elseif(is_array($keywords)) {
            if (empty($keywords)) {
                $this->keywords =false;
            }elseif (!is_string($keywords[0]) AND !empty($keywords[0])) {
                $this->keywords = false;
            }else {
                $this->keywords = $keywords[0];
            }
        }else {
            if (!is_string($keywords) AND !empty($keywords)) {
                $this->keywords = false;
            }else {
                $this->keywords = $keywords;
            }
        }
    }

    /**
     * @return array
     */
    public function getPTag()
    {
        return $this->pTag;
    }

    /**
     * @param $pTag
     */
    public function setPTag($pTag)
    {
        if($pTag === null){
            $this->pTag = false;
        }elseif(is_array($pTag)) {
            if (empty($pTag)) {
                $this->pTag =false;
            }else {
                $this->pTag = $pTag;
            }
        }else {
            $this->pTag = $pTag;
        }
    }

    /**
     * @return array
     */
    public function getSpanTag()
    {
        return $this->spanTag;
    }

    /**
     * @param $spanTag
     */
    public function setSpanTag($spanTag)
    {
        if($spanTag === null){
            $this->spanTag = false;
        }elseif(is_array($spanTag)) {
            if (empty($spanTag)) {
                $this->pTag =false;
            }else {
                $this->spanTag = $spanTag;
            }
        }else {
            $this->spanTag = $spanTag;
        }
    }

    /**
     * @return array
     */
    public function getH2Tag()
    {
        return $this->h2Tag;
    }

    /**
     * @param $h2Tag
     */
    public function setH2Tag($h2Tag)
    {
        if($h2Tag === null){
            $this->h2Tag = false;
        }elseif(is_array($h2Tag)) {
            if (empty($h2Tag)) {
                $this->h2Tag =false;
            }else {
                $this->h2Tag = $h2Tag;
            }
        }else {
            $this->h2Tag = $h2Tag;
        }
    }

    /**
     * @return array
     */
    public function getH3Tag()
    {
        return $this->h3Tag;
    }

    /**
     * @param $h3Tag
     */
    public function setH3Tag($h3Tag)
    {
        if($h3Tag === null){
            $this->h3Tag = false;
        }elseif(is_array($h3Tag)) {
            if (empty($h3Tag)) {
                $this->h3Tag =false;
            }else {
                $this->h3Tag = $h3Tag;
            }
        }else {
            $this->h3Tag = $h3Tag;
        }
    }

    /**
     * @return array
     */
    public function getH4Tag()
    {
        return $this->h4Tag;
    }

    /**
     * @param $h4Tag
     */
    public function setH4Tag($h4Tag)
    {
        if($h4Tag === null){
            $this->h4Tag = false;
        }elseif(is_array($h4Tag)) {
            if (empty($h4Tag)) {
                $this->h4Tag =false;
            }else {
                $this->h4Tag = $h4Tag;
            }
        }else {
            $this->h4Tag = $h4Tag;
        }
    }


    public function getH5Tag()
    {
        return $this->h5Tag;
    }

    /**
     * @param $h5Tag
     */
    public function setH5Tag($h5Tag)
    {
        $this->h5Tag = $h5Tag;
        if($h5Tag === null){
            $this->h5Tag = false;
        }elseif(is_array($h5Tag)) {
            if (empty($h5Tag)) {
                $this->h5Tag =false;
            }else {
                $this->h5Tag = $h5Tag;
            }
        }else {
            $this->h5Tag = $h5Tag;
        }
    }


    public function getH6Tag()
    {
        return $this->h6Tag;
    }

    /**
     * @param $h6Tag
     */
    public function setH6Tag($h6Tag)
    {
        if($h6Tag === null){
            $this->h6Tag = false;
        }elseif(is_array($h6Tag)) {
            if (empty($h6Tag)) {
                $this->h6Tag =false;
            }else {
                $this->h6Tag = $h6Tag;
            }
        }else {
            $this->h6Tag = $h6Tag;
        }
    }


    public function getLiElement()
    {
        return $this->liElement;
    }

    /**
     * @param $liElement
     */
    public function setLiElement($liElement)
    {
        if($liElement === null){
            $this->liElement = false;
        }elseif(is_array($liElement)) {
            if (empty($liElement)) {
                $this->liElement =false;
            }else {
                $this->liElement = $liElement;
            }
        }else {
            $this->liElement = $liElement;
        }
    }

    public function getAnchorTag()
    {
        return $this->anchorTag;
    }

    /**
     * @param $anchorTag
     */
    public function setAnchorTag($anchorTag)
    {
        if($anchorTag === null){
            $this->anchorTag = false;
        }elseif(is_array($anchorTag)) {
            if (empty($anchorTag)) {
                $this->anchorTag =false;
            }else {
                $this->anchorTag = $anchorTag;
            }
        }else {
            $this->anchorTag = $anchorTag;
        }
    }


    public function getEntireData()
    {
        return $this->entireData;
    }

    /**
     * @param $entireData
     */
    public function setEntireData($entireData)
    {
        $this->entireData = $entireData;
    }
}