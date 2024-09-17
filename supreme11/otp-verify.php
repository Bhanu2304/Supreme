<?php
include("include/connection.php");

function send_sms($smsdata){
            $ReceiverNumber=$smsdata['ReceiverNumber'];
            $len=strlen($ReceiverNumber);
            $ReceiverNumber=substr($ReceiverNumber,$len-10,10);

            if(strlen($ReceiverNumber)<11) { $ReceiverNumber='91'.$ReceiverNumber; }

            $SmsText=$smsdata['SmsText'];

            $postdata = http_build_query(
            array(
                    'uname'=>'MasCall',
                    'pass'=>'M@sCaLl@234',
                    'send'=>'mascal',
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


if($_POST)
{
     
    $otp = $_POST['otp'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    $mobile = base64_decode($_POST['mobile']);
    $select = "select * from tbl_register where otp='$otp' and mobile='$mobile'"; 
    
    
    $rsc_reg = mysqli_query($con,$select) ;
    $data = mysqli_fetch_assoc($rsc_reg);
    if($password!=$password2)
    {
        header("location: otp-verify.php?msg=pfail");
    }
    else if(!empty($data))
    {
        $reg_id = base64_encode($data['reg_id']);
        $password = base64_encode($password);
        header("location: public/user-save-password?reg_id=$reg_id&password=$password");
    }
    else
    {
        header("location: otp-verify.php?msg=fail");
    }
    
   
    
}

$msg = $_REQUEST['msg'];
$mobile_enc = $_REQUEST['mobile'];

?>



<html>
    <head>
        <style>
table, th, td {
  
  padding: 4px;
  font-family: Open Sans;
  font-weight: 600;
}
table {
  border-spacing: 15px;
}
</style>

<style>
* {
  box-sizing: border-box;
}

body {
  font: 16px Arial;  
}

/*the container must be positioned relative:*/
.autocomplete {
  position: relative;
  display: inline-block;
}



.autocomplete-items {
  position: absolute;
  border: 1px solid #d4d4d4;
  border-bottom: none;
  border-top: none;
  z-index: 99;
  /*position the autocomplete items to be the same width as the container:*/
  top: 100%;
  left: 0;
  right: 0;
}

.autocomplete-items div {
  padding: 10px;
  cursor: pointer;
  background-color: #fff; 
  border-bottom: 1px solid #d4d4d4; 
}

/*when hovering an item:*/
.autocomplete-items div:hover {
  background-color: #e9e9e9; 
}

/*when navigating through the items using the arrow keys:*/
.autocomplete-active {
  background-color: DodgerBlue !important; 
  color: #ffffff; 
}
</style>



<script>
 
 function validate_otp()
 {
     var otp = document.getElementById("otp").value;
     var password = document.getElementById("password").value;
     var password2 = document.getElementById("password2").value;
     
     if(otp.length!=6)
      {
        alert("Please Fill Valid OTP");
        return false;
      }
      else if(password!=password2)
      {
          alert("Password Not Matched");
        return false;
      }
     
        
    return true;    
 }
 
 
function checkNumber(val,evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode;
	
	if (charCode> 31 && (charCode < 48 || charCode > 57) )
        {            
		return false;
        }
        
	return true;
}
   





</script>
    
    
    </head>
    <body>
        <form method="post" >
        <table cellspacing="2" align="center">
            <tbody>
            
            <tr>
                <td><img src="mas_logo.png" height="50px;" width="100px;" /></td>
            </tr>
            <tr>
                <td><h3 style="font-size: 30px;">Sign me up for a trial account</h3><span style="font-size:20px">Get free call and SMS credits when you sign up.</span></td>
            </tr>
            
            
            
            <tr>
                <td>Password *<br/>
                    <input type="password" style="width: 350px;height: 40px;" autocomplete="off" id="password" name="password"  value="" placeholder="Password" required="" />
                </td>
            </tr>
            <tr>
                <td>Confirm Password *<br/>
                <input type="password" style="width: 350px;height: 40px;" autocomplete="off" id="password2" name="password2" placeholder="Confirm Password" value="" required="" />
                </td>
            </tr>
            <tr>
                <td>OTP* <?php if(!empty($msg)) { echo '<h5><font color="red">OTP Not Valid. Please Try Again!</font></h5>'; } ?><br/>
                    <input type="text" style="width: 350px;height: 40px;" autocomplete="off" id="otp" name="otp" placeholder="OTP" onkeypress="return checkNumber(this.value,event)" value="" required="" />
                </td>
            </tr>
            
            
            <tr>
                <td><button onclick="return validate_otp()" id="signup" name="signup" value="Verify" style="color: white;background-color: green;width: 350px;height: 40px;">Verify</button></td>
            </tr>
            </tbody>
        </table>
            <input type="hidden" name="mobile" id="mobile" value="<?php echo $mobile_enc; ?>" />
            </form>
    </body>
    
</html>

