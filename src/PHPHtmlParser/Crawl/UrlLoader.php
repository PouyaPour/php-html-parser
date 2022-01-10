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
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\TransferStats;
use PHPHtmlParser\Utility\Tools;

class UrlLoader
{

    private static $redirect = [];
    private static $cookieJar=null;
    private static $url='';
    private static $fabricUrl='';
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
     * @param Client $client
     * @param string $url
     * @return mixed
     * @throws Exception
     * @throws GuzzleException
     */
    public static function getHttpWithClint(client $client, string $url)
    {
        $GLOBALS['furtherInformation'] = array();
        self::setUrl($url);
        try {
            $counter = 0;
            do {
                self::setFabricUrl(self::getUrl());
                $crawler = $client->request('GET', self::getUrl(), static::makeStandardHeader());

                if (array_key_exists('Location', $crawler->getHeaders())) {
                    self::setUrl(Url::completeEditUrl(self::getUrl(), self::getFabricUrl()));
                } else {
                    break;
                }
                self::addRedirect(self::createRedirectInfo(
                        $crawler->getHeaders(), $crawler->getStatusCode(), self::getUrl(), self::getFabricUrl()
                ));

                self::setCookie($crawler->getHeaders());
                $counter++;
            } while ($crawler->getStatusCode() >= 300 AND $crawler->getStatusCode() < 400 AND $counter < 10);
            return self::createSuccessResult($crawler);
        } catch (RequestException  $e) {
            return self::createFailureResult($e);
        }catch (Exception  $e) {
            return self::createFailureResult($e);
        }
    }
    private static function makeStandardHeader()
    {
        return [
                'allow_redirects' => false,
                'stream' => false,
                'read_timeout' => 10,
                'cookies' => static::getCookieJar(),
                'on_stats' =>
                        function (TransferStats $stats) {
                            $GLOBALS['furtherInformation'] = $stats->getHandlerStats();
                        }
        ];
    }

    private static function getCookieJar()
    {
        if(self::$cookieJar==null) {
            self::$cookieJar =  new CookieJar();
        }
        return self::$cookieJar;
    }

    /**
     * @return array
     */
    public static function getRedirect(): array
    {
        return self::$redirect;
    }

    /**
     * @param array $redirect
     */
    public static function setRedirect(array $redirect): void
    {
        self::$redirect = $redirect;
    }

    public function addRedirect(array $data)
    {
        self::$redirect [] = $data;
    }

    private static function createRedirectInfo(array $getHeaders, int $getStatusCode, string $url, string $originalUrl)
    {
        return [
                'headers' => $getHeaders,
                'statusCode' => $getStatusCode,
                'url' => $url,
                'originalUrl' => $originalUrl
        ];
    }

    private static function setCookie(array $getHeaders)
    {
        if (array_key_exists('Set-Cookie', $getHeaders)) {
            foreach ($getHeaders['Set-Cookie'] as $cookie) {
                if(is_array($cookie)) {
                    self::getCookieJar()->setCookie(new SetCookie( $cookie));
                }
            }
        }
    }

    private static function getHtml(\Psr\Http\Message\ResponseInterface $crawler)
    {
        $html = $crawler->getBody()->getContents();

        if(strlen($html)> 1024 * 1024){
            throw  new Exception(
                    'over size 1024 * 1024'
            );
        }

        if (empty($html))
            $html = '<html></html>';

        return $html;
    }

    private static function createSuccessResult(\Psr\Http\Message\ResponseInterface $crawler)
    {
        $data = [];
        $data['error'] = false;
        $data['history'] = '';
        $data['furtherInformation'] = $GLOBALS['furtherInformation'];
        $data['status'] = $crawler->getStatusCode();
        $data['headers'] = $crawler->getHeaders();
        $data['redirect'] = self::getRedirect();
        $data['html'] = self::getHtml($crawler);
        $data['text'] = '';
        if ($crawler->getBody()->getSize() === null) {
            $data['size'] = 0;
        }else{
            $data['size'] = $crawler->getBody()->getSize();
        }
        return $data;
    }

    private static function createFailureResult(Exception $e)
    {
        $data = [];
        $data['error'] = self::createErrorArrayResult($e);
        $data['history'] = '';
        $data['furtherInformation'] = array();
        if (empty($e->getResponse())) {
            $data['status'] = 700;
            $data['headers'] = [];
            self::setRedirect(self::createRedirectInfo([], 700, self::getUrl(), self::getFabricUrl()));
        } else {
            $data['status'] = $e->getResponse()->getStatusCode();
            $data['headers'] = $e->getResponse()->getHeaders();
            self::setRedirect(self::createRedirectInfo($e->getResponse()->getHeaders(),
                    getResponse()->getStatusCode(),
                    self::getUrl(),
                    self::getFabricUrl()
            ));
        }
        $data['html'] = '<html></html>';
        $data['text'] = '';
        $data['size'] = 0;
        $data['redirect'] = self::getRedirect();
        return $data;
    }

    private static function createErrorArrayResult(Exception $e)
    {
        $error = [];
        $error['errorMessage'] = $e->getMessage();
        $error['errorCode'] = $e->getCode();
        $error['errorLine'] = $e->getLine();
        $error['errorFile'] = $e->getFile();
        return $error;
    }

    /**
     * @return string
     */
    public static function getUrl(): string
    {
        return self::$url;
    }

    /**
     * @param string $url
     */
    public static function setUrl(string $url): void
    {
        self::$url = $url;
    }

    /**
     * @return string
     */
    public static function getFabricUrl(): string
    {
        return self::$fabricUrl;
    }

    /**
     * @param string $fabricUrl
     */
    public static function setFabricUrl(string $fabricUrl): void
    {
        self::$fabricUrl = $fabricUrl;
    }
}