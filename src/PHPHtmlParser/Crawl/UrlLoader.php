<?php
/**
 * Created by PhpStorm.
 * User: trty
 * Date: 21/05/2017
 * Time: 09:26 PM
 */

namespace PHPHtmlParser\Crawl;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\TransferStats;
use PHPHtmlParser\Utility\Tools;

class UrlLoader
{
    /**
     * @param string $url
     * @return bool|Client
     * @throws Exception
     */
    public static function initialize(string $url)
    {
        try {
            return new Client([
                'base_uri' => $url,
                'verify'   => false,
                'headers' => [
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                    'Pragma' => 'no-cache',
                    'Accept-Encoding' => ['gzip', 'deflate', 'br'],
                    'Accept-Language' => 'en-US,en;q=0.5',
                    'Connection'	 => 'keep-alive',
                    'Upgrade-Insecure-Requests' => 1,
                    'Host' => Tools::getHostName($url),
                    'cookie' => true,
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:61.0) Gecko/20100101 Firefox/61.0',
                    'Cache-Control' => 'no-cache'
                ]
            ]);
        } catch (\Exception $e) {
            echo 'Caught exception: ' .  $e->getMessage();
        }
        return false;
    }

    /**
     * @param string $url
     * @return string
     * @throws Exception
     */
    private static function getPath(string $url)
    {
        $parse_url=parse_url($url);
        if (array_key_exists('path', $parse_url) and array_key_exists('query', $parse_url)){
            return $parse_url['path'] . '?' . $parse_url['query'];
        }else{
            if(array_key_exists('path', $parse_url))
                return $parse_url['path'];
            return '/';
        }
    }

    /**
     * @param string $url
     * @return bool|string
     * @throws Exception
     */
    private static function getHost(string $url)
    {
        $parse_url=parse_url($url);
        if (array_key_exists('scheme', $parse_url)){
            if(array_key_exists('host', $parse_url))
                return $parse_url['scheme'] . '://' . $parse_url['host'];
        }else{
            if(array_key_exists('host', $parse_url))
                return 'http://' . $parse_url['host'];
        }
        return false;
    }

    /**
     * @param Client $client
     * @param string $url
     * @return mixed
     * @throws Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getHttpWithClint(client $client, string $url)
    {
        $GLOBALS['furtherInformation'] = array();
        $cookieJar = new CookieJar();
        try {
            $redirect = [];
            $counter = 0;
            do {
                $originalUrl = $url;
                $crawler = $client->request('GET', $url, [
                    'allow_redirects' => false,
                    'stream' => false,
                    'read_timeout' => 10,
                    'cookies' => $cookieJar,
                    'on_stats' =>
                        function (TransferStats $stats) {
                            $GLOBALS['furtherInformation'] = $stats->getHandlerStats();
                        }
                ]);
                $dataR = [
                    'headers' => $crawler->getHeaders(),
                    'statusCode' => $crawler->getStatusCode(),
                    'url' => $url,
                    'originalUrl' => $originalUrl
                ];
                $redirect [] = $dataR;
                if (array_key_exists('Location', $crawler->getHeaders())) {
                    #dump('orginal location header =>     ');
                    #dump($crawler->getHeader('Location'));
                    $url = $crawler->getHeader('Location')[0];
                    #dump('before anything get location data=>     ' . $url);
                    $url = Url::completeEditUrl($url, $originalUrl);
                } else {
                    break;
                }
                if (array_key_exists('Set-Cookie', $crawler->getHeaders())) {
                    foreach ($crawler->getHeaders()['Set-Cookie'] as $cookie) {
                        if(is_array($cookie)) {
                            $cookieJar->setCookie(new SetCookie( $cookie));
                        }
                    }
                }
                $counter++;
            } while ($crawler->getStatusCode() >= 300 AND $crawler->getStatusCode() < 400 AND $counter < 10);
            $data = [];
            $data['error'] = false;
            $data['history'] = '';
            $data['furtherInformation'] = $GLOBALS['furtherInformation'];
            $data['status'] = $crawler->getStatusCode();
            $data['headers'] = $crawler->getHeaders();
            $data['redirect'] = $redirect;
            $html = '<html></html>';
            if (Url::isHtmlPage($data['headers'])) {
                $html = $crawler->getBody()->getContents();
            }
            if (empty($html))
                $html = '<html></html>';
            if(strlen($html)> 1024 * 1024){
                throw  new Exception(
                    'over size 1024 * 1024'
                );
            }
            $data['html'] = $html;
            $data['text'] = '';
            $data['size'] = $crawler->getBody()->getSize();
            if ($data['size'] === null) {
                $data['size'] = 0;
            }
            return $data;
        } catch (RequestException  $e) {
//            dump($e);
            $data = array();
            $error = array();
            $error['errorMessage'] = $e->getMessage();
            $error['errorCode'] = $e->getCode();
            $error['errorLine'] = $e->getLine();
            $error['errorFile'] = $e->getFile();
            $data['error'] = $error;
            $data['history'] = '';
            $data['furtherInformation'] = array();
            if (empty($e->getResponse())) {
                $data['status'] = 700;
                $data['headers'] = [];
                $data['status'] = 700;
                $data['headers'] = [];
                $dataR = [
                    'headers' => [],
                    'statusCode' => 700,
                    'url' => $url,
                    'originalUrl' => $originalUrl
                ];
                $redirect [] = $dataR;
            } else {
                $data['status'] = $e->getResponse()->getStatusCode();
                $data['headers'] = $e->getResponse()->getHeaders();
                $dataR = [
                    'headers' => $e->getResponse()->getHeaders(),
                    'statusCode' => $e->getResponse()->getStatusCode(),
                    'url' => $url,
                    'originalUrl' => $originalUrl
                ];
                $redirect [] = $dataR;

            }
            $data['html'] = '<html></html>';
            $data['text'] = '';
            $data['size'] = 0;
            $data['redirect'] = $redirect;
//            dump($redirect);
            return $data;
        }catch (Exception  $e) {
//            dump($e);
            $data = array();
            $error = array();
            $error['errorMessage'] = $e->getMessage();
            $error['errorCode'] = $e->getCode();
            $error['errorLine'] = $e->getLine();
            $error['errorFile'] = $e->getFile();
            $data['error'] = $error;
            $data['history'] = '';
            $data['furtherInformation'] = array();
            $data['status'] = 700;
            $data['headers'] = [];
            $data['status'] = 700;
            $data['headers'] = [];
            $dataR = [
                'headers' => [],
                'statusCode' => 700,
                'url' => $url,
                'originalUrl' => $originalUrl
            ];
            $redirect [] = $dataR;
            $data['html'] = '<html></html>';
            $data['text'] = '';
            $data['size'] = 0;
            $data['redirect'] = $redirect;
//            dump($redirect);
            return $data;
        }
    }

    /**
     * @param string $url
     * @return array
     * @throws Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function getHttpWithClientNoRedirect(string $url)
    {
        $GLOBALS['furtherInformation']=array();
        try {
            $client= new Client([
                'base_uri' => self::getHost($url),
                'verify'   => false
            ]);
            $crawler = $client->request('GET', self::getPath($url), [
                'allow_redirects' => false,
                'on_stats' =>
                    function (TransferStats $stats) {
                        $GLOBALS['furtherInformation'] = $stats->getHandlerStats();
                    }
            ]);
            $data=[];
            $data['error']=false;
            $data['history']='';
            $data['furtherInformation']=$GLOBALS['furtherInformation'];
            $data['status']=$crawler->getStatusCode();
            $data['headers']=$crawler->getHeaders();
            $html=$crawler->getBody()->getContents();
            if(empty($html))
                $html='<html></html>';
            $data['html']=$html;
            $data['text']='';
            return $data;
        }catch (\Exception $e){
            $data=array('');
            $error=array();
            $error['errorMessage']=$e->getMessage();
            $error['errorCode']=$e->getCode();
            $error['errorLine']=$e->getLine();
            $error['errorFile']=$e->getFile();
            $data['error']=$error;
            $data['history']='';
            $data['furtherInformation']=array();
            $data['status']=700;
            $data['headers']='';
            $data['html']='<html></html>';
            $data['text']='';
            return $data;
        }
    }
}