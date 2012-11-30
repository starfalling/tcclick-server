TCClick统计平台服务器端
==============

接下来需要做的事情(无先后次序)：

1. 完善缺失的报表(留存设备中的周、月留存，使用时长、使用频率)

2. 修复一些报表(使用频率，设备型号的活跃数据)在大数据量情况下SAE平台不可用的问题

3. 加入自定义事件功能

4. 重构devices表，对devices进行分表，以使得能够在SAE平台上支撑超过一千万台设备的容量，同时将设备号字段由char改为binary，以节省存储空间

5. windows phone 客户端

6. 性能优化，节省云豆

7. 加入账号管理功能

在SAE平台上的安装部署文档参见博文：[http://blog.yorkgu.me/2012/09/29/install_tcclick_on_sina_app_engine/](http://blog.yorkgu.me/2012/09/29/install_tcclick_on_sina_app_engine/)

目前遭遇SAE数据库超配额问题(日活跃较大的情况下)，正着手解决中...