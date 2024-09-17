<?php


	function convertNumberToWordsForIndia($strnum)
{
        $words = array(
        '0'=> '' ,'1'=> 'one' ,'2'=> 'two' ,'3' => 'three','4' => 'four','5' => 'five',
        '6' => 'six','7' => 'seven','8' => 'eight','9' => 'nine','10' => 'ten',
        '11' => 'eleven','12' => 'twelve','13' => 'thirteen','14' => 'fouteen','15' => 'fifteen',
        '16' => 'sixteen','17' => 'seventeen','18' => 'eighteen','19' => 'nineteen','20' => 'twenty',
        '30' => 'thirty','40' => 'fourty','50' => 'fifty','60' => 'sixty','70' => 'seventy',
        '80' => 'eighty','90' => 'ninty');
		
		//echo $strnum = "2070000"; 
		 $len = strlen($strnum);
		 $numword = "Rupees ";
		while($len!=0)
		{
			if($len>=8 && $len<= 9)
			{
				$val = "";
				
				
				if($len == 9)
				{
					$value1 = substr($strnum,0,1);
					$value2 = substr($strnum,1,1);
					$value = $value1 *10 + $value2;
					$value1 =$value1*10;
					$val  = $value;
					//$numword.= $words["$value"]." ";
					$len = 7;
					$strnum =   substr($strnum,2,7);
				}
				if($len== 8)
				{
					$value = substr($strnum,0,1);
					$val = $value;
					//$numword.= $words["$value"]." ";
					$len =7;
					$strnum =   substr($strnum,1,7);
				}
				if($value <=19)
				{
					$numword.= $words["$value"]." ";
				}
				else
				{
					$numword.= $words["$value1"]." ";
					$numword.= $words["$value2"]." ";
				}				
				if($val == 1)
				{
					$numword.=  "Crore ";
				}
				else if($val == 0)
				{
					
				}
				else
				{
				$numword.=  "Crores ";
				}
				
			}
			if($len>=6 && $len<= 7)
			{
				$val = "";
				
				
				if($len == 7)
				{
					$value1 = substr($strnum,0,1);
					$value2 = substr($strnum,1,1);
					$value = $value1 *10 + $value2;
					$value1 =$value1*10;
					$val  = $value;
					//$numword.= $words["$value"]." ";
					$len = 5;
					$strnum =   substr($strnum,2,7);
				}
				if($len== 6)
				{
					$value = substr($strnum,0,1);
					$val = $value;
					//$numword.= $words["$value"]." ";
					$len =5;
					$strnum =   substr($strnum,1,7);
				}
				if($value <=19)
				{
					$numword.= $words["$value"]." ";
				}
				else
				{
					$numword.= $words["$value1"]." ";
					$numword.= $words["$value2"]." ";
				}				
				if($val == 1)
				{
					$numword.=  "Lakh ";
				}
				else if($val == 0)
				{
					
				}
				else
				{
				$numword.=  "Lakhs ";
				}
				
			}
		
			if($len>=4 && $len<= 5)
			{
				$val = "";
				if($len == 5)
				{
					$value1 = substr($strnum,0,1);
					$value2 = substr($strnum,1,1);
					$value = $value1 *10 + $value2;
					$value1 =$value1*10;
					$val  = $value;
					//$numword.= $words["$value"]." ";
					$len = 3;
					$strnum =   substr($strnum,2,4);
				}
				if($len== 4)
				{
					$value = substr($strnum,0,1);
					$val = $value;
					//$numword.= $words["$value"]." ";
					$len =3;
					$strnum =   substr($strnum,1,3);
				}
				if($value <=19)
				{
					$numword.= $words["$value"]." ";
				}
				else
				{
					$numword.= $words["$value1"]." ";
					$numword.= $words["$value2"]." ";
				}				
				if($val == 1)
				{
					$numword.=  "Thousand ";
				}
				else if($val == 0)
				{
					
				}
				else
				{
					$numword.=  "Thousand ";
				}
			}
			if($len==3)
			{
				$val = "";
				$value = substr($strnum,0,1);

				$val  = $value;
				$numword.= $words["$value"]." ";
				$len = 2;
				$strnum =   substr($strnum,1,2);

				if($val == 1)
				{
					$numword.=  "Hundred ";
				}
				else if($val == 0)
				{
					
				}
				else
				{
					$numword.=  "Hundred ";
				}
			}
			if($len>=1 && $len<= 2)
			{
				if($len ==2)
				{
				$value = substr($strnum,0,1);
				$value = $value *10;
				$value1 = $value;
				$strnum =   substr($strnum,1,1);
				$value2 = substr($strnum,0,1);
				$value =$value1 + $value2;				
				}
				if($len ==1)
				{	
					$value = substr($strnum,0,1);
					
				}
				if($value <=19)
				{
					$numword.= $words["$value"]." ";
					$len =0;
				}
				else
				{
					$numword.= $words["$value1"]." ";
					$numword.= $words["$value2"]." ";
					$len =0;
				}
				$numword.=  "Only ";

			}
			
			break;
		}
		return ucwords(strtolower($numword));

}
?>
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
</head>
<body style="font-size:11px;  font-family:Arial, Helvetica, sans-serif; padding: 2px;">
  
