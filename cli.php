<?php
$i= 0;
$flagSync = false;
$data = "";
$fp =fopen("/dev/ttyACM0", "w+");
$cDate = new DateTime();
$lTime = $cDate->getTimestamp();

while(true){
  if( !$fp) {
    echo "Error";die();
  }
  $char = fread($fp, 1);

  if(!$flagSync && $char == "\n"){
    $data = "";
    $flagSync = true;
  } else if($flagSync && $char == "\n"){
    processLine($data);
    $data = "";
  } else if($flagSync){
    $data .= $char;
  }
  usleep(50);
}
fclose($fp);


function processLine($data){
  global $lTime;
  $cDate = new DateTime();
  $obj = json_decode($data);
//  if($obj != null and ($cDate->getTimestamp() - $lTime)>300){
  if($obj != null){
    var_dump($obj);
    $lTime = $cDate->getTimestamp();
    exec("zabbix_sender -c /etc/zabbix/zabbix_agentd.conf -k sensor.temp.celcius -o " . $obj->temp);
  }
}
