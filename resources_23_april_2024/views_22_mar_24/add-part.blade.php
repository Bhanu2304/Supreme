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
                            <h5 class="card-title">Spare Parts</h5>
                            <h5 class="card-title" style="text-color:blue;">
                                <a href="#" onclick="form_toggle('spare_form','view');" style="cursor: pointer;">Add</a> / <a href="#" onclick="form_toggle('view','spare_form');" style="cursor: pointer;">View</a>
                            </h5> 
                                @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                            <form id="spare_form" method="post" action="save-part" style="display:none;">
                                <div class="form-row">
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Brand <font color="red">*</font></label>
                                            <select id="brand_id" name="brand_id" onchange="get_product_category('',this.value)" class="form-control" required="">
                                                <option value="">Brand</option>
                                                <?php foreach($brand_arr as $brand){ ?>       
                                                    <option value="<?php echo $brand['brand_id']; ?>"><?php echo $brand['brand_name']; ?></option>
                                                <?php  }?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4" id="clarion-serial-no" style="display:none;">
                                        <div class="position-relative form-group">
                                            <label>Serial No <font color="red">*</font></label>
                                            <input name="serial_no" id="serial_no" placeholder="Serial No" type="text" onkeypress="return checkNumber(this.value,event)" class="form-control">
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
                                            <select id="product_id" name="product_id" onchange="get_model('',this.value)" class="form-control" required="">
                                                <option value="">Product</option>
                                                
                                            </select>
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Model <font color="red">*</font></label>
                                            <select id="model_id" name="model_id" class="form-control" required="">
                                                <option value="">Model</option>
                                            </select>
                                        </div>
                                        
                                    </div>
                                    
                                
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Part Name <font color="red">*</font></label>
                                            <input name="part_name" id="part_name" placeholder="Part Name" type="text"  class="form-control" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Part No. <font color="red">*</font></label>
                                            <input name="part_no" id="part_no" placeholder="Part No." type="text"  class="form-control" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">HSN Code <font color="red">*</font></label>
                                            <input name="hsn_code" id="hsn_code" placeholder="HSN Code" type="text"  class="form-control" required="">
                                        </div>
                                    </div>
                                
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Procurement Cost <font color="red">*</font></label>
                                            <input name="landing_cost" id="landing_cost" placeholder="Landing Price" type="number" onkeypress="return checkNumber(this.value,event);" class="form-control" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Selling Price <font color="red">*</font></label>
                                            <input name="customer_price" id="customer_price" placeholder="Selling Price" type="number" onkeypress="return checkNumber(this.value,event);" class="form-control" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Discount Price <font color="red">*</font></label>
                                            <input name="discount" id="discount" placeholder="Discount" type="number" onkeypress="return checkNumber(this.value,event);" class="form-control" required="">
                                        </div>
                                    </div>
                                
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Spare Part Tax <font color="red">*</font></label>
                                            <input name="part_tax" id="part_tax" placeholder="Tax" type="text" onkeypress="return checkNumber(this.value,event)" class="form-control" required="">
                                        </div>
                                    </div>
                                </div>
                                
                                 
                                <button type="submit"  class="mt-2 btn btn-primary">Save</button>
                                <a href="#" class="mt-2 btn btn-danger" onclick="form_toggle('view','spare_form');"  title="Cancel">Cancel</a>
                            </form>
                                 
                        </div>    
                            <div id="view" class="card-body">    
                                <div class="form-row">
                                            <div class="col-md-2">
                                                <div class="position-relative form-group">
                                                    <label for="exampleAddress" class="">Brand</label>
                                                    <select id="brand_id_search" name="brand_search" onchange="get_product_category('_search',this.value)" class="form-control">
                                                    <option value="">All</option>
                                                    <?php   foreach($brand_arr as $brand)
                                                            {
                                                                echo '<option value="'.$brand['brand_id'].'">'.$brand['brand_name'].'</option>';
                                                            }?>
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
                                                 
                                             
                                             <div class="col-md-1">
                                            <div class="position-relative form-group">
                                                <br/>
                                                <button type="button" onclick="search_part()" class="mt-2 btn btn-primary">Search</button>
                                            </div>
                                             </div>      
                                         </div>
                                <table class="table table-bordered data-table" id="table_id">
                                <thead>
                                  <tr>	
                                      <th>S.No.</th> 
                                      <th>Brand</th>
                                      <th>Product Detail</th>
                                      <th>Product</th>
                                      <th>Model</th>
                                      <th>Spare Part Name</th>
                                      <th>Part No.</th> 
                                      <th>HSN Code</th>
                                      <th>Landing Cost</th>
                                      <th>Customer Price</th>
                                      <th>Discount</th>
                                      <th>Tax</th>
                                      <th>Status</th>
                                      <th>Action</th> 
                                  </tr>
                                </thead>
                                <tbody>
                                   <?php  
                                      //$Select = "SELECT * FROM `tbl_agent` WHERE ChannelType = 'Agent'";
                                      //$Query  = mysql_query($Select); 
                                      $i = 1;
                                    foreach($part_arr as $Data)
                                    {
                                   ?> 
                                          <tr> 
                                          <td><?php echo $i++;?></td>
                                          <td><?php echo $Data->brand_name;?></td>
                                          <td><?php echo $Data->category_name;?></td>
                                          <td><?php echo $Data->product_name;?></td>
                                          <td><?php echo $Data->model_name;?></td>
                                          <td><?php echo $Data->part_name;?></td>
                                          <td><?php echo $Data->part_no; ?></td>
                                          <td><?php echo $Data->hsn_code; ?></td>
                                          <td><?php echo $Data->landing_cost; ?></td>
                                          <td><?php echo $Data->customer_price; ?></td>
                                          <td><?php echo $Data->discount; ?></td>
                                          <td><?php echo $Data->part_tax; ?></td>
                                          
                                          <td class="Status">@if($Data->part_status=='1') {{'Active'}} @else {{'De-Active'}} @endif</td>
                                          <td class="Officer"><a href="edit-part?spare_id=<?php echo base64_encode($Data->spare_id); ?>" >Edit</a></td>
                                          </tr> 
                              <?php }  ?>
                                </tbody>
                            </table>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 <script>
     
function form_toggle(first,second)
{
    $('#'+first).show();
    $('#'+second).hide();
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
 
 function search_part()
    {
        var brand_id = $('#brand_id_search').val();
        var product_category_id = $('#product_category_id_search').val();
        var product_id = $('#product_id_search').val();
        var model_id = $('#model_id_search').val();
        
        $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
          
          jQuery.ajax({
              url: 'search-part',
              method: 'post',
              data: {
                 brand_id: brand_id,
                 product_category_id:product_category_id,
                 product_id:product_id,
                 model_id:model_id
              },
              success: function(result){
                  
                  $('#table_id').html(result);
                  
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

$('#table_id').DataTable( );
</script>

@endsection
