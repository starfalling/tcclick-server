<?php

class ActivityWithEventTest extends TCClickTestCase{
	
	public function testEventName(){
		$this->assertEquals(1, EventName::idFor("test"));
		$this->assertEquals(1, EventName::idFor("test"));
		$this->assertEquals(1, EventName::idFor("test"));
		$this->assertEquals(2, EventName::idFor("测试啊"));
		$this->assertEquals('test', EventName::nameof(1));
		$this->assertEquals('测试啊', EventName::nameof(2));
	}
	
	public function testEventParam(){
		$event_param = EventParam::createIfNotExists(1, 1); // first create
		$this->assertNotNull($event_param);
		$this->assertEquals(1, $event_param->param_id);
		
		$event_param = EventParam::loadByEventAndName(1, 1); // query
		$this->assertNotNull($event_param);
		$this->assertEquals(1, $event_param->param_id);

		$event_param = EventParam::createIfNotExists(1, 1); // recreate
		$this->assertNotNull($event_param);
		$this->assertEquals(1, $event_param->param_id);
		
		$event_param = EventParam::createIfNotExists(1, 2); // create a second param
		$this->assertNotNull($event_param);
		$this->assertEquals(2, $event_param->param_id);
		
		$event_param = EventParam::createIfNotExists(1, 7); // create a third param
		$this->assertNotNull($event_param);
		$this->assertEquals(3, $event_param->param_id);
		
		$event_param = EventParam::createIfNotExists(2, 1); // create a second event
		$this->assertNotNull($event_param);
		$this->assertEquals(1, $event_param->param_id);
	}
	
	public function testNormalAnalyze(){
		$this->importActivityData('activity-with-events.json');
		$this->importActivityData('activity-with-events2.json');

		$sql = "select * from {client_activities} order by id limit 10";
		$stmt = TCClick::app()->db->query($sql);
		$row1 = $stmt->fetch(PDO::FETCH_ASSOC);
		$row2 = $stmt->fetch(PDO::FETCH_ASSOC);
		$this->assertNotEmpty($row1);
		$this->assertNotEmpty($row2);
		
		$data_uncompressed = gzuncompress($row1['data_compressed']);
		$this->assertNotEmpty($data_uncompressed);
		$analyzer = new Analyzer($row1['server_timestamp'], intval($row1['ip']), $data_uncompressed);
		$analyzer->analyze();
		
		$this->assertEquals('Test Event', EventName::nameof(1));
		$this->assertEquals('点击事件', EventName::nameof(2));
		$this->assertEquals(1, Event::idFor(1));
		$this->assertEquals(2, Event::idFor(2));
		
		$sql = "select `count` from {counter_daily_events} where date='2013-02-04' and event_id=1";
		$count = TCClick::app()->db->query($sql)->fetchColumn(0);
		$this->assertEquals(8, $count);
		
		$analyzer->analyze(); // analyze again
		$sql = "select `count` from {counter_daily_events} where date='2013-02-04' and event_id=1";
		$count = TCClick::app()->db->query($sql)->fetchColumn(0);
		$this->assertEquals(16, $count);
		
		// analyze the second row
		$data_uncompressed = gzuncompress($row2['data_compressed']);
		$this->assertNotEmpty($data_uncompressed);
		$analyzer = new Analyzer($row2['server_timestamp'], intval($row2['ip']), $data_uncompressed);
		$analyzer->analyze();
		
		$sql = "select `count` from {counter_daily_events} where date='2013-02-04' and event_id=2";
		$count = TCClick::app()->db->query($sql)->fetchColumn(0);
		$this->assertEquals(18, $count);
	}
}

