@extends('layouts.app')

@section('content')

<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body"><h5 class="card-title">Edit Accessories</h5>
                            <form method="post" action="update-acc">
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
                                            <label for="exampleAddress" class="">Product Name <font color="red">*</font></label>
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
                                            <label for="exampleAddress" class="">Model Name <font color="red">*</font></label>
                                            <select name="model_id" id="model_id" class="form-control" required="">
                                                <?php
                                                        foreach($model_arr as $model)
                                                        {
                                                            echo '<option value="'.$model->model_id.'" ';
                                                            if($model->model_id==$data['model_id'])
                                                            { echo 'selected';}
                                                            echo '>'.$model->model_name.'</option>';
                                                        }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Accessories <font color="red">*</font></label>
                                            <input name="acc_name" id="acc_name" placeholder="Accessories" type="text" value="<?php echo $data['acc_name']; ?>" class="form-control" required="">
                                        </div>
                                    </div>
                                
                                 <div class="col-md-4">
                                    <div class="position-relative form-group">
                                        <label for="exampleEmail11" class="">Status</label>
                                        <select id="acc_status" name="acc_status" class="form-control" required="">
                                            <option value="1" <?php if($data['acc_status']=='1') { echo 'selected';} ?>>Active</option>
                                            <option value="0" <?php if($data['acc_status']=='0') { echo 'selected';} ?>>De-Active</option>
                                        </select>
                                    </div>
                                     </div>
                                
                                    </div>

                                
                                
                                 <input type="hidden" id="acc_id" name="acc_id" value="<?php echo $data['Acc_Id']; ?>" />
                                 <a href="add-acc" class="mt-2 btn btn-danger" data-original-title="" title="">Back</a>
                                <button type="submit"  class="mt-2 btn btn-primary">Update</button>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
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

menu_select('{{$url}}');                                                              
</script>


@endsection
