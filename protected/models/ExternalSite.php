<?php


class ExternalSite{
	public $id;
	public $code;
	public $user_id;
	public $name;
	public $url;
	public $created_at;
	public $is_admin;
	
	/**
	 * @return User
	 */
	public function getUser(){
		return User::findById($this->user_id);
	}
	
	/**
	 * @return array
	 */
	public static function all(){
		$result = array();
		$sql = "select * from {external_sites}";
		$stmt = TCClick::app()->db->query($sql);
		while(($row=$stmt->fetch(PDO::FETCH_ASSOC)) != null){
			$m = new self();
			$m->initWithDbRow($row);
			$result[] = $m;
		}
		return $result;
	}
	
	/**
	 * @return array
	 */
	public static function allForCurrentUser(){
		$result = array();
		$sql = "select * from {external_sites} where user_id=:user_id";
		$stmt = TCClick::app()->db->query($sql, array(':user_id'=>User::current()->id));
		while(($row=$stmt->fetch(PDO::FETCH_ASSOC)) != null){
			$m = new self();
			$m->initWithDbRow($row);
			$result[] = $m;
		}
		return $result;
	}
	
	/**
	 * @param integer $id
	 * @return ExternalSite
	 */
	public static function findById($id){
		$sql = "select * from {external_sites} where id=:id";
		$stmt = TCClick::app()->db->query($sql, array(':id'=>$id));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if($row){
			$m = new self();
			$m->initWithDbRow($row);
			return $m;
		}
	}
	
	public function delete(){
		$sql = "delete from {external_sites} where id=:id";
		TCClick::app()->db->execute($sql, array(':id'=>$this->id));
	}
	
	private function initWithDbRow($row){
		$this->id = intval($row['id']);
		$this->code = $row['code'];
		$this->user_id = intval($row['user_id']);
		$this->name = $row['name'];
		$this->url = $row['url'];
		$this->created_at = $row['created_at'];
		$this->is_admin = $row['is_admin'] == 1;
	}
	
	public function save(){
		if(!$this->code && !$this->id) return;
		if($this->id){
			$this->update();
		}else{
			$this->insert();
		}
	}
	
	private function update(){
		$sql = "update {external_sites} set code=:code, user_id=:user_id, name=:name, url=:url, is_admin=:is_admin
				where id=:id";
		$params = array(':code'=>$this->code, ':user_id'=>$this->user_id, ':name'=>$this->name, ':url'=>$this->url,
				':is_admin'=>$this->is_admin, ':id'=>$this->id);
		TCClick::app()->db->execute($sql, $params);
	}
	
	private function insert(){
		$sql = "insert into {external_sites} (code, user_id, name, url, is_admin) 
				values (:code, :user_id, :name, :url, :is_admin)
				on duplicate key update id=last_insert_id(id)";
		$params = array(':code'=>$this->code, ':user_id'=>$this->user_id, ':name'=>$this->name, ':url'=>$this->url,
				':is_admin'=>$this->is_admin);
		TCClick::app()->db->execute($sql, $params, $error);
		$this->id = TCClick::app()->db->lastInsertId();
	}
}

