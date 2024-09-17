<html>
<head>
<style>
th#t01 {
    border-right:1px solid black;
	border-bottom:1px solid black;
}
td#t02
{
	border-right:1px solid black;
}
</style>
<style>
th#t03
{
	border-bottom:1px solid black;

}
td#t08
{
	border-top:1px solid black;
	border-right:1px solid black;
	border-left:1px solid black;
	border-bottom :1px solid black;
	font-weight: bold;
	padding-left: 1px;
}
th#t04
{
	border: 1px solid black;
}
th#t05
{
	border-bottom:1px solid black;
	border-Left:1px solid black;
	border-Top:1px solid black;
}
td#t06
{
	border-bottom:1px solid black;
	border-Top:1px solid black;
}
td#t07
{
	border-bottom:1px solid black;
}
tr#t09
{
	border-top:1px solid black;
}
td#t10
{
	border-bottom:1px solid black;
	border-right: 1px solid black;
}

</style>
</head>
<body style="font-size:13px;  font-family:Arial, Helvetica, sans-serif;">
  
<table cellpadding="0" cellspacing="0">
    
	<tr>
		<td>
			<table>
			<tr>
				
				<td align="centre" id="t07" style="font-size:12px; ">
                                <b>SERVICE JOB SHEET</b>         
				</td>

				<td align="right" style="padding-left: 370px;font-size:14px; ">
					<b>CUSTOMER COPY</b>	</td>
			</tr>
			</table>
		</td>	
	</tr>
</table>
<br>

<table width="550" cellpadding="0" cellspacing="0">
<tr>
<td width="270" id="t02" style="font-size:24px; ">
	12835

</td>

<td width="270" align="centre" style="font-size:24px; font-weight: bolder;">
	    
		JVC    Clarion
	
</td>

	</tr>

<tr>
	<td colspan="2">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td width="108" id="t08">
					Job No.
					<br>
					{{$data->job_id}}
				</td>
				<td  width="108" id="t08">
					DATE
					<br>
					{{$data->ob_date}}
				</td>
				<td  width="108" id="t08">
					MODEL No.
					<br>
					{{$data->Contact_No}}
				</td>
				<td  width="108" id="t08">
					Sr. No.
					<br>
					{{$data->job_id}}
				</td>
				<td  width="108" id="t08">
					PURCHASE DATE
					<br>
					{{$data->Bill_Purchase_Date}}
				</td>
			</tr>

		</table>

	</td>

</tr>
<tr>
	<td colspan="2" id="t07">
		CUSTOMER NAME, ADDRESS & CONTACT NO.
		<br>
		{{$data->Customer_Name}}
		<br>
		{{$data->Customer_Address}}&nbsp;&nbsp;{{$data->Customer_Address_Landmark}}&nbsp;&nbsp;{{$data->City}}&nbsp;&nbsp;{{$data->State}}&nbsp;&nbsp;{{$data->Pincode}}
		<br>
		{{$data->Contact_No}}
	</td>
</tr>

<tr>
	<td colspan="2" id="t07">
		CONDITION OF SET
		<br>
		<?php $set_cndn_json = stripslashes($data->set_conditions);
                      $set_list = json_decode($set_cndn_json,true); 
                      
                      foreach($set_list as $setkey=>$set)
                      {
                          $str_set[] = "$setkey:$set";
                      }
                      echo implode(",",$str_set);
                ?>
	</td>
</tr>
	
<tr>
	<td colspan="2" id="t07">
		CUSTOMER COMPLAINT
		<br>
		{{$data->add_cmnt}}
		<br>
		ADD
	</td>
</tr>

<tr>
	<td>
	<table>	
		<tr>
	<td id="t10" width="270">
		ACCESSORY LIST 
                <?php $acc_list_json = stripslashes($data->accesories_list);
                      $acc_list = json_decode($acc_list_json,true); 
                      $a = 0;
                      $cnt = count($acc_list);
                      $acc_keys = array_keys($acc_list);
                      for($i=0;$i<($cnt/2);$i=$i+2)
                      {
                          if($a%2==0)
                          {
                              echo '<br/>';
                          }
                          echo '<input type="checkbox" ';
                          
                          if($acc_list[$acc_keys[$i]]=='Yes' && $acc_list[$acc_keys[$i+1]]=='Available')
                          {
                              echo 'checked';
                          }
                          
                          echo  '> &nbsp;';
                          echo $acc_keys[$i];
                          echo '&nbsp;&nbsp;&nbsp;';
                          $a++;
                      }
                ?>
                
	</td>
		</tr>
		<tr>
	<td id="t10" width="270">
		Special Remarks If Any
		<br>
		{{$data->add_cmnt}}
	</td>
		</tr>

		<tr>
	<td id="t10" width="270">
		I accept the terms & condition
		<br>

		(Sign. Customer)
	</td>
		</tr>
	</table>
</td>
<td>
	<table>	
			<tr>
	<td id="t07" width="270" colspan="3">
		Del. Date/
		(Received by Customer/Authorised Person)
		<br>
		
	</td>
			</tr>
			<tr>
	<td id="t10">
		DEL. DATE
		<br>
		
	</td>
	<td id="t10">
		GATE PASS No.
		<br>
		
	</td>
	<td id="t07">
		CHALLAN No.
		<br>
		
	</td>
		</tr>
	</table>
</td>
</tr>



<tr>

	<td colspan="2" style="font-weight: bold; font-size: 14px;"><br><br> Delivery Hours Monday to Saturday 10.30 hrs. to 17.30 hrs.</td>

</tr>

<tr>
	
	<td colspan="2" style="font-weight: bold; font-size: 16px; text-align: center;"><br><br> Terms & Conditions <br><br></td>

</tr>

<tr>	
	<td colspan="2">1. Article will be delivered on submission of this Receipt only.</td>
</tr>
<tr>	
	<td colspan="2">2. Article will be delivered strictly on cash payment (for out of warranty & samaged cases)</td>
</tr>
<tr>	
	<td colspan="2">3. Any estimate issued by thr Management is provisional and not binding</td>
</tr>
<tr>	
	<td colspan="2">4. All Articles are taken & stored at Owner's risk. The Company shall not be responsible for good damaged by accident fire,transit & theft.</td>
</tr>
<tr>	
	<td colspan="2">5. <b>Check up fee i.e. 50% of Service Charges,</b> as explained at the counter will be charged to items for which estimate is not approved by the customer</td>
</tr>
<tr>	
	<td colspan="2">6. Good sent in for repair with out valid warranty Card/Purchase Invoice will be charged accordingly</td>
</tr>
<tr>	
	<td colspan="2">7. Tampered/Water Logged sets will not be accepted for repair under Warranty</td>
</tr>
<tr>	
	<td colspan="2">8. <b>Defective Components will not be returned to the customer </b></td>
</tr>
<tr>	
	<td colspan="2">9. Full performance of the set to be checked gefore taking delivery service centre will not be responsible after delivery</td>
</tr>
<tr>	
	<td colspan="2">10. Parts which are not readily available & are to be imported order will be placed noly after advance has been deposited by the customer </td>
</tr>
<tr>	
	<td colspan="2">11. Company shall not be responsible for the Optional Block, if found defective during repairs</td>
</tr>

</table>


</body>
</html>