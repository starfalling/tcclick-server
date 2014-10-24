<?php

include_once dirname(dirname(__FILE__)) . '/protected/init.php';

$qqwry_filepath = dirname(__DIR__) . "/protected/data/qqwry.dat";
$seeker = new IpLocationSeekerBinary($qqwry_filepath);
$seeker->saveToTCClickMysql();


                                                             // -- ip138 -- | -- tcclick -- |
echo IpLocationSeekerTCClick::seek("39.12.138.106"), "\n";   //     台湾     |     美国       |
echo IpLocationSeekerTCClick::seek("222.73.254.95"), "\n";   //     上海     |     上海       |
echo IpLocationSeekerTCClick::seek("222.66.37.26"), "\n";    //     上海     |     上海       |
echo IpLocationSeekerTCClick::seek("50.116.14.67"), "\n";    //     美国     |     美国       |
echo IpLocationSeekerTCClick::seek("54.251.190.248"), "\n";  //     美国     |     美国       |
echo IpLocationSeekerTCClick::seek("119.246.71.220"), "\n";  //     香港     |     香港       |
echo IpLocationSeekerTCClick::seek("111.164.34.205"), "\n";  //     天津     |     天津       |
echo IpLocationSeekerTCClick::seek("14.136.51.73"), "\n";    //     香港     |     美国       |
echo IpLocationSeekerTCClick::seek("218.250.126.51"), "\n";  //     香港     |     香港       |
echo IpLocationSeekerTCClick::seek("202.4.201.45"), "\n";    //     香港     |     香港       |
echo IpLocationSeekerTCClick::seek("14.0.143.64"), "\n";     //     香港     |     美国       |
echo IpLocationSeekerTCClick::seek("202.86.154.28"), "\n";   //     澳门     |     澳门       |
echo IpLocationSeekerTCClick::seek("58.153.252.222"), "\n";  //     香港     |     香港       |
echo IpLocationSeekerTCClick::seek("49.217.232.73"), "\n";   //     台湾     |     亚太       |
echo IpLocationSeekerTCClick::seek("175.174.85.166"), "\n";  //     辽宁     |     中国       |

// tcclick 版本与纯真官方程序所得结果相同
