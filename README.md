# Fast and Convenient PHP Page Parser
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
require_once '/vendor/autoload.php';
$data = WebPageProcessor::onePageProcessed('https://www.your-website.com');
var_dump($data->getH1Tag());
var_dump($data->getExternalLinks());
var_dump($data->getImageAlt());
var_dump($data->getHeader());
```