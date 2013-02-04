<?php

class AnalyzeListenerEvent implements IAnalyzeListener{
	
	public function execute($analyze){
		static $event_names = array();
		static $event_ids = array();
		static $event_param_ids = array();
		if(empty($analyze->json->data->events)) return;
		
		$event_counts = array();
		foreach($analyze->json->data->events as $event_item){
			if(empty($event_item->version)) continue;
			if(empty($event_item->name)) continue;
			if(empty($event_item->param)) continue;
			if(empty($event_item->value)) continue;
			
			$name = $event_item->name;
			$param = $event_item->param;
			$value = $event_item->value;
			if(!isset($event_names[$name])) $event_names[$name] = EventName::idFor($name);
			if(!isset($event_names[$param])) $event_names[$param] = EventName::idFor($param);
			if(!isset($event_names[$value])) $event_names[$value] = EventName::idFor($value);
			$name_id = $event_names[$name];
			$param_name_id = $event_names[$param];
			$value_id = $event_names[$value];
			
			if(!isset($event_ids[$name_id])) $event_ids[$name_id] = Event::idFor($name_id);
			$event_id = $event_ids[$name_id];
			
			if(!isset($event_param_ids[$event_id])) $event_param_ids[$event_id] = array();
			if(!isset($event_param_ids[$event_id][$param_name_id])){
				$event_param = EventParam::createIfNotExists($event_id, $param_name_id);
				$event_param_ids[$event_id][$param_name_id] = $event_param->param_id;
			}
			$param_id = $event_param_ids[$event_id][$param_name_id];
			$version_id = Version::idFor($event_item->version);
			
			if(!isset($event_counts[$event_id])) $event_counts[$event_id] = array();
			if(!isset($event_counts[$event_id][$param_id])) $event_counts[$event_id][$param_id] = array();
			if(!isset($event_counts[$event_id][$param_id][$version_id])){
				$event_counts[$event_id][$param_id][$version_id] = array();
			}
			if(!isset($event_counts[$event_id][$param_id][$version_id][$value_id])){
				$event_counts[$event_id][$param_id][$version_id][$value_id] = array();
			}
			$date = date('Y-m-d', $event_item->created_at);
			$event_counts[$event_id][$param_id][$version_id][$value_id][$date] += 1;
		}
		
		$values_for_sql = array();
		foreach($event_counts as $event_id=>$param_data){
			foreach($param_data as $param_id=>$version_data){
				foreach($version_data as $version_id=>$value_data){
					foreach($value_data as $value_id=>$data){
						foreach($data as $date=>$count){
							$values_for_sql[] = "('$date', $event_id, $param_id, $version_id, $value_id, $count)";
// 							echo $date, "\t", $event_id, "\t", $param_id, "\t", $version_id, "\t", 
// 							$value_id, "\t", $count, "\n";
						}
					}
				}
			}
		}
		if(!empty($values_for_sql)){
			$sql = "insert into {counter_daily_events} (date, event_id, param_id, version_id, value_id, count)
			values ".join(',', $values_for_sql)."
			on duplicate key update count=count+values(count)";
			TCClick::app()->db->execute($sql);
		}
	}

}

