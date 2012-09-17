<?php
/**
 * 使用PHP代码从纯真IP数据库转换了之后的sqlite数据库中获取IP地址所在地理位置信息
 * @author york
 */
class IpLocationSeekerTCClick {
	/**
	 * 查找一个IP地址所对应的地理位置信息
	 * @param mixed $ip_address 由字符串或者整数表示的一个IP地址
	 * @return array IP地址所对应的country和area信息
	 */
	public static function seek($ip_address){
		if(is_int($ip_address)) $ip_int = $ip_address;
		else $ip_int = self::ip2int($ip_address);
		
		$sql = "select * from {qqwry} where ip<={$ip_int} order by ip desc limit 1";
		$stmt = TCClick::app()->db->query($sql);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row['name'];
	}
	
	/**
	 * php 自带的 ip2long 函数在 32 位环境下会输出负数，而在 64 位的情况下不会输出负数
	 * 该函数将字符串形式的ip地址转化成一个整数值，并使在 64 环境下运行时按照 32 位条件一样统一输出负数
	 * @param string $ip_address
	 * @return int
	 */
	public static function ip2int($ip_address){
		static $int_max = 2147483647;
		$iplong = ip2long($ip_address);
		if($iplong === false) return 0;
		elseif($iplong <= $int_max){
			return $iplong;
		}else{
			$iplong = $iplong - $int_max - $int_max - 2;
			return $iplong;
		}
	}
}

