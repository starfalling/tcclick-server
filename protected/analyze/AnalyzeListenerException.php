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
			
			// 执行md5计算，去除掉错误日志第一行进行计算
			$exception_for_md5 = substr($exception->exception, strpos($exception->exception, "\n"));
			if ($analyze->device->brand == "Apple"){ // ios 版本，错误日志的md5值进行特殊处理
				// 去除掉每一行最末位的 + 166 这种格式的部分，因为这部分导致记录的错误日志过多，使得错误收集失去价值
				$params[':md5'] = md5(preg_replace('| \\+ [0-9]+$|', '', $exception->exception));
			}else{
				$params[':md5'] = md5($exception_for_md5);
			}
			
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

