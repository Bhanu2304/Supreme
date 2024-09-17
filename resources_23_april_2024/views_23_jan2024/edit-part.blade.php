@extends('layouts.app')

@section('content')


<script>
                                    

menu_select('{{$url}}');   

function get_product_category(brand_id)
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
                  $('#product_category_id').html(result);
                  if(brand_id == 4)
                  {
                    $('#clarion-serial-no').show();
                    $('#serial_no').prop('required', true);
                  }else{
                    $('#clarion-serial-no').hide();
                    $('#serial_no').prop('required', false);
                  }
              }});
 }
 function get_product(product_category_id)
 {
     var brand_id = $('#brand_id').val();
     
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
                 product_category_id:product_category_id
              },
              success: function(result){
                  $('#product_id').html(result);
              }});
 }
 
 function get_model(product_id)
 {
     var brand_id = $('#brand_id').val();
     var product_category_id = $('#product_category_id').val();
     
     
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
                 product_id:product_id
              },
              success: function(result){
                  $('#model_id').html(result);
              }});
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
</script>
<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                           
                            <h5 class="card-title" style="text-color:blue;">
                                Edit Spare Part</h5> 
                            @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                            <form id="spare_form" method="post" action="update-part" >
                                
                                

                                

                                <div class="form-row">
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Brand <font color="red">*</font></label>
                                            <select id="brand_id" name="brand_id" onchange="get_product_category(this.value)" class="form-control" required="">
                                                <option value="">Brand</option>
                                                <?php foreach($brand_arr as $brand){?> 
                                                    <option value="<?php echo $brand['brand_id']; ?>" <?php if($brand['brand_id']==$record['brand_id']) { echo 'selected';} ?>><?php echo $brand['brand_name']; ?></option>     
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4" id="clarion-serial-no"  style="<?php if($record['brand_id']!= 4) { echo 'display:none;';} ?>">
                                        <div class="position-relative form-group">
                                            <label>Serial No <font color="red">*</font></label>
                                            <input name="serial_no" id="serial_no" placeholder="Serial No" type="text" value="<?php echo $record['serial_no']; ?>" onkeypress="return checkNumber(this.value,event)" class="form-control" <?php if($record['brand_id']== 4) { echo 'required';} ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Product Detail <font color="red">*</font></label>
                                            <select id="product_category_id" name="product_category_id" onchange="get_product(this.value)" class="form-control" required="">
                                                <option value="">Product</option>
                                                <?php foreach($product_det as $det){ ?> 
                                                    <option value="<?php echo $det['product_category_id']; ?>" <?php if($det['product_category_id']==$record['product_id']) { echo 'selected';} ?>><?php echo $det['category_name']; ?></option>     
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Product <font color="red">*</font></label>
                                            <select id="product_id" name="product_id" onchange="get_model(this.value)" class="form-control" required="">
                                                <option value="">Product</option>
                                                <?php foreach($product_arr as $prod){?> 
                                                    <option value="<?php echo $prod['product_id']; ?>" <?php if($prod['product_id']==$record['product_id']) { echo 'selected';} ?>><?php echo $prod['product_name']; ?></option>     
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Model <font color="red">*</font></label>
                                            <select id="model_id" name="model_id" class="form-control" required="">
                                                <option value="">Model</option>
                                                <?php foreach($model_arr as $model) { ?> 
                                                            <option value="<?php echo $model['model_id']; ?>" <?php if($model['model_id']==$record['model_id']) { echo 'selected';} ?>><?php echo $model['model_name']; ?></option>     
                                                <?php   }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Part Name <font color="red">*</font></label>
                                            <input name="part_name" id="part_name" placeholder="Part Name" type="text" value="<?php echo $record['part_name']; ?>"  class="form-control" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Part No. <font color="red">*</font></label>
                                            <input name="part_no" id="part_no" placeholder="Part No." type="text" value="<?php echo $record['part_no']; ?>" class="form-control" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">HSN Code <font color="red">*</font></label>
                                            <input name="hsn_code" id="hsn_code" placeholder="HSN Code" type="text" value="<?php echo $record['hsn_code']; ?>" class="form-control" required="">
                                        </div>
                                    </div>
                                
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Landing Price <font color="red">*</font></label>
                                            <input name="landing_cost" id="landing_cost" placeholder="Landing Price" type="number" value="<?php echo $record['landing_cost']; ?>" onkeypress="return checkNumber(this.value,event);" class="form-control" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Customer Price <font color="red">*</font></label>
                                            <input name="customer_price" id="customer_price" placeholder="Customer Price" type="number" value="<?php echo $record['customer_price']; ?>"  onkeypress="return checkNumber(this.value,event);" class="form-control" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Discount Price <font color="red">*</font></label>
                                            <input name="discount" id="discount" placeholder="Discount" type="number" value="<?php echo $record['discount']; ?>"  onkeypress="return checkNumber(this.value,event);" class="form-control" required="">
                                        </div>
                                    </div>
                                
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Spare Part Tax <font color="red">*</font></label>
                                            <input name="part_tax" id="part_tax" placeholder="Tax" type="text" onkeypress="return checkNumber(this.value,event)" value="<?php echo $record['part_tax']; ?>" class="form-control" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">Status</label>
                                            <select id="part_status" name="part_status" class="form-control" required="">
                                                <option value="1" <?php if($record['part_status']=='1') { echo 'selected';} ?>>Active</option>
                                                <option value="0" <?php if($record['part_status']=='0') { echo 'selected';} ?>>De-Active</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="spare_id" value="<?php echo $record['spare_id']; ?>" />
                                <button type="submit"  class="mt-2 btn btn-primary">Update</button>
                                <a href="add-part" class="mt-2 btn btn-danger" title="Back">Back</a>
                            </form>
                                 
                        </div>    
                            
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 

@endsection
