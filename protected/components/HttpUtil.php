<?php

class HttpUtil {
  public static function curl_get($url, $header=array(), $referer=NULL){
    preg_match('|://([^/:]*)|', $url, $matches);
    if($matches) $host = $matches[1];
    else return;
    if(!$header){
	    $header[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
	    $header[] = "Accept-Charset: GB2312,utf-8;q=0.7,*;q=0.7";
	    $header[] = "Accept-Encoding: gzip,deflate";
	    $header[] = "User-Agent: Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; zh-CN; rv:1.9.2) Gecko/20100115 Firefox/3.6";
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    if($referer) curl_setopt($ch, CURLOPT_REFERER, $referer);
    $r = curl_exec($ch);
    curl_close($ch);
    return $r;
  }
  
  public static function curl_post($url, $data=array(), $header=array(), $referer=NULL){
    preg_match('|://([^/:]*)|', $url, $matches);
    if($matches) $host = $matches[1];
    else return;
    $data = (is_array($data)) ? http_build_query($data) : $data;
    if(!$header){
	    $header[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
	    $header[] = "Accept-Charset: GB2312,utf-8;q=0.7,*;q=0.7";
	    $header[] = "Accept-Encoding: gzip,deflate";
	    $header[] = "User-Agent: Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; zh-CN; rv:1.9.2) Gecko/20100115 Firefox/3.6";
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    if($referer) curl_setopt($ch, CURLOPT_REFERER, $referer);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $r = curl_exec($ch);
    curl_close($ch);
    return $r;
  }
}
