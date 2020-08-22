<?php
/**
 * Created by PhpStorm.
 * User: trty
 * Date: 20/09/2017
 * Time: 07:28 PM
 */

namespace Seosazi\Utility;



use Exception;
use MongoDB\BSON\ObjectID;
use Seosazi\Storage\WorkWithStorage;

class PagePropertyQuery
{
    private $properties = [];
    private $pagesID = [];
    /** @var  int */
    public $crawlJobID;


    /**
     * @param $websiteID
     * @return PagePropertyQuery
     * @throws Exception
     */
    public static function makeGetAllTitlesQuery($websiteID)
    {
        $query = new PagePropertyQuery();
        $query->title();
        $query->canonical();
        $query->addAllPages($websiteID);
        return $query;
    }

    /**
     * @param $websiteID
     * @return PagePropertyQuery
     * @throws Exception
     */
    public static function makeGetAllDescriptionsQuery($websiteID)
    {
        $query = new PagePropertyQuery();
        $query->title();
        $query->description();
        $query->canonical();
        $query->addAllPages($websiteID);
        return $query;
    }

    /**
     * @param $websiteID
     * @return PagePropertyQuery
     * @throws Exception
     */
    public static function makeGetAllPageQuery($websiteID)
    {
        $query = new PagePropertyQuery();
        $query->title();
        $query->description();
        $query->parent();
        $query->canonical();
        $query->addAllPages($websiteID);
        return $query;
    }

    /**
     * @param int $crawlJobID
     * @param ObjectID $attributeID
     * @return LimitedProcessPage
     * @throws Exception
     */
    public static function makeGetTitlesQueryOnePage(int $crawlJobID, ObjectID $attributeID)
    {
        $query = new PagePropertyQuery();
        $query->setCrawlJobID($crawlJobID);
        $query->title();
        $query->canonical();
        $query->addPage($attributeID);
        $result = $query->getResult();
        return $result[0];
    }
    /**
     * @param int $crawlJobID
     * @param ObjectID $attributeID
     * @return LimitedProcessPage
     * @throws Exception
     */
    public static function makeGetH1TagQueryOnePage(int $crawlJobID, ObjectID $attributeID)
    {
        $query = new PagePropertyQuery();
        $query->setCrawlJobID($crawlJobID);
        $query->title();
        $query->H1Tag();
        $query->addPage($attributeID);
        $result = $query->getResult();
        return $result[0];
    }

    /**
     * @param int $crawlJobID
     * @param ObjectID $attributeID
     * @return LimitedProcessPage
     * @throws Exception
     */
    public static function makeGetDepthQueryOnePage(int $crawlJobID, ObjectID $attributeID)
    {
        $query = new PagePropertyQuery();
        $query->setCrawlJobID($crawlJobID);
        $query->title();
        $query->depth();
        $query->addPage($attributeID);
        $result = $query->getResult();
        return $result[0];
    }

    /**
     * @param int $crawlJobID
     * @param ObjectID $attributeID
     * @return LimitedProcessPage
     * @throws Exception
     */
    public static function makeGetImageAltQueryOnePage(int $crawlJobID, ObjectID $attributeID)
    {
        $query = new PagePropertyQuery();
        $query->setCrawlJobID($crawlJobID);
        $query->title();
        $query->ImageAlt();
        $query->addPage($attributeID);
        $result = $query->getResult();
        return $result[0];
    }

    /**
     * @param int $crawlJobID
     * @param ObjectID $attributeID
     * @return LimitedProcessPage
     * @throws Exception
     */
    public static function makeGetBodyTextQueryOnePage(int $crawlJobID, ObjectID $attributeID)
    {
        $query = new PagePropertyQuery();
        $query->setCrawlJobID($crawlJobID);
        $query->title();
        $query->bodyText();
        $query->addPage($attributeID);
        $result = $query->getResult();
        return $result[0];
    }

    /**
     * @param int $crawlJobID
     * @param ObjectID $attributeID
     * @return LimitedProcessPage
     * @throws Exception
     */
    public static function makeGetDescriptionQueryOnePage(int $crawlJobID, ObjectID $attributeID)
    {
        $query = new PagePropertyQuery();
        $query->setCrawlJobID($crawlJobID);
        $query->title();
        $query->description();
        $query->canonical();
        $query->addPage($attributeID);
        $result = $query->getResult();
        return $result[0];
    }

    /**
     * @param int $crawlJobID
     * @param ObjectID $attributeID
     * @return LimitedProcessPage
     * @throws Exception
     */
    public static function makeGetExternalLinksQueryOnePage(int $crawlJobID, ObjectID $attributeID)
    {
        $query = new PagePropertyQuery();
        $query->setCrawlJobID($crawlJobID);
        $query->title();
        $query->externalLinks();
        $query->addPage($attributeID);
        $result = $query->getResult();
        return $result[0];
    }

    /**
     * @param $pageID
     * @return LimitedProcessPage
     * @throws Exception
     */
    public static function makeGetInfoQueryOnePage($pageID)
    {
        $query = new PagePropertyQuery();
        $query->addPage($pageID);
        $result = $query->getResult();
        return $result[0];
    }

