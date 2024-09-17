@extends('layouts.app')

@section('content') 

<script>
menu_select('{{$url}}');

function get_npc_estimation()
{
    var part_str = $('#part_arr_str').val();
    var part_arr = part_str.split(",");
   var grand_total = 0;
   for (const part_id of part_arr) {
    var customer_amount = $('#customer_price'+part_id).val();
    var gst_per = $('#gst'+part_id).val();  
    var no_pen_part = $('#no_pen_part'+part_id).val();  
     var total = 0;
     var gst=0;
     var net_total = 0;
     if(customer_amount!=='' && gst!=='')
     {
         total = parseFloat(customer_amount)*parseFloat(no_pen_part);
         gst = total*parseFloat(gst_per)/100;
         net_total = total+gst;
     }
     else if(customer_amount!=='')
     {
        net_total = total = parseFloat(customer_amount)*parseFloat(no_pen_part);
     }
     
     $('#total'+part_id).html(net_total);
     grand_total += net_total;
     
    }
   
   $('#total_estimation').html(grand_total);
    
}
</script>

<script type="text/javascript" src="./js/job_case.js"></script>

<?php $tab = Session::get('tab'); ?>

<div class="app-main"> 
    <div class="app-main__outer">
        <div class="app-main__inner">
        
     <div class="tab-content">
         @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
         @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
         <div class="tab-pane tabs-animation fade <?php if($tab!=='1') { ?>show active <?php } ?>" id="tab-content-0" role="tabpanel">
             <div class="main-card mb-3 card"> 
                    <div class="card-body">                                        
                        <h5 class="card-title">Approve Job Estimate Request</h5>
                        <form method="post" name="save-npc-job-estimate-approval" id="save_npc_estimate_approval" action="save-npc-estimate-approval">
                        <table border="1" id="tbl_part">
                            <thead id="thead">
                            <tr>
                                <th>Sr. No.</th>
                                <th>Part Name</th>
                                <th>Part Number</th>
                                <th>Quantity</th>
                                <th>Color</th>
                                <th>Part Type</th>
                                <th>Customer Price Per Unit</th>
                                <th>GST %</th>
                                <th>Total Amount</th>
                                <th>Action</th>
                            </tr>  
                                <?php $i = 1; $partid_arr = array(); foreach($tagg_part as $tpart) { ?>
                                    <tr id="tr<?php echo $tpart->part_id; ?>">
                                        <td>{{$i++}}</td>
                                        <td>
                                            <select id="part_name<?php echo $tpart->part_id; ?>"   class="form-control" name="SparePart[<?php echo $tpart->part_id;?>][spare_id]" onchange="get_partno('<?php echo $tpart->part_id;?>',this.value)" required="">
                                            <option value="">Select</option>
                                            <?php
                                                    foreach($part_arr as $part)
                                                    {
                                                        ?><option value="<?php echo $part->spare_id; ?>" <?php if($part->spare_id==$tpart->spare_id) { echo 'selected';} ?>><?php echo $part->part_name; ?></option>     
                                                    <?php } ?>
                                    
                                        </select>
                                        </td>
                                        <td>
                                            <select id="part_no<?php echo $tpart->part_id; ?>" name="SparePart[<?php echo $tpart->part_id;?>][part_no]"  class="form-control" required="" >
                                                <option value="<?php echo $tpart->part_no; ?>"><?php echo $tpart->part_no; ?></option>
                                            </select>
                                        </td>
                                        <td>
                                            <input onKeyPress="return checkNumber(this.value,event);" maxlength="5" name="SparePart[<?php echo $tpart->part_id;?>][pending_parts]" id="no_pen_part<?php echo $tpart->part_id; ?>" class="form-control"  type="text"  value="<?php echo $tpart->pending_parts;?>" >
                                        </td>
                                        <td>
                                            <input id="color<?php echo $tpart->part_id; ?>" name="SparePart[<?php echo $tpart->part_id;?>][color]" class="form-control" type="text"  value="<?php echo $tpart->color;?>" >
                                        </td>
                                        <td>
                                            <select id="charge_type<?php echo $tpart->part_id; ?>" name="SparePart[<?php echo $tpart->part_id;?>][charge_type]" class="form-control"  >
                                                <option value="Chargeable" <?php if($tpart->charge_type=='Chargeable') { echo 'selected';} ?>>Chargeable</option>
                                                <option value="Non Chargeable" <?php if($tpart->charge_type=='Non Chargeable') { echo 'selected';} ?>>Non Chargeable</option>
                                            </select>
                                        </td>

                                        <td>
                                            <input maxlength="5" name="SparePart[<?php echo $tpart->part_id;?>][customer_price]" id="customer_price<?php echo $tpart->part_id; ?>" class="form-control cust_price"  type="text"  value="<?php echo $tpart->customer_price;?>" onblur="get_npc_estimation();"<?php if($tpart->charge_type=='Non Chargeable') { echo 'readonly';}else{echo "required";} ?>>
                                        </td>
                                        <td>
                                           
                                            <input list="browsers" id="gst<?php echo $tpart->part_id; ?>" name="SparePart[<?php echo $tpart->part_id;?>][gst]" class="form-control gst" onchange="get_npc_estimation()">
                                                <datalist id="browsers">
                                                    <option value="10">
                                                    <option value="15">
                                                    <option value="20">
                                                    <option value="25">
                                                    <option value="30">
                                                </datalist>
                                        </td>
                                        <td id="total<?php echo $tpart->part_id;?>">
                                            <?php echo $tpart->total;?>
                                        </td>
                                        <td>
                                            <button type="button" class="mt-2 btn btn-danger" onclick="del_part('<?php echo $tpart->part_id; ?>');" >Remove</button>
                                        </td>
                                    </tr>

                                <?php $partid_arr[] = $tpart->part_id; } 
                                    foreach($labr_part as $tpart) { $tpart->part_id = $tpart->tlp_id; ?>
                                    <tr id="tr<?php echo $tpart->part_id; ?>">
                                        <td>{{$i++}}</td>
                                        <td>
                                            <select id="part_name<?php echo $tpart->part_id; ?>"   class="form-control" name="LabPart[<?php echo $tpart->part_id;?>][spare_id]" onchange="get_partno('<?php echo $tpart->part_id;?>',this.value)" required="">
                                            <option value="">Select</option>       
                                        <?php  foreach($lab_part as $lcd)
                                            {
                                        ?>        <option value="<?php echo $lcd->symptom_type; ?>" <?php if($lcd->symptom_type==$tpart->symptom_type) { echo 'selected';} ?>><?php echo $lcd->symptom_type; ?></option>    
                                    <?php   } ?>
                                        </select>
                                        </td>
                                        <td>
                                            <select id="part_no<?php echo $tpart->part_id; ?>" name="LabPart[<?php echo $tpart->part_id;?>][part_no]"  class="form-control" required="" >
                                                <option value="<?php echo $tpart->lab_id; ?>"><?php echo $tpart->symptom_name; ?></option>
                                            </select>
                                        </td>
                                        <td>
                                            <input onKeyPress="return checkNumber(this.value,event);" maxlength="5" name="LabPart[<?php echo $tpart->part_id;?>][pending_parts]" id="no_pen_part<?php echo $tpart->part_id; ?>" class="form-control"  type="text"  value="<?php echo $tpart->pending_parts;?>" >
                                        </td>
                                        <td>
                                            <input id="color<?php echo $tpart->part_id; ?>" name="LabPart[<?php echo $tpart->part_id;?>][color]" class="form-control" type="text"  value="<?php echo $tpart->color;?>" >
                                        </td>
                                        <td>
                                            <select id="charge_type<?php echo $tpart->part_id; ?>" name="LabPart[<?php echo $tpart->part_id;?>][charge_type]" class="form-control"  >
                                                <option value="Chargeable" <?php if($tpart->charge_type=='Chargeable') { echo 'selected';} ?>>Chargeable</option>
                                                <option value="Non Chargeable" <?php if($tpart->charge_type=='Non Chargeable') { echo 'selected';} ?>>Non Chargeable</option>
                                            </select>
                                        </td>

                                        <td>
                                            <input maxlength="5" name="LabPart[<?php echo $tpart->part_id;?>][customer_price]" id="customer_price<?php echo $tpart->part_id; ?>" class="form-control cust_price"  type="text"  value="<?php echo $tpart->customer_price;?>" onblur="get_npc_estimation();" required="">
                                        </td>
                                        <td>
                                            <input maxlength="5" name="LabPart[<?php echo $tpart->part_id;?>][gst]" id="gst<?php echo $tpart->part_id; ?>" class="form-control gst"  type="text"  value="<?php echo $tpart->gst;?>" onblur="get_npc_estimation()"  required="">
                                        </td>
                                        <td id="total<?php echo $tpart->part_id;?>">
                                            <?php echo $tpart->total;?>
                                        </td>
                                        <td>
                                            <button type="button" class="mt-2 btn btn-danger" onclick="del_part('<?php echo $tpart->part_id; ?>');" >Remove</button>
                                        </td>
                                    </tr>

                                <?php $partid_arr[] = $tpart->part_id; } ?>
                                </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5"></td>
                                    <td colspan="3" style="text-align:right;background: yellow;">Total Estimation</td>
                                    <td id="total_estimation" style="background:papayawhip;"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="10" align="right"><button id="npc_approve"   type="submit" class="mt-2 btn btn-primary"  >Save Part Price</button> </td>
                                </tr>
                            </tbody>
                        </table>  
                        <input  type="hidden" id="tag_id" name="tag_id" value="<?php echo $tag_id;?>" />
                        <input  type="hidden" id="part_arr_str" name="part_arr_str" value="<?php echo implode(",",$partid_arr);?>" />
                        </form>       

                            

                              
                    </div>                                      



                    <div class="app-wrapper-footer">
                        <div class="app-footer">

                        </div>   
                    </div>
                
        </div>
         </div>
        
         
    </div>
        </div>
    </div>  
