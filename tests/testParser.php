<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once('../domainParser.php');

echo '<pre>';

$a = new \WhitePhoenixMedia\Utilities\DomainParser\domainParser('www.example.co.uk');
print_r($a);

$a = new \WhitePhoenixMedia\Utilities\DomainParser\domainParser('www.example.com');
print_r($a);

$a = new \WhitePhoenixMedia\Utilities\DomainParser\domainParser('example.co.uk');
print_r($a);

$a = new \WhitePhoenixMedia\Utilities\DomainParser\domainParser('example.com');
print_r($a);

echo '</pre>';
