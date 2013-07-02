<?php
	
	registerService('facebook');

	function facebook_init($data, $apiResponseCode) {

		include('configs/facebook_conf.php');

		list($typeParam, $typeParamStr) = setQueryVars('type');
		list($limitParam, $limitParamStr) = setQueryVars('limit');
		list($beforeParam, $beforeParamStr) = setQueryVars('before');
		list($afterParam, $afterParamStr) = setQueryVars('after');

	    $app_id = $conf['appId'];
	    $app_secret = $conf['appSecret'];
	    $profile_id = $conf['profileId'];
	    $authToken = '';

		$curlAuth = array(
			CURLOPT_URL => "https://graph.facebook.com/oauth/access_token?grant_type=client_credentials&client_id={$app_id}&client_secret={$app_secret}",
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 20
		);
		$authToken = getUrl($curlAuth);

		$curlData = array(
			CURLOPT_URL => "https://graph.facebook.com/{$profile_id}/{$typeParam}?{$authToken}{$limitParamStr}{$afterParamStr}{$beforeParamStr}",
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_TIMEOUT => 20
		);

		list($response, $gotErrors) = getUrl($curlData);

	    $data['code'] = $gotErrors == true ? 2 : 1;
	    $data['status'] = $apiResponseCode[$data['code']]['HTTP Response'];
	    $data['data'] = $response;

	    $returnData = array();
	    $returnData['status'] = $data['status'];
	    $returnData['data'] = $data;

	    return $returnData;
	}

?>