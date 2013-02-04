
create table if not exists tcclick_client_activities(
	id integer unsigned not null primary key auto_increment,
	server_timestamp integer unsigned not null,
	ip integer not null default 0,
	data_compressed mediumblob
) engine myisam;


create table if not exists tcclick_channels(
	id smallint unsigned not null primary key auto_increment,
	channel varchar(255),
	unique key channel(channel)
)engine myisam character set utf8;
create table if not exists tcclick_devices(
	id integer unsigned not null primary key auto_increment,
	udid char(32) not null,
	channel_id smallint unsigned not null,
	version_id smallint unsigned not null,
	created_at timestamp default current_timestamp,
	unique key udid (udid),
	key created_at (created_at)
)engine myisam;


create table if not exists tcclick_counter_daily(
	`date` date,
	`new_devices_count` integer unsigned not null default 0,
	`all_devices_count` integer unsigned not null default 0,
	`active_devices_count` integer unsigned not null default 0,
	`update_devices_count` integer unsigned not null default 0,
	`open_times` integer unsigned not null default 0,
	`open_times_with_seconds_spent` integer unsigned not null default 0,
	`seconds_spent` integer unsigned not null default 0,
	primary key (`date`)
)engine myisam;
create table if not exists tcclick_counter_hourly_new(
	`date` date,
	`hour` tinyint,
	`channel_id` smallint unsigned not null,
	`count` integer unsigned not null default 0,
	primary key (`date`, `hour`, `channel_id`)
)engine myisam;
create table if not exists tcclick_counter_hourly_active(
	`date` date,
	`hour` tinyint,
	`channel_id` smallint unsigned not null,
	`count` integer unsigned not null default 0,
	primary key (`date`, `hour`, `channel_id`)
)engine myisam;
create table if not exists tcclick_counter_hourly_open_times(
	`date` date,
	`hour` tinyint,
	`count` integer unsigned not null default 0,
	primary key (`date`, `hour`)
)engine myisam;
create table if not exists tcclick_counter_hourly_update(
	`date` date,
	`hour` tinyint,
	`channel_id` smallint unsigned not null,
	`count` integer unsigned not null default 0,
	primary key (`date`, `hour`, `channel_id`)
)engine myisam;
create table if not exists tcclick_counter_daily_new(
	`date` date,
	`channel_id` smallint unsigned not null,
	`count` integer unsigned not null default 0,
	primary key (`date`, channel_id)
)engine myisam;
create table if not exists tcclick_counter_daily_active(
	`date` date,
	`channel_id` smallint unsigned not null,
	`count` integer unsigned not null default 0,
	primary key (`date`, channel_id)
)engine myisam;
create table if not exists tcclick_counter_daily_update(
	`date` date,
	`channel_id` smallint unsigned not null,
	`count` integer unsigned not null default 0,
	primary key (`date`, channel_id)
)engine myisam;
create table if not exists tcclick_counter_daily_update_with_version(
	`date` date,
	`version_id` smallint unsigned not null,
	`count` integer unsigned not null default 0,
	primary key (`date`, version_id)
)engine myisam;


create table if not exists tcclick_versions(
	id smallint unsigned not null primary key auto_increment,
	version varchar(255),
	unique key version(version)
)engine myisam character set utf8;
create table if not exists tcclick_counter_daily_new_version(
	`date` date,
	`version_id` smallint unsigned not null,
	`count` integer unsigned not null default 0,
	primary key (`date`, version_id)
)engine myisam;
create table if not exists tcclick_counter_daily_active_version(
	`date` date,
	`version_id` smallint unsigned not null,
	`count` integer unsigned not null default 0,
	primary key (`date`, version_id)
)engine myisam;

create table if not exists tcclick_os_versions(
	id smallint unsigned not null primary key auto_increment,
	version varchar(255),
	unique key version(version)
)engine myisam character set utf8;
create table if not exists tcclick_counter_daily_new_os_version(
	`date` date,
	`version_id` smallint unsigned not null,
	`count` integer unsigned not null default 0,
	primary key (`date`, version_id)
)engine myisam;
create table if not exists tcclick_counter_daily_active_os_version(
	`date` date,
	`version_id` smallint unsigned not null,
	`count` integer unsigned not null default 0,
	primary key (`date`, version_id)
)engine myisam;

