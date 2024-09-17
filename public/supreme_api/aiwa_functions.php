<?php

class Dialer_Functions{
    
    public $conn = '';
    
    public function jsonError()                    //For Json Error
{
    $error='';
switch (json_last_error()) {
        case JSON_ERROR_NONE:
           $error='';
        break;
        case JSON_ERROR_DEPTH:
            $error= ' - Maximum stack depth exceeded';
        break;
        case JSON_ERROR_STATE_MISMATCH:
            $error=' - Underflow or the modes mismatch';
        break;
        case JSON_ERROR_CTRL_CHAR:
            $error= ' - Unexpected control character found';
        break;
        case JSON_ERROR_SYNTAX:
            $error=' - Syntax error, malformed JSON';
        break;
        case JSON_ERROR_UTF8:
            $error= ' - Malformed UTF-8 characters, possibly incorrectly encoded';
        break;
        default:
            $error= ' - Unknown error';
        break;
    }
    return $error;
}
    
    
    public function dbConnect(){
            $this->conn = mysqli_connect("localhost","root","mas123");
            if (!$this->conn) {
               echo "Unable to connect to DB: " . mysqli_error();
               exit;
            }

            if (!mysqli_select_db($this->conn,"db_aiwa")) {
               echo "Unable to select db_aiwa: " . mysqli_error();
               exit;
            }
    }

