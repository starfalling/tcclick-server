<?php
class ReportsLaunchController extends Controller{
	public function filters(){
		return array("AdminRequiredFilter");
	}
	
	
  
  public function actionIndex(){
    $this->render('index');
  }

  public function actionAjaxDayopenTimes(){
    header("Content-type: application/json;charset=utf-8");
    $json = array("stats"=>array(),"dates"=>self::$all_open_times, "result"=>"success");
    $times_group = array('open_times >=1 and open_times<=2', 'open_times >=3 and open_times<=5', 'open_times >=6 and open_times<=9', 
        'open_times >=10 and open_times<=19', 'open_times >=20 and open_times<=49','open_times >=50');
    $yesterday = date('Y-m-d', time()-86400);  
    $compare_to = '';
    if($_GET['compare_to']) $compare_to = date("Y-m-d", strtotime($_GET['compare_to']));
    if($compare_to == $yesterday) $compare_to = '';
    $table_name = str_replace("-", "_", "{daily_active_devices_$yesterday}");
    $table_name_compare = str_replace("-", "_", "{daily_active_devices_$compare_to}");
    foreach ($times_group as $times){
      $daytime_sql = "select count(*)  from $table_name where  $times ";
      $compare_sql = "select count(*)  from $table_name_compare where $times ";
      $count[] = TCClick::app()->db->query($daytime_sql)->fetchColumn(); 
      $compare_count[] = TCClick::app()->db->query($compare_sql)->fetchColumn();
    } 
    $sum_count = array_sum($count);
    $sum_compare_count = array_sum($compare_count);
    foreach ($count as $row){
      $rate[] = $row/$sum_count;
    }
    foreach ($compare_count as $row){
      $compare_rate[] = $row/$sum_compare_count;
    }
    $json['stats'][] = array('data'=>$rate, 'name'=>$yesterday);
    if($compare_to) $json['stats'][] = array('data'=>$compare_rate, 'name'=>$compare_to);
    echo json_encode($json);
  }
  
