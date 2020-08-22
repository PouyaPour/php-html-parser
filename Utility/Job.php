<?php
/**
 * Created by PhpStorm.
 * User: trty
 * Date: 29/07/2017
 * Time: 11:52 PM
 */

namespace Seosazi\Utility;


use Exception;
use MongoDB\BSON\ObjectID;

abstract class Job
{
    public $args;
    public $attributeId;
    public $crawlerJobResultID;
    /** @var  int */
    private $crawlJobID;

    /**
     * @return mixed
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @param mixed $args
     */
    public function setArgs($args)
    {
        $this->args = $args;
    }

    /**
     *
     */
    public function perform(){}

    /**
     * @return mixed
     */
    public function getAttributeId()
    {
        return $this->attributeId;
    }

    /**
     * @param mixed $attributeId
     */
    public function setAttributeId($attributeId)
    {
        $this->attributeId = $attributeId;
    }

    /**
     * @return ObjectID
     */
    public function getCrawlerJobResultID()
    {
        return $this->crawlerJobResultID;
    }

    /**
     * @param ObjectID $crawlerID
     */
    public function setCrawlerJobResultID(ObjectID $crawlerID)
    {
        $this->crawlerJobResultID = $crawlerID;
    }

    /**
     * @return int
     */
    public function getCrawlJobID(): int
    {
        return $this->crawlJobID;
    }

    /**
     * @param int $crawlJobID
     * @throws Exception
     */
    public function setCrawlJobID(int $crawlJobID)
    {
        if($crawlJobID === null){
            throw new Exception('crawlJobID selected is NULL.');
        }
        if(!is_int($crawlJobID)){
            throw new Exception('crawlJobID Must be int.');
        }
        $this->crawlJobID = $crawlJobID;
    }
}