# Fast and Convenient PHP WebPage and Html Parser
PHPHtmlParser is a fast, convenient, and simple page parser which allows you to use any data of page, such as header, redirects, code status, variant meta tag, H tags, image attributes, links and so on. The goal is to assist you that parse different pages without any problem and use categorized data in your programs.

Let's get started ...         

## Installing PHP Html Parser

This package can be found on packagist and is best loaded using composer. We support php 5.0, 7.0.
The recommended way to install Guzzle is through [Composer](https://getcomposer.org/).

**composer.phar**
```
 "require": {
    "seosazi/php-html-parser": "^1.0"
}
```
or
```
 composer require seosazi/php-html-parser
```

## Usage

Using this class is simple and it is enough to put your page address to get different information of it. The following example is a very simplistic usage of the package.
```php
// Assuming you installed from Composer:
use PHPHtmlParser\Crawl\WebPageProcessor;
require_once '/vendor/autoload.php';
$data = WebPageProcessor::onePageProcessed('https://www.your-website.com');
var_dump($data->getH1Tag());//get array of H1 tag
var_dump($data->getExternalLinks());//get array of all external link (Url class)
var_dump($data->getImageAlt());//get array of all image address and alt tag of them
var_dump($data->getHeader());//get array of header parameters
```

## Full list of WebPageProcessor parameters
| Parameter | Description |
| ------ | ------ |
| getUrlInfo | Get information about your information, for example home address, last url after possible redirects and first url |
| getHtml | Get page html |
| getUrl | Get the url that you entered |
| getStatus | Get the status code of your address, if the code is more than 600 it means that this address is inaccessible |
| getHeader | Get an array of all header parameters, including Connection, Cache-Control, Set-Cookie, Vary, Content-Type, Transfer-Encoding, Date, Server, Alt-Svc, x-encoded-content-encoding, Keep-Alive, P3P, and so on. |
| getUrlBeforeRedirect | Get the url before redirecting |
| getRedirect | Get an array of all possible redirects, including header parameter, status code, url |
| getBaseTag | Get base tag |
| getHeaderContentType | Get url content type |
| getBodyText | Get text in body tag |
| getTitle | Get page title |
| getKeywords | Get the meta keywords tag |
| getDescription | Get the meta description tag|
| getCanonical | Get a canonical tag | 
| getPTag | Get an array of all p tag |
| getSpanTag | Get an array of all span tag | 
| getH1Tag | Get an array of all H1 tag | 
| getH2Tag | Get an array of all H2 tag | 
| getH3Tag | Get an array of all H3 tag | 
| getH4Tag | Get an array of all H4 tag | 
| getH5Tag | Get an array of all H5 tag | 
| getH6Tag | Get an array of all H6 tag | 
| getLiElement | Get an array of all Li element | 
| getImageAlt | Get an array of all image address with alt tag | 
| getAnchorTag | Get an array of all anchor text | 
| getLinks | Get an array of all links. for better use of link, the Url class was created for easy use with the url. more information | 
| getInternalLinks | Get an array of all internal links. for better use of link, the Url class was created for easy use with the url. more information | 
| getExternalLinks | Get an array of all external links. for better use of link, the Url class was created for easy use with the url. more information |
| getEntireData | Get an array of all data 


