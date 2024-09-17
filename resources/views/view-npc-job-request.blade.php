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

<?php $tab = Session::get('tab'); ?>

<div class="app-main"> 
    <div class="app-main__outer">
        <div class="app-main__inner">
        
     <div class="tab-content">
         @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
         @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
         <div class="tab-pane tabs-animation fade <?php if($tab!=='1') { ?>show active <?php } ?>" id="tab-content-0" role="tabpanel">
             <div class="main-card mb-3 card"> 

                    <div class="card-body"><h5 class="card-title">VIEW JOB ESTIMATE REQUEST</h5>
                        <form method="get" action="{{route('view-npc-job-request')}}" class="form-horizontal">
                            
                            <div class="form-row">
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>Brand</label>
                                        <select id="brand_id2" name="brand_id" onchange="get_product_detail('2',this.value)" class="form-control">
                                            <option value="">Brand</option>
                                            <?php foreach($brand_arr as $brand) {?>       
                                                <option value="<?php echo $brand['brand_id']; ?>" <?php if($brand_id==$brand['brand_id']){ echo "selected"; } ?>><?php echo $brand['brand_name']; ?></option>     
                                            <?php  }?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>Product Category</label>
                                        <select id="product_category_id2" name="product_category" onchange="get_product('2',this.value)" class="form-control">
                                            <option value="">Product Category</option>
                                            <option value="All" <?php echo ($product_category == 'All') ? 'selected' : ''; ?>>All</option>
                                            <?php foreach($category_master as $category) {?>       
                                                <option value="<?php echo $category->product_category_id; ?>" <?php if($product_category==$category->product_category_id){ echo "selected"; } ?>><?php echo $category->category_name; ?></option>     
                                            <?php  }?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>Product</label>
                                        <select id="product_id2" name="product" onchange="get_model('2',this.value)" class="form-control">
                                            <option value="">Product</option>
                                            <option value="All" <?php echo ($product == 'All') ? 'selected' : ''; ?>>All</option>
                                            <?php foreach($model_master as $model){ ?>
                                                <option value="<?php echo $model->product_id; ?>" <?php if($product==$model->product_id){ echo "selected"; } ?>><?php echo $model->product_name; ?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="exampleAddress" class="">Model No.<font color="red">*</font></label>
                                        <select id="model_id2" name="model" onchange="get_partcode('2',this.value)" class="form-control">
                                            <option value="">Model No.</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Asc Name</label>
                                        <select id="asc_name" name="asc_name" class="form-control" >
                                            <option value="All">All</option>
                                            @foreach($asc_master as $asc)
                                                <option value="{{$asc->center_id}}" <?php if( $center_id==$asc->center_id) 
                                                    { echo 'selected';} ?>>{{$asc->center_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>From Date</label>
                                        <input name="from_date" autocomplete="off" id="from_date" placeholder="From" type="text" value="<?php echo $from_date; ?>" class="form-control datepicker">
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>To Date</label>
                                        <input name="to_date" autocomplete="off" id="to_date" placeholder="To" type="text" value="<?php echo $to_date; ?>" class="form-control datepicker">
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                    <div class="position-relative form-group">
                                        <input type="submit" class="btn btn-primary" value="Search">
                                        &nbsp;<a href="{{route('home')}}" class="btn btn-danger">Exit</a>
                                        <!-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input role="tab" type="submit" value="Raise Po Request" class="btn btn-primary" id="tab-1" data-toggle="tab" href="#tab-content-1"> -->
                                    </div>
                                    
                                </div>
                            </div>
                        </form>
                    </div>
                
                    <div class="card-body">                                        
                        <h5 class="card-title">View Job Estimate Request</h5>

                        <table class="table" id="table_id">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>ASC Name</th>
                                <th>Date</th>
                                <th>Job No.</th>
                                <th>Brand</th>
                                <th>Product Category</th>
                                <th>Model</th>
                                <th>Model No.</th>
                                
                                <th>No. of Parts</th>
                                <th>Tot. Qty</th>
                                <th>Action</th>
                                
                            </tr>
                            </thead>
                            <tbody>
                            <?php   $srno = 1;
                                    foreach($part_arr as $req)
                                    {
                                        echo '<tr>';
                                            echo '<td>'.$srno++.'</td>';
                                            echo '<td>'.$req->center_name.'</td>';
                                            echo '<td>'.date('d-m-Y',strtotime($req->created_at)).'</td>';
                                            echo '<td>'.$req->job_no.'</td>';
                                            
                                            echo '<td>'.$req->Brand.'</td>';
                                            echo '<td>'.$req->Product_Detail.'</td>';
                                            echo '<td>'.$req->Product.'</td>';
                                            echo '<td>'.$req->Model.'</td>';
                                            echo '<td>'.$req->part_count.'</td>';
                                            echo '<td>'.$req->qty.'</td>';
                                            echo '<td><a href="npc-job-estimate-approval?tag_id='.base64_encode($req->TagId).'">View Details</a></td>';
                                        echo '</tr>';
                                    }
                            ?>
                            </tbody>
                        </table>       

                            

                              
                    </div>                                      



                    <div class="app-wrapper-footer">
                        <div class="app-footer">

                        </div>   
                    </div>
                
        </div>
         </div>
        <div class="tab-pane tabs-animation fade <?php if($tab==='1') { ?>show active <?php } ?>" id="tab-content-1" role="tabpanel">
            <div class="main-card mb-3 card">
                <form method="post" action="save-request-entry" >
                    <div class="card-body">                                        
                        <h5 class="card-title">Raise Purchase Order</h5> <h5 class="card-title">Date : <?php echo date('d-m-y'); ?></h5> <h5 class="card-title">Sr No. : <?php echo $new_request_no; ?></h5>
                                 
                                <div class="form-row">
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Brand <font color="red">*</font></label>
                                            <select id="brand_id1" name="brand" onchange="get_product_detail('1',this.value)" class="form-control" required="">
                                                <option value="">Brand</option>
                                                <?php
                                                        foreach($brand_arr as $brand)
                                                        {
                                                            ?>       <option value="<?php echo $brand['brand_id']; ?>"><?php echo $brand['brand_name']; ?></option>     
                                                <?php   }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Product Category<font color="red">*</font></label>
                                            <select id="product_category_id1" name="product_category" onchange="get_product('1',this.value)" class="form-control" required="">
                                                <option value="">Product Category</option>
                                                
                                            </select>
                                        </div>
                                        
                                    </div>
                                
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Model <font color="red">*</font></label>
                                            <select id="product_id1" name="product" onchange="get_model('1',this.value)" class="form-control" required="">
                                                <option value="">Product</option>
                                                
                                            </select>
                                        </div>
                                    </div>
                                
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Model No.<font color="red">*</font></label>
                                            <select id="model_id1" name="model" onchange="get_partcode('',this.value)" class="form-control" required="">
                                                <option value="">Model</option>
                                            </select>
                                        </div>
                                    </div>
                                
                                    <div class="col-md-2">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Part Code <font color="red">*</font></label>
                                            <select multiple="" id="part_code" name="part_code" class="form-control" onchange="add_part(this.value)" >
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                            </div>
                              <div id="part_arr"></div>
                                 <br>
                                 <br>

                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Remarks <font color="red">*</font></label>
                                        <textarea id="remarks" name="remarks" placeholder="Remarks" required="" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>    

                        <div class="form-row">
                            <div class="col-md-2">
                                <div class="position-relative form-group">
                                    <a href="dashboard"  class="mt-2 btn btn-danger">Back</a>
                                     <button type="submit"  class="mt-2 btn btn-primary" >Save</button>
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