</div> 

<script>
    
    function set_value_by_id(div_id,value)
    {
        $('#'+div_id).val(value);
    }
    function get_value_by_id(div_id)
    {
        var div_value = $('#'+div_id).val();
        if(div_value==='')
        {
            return 0;
        }
        else
        {
            return div_value;
        }
    }
    function cal_tot_val(srno)
    {
        var qty = get_value_by_id('qty'+srno);
        var rate = get_value_by_id('rate'+srno);
        var total = qty*rate;
        set_value_by_id('total'+srno,total.toFixed(2));
        
        
        get_total_summary();
    }
    function get_total_summary()
    {
        var rate_arr = document.getElementsByName('SparePart[rate][]');
        var qty_arr = document.getElementsByName('SparePart[qty][]');
        
        var total = 0;
        for (var i = 0; i <rate_arr.length; i++) {
            var rate=rate_arr[i].value;
            //console.log("parePart[rate]["+i+"].value="+inp.value);
            var qty = qty_arr[i].value;
            total += parseFloat(rate)*parseFloat(qty);
            //console.log(total);
        }
        //alert(total);
        document.getElementById('total').value = total.toFixed(2);


    }
    
function get_product_detail(div_id,brand_id)
{
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-product-category-by-brand-id',
              method: 'post',
              data: {
                 brand_id: brand_id,
                 all:'1'
              },
              success: function(result){
                  $('#product_category_id'+div_id).html(result)
              }});
 }
 
 function get_product(div_id,product_category_id)
 {
     var brand_id = $('#brand_id'+div_id).val();
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-product-by-brand-id',
              method: 'post',
              data: {
                 brand_id: brand_id,
                 product_category_id:product_category_id,
                 all:'1'
              },
              success: function(result){
                  $('#product_id'+div_id).html(result);
              }});
 }
 
 function get_model(div_id,product_id)
 {
     var brand_id = $('#brand_id'+div_id).val();
    var product_category_id = $('#product_category_id'+div_id).val();
     
     
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-model-by-product-id',
              method: 'post',
              data: {
                  brand_id: brand_id,
                 product_category_id:product_category_id,
                 product_id: product_id,
                 all:'1'
              },
              success: function(result){
                  $('#model_id'+div_id).html(result);
              }});
 }

