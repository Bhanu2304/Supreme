@extends('layouts.app')

@section('content')

<script>
                                    

menu_select('{{$url}}');                                                             
</script>
<div class="app-main" style="padding-top:2px; ">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title">Tax Invoice</h5>
                            
                            
                            @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                            <form id="spare_form" method="post" action="save-invoice" >
                                <table class="table" style="margin-bottom:0.25rem;line-height: 0.3;">
                                    <tr >
                                        <td ><?php echo "Invoice No.: $TagId"; ?></td>
                                        <td><?php echo "GSTIN: ".$sc['gst_no']; ?></td>
                                        <td><?php echo "State Code : $state_code_center"; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"><?php echo "Invoice Date.: ".date('Y-m-d'); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"><?php echo "Billing Address: "; ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo "Name:"; ?></td>
                                        <td><?php echo $data['Customer_Name']; ?></td>
                                        <td><?php echo 'GSTIN: <input type="text" name="gstin" id="gstin" placeholder="GST No." value="'.$data['gstin'].'" style="height:18px;border-top-style: hidden;
  border-right-style: hidden;
  border-left-style: hidden;" >'; ?></td>
                                    </tr>
                                    <tr>
                                        <td rowspan="6" align="top"><?php echo "Address"; ?></td>
                                        <td rowspan="4" align="top"><?php echo $data['Customer_Address'].' '.$data['City'].' '.$data['State']; ?></td>
                                        <td><?php echo "State Code: ".$state_code_client; ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo "MAKE"; ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo "MODEL: ".$data['Model']; ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo "SERIAL NO.: ".$data['Serial_No']; ?></td>
                                    </tr>
                                    
                                    <tr>
                                        <td><?php echo "DLR CODE: ".$data['Contact_No']; ?></td>
                                        <td><?php echo "Job Order: ".$data['TagId']; ?></td>
                                    </tr>
                                    
                                    <tr>
                                        <td><?php echo "Contact Number: ".$data['Contact_No']; ?></td>
                                        <td><?php echo "VIN NO.: ".$data['TagId']; ?></td>
                                    </tr>
                                    
                                    <tr>
                                        <td colspan="3"></td>
                                    </tr>
                                </table>
                                
                                <table id="part_table" border="1" width="100%" >
<!--                                <table class="table">-->
                                    <tr>
                                        <th rowspan="2">Sr. No.</th>
