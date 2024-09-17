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

</style>
</head>
<body style="font-size:11px;  font-family:Arial, Helvetica, sans-serif;">
  
<table cellpadding="0" cellspacing="0" >
    
	<tr>
		<td>
			<table>
			<tr>
				<td width="370">
                       <b>Pioneer</b>         
				</td>

				<td align="right">
					Customer's Copy	</td>
			</tr>
			</table>
		</td>	
	</tr>
</table>
<br>

<table width="550" cellpadding="0" cellspacing="4">
<tr>
<td width="270" id="t08">
	<table>
		<tr>
			<td>
				REPAIR NO
			</td>
			<td id="t07" width="170">
				{{$data->job_id}}
			</td>
		</tr>
		<tr>
			<td>
				SHOWROOM
			</td>
			<td id="t07" width="170">
				{{$data->center_id}}
			</td>
		</tr>
		<tr>
			<td>
				ASC CODE
			</td>
			<td id="t07" width="170">
				{{$data->asc_code}}
			</td>
		</tr>
		<tr>
			<td colspan="2">
				
			</td>
			
		</tr>
		<tr id="t09">
			<td colspan="2"><hr></td>
		</tr>
		<tr>
			<td>
				NAME
			</td>
			<td id="t07" width="170">
				{{$data->Customer_Name}}
			</td>
		</tr>
		<tr>
			<td>
				ADDRESS
			</td>
			<td id="t07" width="170">
				{{$data->Customer_Address}}
			</td>
		</tr>
		<tr>
			<td>
				
			</td>
			<td id="t07" width="170">
				{{$data->City}} {{$data->State}} {{$data->Pincode}}
			</td>
		</tr>
		<tr>
			<td>
				
			</td>
			<td id="t07" width="170">
				{{$data->Customer_Address_Landmark}}
			</td>
		</tr>
		<tr>
			<td>
				TEL (H)
			</td>
			<td id="t07" width="170">
				{{$data->Contact_No}}
			</td>
		</tr>
		<tr>
			<td>
				TEL (O)
			</td>
			<td id="t07" width="170">
				{{$data->Contact_No}}
			</td>
		</tr>
		<tr>
			<td>
				MOBILE
			</td>
			<td id="t07" width="170">
				{{$data->Contact_No}}
			</td>
		</tr>
	</table>	

</td>

<td width="270" id="t08">
	<table>
		<tr>
			<td>
				PURCHASE DATE
			</td>
			<td id="t07" width="140">
				{{$data->Bill_Purchase_Date}}
			</td>
		</tr>
		<tr>
			<td>
				RECEIVE DATE
			</td>
			<td id="t07" width="140">
				{{$data->ob_date}}
			</td>
		</tr>
		<tr>
			<td>
				SEND TO ASC DATE
			</td>
			<td id="t07" width="140">
				
			</td>
		</tr>
		<tr>
			<td>
				RECEIVE FROM ASC DATE
			</td>
			<td id="t07" width="140">
				
			</td>
		</tr>
			<tr id="t09">
			<td colspan="2"><hr></td>
		</tr>
		<tr>
			<td>
				MODEL
			</td>
			<td id="t07" width="140">
				{{$data->Model}}
			</td>
		</tr>
		<tr>
			<td>
				S/NO
			</td>
			<td id="t07" width="140">
				{{$data->Serial_No}}
			</td>
		</tr>
		<tr>
			<td>
				WARRANTY NO
			</td>
			<td id="t07" width="140">
				{{$data->warranty_card}}
			</td>
		</tr>
		<tr>
			<td>
				<input type="checkbox"> &nbsp;&nbsp; CHARGE
			</td>
			<td width="140">
				<input type="checkbox"> &nbsp;&nbsp; REPEAT
			</td>
		</tr>
		<tr>
			<td>
				<input type="checkbox"> &nbsp;&nbsp; WARRANTY
			</td>
			<td id="t07" width="140">
				<input type="checkbox"> &nbsp;&nbsp; OTHERS
			</td>
		</tr>
		<tr>
			<td>
				EXPIRY DATE
			</td>
			<td id="t07" width="140">
				TEST
			</td>
		</tr>
		<tr>
			<td>
				ESTIMATED REPAIR CHARGE
			</td>
			<td id="t07" width="140">
				{{$data->estmt_charge}}
			</td>
		</tr>
	</table>        

	
