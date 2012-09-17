<?php

class DBConnection{
	protected $db_master;
	protected $db_slave;
	
	public function __construct() {
		$options = array(PDO::ATTR_PERSISTENT, MYSQL_PERSISTENT);
		
		$this->db_master = new PDO(MYSQL_DSN_MASTER, MYSQL_USER_MASTER, MYSQL_PASS_MASTER, $options);
		if(MYSQL_DSN_MASTER == MYSQL_DSN_SLAVE){
			$this->db_slave = $this->db_master;
		}else{
			$this->db_slave = new PDO(MYSQL_DSN_SLAVE, MYSQL_USER_SLAVE, MYSQL_PASS_SLAVE, $options);
		}
	}
	
	/**
	 * delegate for function PDO::lastInsertId
	 * @return string
	 */
	public function lastInsertId(){
		return $this->db_master->lastInsertId();
	}
	
	/**
	 * execute an insert or update sql statement on the master database
	 * @param string $sql
	 * @param array $params
	 * @return mixed row count affected by this sql, or false when error occured
	 */
	public function execute($sql, $params=null, &$errorInfo=null){
		$stmt = $this->db_master->prepare($this->updateTablePrefixForSql($sql));
		if($params && is_array($params)){
			foreach($params as $key=>&$value){
				$stmt->bindParam($key, $value);
			}
		}
		if(!$stmt->execute()){
			$errorInfo = $stmt->errorInfo();
			TCClick::error($errorInfo);
			return false;
		}
		return $stmt->rowCount();
	}
	
	/**
	 * query some rows on the slave database
	 * @param string $sql
	 * @param array $params
	 * @return PDOStatement
	 */
	public function query($sql, $params=null){
		$stmt = $this->db_slave->prepare($this->updateTablePrefixForSql($sql));
		if($params && is_array($params)){
			foreach($params as $key=>&$value){
				$stmt->bindParam($key, $value);
			}
		}
		if(!$stmt->execute()) TCClick::error($stmt->errorInfo());
		return $stmt;
	}
	
	private function updateTablePrefixForSql($sql){
		return preg_replace("/{([_a-zA-Z0-9]+)}/", MYSQL_TABLE_PREFIX . "$1", $sql);
	}
}

