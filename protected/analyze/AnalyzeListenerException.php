<?php

/**
 * listener to deal with app usage statistics, just like open times and seconds spent
 * @author york
 */
class AnalyzeListenerException implements IAnalyzeListener{
	
	public function execute($analyze){
		if(!$analyze->device->version_id) return;
		if(!$analyze->json->data->exceptions) return;
		
		foreach($analyze->json->data->exceptions as $exception){
			$sql = "insert into {exceptions} (md5, version_id, `count`, updated_at, exception)
			values (:md5, :version_id, 1, :updated_at, :exception)
			on duplicate key update `count`=`count`+1, updated_at=:updated_at";
			$params = array();
			$params[':updated_at'] = date("Y-m-d H:i:s", $analyze->server_timestamp);
			$params[':exception'] = $exception->exception;
			if($exception->md5) $params[':md5'] = $exception->md5;
			else $params[':md5'] = md5($exception->exception);
			$params[':version_id'] = $analyze->device->version_id;
			TCClick::app()->db->execute($sql, $params);
		}
		
		// 增加出错次数的计数器
		$date = date("Y-m-d", $analyze->server_timestamp);
		$count = count($analyze->json->data->exceptions);
		$sql = "insert into {counter_exceptions} (`date`, `count`, `version_id`) 
		values ('$date', $count, 0) on duplicate key
		update `count`=`count`+$count";
		TCClick::app()->db->execute($sql);
		$sql = "insert into {counter_exceptions} (`date`, `count`, `version_id`) 
		values ('$date', $count, {$analyze->device->version_id}) on duplicate key
		update `count`=`count`+$count";
		TCClick::app()->db->execute($sql);
	}
}