create table if not exists tcclick_resolutions(
	id smallint unsigned not null primary key auto_increment,
	resolution varchar(255),
	unique key resolution(resolution)
)engine myisam character set utf8;
create table if not exists tcclick_counter_daily_new_resolution(
	`date` date,
	`resolution_id` smallint unsigned not null,
	`count` integer unsigned not null default 0,
	primary key (`date`, resolution_id)
)engine myisam;
create table if not exists tcclick_counter_daily_active_resolution(
	`date` date,
	`resolution_id` smallint unsigned not null,
	`count` integer unsigned not null default 0,
	primary key (`date`, resolution_id)
)engine myisam;

create table if not exists tcclick_carrier(
	id smallint unsigned not null primary key auto_increment,
	carrier varchar(255),
	unique key carrier(carrier)
)engine myisam character set utf8;
create table if not exists tcclick_counter_daily_new_carrier(
	`date` date,
	`carrier_id` smallint unsigned not null,
	`count` integer unsigned not null default 0,
	primary key (`date`, carrier_id)
)engine myisam;
create table if not exists tcclick_counter_daily_active_carrier(
	`date` date,
	`carrier_id` smallint unsigned not null,
	`count` integer unsigned not null default 0,
	primary key (`date`, carrier_id)
)engine myisam;

create table if not exists tcclick_networks(
	id smallint unsigned not null primary key auto_increment,
	network varchar(255),
	unique key network(network)
)engine myisam character set utf8;
create table if not exists tcclick_counter_daily_active_network(
	`date` date,
	`network_id` smallint unsigned not null,
	`count` integer unsigned not null default 0,
	primary key (`date`, network_id)
)engine myisam;


create table if not exists tcclick_models(
	id integer unsigned not null primary key auto_increment,
	brand varchar(100),
	model varchar(100),
	unique key brand_model(brand, model)
)engine myisam character set utf8;
create table if not exists tcclick_counter_daily_new_model(
	`date` date,
	`model_id` integer unsigned not null,
	`count` integer unsigned not null default 0,
	primary key (`date`, model_id)
)engine myisam;
create table if not exists tcclick_counter_daily_active_model(
	`date` date,
	`model_id` integer unsigned not null,
	`count` integer unsigned not null default 0,
	primary key (`date`, model_id)
)engine myisam;


create table if not exists tcclick_areas(
	id smallint unsigned not null primary key auto_increment,
	area varchar(50),
	key area(area)
)engine myisam character set utf8;
insert ignore into tcclick_areas(area) values ('中国'),('北京'),('上海'),('天津'),('重庆'),
('安徽'),('福建'),('甘肃'),('广东'),('广西'),('贵州'),('海南'),('河北'),('河南'),
('黑龙江'),('湖北'),('湖南'),('吉林'),('江苏'),('江西'),('辽宁'),('内蒙古'),('宁夏'),
('青海'),('山东'),('山西'),('陕西'),('四川'),('西藏'),('新疆'),('云南'),('浙江'),
('香港'),('澳门'),('台湾');
create table if not exists tcclick_counter_daily_new_area(
	`date` date,
	`area_id` smallint unsigned not null,
	`count` integer unsigned not null default 0,
	primary key (`date`, area_id)
)engine myisam;
create table if not exists tcclick_counter_daily_active_area(
	`date` date,
	`area_id` smallint unsigned not null,
	`count` integer unsigned not null default 0,
	primary key (`date`, area_id)
)engine myisam;





create table if not exists tcclick_counter_daily_seconds_spent_per_open(
	`date` date,
	`seconds_spent_id` tinyint unsigned not null,
	`count` integer unsigned not null default 0,
	primary key (`date`, `seconds_spent_id`)
);
create table if not exists tcclick_counter_daily_seconds_spent_per_day(
	`date` date,
	`seconds_spent_id` tinyint unsigned not null,
	`count` integer unsigned not null default 0,
	primary key (`date`, `seconds_spent_id`)
);





-- 周活跃的计数器
create table if not exists tcclick_counter_weekly_active(
	`date` date,
	`channel_id` smallint unsigned not null,
	`count` integer unsigned not null default 0,
	primary key (`date`, channel_id)
)engine myisam;
-- 月活跃的计数器
create table if not exists tcclick_counter_monthly_active(
	`date` date,
	`channel_id` smallint unsigned not null,
	`count` integer unsigned not null default 0,
	primary key (`date`, channel_id)
)engine myisam;