function get_part_name(div_id,part_code)
 {
     
     var brand_id = $('#brand_id'+div_id).val();
     var product_category_id = $('#product_category_id'+div_id).val();
     var product_id = $('#product_id'+div_id).val();
     var model_id = $('#model_id'+div_id).val();
     
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-part-name',
              method: 'post',
              data: {
                 brand_id: brand_id,
                 product_category_id:product_category_id,
                 product_id:product_id,
                 model_id: model_id,
                 part_code: part_code
              },
              success: function(result){
                  $('#part_name'+div_id).html(result)
              }});
 }
    
 function get_partcode(div_id,model_id)
 {
    var brand_id = $('#brand_id1'+div_id).val();
    var product_category_id = $('#product_category_id1'+div_id).val();
    var product_id = $('#product_id1'+div_id).val();
    
     
     
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-part-no',
              method: 'post',
              data: {
                 brand_id: brand_id,
                 product_category_id:product_category_id,
                 product_id:product_id,
                 model_id: model_id
                  
              },
              success: function(result){
                  $('#part_code'+div_id).html(result);
              }});
 }

function get_hsn_code(div_id,part_no)
 {
     var brand_id = $('#brand_id'+div_id).val();
    var product_category_id = $('#product_category_id'+div_id).val();
    var product_id = $('#product_id'+div_id).val();
    var model_id = $('#model_id'+div_id).val();
     var part_name = $('#part_name'+div_id).val();
     
     
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-hsn-code',
              method: 'post',
              data: {
                  brand_id: brand_id,
                 product_category_id:product_category_id,
                 product_id:product_id,
                 model_id: model_id,
                 part_name: part_name,
                 part_no: part_no 
              },
              success: function(result){
                  $('#hsn_code'+div_id).html(result);
              }});
 }
    
