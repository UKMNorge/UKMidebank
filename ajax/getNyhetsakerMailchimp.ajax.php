<?php
use UKMNorge\OAuth2\HandleAPICall;
require_once('UKMconfig.inc.php');

$handleCall = new HandleAPICall([], [], ['GET', 'POST'], false);

$retArr = [];

$allCampaigns = fetchMailchimp('campaigns?sort_field=send_time&sort_dir=DESC&status=sent&count=15');
foreach($allCampaigns->campaigns as $camp) {
    $item['send_time'] = $camp->send_time;
    $item['link'] = $camp->long_archive_url;
    $item['name'] = fetchMailchimp('campaigns/' . $camp->id)->settings->title;
    $retArr[] = $item;
}

$handleCall->sendToClient($retArr);


function fetchMailchimp($url) {
    $api_key = MAILCHIMP_API_KEY_2024;
    $endpoint = 'https://us1.api.mailchimp.com/3.0/' . $url;

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $api_key,
        ),
    ));

    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);


    header('Access-Control-Allow-Origin: *'); // Replace with your actual domain
    header('Content-Type: application/json');

    return json_decode($response);
}