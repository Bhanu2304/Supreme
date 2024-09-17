@extends('layouts.app')

@section('content')

<script>
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

menu_select('{{$url}}');                                                              
</script>
<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body"><h5 class="card-title">Edit Model</h5>
                            <form method="post" action="update-model">
                                @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                

                                

                                <div class="form-row">
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Brand <font color="red">*</font></label>
                                            <select name="brand_id" id="brand_id" onchange="get_product_category(this.value)" class="form-control" required="">
                                                <option value="">Select</option>
                                                <?php   foreach($brand_master as $brand)
                                                        {
                                                            echo '<option value="'.$brand['brand_id'].'"';
                                                            if($data['brand_id']==$brand['brand_id'])
                                                            { echo 'selected';}
                                                            echo '>'.$brand['brand_name'].'</option>';
                                                        }
                                                        ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Product Detail <font color="red">*</font></label>
                                            <select name="product_category_id" id="product_category_id" class="form-control" required="">
                                                <option value="">Select</option>
                                                <?php   foreach($category_master as $category)
                                                        {
                                                            echo '<option value="'.$category['product_category_id'].'"';
                                                            if($data['product_category_id']==$category['product_category_id'])
                                                            { echo 'selected';}
                                                            echo '>'.$category['category_name'].'</option>';
                                                        }
                                                        ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Product Name</label>
                                            <select name="product_id" id="product_id" class="form-control" required="">
                                                <?php
                                                        foreach($product_arr as $prod)
                                                        {
                                                            echo '<option value="'.$prod->product_id.'" ';
                                                            if($prod->product_id==$data['product_id'])
                                                            { echo 'selected';}
                                                            echo '>'.$prod->product_name.'</option>';
                                                        }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Model</label>
                                            <input name="model_name" id="model_name" placeholder="Model" type="text" value="<?php echo $data['model_name']; ?>" class="form-control" required="">
                                        </div>
                                    </div>
                                

                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Details</label>
                                            <input name="model_det" id="model_det" placeholder="Details" type="text" value="<?php echo $data['model_det']; ?>" class="form-control" required="">
                                        </div>
                                    </div>


                                
                                 
                                     <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="exampleEmail11" class="">Status</label>
                                        <select id="model_status" name="model_status" class="form-control" required="">
                                            <option value="1" <?php if($data['model_status']=='1') { echo 'selected';} ?>>Active</option>
                                            <option value="0" <?php if($data['model_status']=='0') { echo 'selected';} ?>>De-Active</option>
                                        </select>
                                    </div>
                                     </div>
                                 </div>
                                 <input type="hidden" id="model_id" name="model_id" value="<?php echo $data['model_id']; ?>" />
                                 <a href="add-model" class="mt-2 btn btn-danger" data-original-title="" title="">Back</a>
                                <button type="submit"  class="mt-2 btn btn-primary">Update</button>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
 


@endsection
