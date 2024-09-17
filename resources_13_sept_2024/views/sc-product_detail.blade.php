@extends('layouts.app')
@section('content')

<script>
menu_select('{{$url}}');  

function reloadPage(){
    location.reload(true);
}

function get_prod(){
    var brand_id  =   $("#brand_id_search").val();
    var product_category_id       =   $.trim($("#product_category_id_search").val());
    var center       =   $.trim($("#center_id_search").val());
    var product_id       =   $.trim($("#product_id_search").val());
    var model_id       =   $.trim($("#model_id_search").val());
    
    $.post('get-sc-product',{brand_id:brand_id,product_category_id:product_category_id,center_id:center,product_id:product_id,model_id:model_id}, function(data){
        $('#product_view').html(data);
    }); 
     
}

function get_product_category(div_id,brand_id)
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
                  $('#product_category_id'+div_id).html(result);
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
                 product_id:product_id,
                 all:'1'
              },
              success: function(result){
                  $('#model_id'+div_id).html(result);
              }});
 }


function validate_prod(){
    $("#msgerr").remove();
    
    var brand_id  =   $("#brand_id").val();
    var product_category_id       =   $.trim($("#product_category_id").val());
    var center       =   $.trim($("#center_id").val());
    var product_id       =   $.trim($("#product_id").val());
    var model_id       =   $.trim($("#model_id").val());
     
    
    if(brand_id ==""){
        $("#brand_id").focus();
        $("#brand_id").after("<span id='msgerr' style='color:red;'>Please Select Brand.</span>");
        return false;
    }
    else if(product_category_id ==""){
        $("#product_category_id").focus();
        $("#product_category_id").after("<span id='msgerr' style='color:red;'>Please Select Product Detail.</span>");
        return false;
    }
    else if(center ==""){
        $("#center_id").focus();
        $("#center_id").after("<span id='msgerr' style='color:red;'>Please Select Service Center.</span>");
        return false;
    }
    else if(product_id ==""){
        $("#product_id").focus();
        $("#product_id").after("<span id='msgerr' style='color:red;'>Please Select Product.</span>");
        return false;
    }
    else if(model_id ==""){
        $("#model_id").focus();
        $("#model_id").after("<span id='msgerr' style='color:red;'>Please Select Model.</span>");
        return false;
    }
    
    
    return true;    
}



 

function form_toggle(first,second)
{
    $('#'+first).show();
    $('#'+second).hide();
}
</script>

<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title">Map Product Detail</h5>
                            <h5 class="card-title" style="text-color:blue;">
                                <a href="#" onclick="form_toggle('pin_form','table_id');" style="cursor: pointer;">Add</a> / <a href="#" onclick="form_toggle('table_id','pin_form');" style="cursor: pointer;">View</a>
                            </h5> 
                            
                                @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                  
                                 <form id="pin_form"  method="post" action="{{route('save-map-product')}}" class="form-horizontal" onSubmit="return validate_pincode()" style="display: none;">
                           
                            <div class="form-row">
                                <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">Service Center</label>
                                            <select name="center_id" id="center_id"  class="form-control chzn-select chzn-rtl" tabindex="9" required="">
                                                <option value="">Select</option>
                                                @foreach($center_arr as $center)
                                                <option value="{{$center->center_id}}">{{$center->center_name}} - {{$center->state_name}} - {{$center->city}} - {{$center->pincode}}</option>
                                                
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Brand <font color="red">*</font></label>
                                            <select name="brand_id" id="brand_id" onchange="get_product_category('',this.value)" class="form-control" required="">
                                                <option value="">Select</option>
                                                <option value="All">All</option>
                                                <?php   foreach($brand_master as $brand)
                                                        {
                                                            echo '<option value="'.$brand['brand_id'].'">'.$brand['brand_name'].'</option>';
                                                        }
                                                        ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Product Category <font color="red">*</font></label>
                                            <select name="product_category_id" id="product_category_id" onchange="get_product('',this.value)" class="form-control" required="">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Product <font color="red">*</font></label>
                                            <select name="product_id" id="product_id" onchange="get_model('',this.value)" class="form-control" required="">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Model <font color="red">*</font></label>
                                            <select name="model_id" id="model_id" class="form-control" required="">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                            </div>
                            
                            
                            
                                
                            
                            <div class="form-row">
                                
                                
                                     <div class="col-md-6">
                                         <input type="submit"  class="btn btn-success btn-grad" data-original-title="" onclick="return validate_prod()" title="" value="Save" >
                                &nbsp;<a href="map-product-detail" class="btn btn-danger btn-grad" data-original-title="" title="">Exit</a>
                            </div>
                            </div> 
                        </form>
                                 <div id="table_id">
                                     <div class="form-row">
                                         <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">Service Center</label>
                                            <select name="center_id" id="center_id_search"  class="form-control">
                                                <option value="">Select</option>
                                                @foreach($center_arr as $center)
                                                <option value="{{$center->center_id}}">{{$center->center_name}} - {{$center->state_name}} - {{$center->city}} - {{$center->pincode}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Brand</label>
                                            <select id="brand_id_search" name="brand_search" onchange="get_product_category('_search',this.value)" class="form-control">
                                             <option value="">All</option>
                                             <?php   foreach($brand_master as $brand)
                                                        {
                                                            echo '<option value="'.$brand['brand_id'].'">'.$brand['brand_name'].'</option>';
                                                        }
                                                        ?>
                                         </select>
                                         </div>
                                             </div>
                                             <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Product Category</label>
                                            <select id="product_category_id_search" name="product_category_search" onchange="get_product('_search',this.value)" class="form-control">
                                                <option value="">All</option>
                                               
                                            </select>
                                         </div>
                                             </div>         
                                             <div class="col-md-2">     
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Product</label>
                                            <select id="product_id_search" name="product_search" onchange="get_model('_search',this.value)" class="form-control">
                                                <option value="">All</option>
                                               
                                            </select>
                                         </div>         
                                             </div>
                                         
                                         <div class="col-md-2">     
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Model</label>
                                            <select id="model_id_search" name="model_search"  class="form-control">
                                                <option value="">All</option>
                                               
                                            </select>
                                         </div>         
                                             </div>
                                 <div class="col-md-3">
                                     <div class="position-relative form-group">
                                         <label for="examplePassword11" class="">&nbsp;</label><br>
                                         
                                         <button type="button"  class="btn btn-success btn-grad" data-original-title="" title="" onclick="get_prod()" value="Search" > Search</button>
                                     </div>
                                     </div>        
                                              
                            </div>
                            
                            
                            
                                     <div id="product_view">
                                         
                                     </div>
                            
                            
                                 </div>         
                                 
                                 
                                 
                    </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection
