<?php

    function response($apiResponse){
        $httpResponseCode = array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found'
        );

        header('HTTP/1.1 ' . $apiResponse[0]['status'] . ' ' . $httpResponseCode[$apiResponse[0]['status']]);
        header('Content-Type: application/json; charset=utf-8');

        $json_response = json_encode($apiResponse, true);

        echo $json_response;
        exit;
    }


    function returnError($msg, $type = '', $code = '') {
        $response = array(
            'message' => $msg,
            'type' => $type,
            'code' => $code
        );
        $response = json_encode($response);
        exit($response);
    }


    function _BaseString($base_url, $method, $values) {
        $ret = array();
        ksort($values);
        foreach($values as $key=>$value)
        $ret[] = $key."=".rawurlencode($value);
        return $method."&".rawurlencode($base_url).'&'.rawurlencode(implode('&', $ret));
    }


    function _AuthorizationHeader($oauth) {
        $ret = 'Authorization: OAuth ';
        $values = array();
        
        foreach($oauth as $key=>$value) {
            $values[] = $key.'="'.rawurlencode($value).'"';
        }

        $ret .= implode(', ', $values);
        return $ret;
    }


    function arraySearch2d($searchVal, $arrayName) {
        for ($i = 0, $l = count($arrayName); $i < $l; ++$i) {
            if (in_array($searchVal, $arrayName[$i])) {
                return $i;
            }
        }
        return false;
    }


    function getQueryParamString($id) {
        if (isset($_GET[$id])) {
            $queryString = '&' . $id . '=' . $_GET[$id];
        } else {
            $queryString = '';
        }

        return $queryString;
    }


    function getUrlParams($returnType, $data = '') {
        $queryString = NULL;
        if ($returnType == 'url') {
            foreach($_GET as $name => $value) {
                $queryString .= $name . '=' . $value . '&';
            };

        } else if ($returnType == 'array') {
            foreach($data as $name => $value) {
                $queryString .= $name . '=' . $value . '&';
            };
        }
        return $queryString;
    }


    function setQueryVars($id) {
    
        if (isset($_GET[$id])) {
            $val = $_GET[$id];
            $queryStr = '&' . $id . '=' . $val;
        } else {
            $val = '';
            $queryStr = '';
        }

        return array (
            $val,
            $queryStr
        );
    }


    function getUrl($options){
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $feedData = curl_exec($ch);
        $curlErrorNum = curl_errno($ch);
        $curlError = curl_error($ch);
        curl_close($ch); 

        if ($curlErrorNum > 0) {
            $feedData = NULL;

            return array(
                $feedData,
                true
            );

        } else {
            
            return array(
                $feedData,
                false
            );
        }

        
    }


    function checkCache($filename, $cachePath, $expTime) {
        $fullPath = $cachePath . $filename . '.text';
        $fileTime = @filemtime($fullPath);
        $curTime = time();

        if (file_exists($fullPath) && ($curTime - $fileTime) / 60 < $expTime) {
            return true;
        }

        return false;
    }


    function setCache($filename, $cachePath, $obj) {
        $fullPath = $cachePath . $filename . '.text';
        $data = base64_encode(serialize($obj));
        file_put_contents($fullPath, $data);

    }


    function getCache($filename, $cachePath) {
        $fullPath = $cachePath . $filename . '.text';
        if (file_exists($fullPath)) {

            $data = file_get_contents($fullPath);

            $data = base64_decode($data);
            $data = unserialize($data);
            
            return $data;
        } 

        return false;
    }

    function fileNameFriendly($string) {
       $string = str_replace('', '-', $string); // Replaces all spaces with hyphens.
       return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }


    function returnJson($val) {
        // This needs to be tested and extended
        
        if (is_array($val)) {
            exit(json_encode($val));
        } else if (is_object($val)) {
            exit(json_encode($val));
        }
        exit($val);
    }


    function pagination($data = '', $limit = '') {
        $page = isset($_GET['page']) ? $_GET['page'] : 0;
        $totalResults = count($data);
        $totalPages = ceil($totalResults / $limit);
        $offset = $limit * ($page -1);

        return array(
            array(
                'totalResults' => $totalResults,
                'totalPages' => $totalPages
            ),
            $offset
        );

    }


    function registerService($service) {
        global $registeredService;
        array_push($registeredService, $service);
    }



?>