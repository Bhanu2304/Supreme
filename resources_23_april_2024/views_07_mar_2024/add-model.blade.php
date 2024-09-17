@extends('layouts.app')

@section('content')


<script>
    menu_select('{{$url}}'); 
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
                        <div class="card-body"><h5 class="card-title"> Model</h5>
                            
                            <h5 class="card-title" style="text-color:blue;">
                                <a href="#" onclick="form_toggle('state_form','view');" style="cursor: pointer;">Add</a> / <a href="#" onclick="form_toggle('view','state_form');" style="cursor: pointer;">View</a>
                            </h5> 
                            
                            
                            <form id="state_form" method="post" action="save-model" style="display:none;">
                                @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                

                                

                                <div class="form-row">
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Brand</label>
                                            <select name="brand_id" id="brand_id" onchange="get_product_category('',this.value)" class="form-control" required="">
                                                <option value="">Select</option>
                                                <?php   foreach($brand_master as $brand)
                                                        {
                                                            echo '<option value="'.$brand['brand_id'].'">'.$brand['brand_name'].'</option>';
                                                        }
                                                        ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Product Category <font color="red">*</font></label>
                                            <select name="product_category_id" id="product_category_id" onchange="get_product('',this.value)" class="form-control" required="">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Product <font color="red">*</font></label>
                                            <select name="product_id" id="product_id" class="form-control" required="">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Model <font color="red">*</font></label>
                                            <input name="model_name" id="model_name" placeholder="Model" type="text" class="form-control" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Details</label>
                                            <input name="model_det" id="model_det" placeholder="Details" type="text" class="form-control" >
                                        </div>
                                    </div>
                                </div>

                                


                                
                                 
                                <button type="submit"  class="mt-2 btn btn-primary">Save</button>
                                <a href="#" onclick="form_toggle('view','state_form');" class="mt-2 btn btn-danger"  title="Back">Exit</a>
                            </form>
                            
                            <div id="view">
                                
                                
                                         <div class="form-row">
                                             <div class="col-md-3">
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
                                             <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Product Detail</label>
                                            <select id="product_category_id_search" name="product_category_search" onchange="get_product('_search',this.value)" class="form-control">
                                                <option value="">All</option>
                                               
                                            </select>
                                         </div>
                                             </div>         
                                             <div class="col-md-4">     
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Product</label>
                                            <select id="product_id_search" name="product_search"  class="form-control">
                                                <option value="">All</option>
                                               
                                            </select>
                                         </div>         
                                             </div>        
                                                 
                                             
                                             <div class="col-md-1">
                                            <div class="position-relative form-group">
                                                <br/>
                                                <button type="button" onclick="search_model()" class="mt-2 btn btn-primary">Search</button>
                                            </div>
                                             </div>      
                                         </div>
                                
                                
                                
                                <table id="table_id" class="table table-striped table-bordered" >
                              <thead>
                                 <tr>
                                    <th>Sr.No</th>
                                    <th>Brand</th>
                                    <th>Product Details</th>
                                    <th>Product Name</th>
                                    <th>Model Name</th>
                                    <th>Details</th>
                                    <th>Create Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                 </tr>
                              </thead>
                              <tbody>
                                  @php $i = 0; @endphp
                                    @foreach($DataArr as $Data)
                                  
                                  
                                 <tr>
                                    <td>{{++$i}}</td>
                                    <td>{{$Data->brand_name}}</td>
                                    <td>{{$Data->category_name}}</td>
                                    <td>{{$Data->product_name}}</td>
                                    <td>{{$Data->model_name}}</td>
                                    <td>{{$Data->model_det}}</td>
                                    <td class="Officer">{{$Data->created_at}}</td>
                                    <td class="Status">@if($Data->model_status=='1') {{'Active'}} @else {{'De-Active'}} @endif</td>
                                    <td class="Officer"><a href="edit-model?model_id=<?php echo base64_encode($Data->model_id); ?>" >Edit</a></td>
                                 </tr>
                                 @endforeach
                                
                              </tbody>
                           </table>
                            </div>
                            
                            
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
 
<script>
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
                 brand_id: brand_id 
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
                 product_category_id:product_category_id
              },
              success: function(result){
                  $('#product_id'+div_id).html(result);
              }});
 }
 
 function search_model()
    {
        var brand_id = $('#brand_id_search').val();
        var product_category_id = $('#product_category_id_search').val();
        var product_id = $('#product_id_search').val();
        
        $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
          
          jQuery.ajax({
              url: 'search-model',
              method: 'post',
              data: {
                 brand_id: brand_id,
                 product_category_id:product_category_id,
                 product_id:product_id
              },
              success: function(result){
                  
                  $('#table_id').html(result);
                  
              }});
    }
 
 
 
    $('#table_id').DataTable( );
    </script>

@endsection
