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
                        <div class="card-body"><h5 class="card-title">Set Condition</h5>
                            <h5 class="card-title" style="text-color:blue;">
                                <a href="#" onclick="form_toggle('add_cndn','view');" style="cursor: pointer;">Add</a> / <a href="#" onclick="form_toggle('view','add_cndn');" style="cursor: pointer;">Preview</a>
                            </h5>
                            <form method="post" action="update-cndn" id="add_cndn">
                                @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                
                                 <table class="table">
                                     <tr>
                                         <th>Brand</th>
                                         <th>Product Detail</th>
                                         <th>Product</th>
                                         <th>Model</th>
                                         <th>Field</th>
                                         <th>Option#1</th>
                                         <th>Option#2</th>
                                         <th>Type</th>
                                         <th>Priority</th>
                                         <th>Action</th>
                                     </tr>
                                     
                                     <tr>
                                         <td><select name="brand_id" id="brand_id" onchange="get_product_category('',this.value)"  >
                                                <option value="">Select</option>
                                                <?php   foreach($brand_master as $brand)
                                                        {
                                                            echo '<option value="'.$brand['brand_id'].'">'.$brand['brand_name'].'</option>';
                                                        }
                                                        ?>
                                            </select>
                                         </td>
                                         <td><select name="product_category_id" id="product_category_id" onchange="get_product('',this.value)"  >
                                                <option value="">Select</option>
                                            </select>
                                         </td>
                                         <td><select name="product_id" id="product_id" onchange="get_model('',this.value)"  >
                                                <option value="">Select</option>
                                            </select>
                                         </td>
                                         <td><select name="model_id" id="model_id"  >
                                                <option value="">Select</option>
                                            </select>
                                         </td>
                                         
                                         
                                         <td><input type="text"    id="field_name"         placeholder="Field"     ></td>
                                         <td><input type="text"    id="sub_field_name"     placeholder="Option#1"  ></td>
                                         <td><input type="text"    id="opt"                placeholder="Option#2"  ></td>                
                                         <td>
                                             <select  id="field_type" required="">
                                                 <option value="Drop-Down">Dropdown</option>
                                             </select>
                                         </td>
                                         <td><input type="text"    id="con_remarks"                placeholder="Remarks"  ></td>
                                         <td><button type="button" onclick="add_cndn()" class="mt-2 btn btn-primary">Add</button></td>
                                     </tr>
                                     
                                     
                                     
                                     <?php 
                                            foreach($cndn_arr as $cndn)
                                            {
                                     ?>
                                     <tr>
                                         <td><select name="cndn[{{$cndn->con_id}}][brand_id]" id="brand_id{{$cndn->con_id}}" onchange="get_product_category('{{$cndn->con_id}}',this.value)" required="">
                                                <option value="">Select</option>
                                                <?php
                                                        foreach($brand_master as $brand)
                                                        {
                                                            ?>       <option value="<?php echo $brand['brand_id']; ?>" <?php if($cndn->brand_id==$brand['brand_id']) { echo 'selected';} ?>><?php echo $brand['brand_name']; ?></option>     
                                                <?php   }
                                                ?>
                                            </select>
                                         </td>
                                         <td><select name="cndn[{{$cndn->con_id}}][product_category_id]" id="product_category_id{{$cndn->con_id}}" onchange="get_product('{{$cndn->con_id}}',this.value)" required="">
                                                <option value="">Select</option>
                                                <?php
                                                        foreach($record['product_category_id'][$cndn->brand_id] as $key=>$part)
                                                        {
                                                            ?>       <option value="<?php echo $key; ?>"
                                                                    <?php if($cndn->product_category_id==$key) { echo 'selected';} ?>
                                                                    ><?php echo $part; ?></option>     
                                                <?php   }
                                                ?>
                                            </select>
                                         </td>
                                         <td><select name="cndn[{{$cndn->con_id}}][product_id]" id="product_id{{$cndn->con_id}}" onchange="get_model('{{$cndn->con_id}}',this.value)" required="">
                                                <option value="">Select</option>
                                                <?php
                                                        foreach($record['product_id'][$cndn->brand_id.'##'.$cndn->product_category_id] as $key=>$part)
                                                        {
                                                            ?>       <option value="<?php echo $key; ?>"
                                                                    <?php if($cndn->product_id==$key) { echo 'selected';} ?>
                                                                    ><?php echo $part; ?></option>     
                                                <?php   }
                                                ?>
                                            </select>
                                         </td>
                                         <td><select name="cndn[{{$cndn->con_id}}][model_id]" id="model_id{{$cndn->con_id}}" required="">
                                                <option value="">Select</option>
                                                <?php
                                                        foreach($record['model_id'][$cndn->brand_id.'##'.$cndn->product_category_id.'##'.$cndn->product_id] as $key=>$part)
                                                        {
                                                            ?>       <option value="<?php echo $key; ?>"
                                                                    <?php if($cndn->model_id==$key) { echo 'selected';} ?>
                                                                    ><?php echo $part; ?></option>     
                                                <?php   }
                                                ?>
                                            </select>
                                         </td>
                                         
                                         
                                         <td><input type="text" name="cndn[{{$cndn->con_id}}][field_name]"       id="field_name{{$cndn->con_id}}"         placeholder="Field"       value="{{$cndn->field_name}}"   required="" ></td>
                                         <td><input type="text" name="cndn[{{$cndn->con_id}}][sub_field_name]"   id="sub_field_name{{$cndn->con_id}}"     placeholder="Option#1"    value="{{$cndn->sub_field_name}}"   required="" ></td>
                                         <td><input type="text" name="cndn[{{$cndn->con_id}}][opt]"              id="opt{{$cndn->con_id}}"                placeholder="Option#2"    value="{{$cndn->opt}}"   required="" ></td>
                                         <td>
                                             <select name="cndn[{{$cndn->con_id}}][field_type]" id="field_type{{$cndn->con_id}}" required="">
                                                 <option value="Drop-Down">Dropdown</option>
                                             </select>
                                         </td>
                                         <td><input type="number" name="cndn[{{$cndn->con_id}}][priority]"        id="priority{{$cndn->con_id}}"         placeholder="Priority"       value="{{$cndn->priority}}"   required="" style="width:50px;text-align: center;" ></td>
                                         <td><button type="button" onclick="remove_cndn({{$cndn->con_id}})" class="mt-2 btn btn-primary">Remove</button></td>
                                         
                                         
                                     </tr>
                                     <?php 
                                            }
                                     ?>
                                     
                                     
                                     
                                     
                                 </table>

                                 


                                
                                 
                                <button type="submit"  class="mt-2 btn btn-primary">Update</button>
                                <a href="dashboard" class="mt-2 btn btn-danger"  title="dashboard">Exit</a>
                            </form>
                            <div id="view"  style="display: none;">
                                
                                <div class="form-row">
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
                                            <label for="exampleAddress" class="">Product Detail</label>
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
                                                <button type="button" onclick="search_cndn()" class="mt-2 btn btn-primary">Search</button>
                                            </div>
                                             </div>      
                                         </div>
                                
                            <table class="table" id="table_id">
                                     <tr>
                                         <th>Brand</th>
                                         <th>Product Detail</th>
                                         <th>Product</th>
                                         <th>Model</th>
                                         <th>Field</th>
                                         <th>Option#1</th>
                                         <th>Option#2</th>
                                     </tr>
                                     
                                     
                                     
                                     
                                     
                                     <?php  $field_exist = array();
                                            foreach($cndn_arr as $cndn)
                                            {
                                     ?>
                                     <tr>
                                         <td>
                                             <?php if(!in_array($cndn->field_name,$field_exist)) { 
                                                 
                                                 echo $record['brand_id'][$cndn->brand_id];
                                             }
                                        ?>
                                             
                                         </td>
                                         
                                         <td>
                                             <?php if(!in_array($cndn->field_name,$field_exist)) { 
                                                 echo $record['product_category_id'][$cndn->brand_id][$cndn->product_category_id];
                                             }
                                        ?>
                                         </td>
                                         
                                         <td>
                                             <?php if(!in_array($cndn->field_name,$field_exist)) {      
                                                 echo $record['product_id'][$cndn->brand_id.'##'.$cndn->product_category_id][$cndn->product_id];
                                             }
                                        ?>  
                                         </td>
                                         
                                         <td>
                                             <?php if(!in_array($cndn->field_name,$field_exist)) {      
                                                 echo $record['model_id'][$cndn->brand_id.'##'.$cndn->product_category_id.'##'.$cndn->product_id][$cndn->model_id];
                                             }
                                        ?>  
                                         </td>
                                         
                                         
                                         <td>
                                             <?php if(!in_array($cndn->field_name,$field_exist)) { 
                                                 $field_exist[] = $cndn->field_name;
                                                 echo $cndn->field_name;
                                             }
                                        ?>
                                             
                                         </td>
                                         <td><?php echo $cndn->sub_field_name; ?></td>
                                         <td>
                                             <select >
                                            <?php $opt_arr = explode('/',$cndn->opt); 
                                                    foreach($opt_arr as $opt)
                                                    {
                                                        echo '<option value="'.$opt.'">'.$opt.'</option>';
                                                    }
                                                 ?>
                                             </select>
                                             </td>
                                         
                                         
                                         
                                         
                                         
                                     </tr>
                                     <?php 
                                            }
                                     ?>
                                     
                                     
                                     
                                     
                                 </table>    
                            </div>
                            
                            
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
 