create table if not exists tcclick_retention_rate_daily(
	`date` date not null,
	`channel_id` smallint unsigned not null,
	`new_count` integer unsigned not null,
	`retention1` smallint not null default -1,
	`retention2` smallint not null default -1,
	`retention3` smallint not null default -1,
	`retention4` smallint not null default -1,
	`retention5` smallint not null default -1,
	`retention6` smallint not null default -1,
	`retention7` smallint not null default -1,
	`retention8` smallint not null default -1,
	primary key (`date`, `channel_id`)
)engine myisam;
drop table if exists tcclick_retention_rate_weekly;
create table if not exists tcclick_retention_rate_weekly(
	`date` date not null,
	`channel_id` smallint unsigned not null,
	`new_count` integer unsigned not null,
	`retention1` smallint not null default -1,
	`retention2` smallint not null default -1,
	`retention3` smallint not null default -1,
	`retention4` smallint not null default -1,
	`retention5` smallint not null default -1,
	`retention6` smallint not null default -1,
	`retention7` smallint not null default -1,
	`retention8` smallint not null default -1,
	primary key (`date`, `channel_id`)
)engine myisam;
create table if not exists tcclick_retention_rate_monthly(
	`date` date not null,
	`channel_id` smallint unsigned not null,
	`new_count` integer unsigned not null,
	`retention1` smallint not null default -1,
	`retention2` smallint not null default -1,
	`retention3` smallint not null default -1,
	`retention4` smallint not null default -1,
	`retention5` smallint not null default -1,
	`retention6` smallint not null default -1,
	`retention7` smallint not null default -1,
	`retention8` smallint not null default -1,
	primary key (`date`, `channel_id`)
)engine myisam;




create table if not exists tcclick_exceptions(
	`id` integer unsigned not null primary key auto_increment,
	`md5` char(32),
	`version_id` smallint unsigned not null,
	`status` tinyint default 0, -- 错误日志的状态，有三种状态：正常、修复、忽略
	`located` tinyint not null default 0, -- 是否已经定位了，用在ios当中
	`count` integer,
	`exception` text,
	`updated_at` timestamp default current_timestamp,
	unique key version_id_md5(version_id, md5)
)engine myisam;
-- alter table tcclick_exceptions add column `located` tinyint not null default 0 after `fixed`;
-- alter table tcclick_exceptions change column fixed status tinyint default 0;
create table if not exists tcclick_counter_exceptions(
	`date` date,
	`version_id` smallint unsigned not null default 0,
	`count` integer unsigned not null default 1,
	primary key (`date`, `version_id`)
)engine myisam;


-- 可登录查看报表的账号系统
create table if not exists tcclick_users(
	`id` integer unsigned not null primary key auto_increment,
	`username` char(20),
	`password_salt` char(4) not null,
	`password_sha1` char(40) not null,
	`status` tinyint default 0, -- 用户的状态，有两种状态，正常状态和禁用状态
	`created_at` timestamp default current_timestamp,
	unique key username(username)
);
insert ignore into tcclick_users (username, password_salt, password_sha1) values
('admin', '1234', '7b902e6ff1db9f560443f2048974fd7d386975b0');
create table if not exists tcclick_access_tokens(
	`access_token` char(40) not null primary key,
	`expire_at` datetime,
	`user_id` integer unsigned
);
create table if not exists tcclick_user_channels( -- 用户可以查看哪些渠道的数据
	`user_id` integer unsigned not null,
	`channel_id` smallint unsigned not null,
	primary key (user_id, channel_id)
);

-- 客户端事件相关的表
create table if not exists tcclick_event_names( -- 这个表存储了所有事件系统里面用到了的名称
	`id` integer unsigned not null primary key auto_increment,
	`name` varchar(255) not null,
	unique key name(name)
);
create table if not exists tcclick_events(
	`id` tinyint unsigned not null primary key auto_increment,
	`name_id` integer unsigned not null, -- 收到的事件的名字
	`alias_id` integer unsigned not null, -- 后台设置的事件的别名
	unique key name_id(name_id)
);
create table if not exists tcclick_event_params(
	`event_id` tinyint unsigned not null,
	`param_id` tinyint unsigned not null default 0, -- 参数的ID
	`name_id` integer unsigned not null, -- 这个参数的名字
	`alias_id` integer unsigned not null, -- 这个参数的别名
	primary key (event_id, name_id)
);
create table if not exists tcclick_counter_daily_events(
	`date` date not null,
	`event_id` tinyint unsigned not null,
	`param_id` tinyint unsigned not null,
	`version_id` smallint unsigned not null,
	`value_id` integer unsigned not null, -- 取值在 event_names 里面的 ID
	`count` integer unsigned not null,
	primary key (`event_id`, `date`, `version_id`, `param_id`, `value_id`)
);


-- 给表 tcclick_counter_daily_new 的 channel_id 字段创建索引
create index channel_id on tcclick_counter_daily_new(channel_id);

