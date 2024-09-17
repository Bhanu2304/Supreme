@extends('layouts.app')

@section('content')


<script>
                                    

menu_select('{{$url}}');                                                             
</script>

<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title">Center Inventory</h5>
                            <h5 class="card-title" style="text-color:blue;">
                                <a href="#" onclick="form_toggle('spare_form','view');" style="cursor: pointer;">Add</a> / <a href="#" onclick="form_toggle('view','spare_form');" style="cursor: pointer;">View</a>
                            </h5>
                            
                                 @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                 <form id="spare_form" method="post" action="save-allocate-inv" style="display: none;">
                                     
                                   <div class="form-row">
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Brand <font color="red">*</font></label>
                                            <select name="brand_id" id="brand_id" onchange="get_product_category('',this.value)" class="form-control" required="">
                                                <option value="">Select</option>
                                                <?php   foreach($brand_arr as $brand)
                                                        {
                                                            echo '<option value="'.$brand['brand_id'].'">'.$brand['brand_name'].'</option>';
                                                        }
                                                        ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Product Detail <font color="red">*</font></label>
                                            <select name="product_category_id" id="product_category_id" onchange="get_product('',this.value)" class="form-control" required="">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Product <font color="red">*</font></label>
                                            <select name="product_id" id="product_id" onchange="get_model('',this.value)" class="form-control" required="">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Model <font color="red">*</font></label>
                                            <select name="model_id" id="model_id" onchange="get_part_name('',this.value)" class="form-control" required="">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Part Name <font color="red">*</font></label>
                                            <select id="part_name" name="part_name" class="form-control" onchange="get_partno('',this.value)" required="">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Part No. <font color="red">*</font></label>
                                            <select id="part_no" name="part_no" onchange="get_hsn_code('',this.value)" class="form-control" required="">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">HSN Code <font color="red">*</font></label>
                                            <select id="hsn_code" name="hsn_code" class="form-control"  required="">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Stock Type <font color="red">*</font></label>
                                            <select id="stock_type" name="stock_type" class="form-control"  required="">
                                                <option value="">Select</option>
                                                <option value="Advance">Advance</option>
                                                <option value="Billing">Billing</option>
                                            </select>
                                        </div>
                                    </div>   
                                
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Stock Quantity <font color="red">*</font></label>
                                            <input name="stock_qty" id="stock_qty" placeholder="Stock Quantity" type="number" onkeypress="return checkNumber(this.value,event)" class="form-control" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Raw No. <font color="red">*</font></label>
                                            <input name="raw_no" id="raw_no" placeholder="Raw No." type="text"  class="form-control" required="">
                                        </div>
                                    </div>
                                     <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Center <font color="red">*</font></label>
                                            <select id="center_id" name="center_id" class="form-control"  required="">
                                                <option value="">Select</option>
                                                <?php
                                                        foreach($center_arr as $center)
                                                        {
                                                            ?>       <option value="<?php echo $center->center_id; ?>"><?php echo $center->center_name; ?></option>     
                                                <?php   }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>                                
                                                                                               
                                
                                
                                
                                 
                                <button type="submit"  class="mt-2 btn btn-primary">Save</button>
                                <a href="#" onclick="form_toggle('view','spare_form');" class="mt-2 btn btn-danger"   title="Cancel">Cancel</a>
                                </form>
                                 
                                 <div id="view">
                                     
                                 <table id="table_id" class="table table-striped table-bordered" style="width:100%">
                              <thead>
                                 <tr>
                                    <th>Sr.No</th>
                                    
                                    <th>Center Name</th>
                                    <th>Brand</th>
                                    <th>Product Details</th>
                                    <th>Product</th>
                                    <th>Model</th>
                                    
                                    <th>Spare Part Name</th>
                                    <th>Spare Part Code</th>
                                    <th>HSN Code</th>
                                    <th>Stock Qty</th>
                                    <th>Balance Qty</th>
                                    <th>Average Consumption</th>
                                   
                                    <th>MOL</th>
                                    <th>Action</th>
                                 </tr>
                              </thead>
                              <tbody>
                                  @php $i = 0; @endphp
                                    @foreach($stock_arr as $Data)
                                  
                                  
                                 <tr>
                                    <td>{{++$i}}</td>
                                    
                                    <td>{{$Data->center_name}}</td>
                                    <td>{{$Data->brand_name}}</td>
                                    <td>{{$Data->category_name}}</td>
                                    <td>{{$Data->product_name}}</td>
                                    <td>{{$Data->brand_name}}</td>
                                    <td>{{$Data->part_name}}</td>
                                    <td>{{$Data->part_no}}</td>
                                    <td>{{$Data->hsn_code}}</td>
                                    <td>{{$Data->stock_qty}}</td>
                                    <td class="Officer"><?php $consuption = $cnpt_arr[$Data->center_id][$Data->part_name][$Data->part_no][$Data->hsn_code]; echo round($Data->stock_qty-$consuption); ?></td>
                                    <td class="Officer"><?php echo $consuption ?></td>
                                    <td class="Officer"><?php echo round($consuption *1.5); ?></td>
                                    <td>
                                        <a href="center-inv-details?inv_id=<?php echo base64_encode($Data->inv_id); ?>">Details</a>
                                    </td>
                                    
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
                 product_id:product_id
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
                  $('#part_name').html(result);
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
                  $('#part_no').html(result);
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
                 part_name:part_name,
                 part_no: part_no 
              },
              success: function(result){
                  $('#hsn_code').html(result);
              }});
 }
 
 function get_stock(hsn_code)
 {
    var part_name = document.getElementById('part_name').value;
    var part_no = document.getElementById('part_no').value;
    var hsn_code = document.getElementById('hsn_code').value;
     
     
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-stock',
              method: 'post',
              data: {
                  part_name:part_name,
                  hsn_code:hsn_code,
                 part_no: part_no 
              },
              success: function(result){
                  $('#stock_qty').val(result)
              }});
 }
 
 $('#table_id').DataTable( );
 
</script>

@endsection
