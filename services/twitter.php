<?php

	registerService('twitter');

	function twitter_init($data, $apiResponseCode) {

		include('configs/twitter_conf.php');

		$baseUrl = $conf['base_url'];
		$args = array(
			'screen_name' => $conf['screen_name'],
			'count' => $conf['count']
		);

		$type = $_GET['type'];
		if ($type == 'timeline') {
			$resource = 'statuses/user_timeline.json';
		} else if ($type == 'user') {
			$resource = 'users/show.json';
		} else if ($type == 'media') {
			$resource = 'statuses/user_timeline.json';
			$args = $args + array('include_entities' => true);
		} else if ($type == 'hashtags') {
			$resource = 'statuses/user_timeline.json';
		}

		$urlParams = getUrlParams('array', $args);
		$fullUrl = $baseUrl . $resource . '?' . $urlParams;
		$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;

		$oauth = array(
			'oauth_consumer_key' => $conf['consumer_key'],
			'oauth_nonce' => time(),
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_token' => $conf['oauth_access_token'],
			'oauth_timestamp' => time(),
			'oauth_version' => '1.0'
		);

		$composite_request = _BaseString($baseUrl . $resource, 'GET', array_merge($oauth, $args));
		$composite_key = rawurlencode($conf['consumer_secret']).'&'.rawurlencode($conf['oauth_access_token_secret']);
		$oauth_signature = base64_encode(hash_hmac('sha1', $composite_request, $composite_key, true));
		$oauth['oauth_signature'] = $oauth_signature;

		$options = array(
			CURLOPT_HTTPHEADER => array(_AuthorizationHeader($oauth),'Expect:'),
			CURLOPT_HEADER => false,
			CURLOPT_URL => $fullUrl,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false
		);

		list($response, $gotErrors) = getUrl($options);
		
		$response = json_decode($response);
		if ($type != 'user'){
			foreach ($response as $key) {
				unset($key->user);  
			}
		}

		list($pagerData, $offset) = pagination($response, $limit);

		if ($type == 'timeline') {
				if (is_string($response)) {
					$response = json_decode($data, true);
				}
				$transformedData[] = array_slice($response, $offset, $limit);
				array_push($transformedData, $pagerData);

		} else if ($type == 'user') {

			$transformedData = $response;

		} else if ($type == 'media') {
			$media = array();
			foreach ($response as $value) {
				// preg_match_all('/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/', $value->text, $media);
				// TBD - I don't believe this is correct. Media doesn't seem to contain all images. Need to investigate.
				if (property_exists($value->entities, 'media')) {
					array_push($media, $value->entities->media);
				}
			}
			$transformedData = $media;

		} else if ($type == 'hashtags') {
			// TBD - This will be used to something like return posts with certain hashtags. For now it just returns a list of hashtags.
			$hashTags = array();
			foreach ($response as $value) {
				if ($value->entities->hashtags) {
					array_push($hashTags, $value->entities->hashtags[0]->text);
				}
			}
			$transformedData = $hashTags;
		}

	    $data['code'] = $gotErrors == true ? 2 : 1;
	    $data['status'] = $apiResponseCode[$data['code']]['HTTP Response'];
	    $data['data'] = $transformedData;

	    $returnData = array();
	    $returnData['status'] = $data['status'];
	    $returnData['data'] = $data;


	    return $returnData;

	}

?>