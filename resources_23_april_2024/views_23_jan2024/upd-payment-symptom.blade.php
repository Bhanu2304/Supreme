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
                            <h5 class="card-title">Update Payment Status</h5>
                            
                            
                            @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                            <form method="post" action="save-payment-symptom" >
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
                                        <td><?php echo 'GSTIN: '.$data['gstin']; ?></td>
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
                                        <td><?php echo "VIN NO.: ".''; ?></td>
                                    </tr>
                                    
                                    <tr>
                                        <td colspan="3"></td>
                                    </tr>
                                </table>
                                
                                <table id="part_table" border="1" width="100%" >
<!--                                <table class="table">-->
                                    
                                    
                                    
                                    
                                    
                                
                                    
                                    
                                    <tr>
                                        <td colspan="4" align="center"><b>Summary</b></td>
                                        <td colspan="2" align="center"><b>Amount</b></td>
                                    </tr>
                                    
                                    <tr>
                                        <td colspan="4" align="center"><b>Total Invoice Value</b></td>
                                        <td colspan="2" align="center" id="sum_total"><?php echo $data['total_invoice']; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" align="center"><b>Total Discounts</b></td>
                                        <td colspan="2" align="center" id="sum_disc"><?php echo $data['total_discount']; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" align="center"><b>Total Taxable Value</b></td>
                                        <td colspan="2" align="center" id="sum_net_total"><?php echo $data['total_taxable_value']; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" align="center"><b>Total CGST</b></td>
                                        <td colspan="2" align="center" id="sum_cgst"><?php echo $data['total_cgst']; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" align="center"><b>Total SGST</b></td>
                                        <td colspan="2" align="center" id="sum_sgst"><?php echo $data['total_sgst']; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" align="center"><b>Total IGST</b></td>
                                        <td colspan="2" align="center" id="sum_igst"><?php echo $data['total_igst']; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" align="center"><b>Grand Total</b></td>
                                        <td colspan="2" align="center" id="sum_grnd"><?php echo $data['grand_total']; ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" align="center"><b>Round Off</b></td>
                                        <td colspan="2" align="center" id="sum_round_off"><?php echo $data['round_off_value']; ?></td>
                                    </tr>
                                    
                                    <tr>
                                        <td colspan="4" align="center"><b>Net Payable Amount</b></td>
                                        <td colspan="2" align="center" id="sum_payable"><?php echo $data['total_payable']; ?></td>
                                    </tr>
                                    
                                    <tr>
                                        <td colspan="4" align="center"><b>Payment Status</b> <span style="color: #f00;">*</span></td>
                                        <td colspan="2" align="center">
                                            <select id="payment_status" name="payment_status" onchange="get_payment_source(this.value)" class="form-control" required="">
                                                <option value="">Select</option>
                                                <option value="Payment Received" <?php if($data['payment_status']=='Payment Received') { echo 'selected'; }  ?>>Payment Received</option>
                                                <option value="Payment not received" <?php if($data['payment_status']=='Payment not received') { echo 'selected'; }  ?>>Payment not received</option>
                                            </select>
                                        </td>
                                    </tr>
                                    
                                    <tr style="<?php if($data['payment_status']!='Payment Received') { echo "display: none;"; } ?>" id="div_source">
                                        <td colspan="4" align="center"><b>Source</b> <span style="color: #f00;">*</span></td>
                                        <td colspan="2" align="center"  >
                                            <select id="payment_source" name="payment_source" class="form-control">
                                                <option value="">Select</option>
                                                <option value="Cash" <?php if($data['payment_source']=='Cash') { echo 'selected'; }  ?>>Cash</option>
                                                <option value="Card" <?php if($data['payment_source']=='Card') { echo 'selected'; }  ?>>Card</option>
                                                <option value="Wallet" <?php if($data['payment_source']=='Wallet') { echo 'selected'; }  ?>>Wallet</option>
                                                <option value="Internet Banking" <?php if($data['payment_source']=='Internet Banking') { echo 'selected'; }  ?>>Internet Banking</option>
                                                <option value="NEFT" <?php if($data['payment_source']=='NEFT') { echo 'selected'; }  ?>>NEFT</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" align="center"><b>Symptom Code</b> <span style="color: #f00;">*</span></td>
                                        <td colspan="2" align="center">
                                            <select id="Symptom" name="Symptom" class="form-control" >
                                                            <option value="">Select</option>
                                                            <?php   foreach($sypt_master as $sypt_id=>$sypt_name)
                                                                {
                                                                    echo '<option value="'.$sypt_name.'">'.$sypt_name.'</option>';
                                                                }
                                                        ?>
                                                        </select>
                                        </td>
                                    </tr>
                                    </table>
                                
                                <input type="hidden" name="tag_id" id="tag_id" value="<?php echo $TagId; ?>" >
                                <button type="submit" onclick="return validate_payment();" class="mt-2 btn btn-primary">Save</button>
                            </form>
                                
                        </div>    
                           
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 <script>
    function get_payment_source(payment_status)
    {
        if(payment_status==='Payment Received')
        {
            $('#div_source').show();
        }
        else
        {
            $('#div_source').hide();
        }
    }
    
    function validate_payment()
    {
        var payment_status = $('#payment_status').val();
        if(payment_status==='')
        {
            alert("Please Select Payment Status");
            return false;
        }
        else if(payment_status==='Payment Received')
        {
            var payment_source = $('#payment_source').val();
            if(payment_source=='')
            {
                alert('Please Select Source of Payment');
                return false;
            }
            else
            {
                return true;
            }
        }
        else
        {
            return true;
        }
    }
    
    
</script>

@endsection
