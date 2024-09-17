<?php
require_once('mailer/class.phpmailer.php');

function send_email($emaildata)
{
	$mailBody = $emaildata['EmailText'];
	$mailBody = str_replace("\\",'',$mailBody);

	// Send Email
	$mail = new PHPMailer();

	$mail->IsHTML(true);
	$mail->IsSMTP();

	$mail->Host 	 = "smtp.teammas.in";
	$mail->SMTPDebug = 1; 
	$mail->SMTPAuth  = true;
	$mail->Host      = "smtp.teammas.in";
	$mail->Port      = 587;
	$mail->Username  = "ispark@teammas.in";
	$mail->Password  = "abc@123#1";
		
	$mail->SetFrom($emaildata['SenderEmail']['Email'],$emaildata['SenderEmail']['Name']);
	$mail->AddReplyTo($emaildata['ReplyEmail']['Email'],$emaildata['ReplyEmail']['Name']);
	$mail->Subject = $emaildata['Subject'];

	$mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
	$mail->MsgHTML($mailBody);
	
	$mail->AddAddress($emaildata['ReceiverEmail']['Email']);
	if(array_key_exists('AddTo',$emaildata))
	{
		$addto=$emaildata['AddTo'];
		foreach($addto as $v) { $mail->AddAddress($v); }
	}	
	if(array_key_exists('AddCc',$emaildata))
	{
		$addcc=$emaildata['AddCc'];
		foreach($addcc as $v) { if($v!='') { $mail->AddCC($v); } }
	}
	if(array_key_exists('AddBcc',$emaildata))
	{
		$addbcc=$emaildata['AddBcc'];
		foreach($addbcc as $v) { if($v!='') { $mail->AddBCC($v); } }
	}
	//if($emaildata['Attachment']!="") { $mail->AddAttachment($emaildata['Attachment']); }
	if(array_key_exists('Attachment',$emaildata))
	{
		$addattachment=$emaildata['Attachment'];
		if(is_array($emaildata['Attachment']))
		{
		foreach($addattachment as $v) { $mail->AddAttachment($v); }
		}
		else
		{
		 $mail->AddAttachment($emaildata['Attachment']);
		}
	}
	
	if(!$mail->Send()) { $msg="Mailer Error: " . $mail->ErrorInfo; } 
	else { $msg= "1";}

	return $msg; 
}


//	ob_start();
//	include("a3c-mis-sheet.php");
//	$excel_sheet=ob_get_clean();
//	file_put_contents($filename,$excel_sheet);

	// CSM List
?> 