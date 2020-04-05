<?php
require __DIR__ .'/../vendor/autoload.php';
use Nesk\Puphpeteer\Puppeteer;
 
$targetUrl = 'https://ec.toranoana.shop/tora/ec/bok/pages/all/announce/schedule/2020/02/benefit/1/';
 
$puppeteer = new Puppeteer;
$browser = $puppeteer->launch();
$page = $browser->newPage();
$page->goto($targetUrl);
 
$page->screenshot(['path' => 'example.png']);
$browser->close();