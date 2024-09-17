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
                <form method="post" action="upd-req-inv-entry" >
                    <div class="card-body">                                        
                        <h5 class="card-title">Edit Inventory Request</h5>
                        @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif

                        <div id="part_arr">
                            
                            <?php foreach($data_part_arr as $part_det) { ?>
                            
                            <div class="form-row">
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Brand <font color="red">*</font></label>
                                            <select id="brand_id<?php echo $part_det->req_part_id; ?>" name="SparePart[brand_id][]" onchange="get_product_detail('<?php echo $part_det->req_part_id; ?>',this.value)" class="form-control" required="">
                                                <option value="">Brand</option>
                                                <?php
                                                        foreach($brand_arr as $brand)
                                                        {
                                                            ?>       <option value="<?php echo $brand->brand_id; ?>" <?php if($part_det->brand_id==$brand->brand_id) { echo 'selected';} ?>><?php echo $brand->brand_name; ?></option>     
                                                <?php   }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                
                                <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Product Detail<font color="red">*</font></label>
                                            <select id="product_category_id<?php echo $part_det->req_part_id; ?>" name="SparePart[product_category_id][]" onchange="get_product('<?php echo $part_det->req_part_id; ?>',this.value)" class="form-control" required="">
                                                <option value="">Product Detail</option>
                                                <?php
                                                        foreach($record['product_category_id'][$part_det->brand_id] as $key=>$part)
                                                        {
                                                            ?>       <option value="<?php echo $key; ?>"
                                                                    <?php if($part_det->product_category_id==$key) { echo 'selected';} ?>
                                                                    ><?php echo $part; ?></option>     
                                                <?php   }
                                                ?>
                                            </select>
                                        </div>
                                        
                                    </div>
                                
                                
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Product <font color="red">*</font></label>
                                            <select id="product_id<?php echo $part_det->req_part_id; ?>" name="SparePart[product_id][]" onchange="get_model('<?php echo $part_det->req_part_id; ?>',this.value)" class="form-control" required="">
                                                <option value="">Product</option>
                                                <?php
                                                        foreach($record['product_id'][$part_det->product_category_id] as $key=>$part)
                                                        {
                                                            ?>       <option value="<?php echo $key; ?>"
                                                                    <?php if($part_det->product_id==$key) { echo 'selected';} ?>
                                                                    ><?php echo $part; ?></option>     
                                                <?php   }
                                                ?>
                                            </select>
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Model <font color="red">*</font></label>
                                            <select id="model_id<?php echo $part_det->req_part_id; ?>" name="SparePart[model_id][]" onchange="get_part_name('<?php echo $part_det->req_part_id; ?>',this.value)" class="form-control" required="">
                                                <option value="">Model</option>
                                                <?php
                                                        foreach($record['model_id'][$part_det->product_id] as $key=>$part)
                                                        {
                                                            ?>       <option value="<?php echo $key; ?>"
                                                                    <?php if($part_det->model_id==$key) { echo 'selected';} ?>
                                                                    ><?php echo $part; ?></option>     
                                                <?php   }
                                                ?>
                                            </select>
                                        </div>
                                        
                                    </div>
                                
                                
                                
                                    <div class="col-md-2">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Spare Part </label>
                                            <select id="part_name<?php echo $part_det->req_part_id; ?>" name="SparePart[part_name][]" class="form-control" onchange="get_partno('<?php echo $part_det->req_part_id; ?>',this.value)" >
                                                <option value="">Select</option>
                                                <?php
                                                        foreach($record['part_name'][$part_det->model_id] as $part)
                                                        {
                                                            ?>       <option value="<?php echo $part; ?>"
                                                                    <?php if($part_det->part_name==$part) { echo 'selected';} ?>
                                                                    ><?php echo $part; ?></option>     
                                                <?php   }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Part No. </label>
                                            <select id="part_no<?php echo $part_det->req_part_id; ?>" name="SparePart[part_no][]" class="form-control" onchange="get_hsn_code('<?php echo $part_det->req_part_id; ?>',this.value)" >
                                                <option value="">Select</option>
                                                <?php
                                                        foreach($record['part_no'][$part_det->part_name] as $part)
                                                        {
                                                            ?>       <option value="<?php echo $part; ?>"
                                                                    <?php if($part_det->part_no==$part) { echo 'selected';} ?>
                                                                    ><?php echo $part; ?></option>     
                                                <?php   }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">HSN Code </label>
                                            <select id="hsn_code<?php echo $part_det->req_part_id; ?>" name="SparePart[hsn_code][]"  class="form-control" onchange="get_rate('rate<?php echo $part_det->req_part_id; ?>','<?php echo $part_det->req_part_id; ?>')" required="">
                                                <?php
                                                        foreach($record['hsn_code'][$part_det->part_no] as $part)
                                                        {
                                                            ?>       <option value="<?php echo $part; ?>"
                                                                    <?php if($part_det->hsn_code==$part) { echo 'selected';} ?>
                                                                    ><?php echo $part; ?></option>     
                                                <?php   }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-1">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Rate </label>
                                            <input type="text" name="SparePart[rate][]" id="rate<?php echo $part_det->req_part_id; ?>" value="<?php echo $part_det->rate; ?>" onkeypress="return checkNumber(this.value,event)" onblur="cal_tot_val('<?php echo $part_det->req_part_id; ?>')" class="form-control" required="" />
                                        </div>
                                    </div>

                                    <div class="col-md-1">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Quantity </label>
                                            <input type="text" name="SparePart[qty][]" id="qty<?php echo $part_det->req_part_id; ?>" value="<?php echo $part_det->qty; ?>" onkeypress="return checkNumber(this.value,event)" onblur="cal_tot_val('<?php echo $part_det->req_part_id; ?>')" class="form-control" required="" />
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Total </label>
                                            <input type="text" name="SparePart[total][]" id="total<?php echo $part_det->req_part_id; ?>" value="<?php echo $part_det->total; ?>" class="form-control" required="" />
                                        </div>
                                    </div>

                                    <div class="col-md-1">
                                        <div class="position-relative form-group"><br/><br/>
                                            <span class="fa fa-plus" style="width:80px;" onclick="add_part();"></span>
                                        </div>
                                    </div>

                            </div>
                            <?php } ?>
                        </div>       

                        <div class="form-row">
                            <div class="col-md-6">
                                <div class="position-relative form-group">
                                    <textarea id="remarks" name="remarks" placeholder="Remarks" required="" class="form-control"><?php echo $req_det->remarks; ?></textarea>
                                </div>
                            </div>    
                        </div>    

                        <div class="form-row">
                            <div class="col-md-2">
                                <div class="position-relative form-group">
                                    <a href="view-req-inv"  class="mt-2 btn btn-danger">Back</a>
                                     <button type="submit"  class="mt-2 btn btn-primary" >Update</button>
                                </div>
                            </div>


                        </div>       
                    </div>                                      



                    <div class="app-wrapper-footer">
                        <div class="app-footer">

                        </div>   
                    </div>
                    <input type="hidden" id="req_id" name="req_id" value="<?php echo $req_det->req_id; ?>">
                    
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
                 brand_id: brand_id 
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
                 product_category_id:product_category_id
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
                 product_id: product_id 
              },
              success: function(result){
                  $('#model_id'+div_id).html(result);
              }});
 }

function get_part_name(div_id,model_id)
 {
     
     var brand_id = $('#brand_id'+div_id).val();
    var product_category_id = $('#product_category_id'+div_id).val();
    var product_id = $('#product_id'+div_id).val();
     
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
                 model_id: model_id 
              },
              success: function(result){
                  $('#part_name'+div_id).html(result)
              }});
 }
    
    function get_partno(div_id,part_name)
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
              url: 'get-part-no',
              method: 'post',
              data: {
                 brand_id: brand_id,
                 product_category_id:product_category_id,
                 product_id:product_id,
                 model_id: model_id,
                 part_name: part_name 
              },
              success: function(result){
                  $('#part_no'+div_id).html(result);
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
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-add-req-part',
              method: 'post',
              
              success: function(result){
                  $('#part_arr').append(result);
              }});
 }
 
 function del_part(del_div)
 {
     $('#'+del_div).remove();
 }
 
 
</script>

@endsection
