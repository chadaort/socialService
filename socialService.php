<?php

$registeredService = array();
$data = array();
$expTime = 1; // In minutes

include('functions.php');
include('services/flickr.php');
include('services/facebook.php');
include('services/twitter.php');

$apiResponseCode = array(
    0 => array('HTTP Response' => 400, 'Message' => 'Unknown Error'),
    1 => array('HTTP Response' => 200, 'Message' => 'Success'),
    2 => array('HTTP Response' => 404, 'Message' => 'Invalid Request'),
    3 => array('HTTP Response' => 400, 'Message' => 'Invalid Response Format')
);

list($methodParam, $methodParamStr) = setQueryVars('method');
$cachePath = 'cache/';
$cacheName = fileNameFriendly($_SERVER['QUERY_STRING']);

if (in_array($methodParam, $registeredService)) {

    if(checkCache($cacheName, $cachePath, $expTime)) {

        $response = getCache($cacheName, $cachePath);

    } else {

        $init = $methodParam . '_init';
        $serviceResponse = $init($data, $apiResponseCode);
        $responseStatus = $serviceResponse['status'];

        $data = $serviceResponse['data'];
        array_push($data, $init($data, $apiResponseCode));

        if ($responseStatus == 200) {
            setCache($cacheName, $cachePath, $data);
        }

        $response = $data;
    }

} else {

    $data['code'] = 2;
    $data['status'] = 404;
    $data['data'] = NULL;
    $response = $data;

}

response($response);
 
?>
            