    public function send_sms($smsdata){
            $ReceiverNumber=$smsdata['ReceiverNumber'];
            $len=strlen($ReceiverNumber);
            $ReceiverNumber=substr($ReceiverNumber,$len-10,10);

            if(strlen($ReceiverNumber)<11) { $ReceiverNumber='91'.$ReceiverNumber; }

            $SmsText=$smsdata['SmsText'];

            $postdata = http_build_query(
            array(
                    'uname'=>'MasCall',
                    'pass'=>'M@sCaLl@234',
                    'send'=>'Ispark',
                    'dest'=>$ReceiverNumber,
                    'msg'=>$SmsText
            )
            );

            $opts = array('http' =>
            array(
                    'method'  => 'POST',
                    'header'  => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $postdata
            )
            );

            $context  = stream_context_create($opts);

            return $result = file_get_contents('http://www.unicel.in/SendSMS/sendmsg.php', false, $context);
    }
    
    
    public function save_detail()
    {
        $post = implode(',',$_POST);
        $keys = implode(',',array_keys($_POST));
        $sql = mysqli_query($this->conn,"insert into todo set todo_test='$keys'");
        $sql = mysqli_query($this->conn,"insert into todo set todo_test='$post'");
    }
    
    
    public function userLogin(){ 
        
        $user = $_REQUEST['user'];
        $pass = $_REQUEST['pass'];
        
        $sql = "SELECT * FROM tbl_service_engineer WHERE email='$user' AND pass='$pass' limit 1";
        $result=mysqli_query($this->conn,$sql); 
        $record = mysqli_fetch_assoc($result);
        
        
        
        if(empty($record))
        {
            print_r(json_encode(array('status' => 'Failed','errorMsg'=>"Either User or PASSWORD is Invalid",'se_id'=>$record['se_id'],'vendor_id'=>$record['vendor_id'],'display_name'=>$record['se_name'],'prof_photo'=>$record['profile_photo']))); exit;
        }
        else
        {   
            print_r(json_encode(array('status' => 'Success','errorMsg'=>"Login Successfully.",'se_id'=>$record['se_id'],'vendor_id'=>$record['vendor_id'],'display_name'=>$record['se_name'],'prof_photo'=>$record['profile_photo']))); exit;
        }
        
        
        exit;
       // $insTime = mysqli_query($this->conn,"INSERT INTO `login_master` SET userid='$username',IPAddress='$ipAddress',loginDate=CURDATE(),loginTime=TIME(NOW()),loginStatus='$loginStatus'");
    }
    
    
    
    
    public function get_case(){ 
        
        $se_id = $_REQUEST['se_id'];
    
        $sel = "SELECT TagId,
se_id,
allocation_date,
allocate_by,
re_allocation_date,
re_allocate_by,
mail_send,
mail_send_status,
tag_type,
Customer_Category,
Pincode,
Customer_Name,
Customer_Address,
Customer_Address_Landmark,
Contact_No,
Alternate_Contact_No,
State,
City,
email,
Residence_No,
Gst_No,
Registration_Name,
Ext1,
Ext2,
Ext3,
Product,
Brand,
Serial_No,
Model,
Bill_Purchase_Date,
Warrenty_End_Date,
Date_of_Installation,
call_type,
amc_no,
amc_expiry_date,
warranty_status,
dealer_name,
invoice_no,
service_type,
entity_name,
accessories_required,
product_remark,
Ext4,
Ext5,
Ext6,
tag_voc,
remark,
device_image,
invoice_copy,
invoice_copy warranty_card_copy,
product_serial_no,
product_photo1,
product_photo2,
PO_Status,
PO_Type,
Job_Status,
observation,
call_status,
ob_date,
ob_by,
rma_status,
rma_approve,
rma_remark,
rma_date,
rma_by,
part_status,
docket_no,
courier_det,
docket_no consignee_no,
courier_det consignee_det,
part_remark,
part_approve,
part_date,
part_by,
case_close,
created_at,
created_by,
updated_at,
updated_by FROM `tagging_master` WHERE se_id='$se_id' AND send_status='1' limit 100";
        $sql = mysqli_query($this->conn,$sel);

        $case_list = array(); $case_receive = 'fail';
        while($record = mysqli_fetch_assoc($sql))
        {
            $case_receive = 'Success';
            $record['ACTION'] = 'AddData';
            $case_list[] = $record;
            $upd = "update `tagging_master` set send_status='0',send_date=now() where TagId='{$record['TagId']}' limit 1";
            $rsc_upd = mysqli_query($this->conn,$upd);

        }
        
        print_r(json_encode(array('status' => $case_receive,'case_list'=>$case_list))); exit;
        
        
        exit;
        
    }
    
    
    public function save_case()
    {
        $se_id = $_REQUEST['se_id'];
        $TagId = $_REQUEST['TagId'];
        $se_voc = $_REQUEST['se_voc'];
        $se_remark = addslashes($_REQUEST['se_remark']);
        $Lat = $_REQUEST['Lat'];
        $Lon = $_REQUEST['Lon'];
        $se_date = $_REQUEST['se_date'];
        
        $localPath = "/var/www/html/aiwa/storage/app/aiwa/$TagId";
        if (!file_exists($localPath))
        {
            mkdir("$localPath", 0777, true);
        }
        
       
        
        $device_file = $_FILES['device_image'];
        if(!empty($device_file['tmp_name']))
        {   
           $Doc_Name= $device_file['name']; 
           $device_image = $device_file['name']; 
            move_uploaded_file($device_file['tmp_name'], "$localPath/$Doc_Name");
        }
        
        
        $warranty_file = $_FILES['warranty_card_copy'];
        if(!empty($warranty_file['tmp_name']))
        {   
           $Doc_Name= $warranty_file['name']; 
           $warranty_card_copy = $warranty_file['name']; 
            move_uploaded_file($warranty_file['tmp_name'], "$localPath/$Doc_Name");
        }
        
        $serial_no = $_FILES['product_serial_no'];
        if(!empty($serial_no['tmp_name']))
        {   
           $Doc_Name= $serial_no['name']; 
           $product_serial_no = $serial_no['name']; 
            move_uploaded_file($serial_no['tmp_name'], "$localPath/$Doc_Name");
        }
        
        $product_photo1_file = $_FILES['product_photo1'];
        if(!empty($product_photo1_file['tmp_name']))
        {   
           $Doc_Name= $product_photo1_file['name']; 
           $product_photo1 = $product_photo1_file['name']; 
            move_uploaded_file($product_photo1_file['tmp_name'], "$localPath/$Doc_Name");
        }
        
        $product_photo2_file = $_FILES['product_photo2'];
        if(!empty($product_photo2_file['tmp_name']))
        {   
           $Doc_Name= $product_photo2_file['name']; 
           $product_photo2 = $product_photo2_file['name']; 
            move_uploaded_file($product_photo2_file['tmp_name'], "$localPath/$Doc_Name");  
        }
        
        $ins_tag = "insert into tbl_capture_details set TagId='$TagId',se_id='$se_id',se_voc='$se_voc',se_remark='$se_remark',Lat='$Lat',Lon='$Lon',se_date='$se_date',device_image='$device_image',invoice_copy='$warranty_card_copy',product_serial_no='$product_serial_no',product_photo1='$product_photo1',product_photo2='$product_photo2' ";
        $sql_ins = mysqli_query($this->conn,$ins_tag);
        $LastId = mysqli_insert_id($this->conn);
        
        $upd_tag = "update tagging_master set LastId='$LastId',case_tag='1',se_voc='$se_voc',se_remark='$se_remark',Lat='$Lat',Lon='$Lon',se_date='$se_date',device_image='$device_image',invoice_copy='$warranty_card_copy',product_serial_no='$product_serial_no',product_photo1='$product_photo1',product_photo2='$product_photo2' where TagId='$TagId' limit 1";
        $sql_upd = mysqli_query($this->conn,$upd_tag);
        
        if($sql_upd)
        {
            print_r(json_encode(array('status' => 'Success'))); exit;
        }
        else
        {
            print_r(json_encode(array('status' => 'Fail'))); exit;
        }
        exit;
    }
    
    
    public function save_profile()
    {
        $se_id = $_REQUEST['se_id'];
        $profile_name = addslashes($_REQUEST['profile_name']);
        $profile_no = $_REQUEST['profile_no'];
        $prof_email = $_REQUEST['prof_email'];
        $prof_pincode = $_REQUEST['prof_pincode'];
        $prof_active = $_REQUEST['prof_active'];
        
        $localPath = "/var/www/html/aiwa/storage/app/profile/$se_id";
        if (!file_exists($localPath))
        {
            mkdir("$localPath", 0777, true);
        }
        
        $prof_photo_file = $_FILES['profile_photo'];
        
        foreach($_FILES as $file)
        {
            //$FILES_ARR[] = implode('"',$file);
            foreach ($file as $key=>$value)
            {
                $FILES_ARR[] = "$key='$value'";
            }
        } 
        
        $prof_photo = addslashes(implode(',',$FILES_ARR));
        
        if(!empty($prof_photo_file['tmp_name']))
        {   
           $Doc_Name= $prof_photo_file['name']; 
           $prof_photo = $prof_photo_file['name']; 
            move_uploaded_file($prof_photo_file['tmp_name'], "$localPath/$Doc_Name");
            $prof_photo = addslashes($prof_photo); 
            $qr_prof_photo = "profile_photo='$prof_photo',";
        }
        
        
        if(!empty($prof_pincode))
        {
            $qr_prof_pin = "phone='$profile_no',";
        }
        
        if(!empty($profile_name))
        {
            $qr_profile_name = "se_name='$profile_name',";
        }
        
        $ins_tag = "INSERT INTO tbl_profile SET se_id='$se_id',profile_name='$profile_name',profile_no='$profile_no',
        prof_email='$prof_email',prof_pincode='$prof_pincode',prof_photo='$prof_photo',created_at=NOW() 
        ";
        $sql_ins = mysqli_query($this->conn,$ins_tag);
        
        $upd_prof = "update tbl_service_engineer set $qr_prof_photo $qr_prof_pin $qr_profile_name updated_at=now(),updated_by='$se_id' where se_id='$se_id' limit 1";
        $sql_upd = mysqli_query($this->conn,$upd_prof);
        
        if($sql_upd)
        {
            print_r(json_encode(array('status' => 'Success'))); exit;
        }
        else
        {
            print_r(json_encode(array('status' => 'Fail'))); exit;
        }
        exit;
        
    }
    
    public function save_location()
    {
        $se_id = $_REQUEST['se_id'];
        $Lat = $_REQUEST['Lat'];
        $Lon = $_REQUEST['Lon'];
        
        $ins_tag = "INSERT INTO tbl_se_location SET se_id='$se_id',Lat='$Lat',Lon='$Lon',created_at=NOW() ";
        $sql_ins = mysqli_query($this->conn,$ins_tag);
        
        if($sql_ins)
        {
            print_r(json_encode(array('status' => 'Success'))); exit;
        }
        else
        {
            print_r(json_encode(array('status' => 'Fail'))); exit;
        }
        exit;
    }    
    
    
}
?>