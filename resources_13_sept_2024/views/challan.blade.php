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
td#t04
{
	border-right:1px solid black;
	border-bottom:1px solid black;
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
td#t09
{
	border-right:1px solid black;
}

</style>


<style type="text/css" media="print">
@page {
    size: auto;   /* auto is the initial value */
    margin: 0;  /* this affects the margin in the printer settings */
}
</style>

</head>
<body style="font-size:11px;  font-family:Arial, Helvetica, sans-serif;">
  
<table cellpadding="4" cellspacing="0" width="100%"  border="1">
	<tr>
		<td colspan="6" style="font-size: 18px;" align="center"><u style="text-align: left;">Delivery Challan</u> &nbsp;&nbsp;&nbsp;<span style="text-align: right;">Supreme</span></td>
		<td rowspan="13">&nbsp;</td>
		<td colspan="6" style="font-size: 18px;"  align="center"><u>Gate Pass</u> &nbsp;&nbsp;&nbsp;Supreme</td>
	</tr>
	<tr>
		<td rowspan="2" colspan="4" align="center">SUPREME AUDIOTRONICS PVT LTD<br> A-37,Naraina Indl. Area, Ph-2,New Delhi-110028<br>Tel: 011-49536169 /  9958965995</td>
		<td colspan="2"  align="center">Delivery Challan No.<br><?php echo $record->challan_no; ?></td>
		
		<td rowspan="2" colspan="4"  align="center">SUPREME AUDIOTRONICS PVT LTD<br> A-37,Naraina Indl. Area, Ph-2,New Delhi-110028<br>Tel: 011-49536169 /  9958965995</td>
		<td colspan="2"  align="center">Delivery Challan No.<br><?php echo $record->challan_no; ?></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><?php echo $record->created_at; ?> </td>
		<td colspan="2" align="center"><?php echo $record->created_at; ?> </td>
	</tr>
	<tr>
		<td colspan="2">PAN : AAICS1063L </td>
		<td colspan="4">GSTIN: 07AAICS1063L1Z6</td>
		<td colspan="2">PAN : AAICS1063L </td>
		<td colspan="4">GSTIN: 07AAICS1063L1Z6</td>
	</tr>
	<tr>
		<td colspan="6" align="left"><?php echo $sc_details->center_name.', '.$sc_details->address.' '.$sc_details->city.' '.$state->state_name.' '.$state->pincode; ?></td>
		<td colspan="6" align="left"><?php echo $sc_details->center_name.', '.$sc_details->address.' '.$sc_details->city.' '.$state->state_name.' '.$state->pincode; ?></td>
	</tr>
	<tr>
		<td colspan="4">GSTIN OF CONSIGNEE : <?php if(strlen($sc_details->gst_no)==15) { echo $sc_details->gst_no;} ?></td>
		<td colspan="2">STATE : <?php  echo $state->state_name; ?></td>
		<td colspan="4">GSTIN OF CONSIGNEE : <?php if(strlen($sc_details->gst_no)==15) { echo $sc_details->gst_no;} ?></td>
		<td colspan="2">STATE : <?php  echo $state->state_name; ?></td>
	</tr>
	<tr>
		<td colspan="4">PAN OF CONSIGNEE :<?php if(strlen($sc_details->gst_no)==10) { echo $sc_details->gst_no;} ?></td>
		<td colspan="2">STATE CODE : <?php  echo $state->state_code; ?></td>
		<td colspan="4">PAN OF CONSIGNEE :<?php if(strlen($sc_details->gst_no)==10) { echo $sc_details->gst_no;} ?></td>
		<td colspan="2">STATE CODE : <?php  echo $state->state_code; ?></td>
	</tr>
        
        
        
	<tr>
		<td align="center">S.No.</td>
		<td align="center">Description</td>
		<td align="center">HSN CODE</td>
		<td align="center">QTY</td>
		<td align="center">RATE</td>
		<td align="center">AMOUNT</td>
		<td align="center">S.No.</td>
		<td align="center">Description</td>
		<td align="center">HSN CODE</td>
		<td align="center">QTY</td>
		<td align="center">RATE</td>
		<td align="center">AMOUNT</td>

	</tr>
        
        
        <?php  $srno = 1;
		if (!empty($doc_arr)) {
		foreach ($doc_arr as $doc){?>

			<tr>
				<td align="center"><?php echo $srno; ?></td>
				<td align="center"><?php echo $doc; ?></td>
				<td align="center"></td>
				<td align="center"></td>
				<td align="center"></td>
				<td align="center"></td>
				<td align="center"><?php echo $srno++; ?></td>
				<td align="center"><?php echo $doc; ?></td>
				<td align="center"></td>
				<td align="center"></td>
				<td align="center"></td>
				<td align="center"></td>
			</tr>
			

		<?php }
		 }else{
        foreach($part_arr as $part) { ?>
	<tr>
		<td align="center"><?php echo $srno; ?></td>
		<td align="center"><?php echo $part['part_name']; ?></td>
		<td align="center"><?php echo $part['hsn_code']; ?></td>
		<td align="center"><?php echo $part['qty']; ?></td>
		<td align="center"><?php echo $part['rate']; ?></td>
		<td align="center"><?php echo $part['total']; ?></td>
		<td align="center"><?php echo $srno++; ?></td>
		<td align="center"><?php echo $part['part_name']; ?></td>
		<td align="center"><?php echo $part['hsn_code']; ?></td>
		<td align="center"><?php echo $part['qty']; ?></td>
		<td align="center"><?php echo $part['rate']; ?></td>
		<td align="center"><?php echo $part['total']; ?></td>
	</tr>
        <?php 
	}} ?>
		
	<tr>
		<td colspan="6" align="left">Supply Name:After/For In warranty Repairs & Service on FOC</td>
		<td colspan="6" align="left">Supply Name:After/For In warranty Repairs & Service on FOC</td>
	</tr>
	<tr>
		<td colspan="4" style="height: 120px;"><b style="font-size: 14px;">Not For Sale</b><br> Declaration: we declare that the description and particulars of the goods reffered to herein above are true and accurate to best of our knowledge</td>
		<td colspan="2">Authorised Signatory</td>
		<td colspan="4"><b style="font-size: 14px;">Not For Sale</b><br> Declaration: we declare that the description and particulars of the goods reffered to herein above are true and accurate to best of our knowledge</td>
		<td colspan="2">Authorised Signatory</td>
	</tr>
</table>


</body>
</html>