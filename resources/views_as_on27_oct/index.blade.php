<?php 
//include("include/connection.php");
//include("include/function.php");
//session_start();

// username and password sent from form 
//$myusername=trim($_POST['usercode']); 
//$mypassword=trim($_POST['Pass']); 
//$ipAddress = $_SERVER['REMOTE_ADDR'];
//$msg = $_GET['msg'];

// date("Y-m-d","23-Oct-2015");



// To protect MySQL injection (more detail about MySQL injection)
//$myusername = stripslashes($myusername);
//$mypassword = stripslashes($mypassword);
//$myusername = mysql_real_escape_string($myusername);
//$mypassword = mysql_real_escape_string($mypassword);

//if($_POST['login']!='')
// {
//    
//	$qry="SELECT * FROM tbl_user WHERE UserId='$myusername' AND Password='$mypassword' AND   UserStatus='1' and (UserType='Master'|| UserType='Franchise')";  
//	$result=mysql_query($qry);
//
//		if(mysql_num_rows($result) > 0) 
//	    {    
//			
//                        
//
//                        $member = mysql_fetch_assoc($result);
//			$_SESSION['SESS_ID']       = $member['Id'];
//			$_SESSION['SESS_TYPE']     = $member['UserType'];
//			$_SESSION['SESS_NAME']     = $member['UserName'];
//			//$_SESSION['DisplayName']   = $member['DisplayName'];
//			$_SESSION['Session_Code']  = $member['UCode'];
//			$_SESSION['timeout']	   = time();
//			$_SESSION['loginId'] 	   = $lastId;
//			$flag = false;
//			$msg = '';
//			
//			header("location: home.php");
//			
//
//            
//            }
//		else 
//		{
//    	header("location: index.php?msg=unsucc");
//		}
//		
// }
?>

<?php
//$qry = mysql_query("select * from background_image_master where status='1'");
//$imgdata = mysql_fetch_array($qry);
?>

<!DOCTYPE html>
<html lang="en">
    
<head>
        <title>Paypik Admin</title>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" href="assets/css/bootstrap.min.css" />
		<link rel="stylesheet" href="assets/css/bootstrap-responsive.min.css" />
        <link rel="stylesheet" href="assets/css/maruti-login.css" />
    </head>
    <body>
        <div id="loginbox">            
            <form id="loginform" class="form-vertical" method="post">
				 <div class="control-group normal_text"> <h3><img src="assets/img/logo.png" alt="Logo" /></h3></div>
                <div class="control-group">
                    <div class="controls">
                        <div class="main_input_box">
                            <span class="add-on"><i class="icon-user"></i></span><input type="text" placeholder="Username" name="usercode" />
                        </div>
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <div class="main_input_box">
                            <span class="add-on"><i class="icon-lock"></i></span><input type="password" placeholder="Password" name="Pass" />
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    
                    <span class="pull-right"><input type="submit" class="btn btn-success" value="Login" name="login" /></span>
                </div>
            </form>
            <form id="recoverform" action="#" class="form-vertical">
				<p class="normal_text">Enter your e-mail address below and we will send you instructions how to recover a password.</p>
				
                    <div class="controls">
                        <div class="main_input_box">
                            <span class="add-on"><i class="icon-envelope"></i></span><input type="text" placeholder="E-mail address" />
                        </div>
                    </div>
               
                <div class="form-actions">
                    <span class="pull-left"><a href="#" class="flip-link btn btn-inverse" id="to-login">&laquo; Back to login</a></span>
                    <span class="pull-right"><input type="submit" class="btn btn-info" value="Recover" /></span>
                </div>
            </form>
        </div>
        
        <script src="assets/js/jquery.min.js"></script>  
        <script src="assets/js/maruti.login.js"></script> 
    </body>

</html>
