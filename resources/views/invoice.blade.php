<?php

//$record_arr = $data['record_arr'];
//$sc_details = $data['sc_details'];
?>
<html>
<head>
<style>
hr.new1{border-top: 2px solid #87CEEB;}    
    
    
th#t01 {
    border-right:1px solid black;
	border-bottom:1px solid black;
}
td#t02
{
	border-right:1px solid black;
}

th#t03
{
	border-bottom:1px solid black;
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
td#t08
{
	border-top:2px solid #87CEEB;
}

td#t09
{
	border-top:1px solid #87CEEB;
}



</style>
</head>
<body style="font-size:12px;  font-family:Arial, Helvetica, sans-serif;">
  
<table cellpadding="0" cellspacing="0">
    
	<tr>
		<td>
			<table>
			<tr>
				

				<td align="center">
					<img src="<?php echo  $image_base64 ;?>">
                    
				</td>
                                <td width="370" style="text-align: right;font-size:15px; font-family:Arial, Helvetica, sans-serif;">
                                    Original for Recipient<br>
                                    <font color="#87CEEB" size="20px;"><b>INVOICE <?php echo $record_arr[0]->invoice_no; ?></b></font><br>
                                    <b>Date</b> <?php echo date('F d,Y',strtotime($record_arr[0]->created_at)); ?><br>
                                    <b>Due Date</b> <?php echo date('F d,Y',strtotime($record_arr[0]->created_at.' +10 day')); ?><br>
                                    <b>P. O. Number</b> <?php echo $record_arr[0]->po_no; ?><br>
                                    <b>P.O.  Date</b> <?php echo date('F d,Y',strtotime($record_arr[0]->po_date)); ?>
				</td>
			</tr>
			</table>
		</td>	
	</tr>
</table>

    <hr class="new1">
<table   cellpadding="2" cellspacing="0">


	<tr>
            <td colspan="2"><font color="#87CEEB"><b><i>Sorina TEST 123</i></b></font></td>
		<td colspan="3" ><font color="#87CEEB"><b><i>Bill to:</i></b></font></td>
		<td colspan="3" ></td>
		
	</tr>

	<tr>
		<td colspan="2"  valign="top">
                    Long Baharam, 34-38, B Building Madurai, Tamil Nadu (TN - 33), India<br>  
                    998756334<br>   
                    sorina@aiwa.com<br>
                    www.aiwa.com<br>
                    GSTIN: 123456711111111
		</td>
		<td colspan="3" valign="top">
                    <?php echo $sc_details->center_name; ?><br>
                    <?php echo $sc_details->address; ?><br>
                    <?php echo $sc_details->contact_no; ?><br>
                    <?php echo $sc_details->person_name; ?><br>
                    Place of Supply: <?php echo $sc_details->city; ?><br>
                    GSTIN: <?php echo $sc_details->gst_no; ?>
		</td>
		<td colspan="3" valign="top">
                    Waybill No. 234<br>
                    LR No.: 8256
                    Delivery Note: 05
                    Vehicle No.: B 230 BLR<br>
                    Shipping Method: truck
		</td>
		
	</tr>

	<tr>
	<td colspan="8"  valign = "top" style = "height:200">
	<table  height = "200" cellpadding="0" cellspacing="0" >
		<tr>
                    <td width = "20"  id="t08" style="text-align:center;"><font color="#87CEEB"><b>No</b></font></td>
                    <td width = "150" id="t08" style="text-align:center;"><font color="#87CEEB"><b>PRODUCT / SERVICE NAME</b></font></td>
                <td width = "20"  id="t08" style="text-align:center;"><font color="#87CEEB"><b>HSN / SAC</b></font></td>
                <td width = "30"  id="t08" style="text-align:center;"><font color="#87CEEB"><b>PREPARATION</b></font></td>
                <td width = "30" id="t08" style="text-align:center;"><font color="#87CEEB"><b>UNIT PRICE</b></font></td>
                <td width = "30" id="t08" style="text-align:center;"><font color="#87CEEB"><b>IGST</b></font></td>
                <td width = "30" id="t08" style="text-align:center;"><font color="#87CEEB"><b>CESS</b></font></td>
                <td width = "30" id="t08" style="text-align:center;"><font color="#87CEEB"><b>AMOUNT</b></font></td>
		</tr>

		<?php
			$i=1; 
			foreach($record_arr as $record) :
                            $gst_record_arr[$record->gst]['total'] += $record->total;
                            $gst_record_arr[$record->gst]['gst_amount'] += $record->gst_amount;
                            $gst_record_arr[$record->gst]['grand_total'] += $record->grand_total;
                            $grand_discount += $record->discount_amount;
                            $grand_NET += $record->net_bill;
                            $total +=$record->total;;
                            $gst_amount += $record->gst_amount;
			?>
				<tr>
                                    <td align="center" valign="top" id = "t09" <?php if(count($record_arr)>20) { echo 'style="font-size:8px;"'; } ?> style="text-align:center;background: #DCDCDC;"><?php echo "<br><b>".$i++."</b>"; ?></td>
                                <td align="center" valign="top" id = "t09" <?php if(count($record_arr)>20) { echo 'style="font-size:8px;"'; } ?> style="text-align:left;"><?php echo "<br><b>".$record->model_name.'</b>'; ?><br><?php echo $record->part_name.' '.$record->color; ?></td>
				<td align="center" valign="top" id = "t09" <?php if(count($record_arr)>20) { echo 'style="font-size:8px;"'; } ?> style="text-align:right;background: #DCDCDC;"><?php echo "<br><b>".$record->hsn_code.'</b>'; ?></td>
				<td align="center" valign="top" id = "t09" <?php if(count($record_arr)>20) { echo 'style="font-size:8px;"'; } ?> style="text-align:right;"><?php echo "<br><b>".$record->issued_qty."</b>"; ?></td>
                                <td align="center" valign="top" id = "t09" <?php if(count($record_arr)>20) { echo 'style="font-size:8px;"'; } ?> style="text-align:right;background: #DCDCDC;"><?php echo "<br><b>".$record->asc_amount."</b>"; ?><br>-Discount <?php echo $record->discount; ?>%  </td>
                                <td align="center" valign="top" id = "t09" <?php if(count($record_arr)>20) { echo 'style="font-size:8px;"'; } ?> style="text-align:right;"><?php echo "<br><b>".$record->gst_amount."</b>"; ?><br> <?php echo $record->gst; ?>%</td>
                                <td align="center" valign="top" id = "t09" <?php if(count($record_arr)>20) { echo 'style="font-size:8px;"'; } ?> style="text-align:right;background: #DCDCDC;"><br><b>0.00</b></td>
                                <td align="center" valign="top" id = "t09" <?php if(count($record_arr)>20) { echo 'style="font-size:8px;"'; } ?> style="text-align:right;"><?php echo "<br><b>".$record->grand_total."</b>"; ?></td>
				</tr>
			<?php endforeach; ?>
			
				
			
		<tr>
                    <td height = "<?php echo 15*(12-$i-$j); ?>" id = "t09" style="background: #DCDCDC;"></td>
			<td id = "t09"></td>
			<td id = "t09" style="background: #DCDCDC;"></td>
			<td id = "t09"></td>
                        <td id = "t09" style="background: #DCDCDC;"></td>
                        <td id = "t09"></td>
                        <td id = "t09" style="background: #DCDCDC;"></td>
                        
			<td id = "t09"></td>
		</tr>	
		<tr>
                    <td style="background: #DCDCDC;"></td>
                    <td style="text-align:right;"><b>Shipping & Packaging</b></td>
                    <td style="background: #DCDCDC;"></td>
                    <td></td>
                    <td style="text-align:right;background: #DCDCDC;"><b>100</b></td>
                    <td style="text-align:right;"><b>12.00</b><br>
                        <b>@12.00%</b>
                    </td>
                    <td style="background: #DCDCDC;"></td>
                    
                    <td style="text-align:right;"><b>112.00</b></td>
		</tr>
                <?php foreach($gst_record_arr as $gst=>$row) { ?>
                <tr>
                    <td id="t08" style="background: #DCDCDC;"></td>
                    <td id="t08"></td>
                <td id="t08" width = "48" style="text-align:right;background: #DCDCDC;"><b><?php echo '@'.$gst.'%'; ?></b></td>
                <td id="t08"></td>
                <td id="t08" width = "48" style="text-align:right;background: #DCDCDC;"><b><?php echo $row['total']; ?></b></td>
                <td id="t08" width = "48" style="text-align:right;"><b><?php echo $row['gst_amount']; ?></b></td>
                <td id="t08" style="background: #DCDCDC;"></td>
                <td id="t08" width = "48" style="text-align:right;"><b><?php echo $row['grand_total']; ?></b></td>
                
		</tr>

                <?php $total_plus += $row['total'];
                        //$gst_amount += $row['gst_amount'];
                        $grand_total +=$row['grand_total'];
                        
                } ?>
	
	
                <tr>
                    <td style="background: #DCDCDC;"></td>
                    <td style="text-align:right;"><font color="#87CEEB"><b>TOTAL</b></font></td>
                <td style="background: #DCDCDC;"></td>
                <td></td>
                <td style="text-align:right;background: #DCDCDC;"><font color="#87CEEB"><b><?php echo $total_plus; ?></b></font></td>
                <td style="text-align:right;"><font color="#87CEEB"><b><?php echo $gst_amount; ?></b></font></td>
                <td style="background: #DCDCDC;"></td>
                <td style="text-align:right;"><font color="#87CEEB"><b><?php echo $grand_total; ?></b></font></td>
		</tr>

	<tr>
            <td colspan="4" id="t08"><br>
                    Total <?php echo $record_arr[0]->uc_total; ?>.<br>
                    <a href="#">Pay Now with PayPal</a><br>
                    <font color="#87CEEB"><b>AUTHORIZED SIGNATORY</b></font><br><br><br><br><br><br><br><br>
                    
                    
		</td>
                <td colspan="3" id="t08" style="text-align:right;">
                    <font color="#87CEEB"><b>TOTAL BEFORE TAX</b></font><br>
                    <font color="#87CEEB"><b>DISCOUNT</b></font><br>
                    <font color="#87CEEB"><b>TOTAL TAX AMOUNT</b></font><br>
                    <font color="#87CEEB"><b>ROUNDED OFF</b></font><br>
                    <font color="#87CEEB"><b>TOTAL AMOUNT</b></font><br>
                    <font color="#87CEEB"><b>AMOUNT DUE</b></font><br>
                </td>
                <td id="t08" style="text-align:right;">
                    <font color="#87CEEB"><b><?php echo $total; ?></b></font><br>
                    <font color="#87CEEB"><b>(-)<?php echo $grand_discount; ?></b></font><br>
                    <font color="#87CEEB"><b><?php echo $grand_NET; ?></b></font><br>
                    <font color="#87CEEB"><b><?php echo round(round($grand_NET)-$grand_NET,2); ?></b></font><br>
                    <font color="#87CEEB"><b><?php echo round($grand_total); ?></b></font><br>
                    <font color="#87CEEB"><b><?php echo round($grand_total); ?></b></font><br>
                </td>
	</tr>
        <tr>
            <td colspan="8">
                <font color="#87CEEB"><b>NOTE:</b></font><br>
                Please note that all product are fragile and need to be transported with caution.<br>
                    If invoice has not been paid in 5 days after due date, a tax of 10% of total value is applied to each day of delay.
            </td>
        </tr>
	
		</table>	
	</td>		
	</tr>	

</table>



</body>
</html>