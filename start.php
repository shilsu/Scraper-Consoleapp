<?php
require 'mainScraper.php';
/**
 * 
 * @var unknown
 */
$url = "http://hiring-tests.s3-website-eu-west-1.amazonaws.com/2015_Developer_Scrape/5_products.html";

$dataHandler = new mainScraper($url);
/**
 * 
 * @var returned json array
 */
$jsonData=$dataHandler->getData();

print_r($jsonData);