</td>

	</tr>

<!----- 2ND CELL START ///////////////////////////////////////////////  --->

<tr>
<td width="270" id="t08">
	<table>
		<tr>
			<td colspan="2">
				SERVICE REQUIRED / SYMPTOMS
			</td>
			
		</tr>
		<tr>
			<td id="t07" colspan="2">
				{{$data->service_required}}
			</td>
		</tr>
		<tr>
			<td id="t07" colspan="2">
				{{$data->Symptom}}
			</td>
		</tr>
		<tr>
			<td id="t07" colspan="2">
				{{$data->Symptom}}
			</td>
		</tr>
		<tr>
			<td id="t07" colspan="2">
				{{$data->Symptom}}
			</td>
		</tr>
	</table>	

</td>

<td width="270" id="t08">
	<table>
		<tr>
			<td>
				CONDITION OF SET
			</td>
			<td id="t07" width="140">
				
			</td>
		</tr>
                
                <?php $set_cndn_json = stripslashes($data->set_conditions);
                      $set_list = json_decode($set_cndn_json,true); 
                ?>
                
                <?php 
                    foreach($set_cndn as $cndn=>$cndn_value)
                    {
                        
                    
                ?>
                
		<tr>
			<td colspan="2">
				<?php echo $cndn; $a = 1;
                                foreach($cndn_value as $val)
                                { 
                                    if($a%2==0)
                                    {
                                        echo '<br/>';
                                    }
                                    $a++;
                                ?>
                                <input type="checkbox" <?php if($set_list[$cndn]==$val) { echo "checked";} ?>> {{$val}}
                          <?php } ?>
			</td>
			
		</tr>
                    <?php } ?>
                
                <tr>
			<td>
				ACCESSORY
			</td>
			<td id="t07" width="140">
				
			</td>
		</tr>
		<tr>
                    <td colspan="2">
		<?php $acc_list_json = stripslashes($data->accesories_list);
                      $acc_list = json_decode($acc_list_json,true); 
                      
                      $a = 0;
                      $cnt = count($acc_list); 
                      $acc_keys = array_keys($acc_list);
                      
                      //print_r($acc_list); exit;
                      
                      for($i=0;$i<($cnt/2);$i=$i+2)
                      {
                          if($a%3==0)
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
	</table>        
	
</td>

	</tr>

<!----- 3RD CELL START ///////////////////////////////////////////////  --->
<tr>
	<td id="t08" colspan="2">
	<table width="540">
		<tr>
			<td colspan="4">
				COMMENTS FROM CUSTOMER
			</td>
			
		</tr>
		<tr>
			<td id="t07" colspan="4">
				{{$data->add_cmnt}}
			</td>
		</tr>
		<tr>
			<td id="t07" colspan="4">
				{{$data->add_cmnt}}
			</td>
		</tr>
		<tr>
			<td id="t07" colspan="4">
				{{$data->add_cmnt}}
			</td>
		</tr>
		<tr>
			<td id="t07" colspan="4">
				{{$data->add_cmnt}}
			</td>
		</tr>
		<tr>
			<td>
				CUSTOMER SIGNATURE
			</td>
			<td id="t07">
				Test
			</td>
			<td>
				DATE
			</td>
			<td id="t07">
				Test
			</td>
		</tr>
	</table>	

</td>

</tr>


<!----- 4TH CELL START ///////////////////////////////////////////////  --->
<tr>
	<td id="t08" colspan="2">
	<table width="540">
		<tr>
			<td colspan="4">
				
					<p style="font: bold;">IMPORTANT NOTICE :-</p>
					
					<p>
						Your Pioneer product shall, in the event pf your deposit of the same at our service centre, be
						collected at our service centre within twelve (12) weeks from the date of notification of the 
						completion of repair / service or from the date of which instructions were given to us not to 
						proceed with repair / service, failing which we shall be at liberty to deal with and or otherwise
						dispose of the same at our absolute discretion without any liability to you.

					</p>
					
					<p style="font: bold;">Please refer to the Terms and conditions of service stated overleaf</p>	
			</td>
			
		</tr>
		
	</table>	

</td>

</tr>

<!----- 5TH CELL START ///////////////////////////////////////////////  --->

<tr>
<td width="270" id="t08">
	<table>
		<tr>
			<td>
				REPAIR NO
			</td>
			<td id="t07" width="170">
				{{$data->job_id}}
			</td>
		</tr>
		<tr>
			<td colspan="2">
				ACCESSORY
				<input type="checkbox" name=""> REMOTE
				<input type="checkbox" name=""> ANTENNA
				<input type="checkbox" name=""> A/C CORD
				<input type="checkbox" name=""> RF COED
			</td>
			
		</tr>
		<tr>
			<td colspan="2">
				
				<input type="checkbox" name=""> AV CORD
				<input type="checkbox" name=""> SPEAKER CORD
				<input type="checkbox" name=""> OPERATIONAL MANUAL
			</td>
			
		</tr>
		<tr>
			<td>
				OTHERS
			</td>
			<td id="t07" width="140">
				TEST
			</td>
		</tr>
		<tr id="t09">
			<td colspan="2"><hr></td>
		</tr>
		<tr>
			<td>
				REPAIR NO
			</td>
			<td id="t07" width="170">
				Test
			</td>
		</tr>
		<tr>
			<td colspan="2">
				ACCESSORY
				<input type="checkbox" name=""> REMOTE
				<input type="checkbox" name=""> ANTENNA
				<input type="checkbox" name=""> A/C CORD
				<input type="checkbox" name=""> RF COED
			</td>
			
		</tr>
		<tr>
			<td colspan="2">
				
				<input type="checkbox" name=""> AV CORD
				<input type="checkbox" name=""> SPEAKER CORD
				<input type="checkbox" name=""> OPERATIONAL MANUAL
			</td>
			
		</tr>
		<tr>
			<td>
				OTHERS
			</td>
			<td id="t07" width="140">
				TEST
			</td>
		</tr>
	</table>	

</td>

<td width="270" id="t08">
	<table>
		<tr>
			<td>
				REPAIR NO
			</td>
			<td id="t07" width="170">
				Test
			</td>
		</tr>
		<tr>
			<td colspan="2">
				ACCESSORY
				<input type="checkbox" name=""> REMOTE
				<input type="checkbox" name=""> ANTENNA
				<input type="checkbox" name=""> A/C CORD
				<input type="checkbox" name=""> RF COED
			</td>
			
		</tr>
		<tr>
			<td colspan="2">
				
				<input type="checkbox" name=""> AV CORD
				<input type="checkbox" name=""> SPEAKER CORD
				<input type="checkbox" name=""> OPERATIONAL MANUAL
			</td>
			
		</tr>
		<tr>
			<td>
				OTHERS
			</td>
			<td id="t07" width="140">
				TEST
			</td>
		</tr>
		<tr id="t09">
			<td colspan="2"><hr></td>
		</tr>
		<tr>
			<td>
				REPAIR NO
			</td>
			<td id="t07" width="170">
				Test
			</td>
		</tr>
		<tr>
			<td colspan="2">
				ACCESSORY
				<input type="checkbox" name=""> REMOTE
				<input type="checkbox" name=""> ANTENNA
				<input type="checkbox" name=""> A/C CORD
				<input type="checkbox" name=""> RF COED
			</td>
			
		</tr>
		<tr>
			<td colspan="2">
				
				<input type="checkbox" name=""> AV CORD
				<input type="checkbox" name=""> SPEAKER CORD
				<input type="checkbox" name=""> OPERATIONAL MANUAL
			</td>
			
		</tr>
		<tr>
			<td>
				OTHERS
			</td>
			<td id="t07" width="140">
				TEST
			</td>
		</tr>
	</table>	

</td>

	</tr>

</table>


</body>
</html>