<!--                                        <th rowspan="2">Action</th>-->
                                        <th rowspan="2">Product Description</th>
                                        <th rowspan="2">SAC Code</th>
                                        <th rowspan="2">Qty</th>
                                        <th rowspan="2">Value</th>
                                        <th rowspan="2">Total</th>
                                        <th rowspan="2">Discount</th>
                                        <th rowspan="2">Total Value</th>
                                        <th colspan="2">CGST</th>
                                        <th colspan="2">SGST</th>
                                        <th colspan="2">IGST</th>
                                        
                                    </tr>
                                    <tr>
                                        
                                        <th>Rate</th>
                                        <th>Amount</th>
                                        <th>Rate</th>
                                        <th>Amount</th>
                                        <th>Rate</th>
                                        <th>Amount</th>
                                    </tr>
                                    
                                    
                                    
                                    
                                <?php   $srno = 1; $tot_qty = 0;$tot_val = 0;$tot_without_tax=0; $cgst = 0; $sgst=0; $igst=0; 
                                        $total_invoice = 0.0;
                                        $total_discount = 0.0;
                                        $total_taxable_value = 0.0;
                                        $total_cgst = 0.0;
                                        $total_sgst = 0.0;
                                        $total_igst = 0.0;
                                        $grand_total = 0.0;
                                        $round_off_value = 0.0;
                                        $total_payable = 0.0;
                                        if(empty($data_invoice))
                                        {
                                            
                                        
                                
                                            echo '<tr>';
                                            echo '<td align="center">'.$srno++.'</td>';
                                            
                                           // echo '<td>+</td>';
                                            
                                            echo '<td  align="center">';
                                            echo 'SERVICE CHARGES';
                                            echo '</td>';
                                            
                                            echo '<td  align="center">';
                                            echo 'SERVICE CHARGES';
                                            $key = 'SERVICE CHARGES####9987';
                                            echo '<input type="hidden" name="part_arr[]" value="'.$key.'">';
                                            echo '</td>';
                                            
                                            echo '<td  align="center">';
                                            echo '<input type="text" name="qty['.$key.']" id="qty1" value="" onkeypress="return checkNumber(this.value,event)" onblur="cal_tot_val('."'1'".')" placeholder="Qty" style="width:100px;" />';
                                            echo '</td>';
                                            
                                            echo '<td  align="center">';
                                            echo '<input type="text" name="rate['.$key.']" id="rate1" value="" onkeypress="return checkNumber(this.value,event)" onblur="cal_tot_val('."'1'".')" placeholder="Rate" style="width:100px;" />';
                                            echo '</td>';
                                            
                                            echo '<td  align="center" id="total1">';
                                            echo "0";
                                            $tot_without_tax +=$total;
                                            echo '</td>';
                                            
                                            echo '<td  align="center">';
                                            echo '<input type="text" name="disc['.$key.']" id="disc1" value="" onkeypress="return checkNumber(this.value,event)" onblur="cal_tot_val('."'1'".')" placeholder="Discount" style="width:100px;" />';
                                            echo '</td>';
                                            
                                            echo '<td  align="center"  id="total_after_discount1">';
                                            echo $tot_val += $total = round($total-0,2);
                                            echo '</td>';
                                            
                                            $part_tax = 18;
                                            $tax = 0;
                                            if($state_code_center==$state_code_client)
                                            {
                                                $tax_per = round($part_tax/2,2);
                                                echo '<td align="center">';
                                                echo '<select id="tax1" name="tax['.$key.']" onchange="cal_tot_val('."'1'".');">';
                                                echo '<option value="2.5">2.5%</option>';
                                                echo '<option value="6">6%</option>';
                                                echo '<option value="9" selected>9%</option>';
                                                echo '<option value="14">14%</option>';
                                                echo '</select>';
                                                //echo "$tax_per%";
                                                echo '</td>';
                                                
                                                echo '<td  align="center" id="cgst1">';
                                                echo $tax = round($total*$tax_per/100,2);
                                                $cgst+=$tax;
                                                echo '</td>';
                                                
                                                echo '<td  align="center" id="sgst_per1">';
                                                echo "$tax_per%";
                                                echo '</td>';
                                                echo '<td  align="center" id="sgst1">';
                                                echo $tax;
                                                $sgst +=$tax;
                                                echo '</td>';
                                                
                                                echo '<td></td>';
                                                echo '<td></td>';
                                            }
                                            else
                                            {   
                                                echo '<td>';
                                                echo '</td>';
                                                echo '<td>';
                                                echo '</td>';
                                                
                                                echo '<td>';
                                                echo '</td>';
                                                echo '<td>';
                                                echo '</td>';
                                                
                                                echo '<td  align="center">';
                                                //echo "18%";
                                                echo '<select id="tax1" name="tax['.$key.']" onchange="cal_tot_val('."'1'".');">';
                                                echo '<option value="5">5%</option>';
                                                echo '<option value="12">12%</option>';
                                                echo '<option value="18" selected>18%</option>';
                                                echo '<option value="28">28%</option>';
                                                echo '</select>';
                                                echo '</td>';
                                                echo '<td id="igst1">';
                                                echo $igst +=$tax = round($total*0.18,2);
                                                echo '</td>';
                                            }
                                            
                                            echo '</tr>';
                                                                
                                            foreach($spare_req_master as $key=>$spare)
                                            {
                                                echo '<tr>';
                                                echo '<td align="center">'.$srno.'</td>';
                                                //echo '<td>+ -</td>';

                                                echo '<td  align="center">';
                                                //echo $spare['part_name'];
                                                echo "SPARE PARTS";
                                                echo '</td>';

                                                echo '<td  align="center">';
                                                echo $spare['hsn_code'];
                                                echo '<input type="hidden" name="part_arr[]" value="'.("$key").'">';
                                                echo '</td>';

                                                echo '<td  align="center">';
                                                //$tot_qty +=$spare['qty'];
                                                echo '<input type="text" name="qty['.$key.']" id="qty'.$srno.'" value="'.$spare['qty'].'" onkeypress="return checkNumber(this.value,event)" onblur="cal_tot_val('."'$srno'".')" placeholder="Qty" style="width:100px;" />';
                                                echo '</td>';

                                                echo '<td  align="center">';
                                                
                                                echo '<input type="text" name="rate['.$key.']" id="rate'.$srno.'" value="'.$spare['part_rate'].'" onkeypress="return checkNumber(this.value,event)" onblur="cal_tot_val('."'$srno'".')" placeholder="Rate" style="width:100px;" />';
                                                echo '</td>';

                                                echo '<td  align="center" id="total'.$srno.'">';
                                                echo $total = round($spare['part_rate']*$spare['qty'],2);
                                                $tot_without_tax +=$total;
                                                echo '</td>';

                                                echo '<td  align="center">';
                                                echo '<input type="text" name="disc['.$key.']" id="disc'.$srno.'" value="" onblur="cal_tot_val('."'$srno'".')" onkeypress="return checkNumber(this.value,event)" placeholder="Discount" style="width:100px;" />';
                                                echo '</td>';

                                                echo '<td  align="center" id="total_after_discount'.$srno.'">';
                                                echo $tot_val += $total_value = round($total-0,2);
                                                
                                                echo '</td>';

                                                $part_tax = $spare['part_tax'];
                                                $tax = 0;$cgst=0;$sgst=0;$igst=0;
                                                if($state_code_center==$state_code_client)
                                                {
                                                    $tax_per = round($part_tax/2,2);
                                                    echo '<td  align="center">';
                                                    echo "$tax_per%";
                                                    echo '<input type="hidden" id="tax'.$srno.'" value="'.$tax_per.'">';
                                                    echo '</td>';

                                                    echo '<td  align="center" id="cgst'.$srno.'">';
                                                    echo $tax = round($total*$tax_per/100,2);
                                                    $cgst+=$tax;
                                                    echo '</td>';

                                                    echo '<td  align="center">';
                                                    echo "$tax_per%";
                                                    echo '</td>';
                                                    echo '<td  align="center" id="sgst'.$srno.'">';
                                                    echo $tax;
                                                    $sgst +=$tax;
                                                    echo '</td>';

                                                    echo '<td></td>';
                                                    echo '<td></td>';
                                                }
                                                else
                                                {   
                                                    echo '<td>';
                                                    echo '</td>';
                                                    echo '<td>';
                                                    echo '</td>';

                                                    echo '<td>';
                                                    echo '</td>';
                                                    echo '<td>';
                                                    echo '</td>';

                                                    echo '<td  align="center">';
                                                    echo $part_tax;
                                                    echo '<input type="hidden" id="tax'.$srno.'" value="'.$part_tax.'">';
                                                    echo '</td>';
                                                    echo '<td id="igst'.$srno.'">';
                                                    echo $igst +=$tax = round($total*0.18,2);
                                                    echo '</td>';
                                                }

                                                echo '</tr>';
                                                $srno++;
                                                
                                                $total_invoice += $total_value;
                                                $total_discount += $disc;
                                                $total_taxable_value += $total;
                                                $total_cgst += $cgst;
                                                $total_sgst += $sgst;
                                                $total_igst += $igst;
                                                
                                                
                                            }
                                            
                                            $grand_total =$total_invoice+$total_cgst+$total_sgst+$total_igst;
                                            $total_payable = round($grand_total);
                                            $round_off_value = round($grand_total - $total_payable,2);
                                        }
                                        else
                                        {
                                            foreach($data_invoice as $spare)
                                            {
                                                $key = $spare['part_name'].'##'.$spare['part_no'].'##'.$spare['hsn_code'];
                                                echo '<tr>';
                                                echo '<td align="center">'.$srno.'</td>';
                                                //echo '<td>+ -</td>';

                                                echo '<td  align="center">';
                                                //echo $spare['part_name'];
                                                echo $spare['part_name'];
                                                echo '</td>';

                                                echo '<td  align="center">';
                                                echo $spare['hsn_code'];
                                                echo '<input type="hidden" name="part_arr[]" value="'.("$key").'">';
                                                echo '</td>';

                                                echo '<td  align="center">';
                                                $tot_qty +=$spare['qty'];
                                                echo '<input type="text" name="qty['.$key.']" id="qty'.$srno.'" value="'.$spare['qty'].'" onkeypress="return checkNumber(this.value,event)" onblur="cal_tot_val('."'$srno'".')" placeholder="Qty" style="width:100px;" />';
                                                echo '</td>';

                                                echo '<td  align="center">';
                                                $spare['part_rate'];
                                                echo '<input type="text" name="rate['.$key.']" id="rate'.$srno.'" value="'.$spare['rate'].'" onkeypress="return checkNumber(this.value,event)" onblur="cal_tot_val('."'$srno'".')" placeholder="Rate" style="width:100px;" />';
                                                echo '</td>';

                                                echo '<td  align="center" id="total'.$srno.'">';
                                                echo $total = round($spare['rate']*$spare['qty'],2);
                                                $tot_without_tax +=$total;
                                                echo '</td>';

                                                $disc = 0;
                                                if(!empty($spare['discount']))
                                                {
                                                    $disc = $spare['discount'];
                                                }
                                                
                                                echo '<td  align="center">';
                                                echo '<input type="text" name="disc['.$key.']" id="disc'.$srno.'" value="'.$disc.'" onblur="cal_tot_val('."'$srno'".')" onkeypress="return checkNumber(this.value,event)" placeholder="Discount" style="width:100px;" />';
                                                echo '</td>';

                                                echo '<td  align="center" id="total_after_discount'.$srno.'">';
                                                echo $total_value = round($total-$disc,2);
                                                 $tot_val += $total_value;
                                                echo '</td>';

                                                //$part_tax = $spare['cgst_per'];
                                                $tax = 0;$cgst=0;$sgst=0;$igst=0;
                                                if($state_code_center==$state_code_client)
                                                {
                                                    $tax_per = $spare['cgst_per'];
                                                    echo '<td  align="center">';
                                                    echo "$tax_per%";
                                                    echo '<input type="hidden" id="tax'.$srno.'" value="'.$tax_per.'">';
                                                    echo '</td>';

                                                    echo '<td  align="center" id="cgst'.$srno.'">';
                                                    echo $tax = round($total_value*$tax_per/100,2);
                                                    $cgst+=$tax;
                                                    echo '</td>';

                                                    echo '<td  align="center">';
                                                    echo "$tax_per%";
                                                    echo '</td>';
                                                    echo '<td  align="center" id="sgst'.$srno.'">';
                                                    echo $tax;
                                                    $sgst +=$tax;
                                                    echo '</td>';

                                                    echo '<td></td>';
                                                    echo '<td></td>';
                                                }
                                                else
                                                {   
                                                    echo '<td>';
                                                    echo '</td>';
                                                    echo '<td>';
                                                    echo '</td>';

                                                    echo '<td>';
                                                    echo '</td>';
                                                    echo '<td>';
                                                    echo '</td>';

                                                    echo '<td  align="center">';
                                                    echo $part_tax = $spare['igst_per'];
                                                    echo '<input type="hidden" id="tax'.$srno.'" value="'.$part_tax.'">';
                                                    echo '</td>';
                                                    echo '<td id="igst'.$srno.'">';
                                                    echo $igst +=$tax = round($total_value*$part_tax/100,2);
                                                    echo '</td>';
                                                }
                                                
                                                
                                                $total_invoice += $total_value;
                                                $total_discount += $disc;
                                                $total_taxable_value += $total;
                                                $total_cgst += $cgst;
                                                $total_sgst += $sgst;
                                                $total_igst += $igst;
                                                

                                                
                                                
                                                
                                                
                                                echo '</tr>';
                                                $srno++;
                                            }
                                            
                                            $grand_total =$total_invoice+$total_cgst+$total_sgst+$total_igst;
                                            $total_payable = round($grand_total);
                                            $round_off_value = round($grand_total - $total_payable,2);
                                        }
                                ?>
                                    
                                    <tr>
                                        <th colspan="5" style="text-align: right;">Total</th>
                                        <td align="center" id="gr_total"><?php echo $total_taxable_value; ?></td>
                                        <td  align="center"  id="gr_disc"><?php echo $total_discount; ?></td>
                                        <td  align="center"  id="gr_net_total"><?php echo $tot_val; ?></td>
                                        <td></td><td align="center" id="gr_cgst"><?php echo $cgst; ?></td>
                                        <td></td><td align="center" id="gr_sgst"><?php echo $sgst; ?></td>
                                        <td></td><td align="center" id="gr_igst"><?php echo $igst; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="8" rowspan="11"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" align="center">Summary</td>
                                        <td colspan="2" align="center">Amount</td>
                                    </tr>
                                    
                                    <tr>
                                        <td colspan="4" align="center">Total Invoice Value</td>
                                        <td colspan="2" align="center" id="sum_total"><?php echo $total_taxable_value; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" align="center">Total Discounts</td>
                                        <td colspan="2" align="center" id="sum_disc"><?php echo $total_discount; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" align="center">Total Taxable Value</td>
                                        <td colspan="2" align="center" id="sum_net_total"><?php echo ($total_taxable_value-$total_discount); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" align="center">Total CGST</td>
                                        <td colspan="2" align="center" id="sum_cgst"><?php echo $total_cgst; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" align="center">Total SGST</td>
                                        <td colspan="2" align="center" id="sum_sgst"><?php echo $total_sgst; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" align="center">Total IGST</td>
                                        <td colspan="2" align="center" id="sum_igst"><?php echo $total_igst; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" align="center">Grand Total</td>
                                        <td colspan="2" align="center" id="sum_grnd"><?php echo $grand_total; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" align="center">Round Off</td>
                                        <td colspan="2" align="center" id="sum_round_off"><?php echo $round_off_value; ?></td>
                                    </tr>
                                    
                                    <tr>
                                        <td colspan="4" align="center">Net Payable Amount</td>
                                        <td colspan="2" align="center" id="sum_payable"><?php echo $total_payable; ?></td>
                                    </tr>
                                    
                                    </table>
                                <input type="hidden" name="tax_type" id="tax_type" value="<?php if($state_code_center==$state_code_client) {echo '1';} else {echo '2';} ?>" >
                                <input type="hidden" name="tag_id" id="tag_id" value="<?php echo $TagId; ?>" >
                                <button type="submit" onclick="return validate_gst()" class="mt-2 btn btn-primary">Save</button>
                            </form>
                                
                        </div>    
                           
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 <script>
    
    function validate_gst()
    {
        var gstin = $('#gstin').val();
        if(gstin!='' && gstin.length!=15)
        {
            alert("GST No. Should be at least 15.");
        }
        
        
    }
    
    function get_value_by_id(div_id)
    {
        var div_value = $('#'+div_id).val();
        if(div_value=='')
        {
            return 0;
        }
        else
        {
            return div_value;
        }
    }
    
    function set_value_by_id(div_id,value)
    {
        var div_value = $('#'+div_id).html(value);
    }
    
    function cal_tot_val(srno)
    {
        var qty = get_value_by_id('qty'+srno);
        var rate = get_value_by_id('rate'+srno);
        var disc = get_value_by_id('disc'+srno);
        var tax = get_value_by_id('tax'+srno);
        var tax_type = get_value_by_id('tax_type');
        
        var total = (qty*rate).toFixed(2);
        set_value_by_id('total'+srno,total);
        total = (total-disc).toFixed(2);
        set_value_by_id('total_after_discount'+srno,total);
        
        if(tax_type=='1')
        {
            var tax_val = (total*tax/100).toFixed(2);
            set_value_by_id('sgst_per'+srno,tax+"%");
            set_value_by_id('cgst'+srno,tax_val);
            set_value_by_id('sgst'+srno,tax_val);
        }
        else
        {
            var tax_val = (total*tax/100).toFixed(2);
            set_value_by_id('igst'+srno,tax);
        }
        
        get_total_summary();
    }
    function get_total_summary()
    {
        var row_len = document.getElementById("part_table").rows.length;
        var inv_row = row_len-14; 
        var gr_total = 0.0;var gr_disc = 0.0; 
        var gr_net_total = 0.0;
        var gr_sgst=0.0;var gr_cgst=0.0; 
        var gr_igst = 0.0;
        
        for(var srno=1; srno<=inv_row;srno++)
        {
            var qty = get_value_by_id('qty'+srno);
            var rate = get_value_by_id('rate'+srno);
            var disc = get_value_by_id('disc'+srno);
            disc  = parseFloat(disc);
            var tax = get_value_by_id('tax'+srno);
            var tax_type = get_value_by_id('tax_type');

            var total = (qty*rate).toFixed(2);
            set_value_by_id('total'+srno,total);
            var net_total = (total-disc);
            set_value_by_id('total_after_discount'+srno,net_total.toFixed(2));

            if(tax_type=='1')
            {
                var tax_val = (net_total*tax/100).toFixed(2);
                set_value_by_id('sgst_per'+srno,tax);
                set_value_by_id('cgst'+srno,tax_val);
                set_value_by_id('sgst'+srno,tax_val);
                gr_cgst +=parseFloat(tax_val);
                gr_sgst +=parseFloat(tax_val);
            }
            else
            {
                var tax_val = (net_total*tax/100).toFixed(2);
                set_value_by_id('igst'+srno,tax);
                gr_igst +=parseFloat(tax_val);
            }
            
            gr_total +=parseFloat(total);
            gr_disc +=parseFloat(disc);
        }
        
        set_value_by_id('gr_total',gr_total.toFixed(2));
        set_value_by_id('gr_disc',gr_disc.toFixed(2));
        var gr_net_total = gr_total-gr_disc;
        
        set_value_by_id('gr_net_total',gr_net_total);
        set_value_by_id('gr_cgst',gr_cgst.toFixed(2));
        set_value_by_id('gr_sgst',gr_sgst.toFixed(2));
        set_value_by_id('gr_igst',gr_igst.toFixed(2));
        
        set_value_by_id('sum_total',gr_total.toFixed(2));
        set_value_by_id('sum_disc',gr_disc.toFixed(2));
        set_value_by_id('sum_net_total',gr_net_total.toFixed(2));
        set_value_by_id('sum_cgst',gr_cgst.toFixed(2));
        set_value_by_id('sum_sgst',gr_sgst.toFixed(2));
        set_value_by_id('sum_igst',gr_igst.toFixed(2));
        
        var sum_grnd = (gr_net_total+gr_cgst+gr_sgst+gr_igst);
        
        set_value_by_id('sum_grnd',sum_grnd.toFixed(2));
        
        var net_payable = Math.round(sum_grnd);
        set_value_by_id('sum_payable',net_payable);
        
        var sum_round_off = net_payable-sum_grnd;
        set_value_by_id('sum_round_off',sum_round_off.toFixed(2));
    }
    
    
    function get_mol(balance)
    {
        var mol = parseFloat(balance*1.5);
        document.getElementById('mol').value = mol;
    }
    
