<?php

class TCClickUtil{
	public static function formatSecondsSpent($seconds){
		if($seconds < 60){
			return sprintf("00:%02d", $seconds);
		}else{
			return sprintf("%02d:%02d", $seconds/60, $seconds%60);
		}
	}
	
	/**
	 * 从一个文件中查找第一个大于等于某个值的整数所在的位置
	 * 文件内容为从小到大排序的四个字节的整数拼接然后构成的字符串
	 * @param int $value
	 * @param string $filepath
	 * @return mixed 如果找到了，返回索引值，如果没有找到，返回 false
	 */
	public static function seekIndexThatFirstGreaterOrEqualInFile($value, $filepath){
		$filesize = filesize($filepath);
		$count = $filesize / 4;
		$handle = fopen($filepath, 'r');
		$min_value = unpack('I', fread($handle, 4));
		$min_value = $min_value[1];
		if ($min_value >= $value){
			fclose($handle);
			return 0;
		}
		
		fseek($handle, $filesize-4, SEEK_SET);
		$max_value = unpack('I', fread($handle, 4));
		$max_value = $max_value[1];
		if ($max_value < $value){
			fclose($handle);
			return false;
		}else if ($max_value == $value){
			fclose($handle);
			return $count-1;
		}
		
		$index = self::_seekIndexThatFirstGreaterOrEqualInFile($value, $handle, 0, $count-1);
		fclose($handle);
		return $index;
	}
	
	private static function _seekIndexThatFirstGreaterOrEqualInFile($value, $handle, $left, $right){
		// $left 位置的这个整数一定会比 $value 小
		// $right 位置的这个整数一定会比 $value 大
		$middle = (int)($left + ($right-$left)/2);
		if ($middle == $left) return $right; // 这种情况就是 $right == $left+1
		
		fseek($handle, $middle*4, SEEK_SET);
		$middle_value = unpack('I', fread($handle, 4));
		$middle_value = $middle_value[1];
		if ($middle_value == $value) return $middle;
		else if($middle_value > $value){
			return self::_seekIndexThatFirstGreaterOrEqualInFile($value, $handle, $left, $middle);
		}else{
			return self::_seekIndexThatFirstGreaterOrEqualInFile($value, $handle, $middle, $right);
		}
	}
	

	/**
	 * 从一个文件中查找最后一个小于等于某个值的整数所在的位置
	 * 文件内容为从小到大排序的四个字节的整数拼接然后构成的字符串
	 * @param int $value
	 * @param string $filepath
	 * @return mixed 如果找到了，返回索引值，如果没有找到，返回 false
	 */
	public static function seekIndexThatLastLowerOrEqualInFile($value, $filepath){
		$filesize = filesize($filepath);
		$count = $filesize / 4;
		$handle = fopen($filepath, 'r');
		$min_value = unpack('I', fread($handle, 4));
		$min_value = $min_value[1];
		if ($min_value == $value){
			fclose($handle);
			return 0;
		}else if($min_value > $value){
			fclose($handle);
			return false;
		}
		
		fseek($handle, $filesize-4, SEEK_SET);
		$max_value = unpack('I', fread($handle, 4));
		$max_value = $max_value[1];
		if ($max_value <= $value){
			fclose($handle);
			return $count-1;
		}
		
		$index = self::_seekIndexThatLastLowerOrEqualInFile($value, $handle, 0, $count-1);
		fclose($handle);
		return $index;
	}
	
	private static function _seekIndexThatLastLowerOrEqualInFile($value, $handle, $left, $right){
		// $left 位置的这个整数一定会比 $value 小
		// $right 位置的这个整数一定会比 $value 大
		$middle = (int)($left + ($right-$left)/2);
		if ($middle == $left) return $left; // 这种情况就是 $right == $left+1
	
		fseek($handle, $middle*4, SEEK_SET);
		$middle_value = unpack('I', fread($handle, 4));
		$middle_value = $middle_value[1];
		if ($middle_value == $value) return $middle;
		else if($middle_value > $value){
			return self::_seekIndexThatFirstGreaterOrEqualInFile($value, $handle, $left, $middle);
		}else{
			return self::_seekIndexThatFirstGreaterOrEqualInFile($value, $handle, $middle, $right);
		}
	}
	
	public static function readableDate($time){
		if(is_string($time)) $time = strtotime($time);
		$now = time();
		$duration = $now - $time;
		if($duration<60) return "一分钟内";
		if($duration<180) return "三分钟内";
		if($duration<300) return "五分钟内";
		if($duration<600) return "十分钟内";
		if($duration<1800) return "半小时内";
		if($duration<3600) return "一小时内";
		if($duration<7200) return "两小时内";
		if($duration<18000) return "五小时内";
	
		$today = strtotime(date("Y-m-d 00:00:00"));
		$duration = $today - $time;
		if($duration<0) return "今天";
		elseif($duration<86400) return "昨天";
		elseif($duration<86400*2) return "前天";
		elseif($duration<604800) return "一周内";
		else return date("Y-m-d", $time);
	}
	
