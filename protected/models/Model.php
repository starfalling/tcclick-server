<?php

class Model{
	private static $all_models = null;
	
	/**
	 * get all the models from databases
	 * @return array associated array, key is the model name and value is the model id
	 */
	public static function all(){
		if(self::$all_models === null){
			self::reload();
		}
		return self::$all_models;
	}
	
	private static function reload(){
		self::$all_models = array();
		$sql = "select * from {models}";
		$stmt = TCClick::app()->db->query($sql);
		while(true){
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if(!$row) break;
			self::$all_models[$row['brand']."::".$row['model']] = $row['id'];
		}
	}
	/**
	 * add a model to database by name
	 * @param string $model
	 */
	public static function add($brand, $model){
		$sql = "insert ignore into {models} (brand, model) values (:brand, :model)";
		if(TCClick::app()->db->execute($sql, array(":brand"=>$brand, ":model"=>$model))){
			self::reload();
		}
	}
	
	/**
	 * @param string $brand
	 * @param string $model
	 */
	public static function idFor($brand, $model){
		$all_models = self::all();
		$key = $brand . "::" . $model;
		if(!$all_models[$key]){
			self::add($brand, $model);
			$all_models = self::all();
		}
		return $all_models[$key];
	}
	
	/**
	 * @return string $model
	 */
	public static function nameof($id){
	  foreach (self::all() as $name=>$model_id){
	    if($model_id == $id) return  $name;
	  }
	}
	
	public static function readableNameof($id){
		static $readable_names = array(
				"Apple::iPad1,1"=>"iPad1", "Apple::iPad1,2"=>"iPad1", "Apple::iPad1,3"=>"iPad1", "Apple::iPad1,4"=>"iPad1",
				"Apple::iPad2,1"=>"iPad2", "Apple::iPad2,2"=>"iPad2", "Apple::iPad2,3"=>"iPad2", "Apple::iPad2,4"=>"iPad2",
				"Apple::iPad3,1"=>"iPad3", "Apple::iPad3,2"=>"iPad3", "Apple::iPad3,3"=>"iPad3",
				"Apple::x86_64"=>"模拟器",
		);
		$name = self::nameof($id);
		if ($readable_names[$name]) return $readable_names[$name];
		return $name;
	}
}

