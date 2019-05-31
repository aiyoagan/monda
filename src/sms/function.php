<?php
/**
 *
 * Created by PhpStorm.
 * User: mofuhao <mofh@pvc123.com>
 * Date: 19-5-29
 * Time: 上午11:28
 */
if (!function_exists('post')) {
    function post($url, $params, $headers = null, $setting = null) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        if (stripos($url, 'https://') !== FALSE) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        if (is_array($params)) {
            $params = http_build_query($params);
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        if (is_array($headers)) {
            $_headers = array();
            foreach ($headers as $key => $value) {
                $_headers[] = "{$key}:$value";
            }
            curl_setopt($curl, CURLOPT_HTTPHEADER, $_headers);
        }

        //check and apply the setting
        if ($setting != null) {
            foreach ($setting as $key => $val) {
                curl_setopt($curl, $key, $val);
            }
        }

        $ret = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);
        if (false === $ret) {
            return ("cURLException:" . curl_error($curl));
        }
        if (intval($info['http_code']) == 200) {
            return $ret;
        } else {
            return ("cURLReturnNot200Exception:" . $ret);
        }
    }
}

if (!function_exists('ajaxResult')) {
    function ajaxResult($code, $message = '', $data = []) {
        return json_encode(array('code' => $code, 'message' => $message, 'data' => $data));
    }
}

if (!function_exists('is_json')) {
    function is_json($string) {
        if ($string == "") return false;
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}