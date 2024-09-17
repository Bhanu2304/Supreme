<?php
$con = mysqli_connect("localhost","root","mas123") or die("connection not found");
$db = mysqli_select_db($con,"db_aiwa") or die("error in database"); 
?>