<?php
/**
 * Created by PhpStorm.
 * User: trty
 * Date: 26/05/2017
 * Time: 03:26 AM
 */

namespace PHPHtmlParser\Utility;

use Stringy\StaticStringy;

class Tools
{
    public static function countSubStr($string, $subString)
    {
        return StaticStringy::countSubstr($string,$subString);
    }

    /**
     * @param $url
     * @return string
     */
    public static function getHostName($url)
    {
        $url_parser=parse_url($url);
        $url_end='';
        if(array_key_exists('scheme', $url_parser) && array_key_exists('host', $url_parser)) {
            $url_end= $url_parser['scheme'] . '://' . $url_parser['host'];
        }else{
            $url_end = $url;
        }
        $url_end=rtrim($url_end, "/");
        return $url_end;
    }
}