  /**
   * 求的是日期差值为7 而非自然周
   */
  public  function actionAjaxWeekopenTimes(){
    header("Content-type: application/json;charset=utf-8");
    $json = array("stats"=>array(),"dates"=>self::$all_open_times, "result"=>"success");
    $time = time();
    $yesterday = date('Y-m-d', $time-86400);
    $compare_to = '';
    if($_GET['compare_to']) $compare_to = date("Y-m-d", strtotime($_GET['compare_to']));
    if($compare_to == $yesterday) $compare_to = '';
    $date_name = date('Y-m-d', time()-86400*7).'~'.date('Y-m-d', time()-86400);
    $compar_date_name = date('Y-m-d', strtotime($_GET['compare_to'])-86400*6).'~'.date('Y-m-d', strtotime($_GET['compare_to']));
    $device_counts =array();
    for($i=7; $i>0; $i--){
      $compare_date = date('Y-m-d', strtotime($_GET['compare_to'])-86400*($i-1));
      $date = date('Y-m-d', $time - 86400 * $i);
      $table_name = str_replace("-", "_", "{daily_active_devices_$date}");
      $compare_table_name = str_replace("-", "_", "{daily_active_devices_$compare_date}");
      $daily_sql = "select device_id , open_times from $table_name";
      $compar_daily_sql = "select device_id, open_times from $compare_table_name";
      $stmt = TCClick::app()->db->query($daily_sql);
      $compar_stmt = TCClick::app()->db->query($compar_daily_sql);
      foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
        $device_counts[$date][$row['device_id']] = $row['open_times'];
      }
      foreach($compar_stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
        $compar_device_counts[$date][$row['device_id']] = $row['open_times'];
      }
    } 
    $device_same_sum = array();
    foreach ($device_counts as $key=>$counts){
      foreach($counts as $i => $count){
        $device_same_sum[$i] += $count;
      }
    }
    $compar_device_same_sum = array();
    foreach ($compar_device_counts as $key=>$counts){
      foreach($counts as $i => $count){
        $compar_device_same_sum[$i] += $count;
      }
    }
    $device_same_counts = array_count_values($device_same_sum) ;//所有值出现的次数
    $sum_count = array_sum($device_same_counts);
    //每一组出现次数/总次数 = 每一个百分比
    //阶段的百分比 = 所属阶段的 每一个百分比 之和
    foreach ($device_same_counts as $key=>$count){
      if($key <= 2 and $key >= 1){
        $device_weekly_count_1 += $count / $sum_count;
      }elseif($key >= 3 && $key <= 5){
        $device_weekly_count_2 += $count / $sum_count;
      }elseif($key >= 6 && $key <= 9){
        $device_weekly_count_3 += $count / $sum_count;
      }elseif($key >= 10 && $key <= 19){
        $device_weekly_count_4 += $count / $sum_count;
      }elseif($key >= 20 && $key <= 49){
        $device_weekly_count_5 += $count / $sum_count;
      }elseif($key >= 50 && $key <= 99){
        $device_weekly_count_6 += $count / $sum_count;
      }elseif($key >= 100){
        $device_weekly_count_7 += $count / $sum_count;
      }
    }
    $compar_device_same_counts = array_count_values($compar_device_same_sum) ;
    $compar_sum_count = array_sum($compar_device_same_counts);
    foreach ($compar_device_same_counts as $key=>$count){
      if($key <= 2 and $key >= 1){
        $compar_device_weekly_count_1 += $count / $compar_sum_count;
      }elseif($key >= 3 && $key <= 5){
        $compar_device_weekly_count_2 += $count / $compar_sum_count;
      }elseif($key >= 6 && $key <= 9){
        $compar_device_weekly_count_3 += $count / $compar_sum_count;
      }elseif($key >= 10 && $key <= 19){
        $compar_device_weekly_count_4 += $count / $compar_sum_count;
      }elseif($key >= 20 && $key <= 49){
        $compar_device_weekly_count_5 += $count / $compar_sum_count;
      }elseif($key >= 50 && $key <= 99){
        $compar_device_weekly_count_6 += $count / $compar_sum_count;
      }elseif($key >= 100){
        $compar_device_weekly_count_7 += $count / $compar_sum_count;
      }
    }
    $times_group = array($device_weekly_count_1, $device_weekly_count_2, $device_weekly_count_3,
       $device_weekly_count_4, $device_weekly_count_5, $device_weekly_count_6,$device_weekly_count_7);
    $compar_times_group = array($compar_device_weekly_count_1, $compar_device_weekly_count_2, $compar_device_weekly_count_3,
        $compar_device_weekly_count_4, $compar_device_weekly_count_5, $compar_device_weekly_count_6,$compar_device_weekly_count_7);
   $json['stats'][] = array('data'=>$times_group, 'name'=>$date_name);
   if($compare_to) $json['stats'][]=  array('data'=>$compar_times_group, 'name'=>$compar_date_name);
   echo json_encode($json);
  }
  
  /**
   *  日期为30天的日期差 而非自然月
   *
   */
  public function actionAjaxMonthopenTimes(){
    header("Content-type: application/json; charset=utf-8");
    $json = array("stats"=>array(),"dates"=>self::$all_open_times, "result"=>"success");
    $time=time();
    $yesterday = date('Y-m-d', $time-86400);
    $compare_to = '';
    if($_GET['compare_to']) $compare_to = date("Y-m-d", strtotime($_GET['compare_to']));
    if($compare_to == $yesterday) $compare_to = '';
    $compare_date_name = date('Y-m-d', strtotime($_GET['compare_to'])-86400*30).'~'.date('Y-m-d', strtotime($_GET['compare_to']));
    $date_name = date('Y-m-d', time()-86400*30).'~'.date('Y-m-d', time()-86400);
    for($i=30; $i>0; $i--){
      $date = date('Y-m-d', $time - 86400 * $i);
      $compare_date = date('Y-m-d', strtotime($_GET['compare_to'])-86400*($i-1));
      $table_name = str_replace("-", "_", "{daily_active_devices_$date}");
      $compare_table_name = str_replace("-", "_", "{daily_active_devices_$compare_date}");
      
      $daily_sql = "select device_id , open_times from $table_name";
      $compare_daily_sql = "select device_id, open_times from $compare_table_name";
      $stmt = TCClick::app()->db->query($daily_sql);
      $compare_stmt = TCClick::app()->db->query($compare_daily_sql);
      foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
        $device_counts[$date][$row['device_id']] = $row['open_times'];
      }
      foreach($compare_stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
        $compare_device_counts[$date][$row['device_id']] = $row['open_times'];
      }
    }
    $device_same_sum = array();
    foreach ($device_counts as $key=>$counts){
      foreach($counts as $i => $count){
        $device_same_sum[$i] += $count;
      }
    }
    $compare_device_same_sum = array();
    foreach ($compare_device_counts as $key=>$counts){
      foreach($counts as $i => $count){
        $compare_device_same_sum[$i] += $count;
      }
    }
    $device_same_counts = array_count_values($device_same_sum) ;
    $sum_count = array_sum($device_same_counts);
    foreach ($device_same_counts as $key=>$count){
      if($key <= 2 and $key >= 1){
        $device_weekly_count_1 += $count / $sum_count;
      }elseif($key >= 3 && $key <= 5){
        $device_weekly_count_2 += $count / $sum_count;
      }elseif($key >= 6 && $key <= 9){
        $device_weekly_count_3 += $count / $sum_count;
      }elseif($key >= 10 && $key <= 19){
        $device_weekly_count_4 += $count / $sum_count;
      }elseif($key >= 20 && $key <= 49){
        $device_weekly_count_5 += $count / $sum_count;
      }elseif($key >= 50 && $key <= 99){
        $device_weekly_count_6 += $count / $sum_count;
      }elseif($key >= 100 && $key<=199){
        $device_weekly_count_7 += $count / $sum_count;
      }elseif($key >= 200 && $key<=299){
        $device_weekly_count_8 += $count / $sum_count;
      }elseif($key >= 300 ){
        $device_weekly_count_9 += $count / $sum_count;
      }
    }
    $comapre_device_same_counts = array_count_values($compare_device_same_sum) ;
    $comapre_sum_count = array_sum($comapre_device_same_counts);
    foreach ($comapre_device_same_counts as $key=>$count){
      if($key <= 2 and $key >= 1){
        $compare_device_weekly_count_1 += $count / $comapre_sum_count;
      }elseif($key >= 3 && $key <= 5){
        $compare_device_weekly_count_2 += $count / $comapre_sum_count;
      }elseif($key >= 6 && $key <= 9){
        $compare_device_weekly_count_3 += $count / $comapre_sum_count;
      }elseif($key >= 10 && $key <= 19){
        $compare_device_weekly_count_4 += $count / $comapre_sum_count;
      }elseif($key >= 20 && $key <= 49){
        $compare_device_weekly_count_5 += $count / $comapre_sum_count;
      }elseif($key >= 50 && $key <= 99){
        $compare_device_weekly_count_6 += $count / $comapre_sum_count;
      }elseif($key >= 100 && $key<=199){
        $compare_device_weekly_count_7 += $count / $comapre_sum_count;
      }elseif($key >= 200 && $key<=299){
        $compare_device_weekly_count_8 += $count / $comapre_sum_count;
      }elseif($key >= 300 ){
        $compare_device_weekly_count_9 += $count / $comapre_sum_count;
      }
    }
    $times_group = array($device_weekly_count_1, $device_weekly_count_2, $device_weekly_count_3,
        $device_weekly_count_4, $device_weekly_count_5, $device_weekly_count_6,$device_weekly_count_7,$device_weekly_count_8,$device_weekly_count_9);
    $compare_times_group = array($compare_device_weekly_count_1, $compare_device_weekly_count_2, $compare_device_weekly_count_3,
        $compare_device_weekly_count_4, $compare_device_weekly_count_5, $compare_device_weekly_count_6,$compare_device_weekly_count_7,$compare_device_weekly_count_8,$compare_device_weekly_count_9);
    $json['stats'][] = array('data'=>$times_group, 'name'=>$date_name);
    if($compare_to) $json['stats'][]= array('data'=>$compare_times_group, 'name'=>$compare_date_name);
    echo json_encode($json);
  }
  public static $all_open_times = array(
      '1-2', '3-5', '6-9', '10-19', '20-49', '50-99', '100-199', '200-299','300+',
      ); 
}