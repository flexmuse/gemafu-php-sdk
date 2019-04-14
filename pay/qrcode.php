<?php
$text = $_GET['text']?$_GET['text']:"";
include_once 'phpqrcode.php';
QRcode::png($text);
exit;