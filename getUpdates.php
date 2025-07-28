<?php
$token = '8128275349:AAEUQhTFY5IQ2Ai93drGi4v-evsxHPfiN20'; 

$url = "https://api.telegram.org/bot{$token}/getUpdates";

$response = file_get_contents($url);
header('Content-Type: application/json');
echo $response;