<table cellpadding="0" cellspacing="0" width="100%"  border="1">
	<tr>
		<td colspan="6" style="font-size: 18px;"><u>TAX INVOICE</u></td>
		<td colspan="8" align="right" style="font-size: 18px;">Supreme</td>
	</tr>
	<tr style="background-color: #C0C0C0;">
		<td colspan="6" rowspan="2">Invoice No. :    {{$data->TagId}}</td>
		<td colspan="8">SUPREME AUDIOTRONICS PVT. LTD.</td>
	</tr>
	<tr style="background-color: #C0C0C0;">
		<td colspan="8">A-62, Naraina Indl. Area, Ph-1, New Delhi-110028</td>
	</tr>
	<tr style="background-color: #C0C0C0;">
		<td colspan="6" rowspan="2">Invoice Date :    {{$data->inv_date}}</td>
		<td colspan="8">Tel: 011-45699900</td>
	</tr>
	<tr style="background-color: #C0C0C0;">
		<td colspan="4">GSTIN :- 07AAICS1063L1Z6</td>
		<td colspan="4">State Code:- 07</td>
	</tr>
	<tr style="background-color: #C0C0C0;">
		<td colspan="14" align="right">Email: supremeaudiotronics.service@gmail.com</td>		
	</tr>
	<tr style="background-color: #C0C0C0;">
		<td colspan="14" align="left">Billing Address</td>		
	</tr>
	<tr>
		<td colspan="2" align="center">Name</td>
		<td colspan="6" align="center">{{$data->Customer_Name}}</td>
		<td colspan="2" align="center">GSTIN :-</td>
		<td colspan="4" align="center">{{$data->gstin}}</td>		
	</tr>

	<tr >
		<td colspan="2" rowspan="6" align="center">Address</td>
		<td colspan="6" rowspan="4" align="center">{{$data->Customer_Address}}&nbsp;&nbsp;{{$data->Customer_Address_Landmark}}&nbsp;&nbsp;{{$data->City}}&nbsp;&nbsp;{{$data->State}}&nbsp;&nbsp;{{$data->Pincode}}</td>
		<td colspan="2" align="center">State Code</td>
		<td colspan="4" align="center">{{$data->state_code}}</td>	
				
				</tr>

				<tr>
					<td colspan="2" align="center">Make</td>
					<td colspan="4" align="center"></td>	
				</tr>
				<tr>
					<td colspan="2" align="center">MODEL</td>
					<td colspan="4" align="center">{{$data->MOdel}}</td>	
				</tr>
				<tr>
					<td colspan="2" align="center">SERIAL NO.</td>
					<td colspan="4" align="center"> {{$data->Serial_No}}</td>	
				</tr>
				<tr>
					<td colspan="6" align="center">DLR CODE: {{$data->asc_code}}</td>
					<td colspan="2" align="center">JOB ORDER</td>
					<td colspan="4" align="center">{{$data->TagId}}</td>	
				</tr>
				<tr>
					<td colspan="6" align="center">Contact Number: {{$data->Contact_No}}</td>
					<td colspan="2" align="center">VIN NO</td>
					<td colspan="4" align="center">&nbsp;</td>	
				</tr>
			<tr style="background-color: #C0C0C0;">
				<td colspan="14">&nbsp;</td>		
			</tr>	
			<tr>
				<td align="center" rowspan="2">Sr No.</td>
				<td align="center" rowspan="2">Product Description</td>
				<td align="center" rowspan="2">SAC Code</td>
				<td align="center" rowspan="2">Qty</td>
				<td align="center" rowspan="2">Rate</td>
				<td align="center" rowspan="2">Total</td>
				<td align="center" rowspan="2">Disc.</td>
				<td align="center" rowspan="2">Taxable Value</td>
				<td align="center" colspan="2">CGST</td>
				<td align="center" colspan="2">SGST</td>
				<td align="center" colspan="2">IGST</td>
			</tr>
			<tr>
				<td align="center">Rate%</td>
				<td align="center">Amount</td>
				<td align="center">Rate%</td>
				<td align="center">Amount</td>
				<td align="center">Rate%</td>
				<td align="center">Amount</td>
			</tr>
			<?php 
				$i=1;
				foreach($Invo as $inv)
				{
			?>
			<tr>
				<td align="center">{{$i}}</td>
				<td align="center">{{$inv->part_name}}</td>
				<td align="center">{{$inv->hsn_code}}</td>
				<td align="center">{{$inv->qty}}</td>
				<td align="center">{{$inv->rate}}</td>
				<td align="center">{{$inv->total}}</td>
				<td align="center">{{$inv->discount}}</td>
				<td align="center">{{$inv->taxable_amt}}</td>
				<td align="center">{{$inv->cgst_per}}</td>
				<td align="center">{{$inv->cgst_amt}}</td>
				<td align="center">{{$inv->sgst_per}}</td>
				<td align="center">{{$inv->sgst_amt}}</td>
				<td align="center">{{$inv->igst_per}}</td>
				<td align="center">{{$inv->igst_amt}}</td>

			</tr>
				<?php } ?>
			<tr>
				<td align="center" colspan="5">Total</td>
				<td align="center">{{$data->total_invoice}}</td>
				<td align="center">{{$data->total_discount}}</td>
				<td align="center">{{$data->total_taxable_value}}</td>
				<td align="center">&nbsp;</td>
				<td align="center">{{$data->total_cgst}}</td>
				<td align="center">&nbsp;</td>
				<td align="center">{{$data->total_sgst}}</td>
				<td align="center"></td>
				<td align="center">{{$data->total_igst}}</td>

			</tr>
			<tr>
				<td align="left" rowspan="4" colspan="7">CHEQUE/NEFT/CASH</td>
				<td align="center" colspan="5">Summary</td>
				<td align="center" colspan="2">Amount</td>
			</tr>
			<tr>
				<td align="right" colspan="5">Total Invoice Value</td>
				<td align="center" colspan="2">{{$data->total_invoice}}</td>
			</tr>
			<tr>
				<td align="right" colspan="5">Total Discounts</td>
				<td align="center" colspan="2">{{$data->total_discount}}</td>
			</tr>
			<tr>
				<td align="right" colspan="5">Total Taxable Value</td>
				<td align="center" colspan="2">{{$data->total_taxable_value}}</td>
			</tr>

			<tr>
				<td align="left" rowspan="2" colspan="2">Invoice Amount in words(INR)</td>
				<td align="left" rowspan="2" colspan="5">{{ucwords(convertNumberToWordsForIndia(round($data->total_payable)))}}</td>
				<td align="left" colspan="5">Total CGST</td>
				<td align="center" colspan="2">{{$data->total_cgst}}</td>
			</tr>
			<tr>
				<td align="left" colspan="5">Total SGST</td>
				<td align="center" colspan="2">{{$data->total_sgst}}</td>
			</tr>
			<tr>
				<td align="left" colspan="7" style="background-color: #C0C0C0;">Our Bank Details:</td>
				<td align="right" colspan="5">Total IGST</td>
				<td align="center" colspan="2">{{$data->total_igst}}</td>
			</tr>
			<tr>
				<td align="left" rowspan="3" colspan="7">Bank:- ICICI BANK LTD,E-47,NARAINA VIHAR, NEW DELHI-110028 <br> A/c No.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:- 033505001810 <br> IFSC Code :- ICIC0000335</td>
				<td align="right" colspan="5">GRAND Total</td>
				<td align="center" colspan="2">{{$data->grand_total}}</td>
			</tr>
			<tr>
				<td align="right" colspan="5">Round Off</td>
				<td align="center" colspan="2">{{$data->round_of_value}}</td>
			</tr>
			<tr>
				<td align="right" colspan="5">Net Payable Amount</td>
				<td align="center" colspan="2">{{$data->total_payable}}</td>
			</tr>
			<tr>
				<td align="center" colspan="7">Should you have any enquiries concerning this invoice, please contact at <br><br>
					Tel: 011-49536169 / 99589 659945 <br><br> Thank you for giving us an opportunity to serve you.</td>
				<td align="right" colspan="7" style="font-size: 20px;">SUPREME AUDIOTRONICS PVT. LTD. <br><br> Authorised Signatory</td>
			</tr>

	
</table>


</body>
</html>