<?php
/**
 * Created by PhpStorm.
 * User: trty
 * Date: 26/05/2017
 * Time: 03:26 AM
 */

namespace Seosazi\Utility;


use MongoDB\Model\BSONDocument;
use Stringy\StaticStringy;

class NeedsTools
{
    public static function searchInMultidimensionalArray($array, $column, $value, bool $isUrl=false){
        if ($isUrl) {
            foreach ($array as $key => $product) {
                if (strcmp(trim(urldecode($product[$column]),'/'), trim(urldecode($value), '/')) === 0)
                    return $key;
                #if ( $product[$column] === $value )

            }
        }else {
            foreach ($array as $key => $product) {
                if (strcmp($product[$column], $value) === 0)
                    return $key;
                #if ( $product[$column] === $value )

            }
        }
        return false;
    }

    /**
     *  $dir = SORT_ASC or SORT_DESC
     * @param $arr
     * @param $col
     * @param int $dir
     */
    public static function arraySortByColumn(&$arr, $col, $dir = SORT_DESC ) {
        $sort_col = array();

        foreach ($arr as $key=> $row) {
            $sort_col[$key] = $row[$col];
        }

        array_multisort($sort_col, $dir, $arr);
    }

    public static function searchInArray($array, $value)
    {
        foreach($array as $key => $product)
        {
            if ( $product === $value )
                return $key;
        }
        return false;
    }

    public static function lengthStr($string)
    {
        return StaticStringy::length($string);
    }

    public static function countSubStr($string, $subString)
    {
        return StaticStringy::countSubstr($string,$subString);
    }

    public static function stringSearch($string, $value)
    {
        return StaticStringy::contains($string, $value);
    }

    public static function stringArraySearch($string, $arrayValue)
    {
        return StaticStringy::containsAny($string, $arrayValue);
    }

    public static function checkValidateUrl($url)
    {
        $url = parse_url($url);
        if (!isset($url["host"])) return false;
        return true;
        /*if (!filter_var($url, FILTER_VALIDATE_URL) === false) {
            return true;
        }else{
            return false;
        }*/
    }
    public static function isPartUppercase($string) {
        if(preg_match("/[A-Z]/", $string)===0) {
            return false;
        }
        return true;
    }

    public static function checkUrlInSite($masterSite, $url)
    {
        $parseMasterSite = parse_url($masterSite);
        $parseUrl = parse_url($url);
        if (!empty($url) && !empty($masterSite) && (strpos($parseMasterSite['host'], $parseUrl['host']) !== false)) {
            return true;
        }
        return false;
    }

    public static function ContentTypeUrl($url)
    {
        return get_headers($url, 1)["Content-Type"];
    }

    public static function HttpResponseCode($headers)
    {
        return $headers[0];
    }

    public static function checkUrlIsPage($headers)
    {
        #$headers=get_headers($url, 1);
        if(array_key_exists('Content-Type', $headers)) {
            $ContentType = $headers["Content-Type"];
            if (is_array($ContentType)) {
                if (!NeedsTools::countSubStr($ContentType[0], 'text/html'))
                    return false;
            } else {
                if (!NeedsTools::countSubStr($headers["Content-Type"], 'text/html'))
                    return false;
            }
        }else{
            return false;
        }
        return $headers;
    }

    public static function correctUrl($url)
    {
        $url_parser=parse_url($url);
        $url_end='';
        if(array_key_exists('path', $url_parser) && array_key_exists('query', $url_parser)) {
            $url_end= $url_parser['scheme'] . '://' . $url_parser['host'] . $url_parser['path'] . '?' . $url_parser['query'];
        } elseif (array_key_exists('path', $url_parser) ) {
            $url_end= $url_parser['scheme'] . '://' . $url_parser['host'] . $url_parser['path'];
        } elseif (array_key_exists('query', $url_parser)) {
            $url_end= $url_parser['scheme'] . '://' . $url_parser['host']  . '?' . $url_parser['query'];
        } else {
            $url_end= $url_parser['scheme'] . '://' . $url_parser['host'];
        }
        $url_end=rtrim($url_end, "/");
        return $url_end;
    }

    public static function CheckUrlIsCorrectForCrawl($homeAddress, $url)
    {
        //check url is ok
        if(!self::checkValidateUrl($url))
            return false;

        
        //check inner link
        if(!self::checkUrlInSite($homeAddress, $url))
            return false;
        
        $headers=get_headers($url, 1);
        //check link is webPage not link css, image, ...
        if(!self::checkUrlIsPage($headers))
            return false;
        
        //check url is http code 200
        if(!NeedsTools::countSubStr(self::HttpResponseCode($headers), '200'))
            return false;

        return true;
    }

    /**
     * @param $BSONDocument BSONDocument
     * @return array
     */
    public static function BSONDocumentToArray($BSONDocument)
    {
        $arrayJson = $BSONDocument->getArrayCopy();
        $jsonEncode = json_encode($arrayJson);
        return json_decode($jsonEncode, true);
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

    /**
     * @param $filename
     * @return mixed|string
     */
    public static function get_mime_type($filename) {
        $idx = explode( '.', $filename );
        $count_explode = count($idx);
        $idx = strtolower($idx[$count_explode-1]);

        $mimet = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'docx' => 'application/msword',
            'xlsx' => 'application/vnd.ms-excel',
            'pptx' => 'application/vnd.ms-powerpoint',


            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        if (isset( $mimet[$idx] )) {
            return $mimet[$idx];
        } else {
            return 'webpage';
        }
    }
}