    public function title(){
        $this->properties[] = 'title';
    }
    public function H1Tag(){
        $this->properties[] = 'h1Tag';
    }
    public function depth(){
        $this->properties[] = 'depth';
    }
    public function bodyText(){
        $this->properties[] = 'bodyText';
    }
    public function description(){
        $this->properties[] = 'description';
    }
    public function hTags(){
        $this->properties[] = 'hTags';
    }
    public function ImageAlt(){
        $this->properties[] = 'imageAlt';
    }
    public function canonical(){
        $this->properties[] = 'canonical';
    }
    public function parent(){
        $this->properties[] = 'parent';
    }
    public function externalLinks(){
        $this->properties[] = 'externalLinks';
    }

    /**
     * @param ObjectID $pageID
     * @throws Exception
     */
    public function addPage(ObjectID $pageID){
        if(is_null($pageID)){
            throw new Exception('pageID Must be ObjectID and not NULL');
        }
        $this->pagesID[] = $pageID;
    }

    /**
     * @param int $crawlJobID
     * @throws Exception
     */
    public function addAllPages(int $crawlJobID){
        if(!is_int($crawlJobID)){
            throw new Exception('crawlJobID Must be int');
        }
        $this->pagesID = $crawlJobID;
    }

    /**
     * @return bool|LimitedProcessPage[]
     * @throws Exception
     */
    public function getResult(){
        $processedPages = [];
        $pages=[];
        $selectedFields=[];
        $selectedFields['_id']=true;
        $selectedFields['crawlJobID']=true;
        $selectedFields['attribute.status']=true;
        $selectedFields['attribute.headers']=true;
        $selectedFields['attribute.url']=true;
        $selectedFields['attribute.urlInfo']=true;

        if (in_array('title', $this->properties))
            $selectedFields['attribute.title']=true;

        if (in_array('description', $this->properties))
            $selectedFields['attribute.description']=true;

        if (in_array('depth', $this->properties))
            $selectedFields['attribute.depth']=true;

        if (in_array('bodyText', $this->properties))
            $selectedFields['attribute.body']=true;

        if (in_array('h1Tag', $this->properties))
            $selectedFields['attribute.H1']=true;

        if (in_array('imageAlt', $this->properties))
            $selectedFields['attribute.ImageAttribute']=true;

        if (in_array('canonical', $this->properties))
            $selectedFields['attribute.canonical']=true;

        if (in_array('parent', $this->properties))
            $selectedFields['attribute.parent']=true;

        if (in_array('externalLinks', $this->properties))
            $selectedFields['attribute.externalLinks']=true;

        $options=[
            'projection' => $selectedFields
        ];
        $storage = WorkWithStorage::create();
        if(is_array($this->pagesID)){
            foreach ($this->pagesID as $pageID) {
                $findData= [
                    '_id' => $pageID
                ];
                $result  = $storage->getAttribute($findData,$options)->getResults();
                if (!empty($result)) {
                    foreach ($result as $page) {
                        $pages[] = $page;
                    }
                }
            }
            
        }else{
            $findData=[
                'crawlJobID' => $this->pagesID,
                'status' =>[
                    '$gt' => 0
                    ]
            ];
            $options=[
                'projection' => $selectedFields
            ];
            $pages = $storage->getAttribute($findData,$options)->getResults();
        }
        foreach ($pages as $page) {
            $page['attribute']=NeedsTools::BSONDocumentToArray($page['attribute']);
            $LimitedProcessPage = new LimitedProcessPage();
            $LimitedProcessPage->setUrl($page['attribute']['url']);
            $LimitedProcessPage->setCrawlJobID($page['crawlJobID']);
            $LimitedProcessPage->setAttributeID($page['_id']);
            $LimitedProcessPage->setStatus($page['attribute']['status']);
            $LimitedProcessPage->setHeader($page['attribute']['headers']);
            $LimitedProcessPage->setUrlInfo($page['attribute']['urlInfo']);
            if (in_array('title', $this->properties))
                $LimitedProcessPage->setTitle($page['attribute']['title']);

            if (in_array('description', $this->properties))
                $LimitedProcessPage->setDescription($page['attribute']['description']);

            if (in_array('depth', $this->properties))
                $LimitedProcessPage->setDepth($page['attribute']['depth']);

            if (in_array('bodyText', $this->properties))
                $LimitedProcessPage->setBodyText($page['attribute']['body']);

            if (in_array('h1Tag', $this->properties))
                $LimitedProcessPage->setH1Tag($page['attribute']['H1']);

            if (in_array('imageAlt', $this->properties))
                $LimitedProcessPage->setImageAlt($page['attribute']['ImageAttribute']);

            if (in_array('canonical', $this->properties))
                $LimitedProcessPage->setCanonical($page['attribute']['canonical']);

            if (in_array('parent', $this->properties))
                $LimitedProcessPage->setParent($page['attribute']['parent']);
            if (in_array('externalLinks', $this->properties))
                $LimitedProcessPage->setExternalLinks($page['attribute']['externalLinks']);

            $processedPages[] = $LimitedProcessPage;
        }
        if (empty($processedPages)) {
            return false;
        }
        return $processedPages;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getCrawlJobID(): int
    {
        if($this->crawlJobID === null){
            throw new Exception('crawlJobID is not selected.');
        }
        if(!is_int($this->crawlJobID)){
            throw new Exception('crawlJobID is not selected.');
        }
        return $this->crawlJobID;
    }

    /**
     * @param int $crawlJobID
     * @throws Exception
     */
    public function setCrawlJobID(int $crawlJobID)
    {
        if(!is_int($crawlJobID)){
            throw new Exception('crawlJobID Must be int');
        }
        $this->crawlJobID = $crawlJobID;
    }

}