<script >
function add_cndn()
{
    //alert(url); return false;
    var brand_id = $('#brand_id').val();
    var product_category_id = $('#product_category_id').val();
    var product_id = $('#product_id').val();
    var model_id = $('#model_id').val();
    
    var field_name = $('#field_name').val();
    var sub_field_name = $('#sub_field_name').val();
    var opt = $('#opt').val();
    var field_type = $('#field_type').val();
    var con_remarks = $('#con_remarks').val();
    
    $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'save-cndn',
              method: 'post',
              data: {
                 brand_id:brand_id,
                 product_category_id:product_category_id,
                 product_id:product_id,
                 model_id:model_id,
                 field_name: field_name,
                 sub_field_name:sub_field_name,
                 opt:opt,
                 field_type:field_type,
                 con_remarks:con_remarks
              },
              success: function(result){
                  var msg = '';

                  if(result=='succ')
                  {
                      msg="Record Added Successfully";
                      alert(msg);
                      location.reload();
                  }
                  else if(result=='unsucc')
                  {
                      msg="Record Not Added";
                      alert(msg);
                  }
                  else if(result=='exist')
                  {
                      msg="Record Already Exist.";
                      alert(msg);
                  }
                  else
                  {
                      //msg="Record Already Exist.";
                      alert(result);
                  }
              }});
}

function remove_cndn(id)
{
    $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'delete-cndn',
              method: 'post',
              data: {
                 id: id
              },
              success: function(result){
                  var msg = '';

                  if(result=='succ')
                  {
                      msg="Record Added Successfully";
                      alert(msg);
                      location.reload();
                  }
                  else if(result=='unsucc')
                  {
                      msg="Record Not Added";
                      alert(msg);
                  }
                  else if(result=='exist')
                  {
                      msg="Record Already Exist.";
                      alert(msg);
                  }
              }});
}

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
 
 function search_cndn()
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
              url: 'search-cndn',
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
 
 
 
function form_toggle(first,second)
{
    $('#'+first).show();
    $('#'+second).hide();
}

</script>
@endsection
