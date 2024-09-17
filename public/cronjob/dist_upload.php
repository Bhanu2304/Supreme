<?php

ini_set('max_execution_time', '300');
ini_set('memory_limit','2048M');

$con = mysqli_connect("localhost","root","mas123") or die("connection not found");
$db = mysqli_select_db($con,"db_supreme") or die("error in database"); 


$sel = "SELECT id,state_name,div_name,dist_name,area_name,pin FROM `district_master_upload` where upd='1' limit 1";
$rsc = mysqli_query($con,$sel);

$cnt = 0;
while($record = mysqli_fetch_assoc($rsc))
{
    $updid = $record['id']; 
    $state_name = $record['state_name']; 
    $dist_name = $record['dist_name'];
    $div_name = $record['div_name'];
    $place = $record['area_name'];
    $pin = $record['pin'];
    
    $sel_state_id = "SELECT state_id,region_id FROM `state_master` WHERE state_name='$state_name' limit 1";
    $rsc_state_det = mysqli_query($con,$sel_state_id);
    $state_det = mysqli_fetch_assoc($rsc_state_det);
    $state_id = $state_det['state_id']; 
    
    $dist_qry = "SELECT dist_id FROM `district_master` WHERE state_id='$state_id' AND dist_name='$dist_name' limit 1";
    $rsc_dist_det = mysqli_query($con,$dist_qry);
    $dist_det = mysqli_fetch_assoc($rsc_dist_det);
    $dist_id = $dist_det['dist_id'];
    
    if(empty($dist_id))
    {
        $ins_dist = "INSERT INTO `district_master` SET state_id='$state_id',div_name='$div_name',dist_name='$dist_name'";
        $rsc_dist_det = mysqli_query($con,$ins_dist);
        $dist_id =  mysqli_insert_id($this->conn);        
    }
    else
    {
        $upd_dist = "update `district_master` SET div_name='$div_name' where dist_id='$dist_id' limit 1";
        $rsc_dist_det = mysqli_query($con,$upd_dist);
    }
  
    $sel_pin = "select * from pincode_master where state_id='$state_id' and dist_id='$dist_id' and place='$place' and pincode='$pin' limit 1";
    $rsc_pin = mysqli_query($con,$sel_pin);
    $pin_exist = mysqli_fetch_assoc($rsc_pin);
    
    if(empty($pin_exist))
    {
        $ins_pin = "INSERT INTO `pincode_master` SET Country_Id='1',state_id='$state_id',dist_id='$dist_id',place='$place',pincode='$pin',pin_status='1'"; 
        $rsc_ins_det = mysqli_query($con,$ins_pin);
        $cnt++;
    }
    
    $upd = "UPDATE `district_master_upload` SET upd='0' WHERE id='$updid' LIMIT 1;";
    $rsc_upd_ = mysqli_query($con,$upd);
}

echo $cnt++;

?>