<?php
include_once 'aiwa_functions.php';
$db = new Dialer_Functions();
$db->dbConnect();
ini_set('display_errors','Off');
error_reporting(E_ALL);



switch($_REQUEST['req_id']){
    case 1:
    {
    $db->userLogin();
    break;
    }
    
    case 2:
    {
    $db->get_case();
    break;
    }
    
    case 3:
    {
    $db->save_case();
    break;
    }

    case 4:
    {
    $db->save_profile();
    break;
    }
    
    case 5:
    {
    $db->save_location();
    break;
    }
    
    case 3:
    {
    $db->logout();
    break;
    }
}
?>