<?php

ini_set('error_reporting',true);
 include_once('send_email.php');

$con = mysqli_connect("localhost","root","mas123") or die("connection not found");
$db = mysqli_select_db($con,"db_aiwa") or die("error in database"); 
      


$sel = "SELECT TagId,Pincode FROM tagging_master WHERE mail_send='1'";  
$Rsc    = mysqli_query($con,$sel);
 $PermissionableField = array('Customer_Name'=>'Customer Name','Pincode'=>'Pincode','Contact_No'=>'Contact No','State'=>'State','City'=>'City','email'=>'Email','Product'=>'Product');

 while($Data=mysqli_fetch_assoc($Rsc))
{
     
    $TagId = $Data['TagId'];
    $Pincode = $Data['Pincode'];
    
    $sel2 = "SELECT vendor_id FROM pincode_master WHERE pincode='$Pincode' limit 1";  
    $Rsc2    = mysqli_query($con,$sel2);
    $Data2=mysqli_fetch_assoc($Rsc2);
    $vendor_id = $Data2['vendor_id'];
    
    $sel3 = "SELECT email FROM users WHERE id='$vendor_id' limit 1";  
    $Rsc3    = mysqli_query($con,$sel3);
    $Data3=mysqli_fetch_assoc($Rsc3);
    $email = $Data3['email'];
    
     
    $To = $email;
    //$CC = explode(',','krishna.kumar@teammas.in');
    
     
    $html .= '<table border="2">';
    $html .= '<tr>';
    foreach($PermissionableField as $Label)
    {
        $html .= "<th>$Label</th>";
    }
    $html .= '</tr>';  //exit;


    foreach($data as $record)
    {
        $html .= '<tr>';
        foreach($PermissionableField as $field=>$Label)
        {
            $html .= "<td>".$record->$field."</td>";   
        }
        $html .= '</tr>'; 
    }
                
                
                
    $html .= '</table>'; 
    
    
    $EmailText ='Kindly find these  details.';
    
    $EmailText .="<br/>";
    $EmailText .="<br/>";
    $EmailText .=$html;
    $EmailText .="<br/>";
    $EmailText .="<br/>";

    
    $ReceiverEmail=array('Email'=>$To,'Name'=>''); 
    $SenderEmail=array('Email'=>'ispark@teammas.in','Name'=>'AIWA'); 
    $ReplyEmail=array('Email'=>'ispark@teammas.in','Name'=>'AIWA'); 
    $AddCc = array(); $AddBcc = array();
    //$AddCc +=$CC; 
    

    
    
        
    $Subject="Order Not Dispatched";  
    
    
    
    
    
    $EmailText .="<br/>";
    $EmailText .="<br/>";
    $EmailText .="Regards";
    $EmailText .="<br/>";
    $EmailText .="Dialdesk";
    //$EmailText .=$text;
    $emaildata=array('ReceiverEmail'=> $ReceiverEmail,'SenderEmail'=> $SenderEmail,'ReplyEmail'=> $ReplyEmail,'Subject'=> $Subject,'EmailText'=> $EmailText);
    
    if(!empty($AddCc))
    {
        $emaildata['AddCc'] =  $AddCc;
    }
    
    try{
        $done = send_email( $emaildata);
        mysqli_query($con,"update tagging_master set mail_send='0',mail_send_status='success' where TagId='$TagId' limit 1");
    }
    catch (Exception $e){
        mysqli_query($con,"update tagging_master set mail_send='0',mail_send_status='fail' where TagId='$TagId' limit 1");
       //echo $error = $e.printStackTrace();
        //mysqli_query("insert into bill_summary_send_report_master(Email,MailStatus,CreateDate)values('$email','$error',now())");
    }
 }   

 
 
?>