function get_rate(div_id,index)
 {
     
     var brand_id = $('#brand_id'+index).val();
    var product_category_id = $('#product_category_id'+index).val();
    var product_id = $('#product_id'+index).val();
    var model_id = $('#model_id'+index).val();
     var part_name = $('#part_name'+index).val();
     var part_no = $('#part_no'+index).val();
     var hsn_code = $('#hsn_code'+index).val();
     
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-part-rate',
              method: 'post',
              data: {
                  brand_id: brand_id,
                 product_category_id:product_category_id,
                 product_id:product_id,
                 model_id: model_id,
                 part_name: part_name,
                 part_no: part_no,
                 hsn_code: hsn_code
              },
              success: function(result){
                  $('#'+div_id).val(result);
                  var qty = $('#qty'+index).val();
                  var total = qty*parseFloat(result);
                  $('#total'+index).val(total);
                  
              }});
 }    
    
    
 function add_part()
 {
     var div_id = '1';
     var brand_id = $('#brand_id'+div_id).val();
     var product_category_id = $('#product_category_id'+div_id).val();
     var product_id = $('#product_id'+div_id).val();
     var model_id = $('#model_id'+div_id).val();
     
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
          
          $('#part_arr').html('');
    for (var option of document.getElementById('part_code').options)
    {
        if (option.selected) {
            var sp_id = option.value;
            jQuery.ajax({
              url: 'get-add-req-part',
              method: 'post',
              data:{sp_id:sp_id,brand_id:brand_id,product_category_id:product_category_id,product_id:product_id,model_id:model_id},
              
              success: function(result){
                  $('#part_arr').append(result);
              }});
        }
        
    }
     
     
     
          
     
          
    
 }
 
 function del_part(del_div)
 {

     $('#'+del_div).remove();
 }
 
 
</script>

@endsection