	/**
	 * 根据传入的路径和参数创建一个url地址
	 * @param string $path url地址的路径，传空将使用当前正在访问的url地址
	 * @param string $params 参数
	 */
	public static function createUrl($path=NULL, $params=NULL){
		if(!$path) $url = trim($_SERVER['REQUEST_URI'], '&');
		else $url = $path;
		if($params){
			$need_check_param_already_exists = true;
			if(strpos($url, '?')===false){
				$url .= '?';
				$need_check_param_already_exists = false;
			}
			foreach($params as $k=>$v){
				$k_encoded = urlencode($k);
				$v_encoded = urlencode($v);
				if($need_check_param_already_exists){
					$url = preg_replace("/\\?{$k_encoded}=[^&]+/", '?', $url);
					$url = preg_replace("/&{$k_encoded}=[^&]+/", '', $url);
				}
				if($v === false) continue;
				if($v === null) continue;
				if($url[strlen($url)-1] == '?')  $url .= "{$k_encoded}={$v_encoded}";
				else $url .= "&{$k_encoded}={$v_encoded}";
			}
		}
		if($url[strlen($url)-1]=='?') $url = substr($url, 0, strlen($url)-1);
		return $url;
	}
	
	/**
	 * 生成一个分页器的html代码
	 * @param int $pages_count 总页数
	 * @param int $current_page 当前页，从1开始，默认NULL表示使用$_GET里面的page参数
	 */
	public static function pager($pages_count, $current_page=NULL, $return=false){
		if(!$pages_count) return;
		if($current_page===NULL) $current_page = $_GET['page'] ? intval($_GET['page']) : 1;
		if($return) ob_start();
		
		echo "<div class='pager'>";
		
		if($current_page>1){
			$url = TCClickUtil::createUrl(NULL, array("page"=>1));
			echo "<span class='page'><a href='$url'>首页</a></span>";
			$url = TCClickUtil::createUrl(NULL, array("page"=>$current_page-1));
			echo "<span class='page'><a href='$url'>上一页</a></span>";
		}
		$i=0;
		$to = $current_page<=4 ? $current_page+1 : 2;
		if($to >= $pages_count) $to = $pages_count-1;
		for($i=1; $i<=$to; $i++){
			if($i == $current_page){
				echo "<span class='page current'>{$i}</span>";
			}else{
				$url = TCClickUtil::createUrl(NULL, array("page"=>$i));
				echo "<span class='page'><a href='$url'>$i</a></span>";
			}
		}
		
		if($i<$current_page-1){
			$i = $current_page-1;
			echo '<span class="gap">...</span>';
			$to = $current_page+1;
			if ($to>$pages_count) $to=$pages_count;
			for(; $i<=$to; $i++){
				if($i == $current_page){
					echo "<span class='page current'>{$i}</span>";
				}else{
					$url = TCClickUtil::createUrl(NULL, array("page"=>$i));
					echo "<span class='page'><a href='$url'>$i</a></span>";
				}
			}
		}
		
		if ($i<=$pages_count){
			if($i < $pages_count-1){
				$i = $pages_count-1;
				echo '<span class="gap">...</span>';
			}
			for(; $i<=$pages_count; $i++){
				if($i == $current_page){
					echo "<span class='page current'>{$i}</span>";
				}else{
				$url = TCClickUtil::createUrl(NULL, array("page"=>$i));
						echo "<span class='page'><a href='$url'>$i</a></span>";
				}
			}
		}
		
		if($current_page<$pages_count){
			$url = TCClickUtil::createUrl(NULL, array("page"=>$current_page+1));
			echo "<span class='page'><a href='$url'>下一页</a></span>";
			$url = TCClickUtil::createUrl(NULL, array("page"=>$pages_count));
			echo "<span class='page'><a href='$url'>末页</a></span>";
		}
		
		echo "</div>";
		
		if($return){
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}
	
	public static function selector($options){
		$selected_option_index = 0;
		foreach($options as $i=>&$option){
			$label = $option['label'];
			unset($option['label']);
			$is_current_option_selected = true;
			foreach($option as $key=>$value){
				if($_GET[$key] != $value){
					$is_current_option_selected = false;
				}
			}
			if($is_current_option_selected){
				$selected_option_index = $i;
			}
			$option['url'] = self::createUrl(NULL, $option);
			$option['label'] = $label;
		}
		echo "<div class='selector'>";
		echo "<span class='selected_value'>{$options[$selected_option_index]['label']}</span>";
		echo "<div class='select_list'><ul>";
		foreach($options as $i=>&$option){
			if($i==$selected_option_index){
				echo "<li class='selected'><a href='{$option['url']}'>{$option['label']}</a></li>";
			}else{
				echo "<li><a href='{$option['url']}'>{$option['label']}</a></li>";
			}
		}
		echo "</ul></div>";
		echo "</div>";
	}
}

