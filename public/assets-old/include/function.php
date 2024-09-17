<?php

function get_fosid($id)
{
    $sql = "SELECT DisplayName FROM user_master WHERE UserId = '$id'";
    $rsc = mysql_query($sql);
    $DT = mysql_fetch_array($rsc);
    return $DT['DisplayName'];
}

function send_sms($smsdata)
{
	$ReceiverNumber=$smsdata['ReceiverNumber'];
/*	$len=strlen($ReceiverNumber);
	$ReceiverNumber=substr($ReceiverNumber,$len-10,10);

	if(strlen($ReceiverNumber)<11) { $ReceiverNumber='91'.$ReceiverNumber; } */

	$SmsText=$smsdata['SmsText'];

	$postdata = http_build_query(
	array(
		'uname'=>'MascKarnal',
		'pass'=>'i5J1j(l)',
		'send'=>'DDKMAS',
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
	
	return $result = file_get_contents('http://125.17.14.100/SendSMS/sendmsg.php', false, $context);
}


function Zip($source, $destination)
{
    if (!extension_loaded('zip') || !file_exists($source)) {
        return false;
    }

    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
        return false;
    }

    $source = str_replace('\\', '/', realpath($source));

    if (is_dir($source) === true)
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
        
        foreach ($files as $file)
        {
            /*if (is_dir($file) === true)
            {
                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
				//echo $file."<br/>";
            }
            else */if (is_file($file) === true)
            {
				//echo $file."<br/>";
                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
            }
        }
    }
    else if (is_file($source) === true)
    {
        $zip->addFromString(basename($source), file_get_contents($source));
    }

    return $zip->close();
}

function deleteAll($directory, $empty = false) { 

        if(substr($directory,-1) == "/") { 
            $directory = substr($directory,0,-1); 
        } 

        if(!file_exists($directory) || !is_dir($directory)) { 
            return false; 
        } elseif(!is_readable($directory)) { 
            return false; 
        } else { 
            $directoryHandle = opendir($directory); 

            while ($contents = readdir($directoryHandle)) { 
                if($contents != '.' && $contents != '..') { 
                    $path = $directory . "/" . $contents; 

                    if(is_dir($path)) { 
                         deleteAll($path); 
                    } else { 
                        unlink($path); 
                    } 
                } 
            } 

            closedir($directoryHandle); 

            if($empty == false) { 
                if(!rmdir($directory)) { 
                    return false; 
                } 
            } 

            return true; 
        } 
    } 





?>
