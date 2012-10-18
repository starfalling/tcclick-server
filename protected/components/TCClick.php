<?php
include "Application.php";

class TCClick{

	public static function app(){
		static $app = null;
		if(!$app){
			$app = new Application();
		}
		return $app;
	}
	
	public static function error($message){
		if(defined("TCCLICK_DEBUG") && TCCLICK_DEBUG){
			if(is_array($message)){
				$error_message = "{\n";
				foreach($message as $key=>$value){
					$error_message .= "\t\t{$key}\t\t=> {$value}\n";
				}
				$error_message .= "\t}";
				$message = $error_message;
			}
			$message = date("[Y-m-d H:i:s] ") . $message . "\n\n";
			if(defined('SAE_TMP_PATH')){ // SAE
				sae_debug($message);
			}else{
				$filepath = dirname(dirname(__FILE__)) . "/runtime/application.error.log";
				file_put_contents($filepath, $message, FILE_APPEND);
			}
		}
	}
	
	/**
	 * 计算单次或者日使用使用时长所处于的区间ID，周使用时长不采用该区间分布方式
	 * @param integer $seconds
	 * @return integer
	 */
	public static function secondsSpentIdFor($seconds){
		if($seconds <= 3) return 1;
		if($seconds <= 10) return 2;
		if($seconds <= 30) return 3;
		if($seconds <= 60) return 4;
		if($seconds <= 180) return 5;
		if($seconds <= 600) return 6;
		if($seconds <= 1800) return 7;
		return 8;
	}
	/**
	 * 计算周使用时长所处于的区间ID，单次、日使用时长不采用该区间分布方式
	 * @param integer $seconds
	 * @return integer
	 */
	public static function secondsSpentIdForWeekly($seconds){
		if($seconds <= 30) return 1;
		if($seconds <= 60) return 2;
		if($seconds <= 180) return 3;
		if($seconds <= 600) return 4;
		if($seconds <= 1800) return 5;
		if($seconds <= 3600) return 6;
		if($seconds <= 5400) return 7;
		if($seconds <= 7200) return 8;
		return 9;
	}
	
	/**
	 * 单次或者日使用时长区间的名称描述
	 * @param integer $id
	 * @return string
	 */
	public static function nameForSecondsSpentId($id){
		return self::$all_seconds_spent_names[$id-1];
	}
	
	public static $all_seconds_spent_names = array(
			"1-3秒", "3-10秒", "10-30秒", "30-60秒", "1-3分钟", "3-10分钟", "10-30分钟", "30分钟以上"
	);
	
	/**
	 * 周使用时长区间的名称描述
	 * @param integer $id
	 * @return string
	 */
	public static function nameForSecondsSpentIdWeekly($id){
		return self::$all_seconds_spent_names_weekly[$id];
	}
	
	public static $all_seconds_spent_names_weekly = array(
			"1-30秒", "30-60秒", "1-3分钟", "3-10分钟", "10-30分钟", "30-60分钟",
			"60-90分钟", "90-120分钟", "120分钟以上"
	);
}