function form_toggle(first,second)
{
    $('#'+first).show();
    $('#'+second).hide();
}
 function isInt(n){
    return Number(n) === n && n % 1 === 0;
}

function isFloat(n){
    return Number(n) === n && n % 1 !== 0;
}

 function checkNumber(val,evt)
{
    
    var check_num = isNaN(val);
    var check_float = isFloat(val);
    var charCode = (evt.which) ? evt.which : event.keyCode;
    
    if (charCode> 31 && (charCode < 48 || charCode > 57) && charCode != 46)
        {            
		return false;
        }
    //alert(charCode);
    
    if(charCode==101 || charCode==69 || charCode==43  || charCode==45)
    {
        return false;
    }
    else if(check_num===false)
    {
        return true;
    }
    else if(check_float===true)
    {
        return true;
    }
    else if(val==='')
    {
        return true;
    }
    
    
    else
    {
        //console.log(check_num);
        return false;
    }
  
    
}

function get_partno(part_name)
 {
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-part-no',
              method: 'post',
              data: {
                 part_name: part_name 
              },
              success: function(result){
                  $('#part_no').html(result)
              }});
 }

function get_hsn_code(part_no)
 {
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-hsn-code',
              method: 'post',
              data: {
                 part_no: part_no 
              },
              success: function(result){
                  $('#hsn_code').html(result)
              }});
 }
</script>

@endsection
