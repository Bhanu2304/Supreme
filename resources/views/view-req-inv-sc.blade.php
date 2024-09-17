@extends('layouts.app')

@section('content') 

<script>
                                    

menu_select('{{$url}}');                                                             
</script>
<script>
 function checkNumber(val,evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode;
	
	if (charCode> 31 && (charCode < 48 || charCode > 57) )
        {            
            return false;
        }
        if(val.length>10)
        {
            return false;
        }
	return true;
}


</script>



<div class="app-main"> 
 <div class="app-main__outer">
<div class="app-main__inner">
     <div class="tab-content">
        <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
            <div class="main-card mb-3 card">
                <form method="post"  >
                    <div class="card-body">                                        
                        <h5 class="card-title">View Purchase Order </h5> <h5 class="card-title">Date : <?php echo date('d-m-y',strtotime($req_det->req_date)); ?></h5> <h5 class="card-title">Sr No. : <?php echo $req_det->req_no; ?></h5>
                        @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif

                        
                            <div class="form-row">
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="exampleAddress" class="">Supplier Name <font color="red">*</font></label>
                                        <select id="supplier_name" name="supplier_name" disabled=""  class="form-control" required="">
                                            <option value="">Select Supplier</option>
                                            <?php foreach($supplier_arr as $supplier){?>       
                                                <option value="<?php echo $supplier['id']; ?>" <?php if($req_det->supplier_id==$supplier['id']) { echo 'selected';} ?>><?php echo $supplier['supplier_name']; ?></option>     
                                            <?php   } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="exampleAddress" class="">Brand <font color="red">*</font></label>
                                        <select id="brand_id1" disabled="" name="brand" class="form-control" onchange="get_product_detail('1',this.value)" required="">
                                            <option value="">Brand</option>
                                            <?php
                                                    foreach($brand_arr as $brand)
                                                    {
                                                        ?>       <option value="<?php echo $brand->brand_id; ?>" <?php if($req_det->brand_id==$brand->brand_id) { echo 'selected';} ?>><?php echo $brand->brand_name; ?></option>     
                                            <?php   }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="exampleAddress" class="">Product Category<font color="red">*</font></label>
                                        <select disabled="" id="product_category_id1" name="product_category" onchange="get_product('1',this.value)" class="form-control" required="">
                                            <option value="">Product Category</option>
                                            <?php
                                                    foreach($product_det as $det)
                                                    {
                                                        ?>       <option value="<?php echo $det->product_category_id; ?>"
                                                                <?php if($req_det->product_category_id==$det->product_category_id) { echo 'selected';} ?>
                                                                ><?php echo $det->category_name; ?></option>     
                                            <?php   }
                                            ?>
                                        </select>
                                    </div>      
                                </div>
                            
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="exampleAddress" class="">Model <font color="red">*</font></label>
                                        <select id="product_id1" disabled="" name="product" onchange="get_model('1',this.value)" onchange="get_model('1',this.value)" class="form-control" required="">
                                            <option value="">Product</option>
                                            <?php
                                                    foreach($product_mas as $prod)
                                                    {
                                                        ?>       <option value="<?php echo $prod->product_id; ?>"
                                                                <?php if($req_det->product_id==$prod->product_id) { echo 'selected';} ?>
                                                                ><?php echo $prod->product_name; ?></option>     
                                            <?php   }
                                            ?>
                                        </select>
                                    </div>            
                                </div>
                            
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="exampleAddress" class="">Model No.<font color="red">*</font></label>
                                        <select id="model_id1" disabled="" name="model" onchange="get_partcode('',this.value)" onchange="get_part_name('1',this.value)" class="form-control" required="">
                                            <option value="">Model</option>
                                            <?php
                                                    foreach($model_det as $model)
                                                    {
                                                        ?>       <option value="<?php echo $model->model_id; ?>"
                                                                <?php if($req_det->model_id==$model->model_id) { echo 'selected';} ?>
                                                                ><?php echo $model->model_name; ?></option>     
                                            <?php   }
                                            ?>
                                        </select>
                                    </div>   
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Part Code </label>
                                        <select multiple="" disabled="" id="part_code" name="part_code" class="form-control" onchange="add_part(this.value)" >
                                            <option value="">Select</option>
                                            <?php
                                                    foreach($data_part_arr as $part)
                                                    {
                                                        ?>       <option value="<?php echo $part->part_no; ?>"
                                                                <?php if(1==1) { echo 'selected';} ?>
                                                                ><?php echo $part->part_no; ?></option>     
                                            <?php   }
                                            ?>
                                        </select>
                                    </div>
                                </div>   
                            </div>      
                            
                            
                        <div id="part_arr">    
                            <?php foreach($data_part_arr as $part_det) { $random_no = $part_det->spare_id; $part_name =$part_det->part_name;  ?>
                            
                            <div class="form-row">
                                    <div class="col-md-2">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Part Name <font color="red">*</font></label>
                                            <select id="part_name<?php echo $random_no;?>" disabled="" name="SparePart[<?php echo $random_no;?>][part_name]" class="form-control"  >
                                                <option value="<?php echo $part_name; ?>"><?php echo $part_name; ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Color <font color="red">*</font></label>
                                            <input type="text" autocomplete="off" disabled=""  name="SparePart[<?php echo $random_no;?>][color]" id="color<?php echo $random_no;?>" value="<?php echo $part_det->color; ?>"  class="form-control" required="" />
                                        </div>
                                    </div>
    
                                    <div class="col-md-2">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Current Stock Qty. <font color="red">*</font></label>
                                            <input type="text" autocomplete="off" disabled="" name="SparePart[<?php echo $random_no;?>][current_qty]" id="current_qty<?php echo $random_no;?>" value="<?php echo $part_det->current_qty; ?>" onkeypress="return checkNumber(this.value,event)" class="form-control" required="" />
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Required Qty. <font color="red">*</font></label>
                                            <input type="text" autocomplete="off" disabled="" name="SparePart[<?php echo $random_no;?>][req_qty]" id="req_qty<?php echo $random_no;?>" value="<?php echo $part_det->req_qty; ?>" onkeypress="return checkNumber(this.value,event)" class="form-control" required="" />
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Previous Qty. <font color="red">*</font></label>
                                            <input type="text" autocomplete="off" disabled="" name="SparePart[<?php echo $random_no;?>][previous_qty]" id="previous_qty<?php echo $random_no;?>" value="<?php echo $part_det->previous_qty; ?>" onkeypress="return checkNumber(this.value,event)" class="form-control" required="" />
                                        </div>
                                    </div>

                                    <div class="col-md-1">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Total Qty. <font color="red">*</font></label>
                                            <input type="text" autocomplete="off" disabled=""  name="SparePart[<?php echo $random_no;?>][total_qty]" id="total_qty<?php echo $random_no;?>" value="<?php echo $part_det->total_qty; ?>" onkeypress="return checkNumber(this.value,event)" class="form-control" required="" />
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-3">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Current Purchase Amount <font color="red">*</font></label>
                                            <input type="text" autocomplete="off" disabled="" name="SparePart[<?php //echo $random_no;?>][curr_amt]" id="curr_amt<?php //echo $random_no;?>" value="<?php //echo $part_det->curr_amt; ?>" onkeypress="return checkNumber(this.value,event)" class="form-control" required="" />
                                        </div>
                                    </div> -->
                                    <div class="col-md-2">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Last Purchase Amount <font color="red">*</font></label>
                                            <input type="text" disabled="" autocomplete="off" name="SparePart[<?php echo $random_no;?>][po_amt]" id="po_amt<?php echo $random_no;?>" value="<?php echo $part_det->po_amt; ?>" onkeypress="return checkNumber(this.value,event)" class="form-control" required="" />
                                        </div>
                                    </div>
                            </div>
                            <?php } ?>
                        </div>       

                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <textarea id="remarks" disabled="" name="remarks" placeholder="Remarks" required="" class="form-control"><?php echo $req_det->remarks; ?></textarea>
                                </div>
                            </div>    
                        </div>    

                        <div class="form-row">
                            <div class="col-md-2">
                                <div class="position-relative form-group">
                                    <a href="req-inv-entry-sc"  class="mt-2 btn btn-danger">Back</a>
                                     
                                </div>
                            </div>


                        </div>       
                    </div>                                      



                    <div class="app-wrapper-footer">
                        <div class="app-footer">

                        </div>   
                    </div>
                    
                    
                </form>
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
