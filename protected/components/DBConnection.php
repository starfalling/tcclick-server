<?php

class DBConnection{
	protected $db_master;
	protected $db_slave;
	
	/**
	 * connect to the master and slave database
	 */
	public function connect(){
		$this->connectMaster();
		$this->connectSlave();
	}
	
	/**
	 * connect to the master database
	 */
	private function connectMaster(){
		if(!$this->db_master){
			$options = array(PDO::ATTR_PERSISTENT, MYSQL_PERSISTENT);
			$this->db_master = new PDO(MYSQL_DSN_MASTER, MYSQL_USER_MASTER, MYSQL_PASS_MASTER, $options);
		}
	}
	
	/**
	 * connect to the slave database
	 */
	private function connectSlave(){
		if(!$this->db_slave){
			try{
				$options = array(PDO::ATTR_PERSISTENT, MYSQL_PERSISTENT);
				if(MYSQL_DSN_MASTER == MYSQL_DSN_SLAVE){
					if(!$this->db_master){
						$this->db_master = new PDO(MYSQL_DSN_MASTER, MYSQL_USER_MASTER, MYSQL_PASS_MASTER, $options);
					}
					$this->db_slave = $this->db_master;
				}else{
					$this->db_slave = new PDO(MYSQL_DSN_SLAVE, MYSQL_USER_SLAVE, MYSQL_PASS_SLAVE, $options);
				}
			}catch(PDOException $e){
				TCClick::error($e->getMessage()."\n".$e->getTraceAsString());
				echo "Connect database failed, please contact your administrator for more information.";
				exit;
			}
		}
	}
	
	/**
	 * close the database connection
	 */
	public function close(){
		$this->db_master = null;
		$this->db_slave = null;
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
		if(!$this->db_master) $this->connectMaster();
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
		if(!$this->db_slave) $this->connectSlave();
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
		if(TCCLICK_DEBUG_SQL_STATISTICS){
			if(preg_match_all("/{([_a-zA-Z0-9]+)}/", $sql, $matches)){
				foreach($matches[1] as $tablename){
					$cache_key = "sql:statistics:$tablename";
					TCClick::app()->cache->incr($cache_key, 1, true);
				}
			}
		}
		return preg_replace("/{([_a-zA-Z0-9]+)}/", MYSQL_TABLE_PREFIX . "$1", $sql);
	}
}

