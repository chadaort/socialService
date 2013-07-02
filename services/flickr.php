<?php
	
	registerService('flickr');

	function flickr_init($data, $apiResponseCode) {

		require_once('libs/phpFlickr-3.1/phpFlickr.php');
		include('configs/flickr_conf.php');

	  	$key = $conf['key'];
		$phpFlickrObj = new phpFlickr($key);


		$page = isset($_GET['page']) ? $_GET['page'] : 1;
		$userName = isset($_GET['userName']) ? $_GET['userName'] : NULL;
		$itemsTotal = isset($_GET['itemsTotal']) ? $_GET['itemsTotal'] : NULL;
		$photoset = isset($_GET['photoset']) ? $_GET['photoset'] : NULL;
		$getImg = isset($_GET['getImg']) ? $_GET['getImg'] : NULL;
		$imgSize = isset($_GET['imgSize']) ? $_GET['imgSize'] : NULL;
		$getImgSizes = isset($_GET['getImgSizes']) ? $_GET['getImgSizes'] : NULL;

		$userInfo = $phpFlickrObj->people_findByUsername('envato');
		$userId = $userInfo['id'];

		if ($getImg) {
			$ImgSizes = $phpFlickrObj->photos_getSizes($getImg);
			$itemKey = arraySearch2d($imgSize, $ImgSizes);

			$img = ($ImgSizes[$itemKey]);
			$imgDetails = $phpFlickrObj->photos_getInfo($getImg);

			$response = array_merge($img, $imgDetails['photo']);

		} else if ($getImgSizes) {
			$response = $phpFlickrObj->photos_getSizes($getImgSizes);
		}else if ($photoset) {
			$response = $phpFlickrObj->photosets_getPhotos($photoset, NULL, NULL, $itemsTotal, $page);
		} else if ($userName) {
			$response = $phpFlickrObj->people_getPublicPhotos($userId, NULL, NULL, $itemsTotal, $page);
		} else {
			$response = '';
		}


		$data['code'] = 1;
	    $data['status'] = $apiResponseCode[$data['code']]['HTTP Response'];
	    $data['data'] = $response;

	    $returnData = array();
	    $returnData['status'] = $data['status'];
	    $returnData['data'] = $data;

	    return $returnData;

	}

?>