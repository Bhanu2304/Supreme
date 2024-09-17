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
<script>
    function calculateTotal(random_no) 
    {
        
        var req_qty = parseFloat(document.getElementById('req_qty' + random_no).value) || 0;
        var previous_qty = parseFloat(document.getElementById('previous_qty' + random_no).value) || 0;

        var total_qty = req_qty + previous_qty;

        document.getElementById('total_qty' + random_no).value = total_qty;
    }
</script>

<?php $tab = Session::get('tab'); ?>

<div class="app-main"> 
    <div class="app-main__outer">
        <div class="app-main__inner">
        <ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
            <li class="nav-item">
                <a role="tab" class="nav-link <?php if($tab!=='1') { ?>active <?php } ?>" id="tab-0" data-toggle="tab" href="#tab-content-0">
                    <span>View </span>
                </a>
            </li>
            <li class="nav-item">
                <a role="tab" class="nav-link <?php if($tab==='1') { ?>active <?php } ?>" id="tab-1" data-toggle="tab" href="#tab-content-1">
                    <span>Add </span>
                </a>
            </li>
        </ul>
     <div class="tab-content">
         @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
         @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
         <div class="tab-pane tabs-animation fade <?php if($tab!=='1') { ?>show active <?php } ?>" id="tab-content-0" role="tabpanel">
             <div class="main-card mb-3 card"> 
                    <div class="card-body"><h5 class="card-title">View Purchase Order</h5>
                        <form method="get" action="{{route('req-inv-entry-sc')}}" class="form-horizontal">
                            <div class="form-row">
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>Brand</label>
                                        <select id="brand_id2" name="brand_id" onchange="get_product_detail('2',this.value)" class="form-control">
                                            <option value="">Brand</option>
                                            <?php foreach($brand_arr as $brand) {?>       
                                                <option value="<?php echo $brand['brand_id']; ?>" <?php if($brand_id1==$brand['brand_id']){ echo "selected"; } ?>><?php echo $brand['brand_name']; ?></option>     
                                            <?php  }?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>Product Category</label>
                                        <select id="product_category_id2" name="product_category" onchange="get_product('2',this.value)" class="form-control">
                                            <option value="">Product Category</option>
                                            <option value="All" <?php echo ($product_category1 == 'All') ? 'selected' : ''; ?>>All</option>
                                            <?php foreach($category_master as $category) {?>       
                                                <option value="<?php echo $category->product_category_id; ?>" <?php if($product_category1==$category->product_category_id){ echo "selected"; } ?>><?php echo $category->category_name; ?></option>     
                                            <?php  }?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>Product</label>
                                        <select id="product_id2" name="product" onchange="get_model('2',this.value)" class="form-control">
                                            <option value="">Product</option>
                                            <option value="All" <?php echo ($product1 == 'All') ? 'selected' : ''; ?>>All</option>
                                            <?php foreach($product_master as $model){ ?>
                                                <option value="<?php echo $model->product_id; ?>" <?php if($product1==$model->product_id){ echo "selected"; } ?>><?php echo $model->product_name; ?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="exampleAddress" class="">Model No.<font color="red">*</font></label>
                                        <select id="model_id2" name="model" onchange="get_partcode('2',this.value)" class="form-control">
                                            <option value="">Model No.</option>
                                            <option value="All" <?php echo ($model_id1 == 'All') ? 'selected' : ''; ?>>All</option>
                                            <?php foreach($model_master as $model){ ?>
                                                <option value="<?php echo $model->model_id; ?>" <?php if($model_id1==$model->model_id){ echo "selected"; } ?>><?php echo $model->model_name; ?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Part Code</label>
                                        <select  id="part_code2" name="part_code" class="form-control">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Po Sr. No.</label>
                                        <input type="text" name="po_sr_no" id="po_sr_no" class="form-control" value="<?php echo $po_sr_no1;?>"  placeholder="Po Sr. No.">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>Po Type</label>
                                        <select name="po_type" id="po_type" class="form-control">
                                            <option value="">Select</option>
                                            <option value="Paid" <?php if($po_type == "Paid"){ echo "selected"; } ?>>Paid</option>
                                            <option value="Foc" <?php if($po_type == "Foc"){ echo "selected"; } ?>>Foc</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>Po Status</label>
                                        <select name="po_status" id="po_status" class="form-control">
                                            <option value="">Select</option>
                                            <option value="Inward" <?php if($po_status == "Inward"){ echo "selected"; } ?>>Inward</option>
                                            <option value="Pending" <?php if($po_status == "Pending"){ echo "selected"; } ?> >Pending</option>
                                            <option value="Cancelled" <?php if($po_status == "Cancelled"){ echo "selected"; } ?> >Cancelled</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>From Date</label>
                                        <input name="from_date" autocomplete="off" id="from_date" placeholder="From" type="text" value="<?php echo $from_date1; ?>" class="form-control datepicker">
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>To Date</label>
                                        <input name="to_date" autocomplete="off" id="to_date" placeholder="To" type="text" value="<?php echo $to_date1; ?>" class="form-control datepicker">
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
                        <h5 class="card-title">View PO Request</h5>

                        <table class="table" id="table_id">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>SR No.</th>
                                    <th>Date Of Po Raised</th>
                                    <th>Name of Supplier</th>
                                    <th>Brand</th>
                                    <th>Product Category</th>
                                    <th>Model</th>
                                    <th>Model No.</th>
                                    <th>Part Name</th>
                                    <th>Part Code</th>
                                    <th>Color</th>
                                    <th>Remarks</th>
                                    <!-- <th>Pdf</th> -->
                                    <th>Date of Stock Inward</th>
                                    <th>Inwarded Qty</th>
                                    <th>Status</th>
                                    <th>No. of Parts</th>
                                    <th>Tot. Qty</th>
                                    <th>View</th>
                                    <th>Edit</th>
                                    <th>Pdf</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php   $srno = 1;
                                foreach($req_arr as $req)
                                {
                                    
                                    echo '<tr>';
                                        echo '<td>'.$srno++.'</td>';
                                        echo '<td>'.$req->req_no.'</td>';
                                        echo '<td>'.$req->created_at.'</td>';
                                        echo '<td>'.$req->supplier_name.'</td>';
                                        echo '<td>'.$req->brand_name.'</td>';
                                        echo '<td>'.$req->category_name.'</td>';
                                        echo '<td>'.$req->product_name.'</td>';
                                        echo '<td>'.$req->model_name.'</td>';
                                        echo '<td>'.$req->part_name.'</td>';
                                        echo '<td>'.$req->part_no.'</td>';
                                        echo '<td>'.$req->color.'</td>';
                                        echo '<td>'.$req->remarks.'</td>';
                                        # echo '<td><a href="ho-po-pdf?req_id='.base64_encode($req->req_id).'">Pdf Here</a></td>';
                                        echo '<td>'.$req->approve_date.'</td>';
                                        echo '<td>'.$req->part_required.'</td>';
                                        echo '<td>';
                                        if($req->part_status_pending==1)
                                        {
                                            echo "Pending";
                                        }else if($req->part_status_pending==0)
                                        {
                                            if($req->part_reject == 1)
                                            {
                                                echo "Cancelled";
                                            }else{
                                                echo "Inwarded";
                                            }
                                        }
                                        echo '</td>';
                                        echo '<td>'.$req->part_required.'</td>';
                                        echo '<td>'.$req->qty.'</td>';
                                        echo '<td><a href="view-req-inv-entry-sc?req_id='.base64_encode($req->req_id).'">View</a></td>';
                                        echo '<td><a href="edit-req-inv-entry-sc?req_id='.base64_encode($req->req_id).'">Edit</a></td>';
                                        echo '<td>';
                                        echo '<a href="po-invoice-pdf-sc-new?type=preview&req_id='.base64_encode($req->req_id).'">View</a> || ';
                                        echo '<a href="po-invoice-pdf-sc-new?req_id='.base64_encode($req->req_id).'">Download</a>';
                                        echo '</td>';
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
                <form method="post" action="save-request-entry-sc" >
                    <div class="card-body">                                        
                        <h5 class="card-title">Raise Purchase Order</h5> <h5 class="card-title">Date : <?php echo date('d-m-y'); ?></h5> <h5 class="card-title">Sr No. : <?php echo $new_request_no; ?></h5>
                                 
                                <div class="form-row">

                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Supplier Name <font color="red">*</font></label>
                                            <select id="supplier_name" name="supplier_name"  class="form-control" required="">
                                                <option value="">Select Supplier</option>
                                                <?php
                                                        foreach($supplier_arr as $supplier)
                                                        {
                                                            ?>       <option value="<?php echo $supplier['id']; ?>"><?php echo $supplier['supplier_name']; ?></option>     
                                                <?php   }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">PO Type <font color="red">*</font></label>
                                            <select id="po_type" name="po_type" class="form-control" required="">
                                                <option value="">PO Type</option>
                                                <option value="FOC">FOC</option>
                                                <option value="Paid">Paid</option>
                                            </select>
                                        </div>
                                    </div>
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
              url: 'get-part-rate-sc',
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
            if(sp_id != "")
            {
                jQuery.ajax({
                url: 'get-add-req-part-sc',
                method: 'post',
                data:{sp_id:sp_id,brand_id:brand_id,product_category_id:product_category_id,product_id:product_id,model_id:model_id},
                
                success: function(result){
                    $('#part_arr').append(result);
                }});
            }
            
        }
        
    }
     
     
     
          
     
          
    
 }
 
 function del_part(del_div)
 {
     $('#'+del_div).remove();
 }
 
 
</script>

@endsection
