<?php


class ExternalCode{
	public $id;
	public $code;
	public $user_id;
	public $created_at;
	
	/**
	 * @return User
	 */
	public function getUser(){
		return User::findById($this->user_id);
	}
	
	
	/**
	 * @return ExternalCode
	 */
	public static function create(){
		$m = new self();
		$m->code = sha1(uniqid());
		$m->user_id = User::current()->id;
		$sql = "insert into {external_codes} (code, user_id) values
				('{$m->code}', {$m->user_id})
				on duplicate key update id=last_insert_id(id)";
		$result = TCClick::app()->db->execute($sql);
		$m->id = TCClick::app()->db->lastInsertId();
		return $m;
	}
	
	/**
	 * @return array
	 */
	public static function all(){
		$result = array();
		$sql = "select * from {external_codes}";
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
		$sql = "select * from {external_codes} where user_id=:user_id";
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
	 * @return ExternalCode
	 */
	public static function findById($id){
		$sql = "select * from {external_codes} where id=:id";
		$stmt = TCClick::app()->db->query($sql, array(':id'=>$id));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if($row){
			$m = new self();
			$m->initWithDbRow($row);
			return $m;
		}
	}
	
	public static function deleteById($id){
		$sql = "delete from {external_codes} where id=:id";
		TCClick::app()->db->execute($sql, array(':id'=>$id));
	}
	
	private function initWithDbRow($row){
		$this->id = intval($row['id']);
		$this->code = $row['code'];
		$this->user_id = intval($row['user_id']);
		$this->created_at = $row['created_at'];
	}
}

