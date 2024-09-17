@extends('layouts.app')

@section('content') 
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,400i,700,700i">
<script src= "https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> 
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<!-- DataTables JavaScript -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<!-- TableExport JavaScript -->
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.1.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.1.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/vfs_fonts.js"></script>
<script>
    jQuery(document).ready(function($) {
    // Use $ for jQuery code here
        $('#table1').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Download Excel', 
                    className: 'btn btn-warning', 

                }
            ],
            ordering: false
        });
        
    });
</script>
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
     var brand_id = $('#brand'+div_id).val();
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

 function get_modelfirst(div_id,product_id)
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


</script>



<div class="app-main"> 
    <div class="app-main__outer">
        <div class="app-main__inner">
        <ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
            <li class="nav-item">
                <a role="tab" class="nav-link <?php if((isset($tab1) && $tab1 == "tab1") || (!isset($tab1) && !isset($tab2))){echo "active"; }?>" id="tab-0" data-toggle="tab" href="#tab-content-0">
                    <span>Search </span>
                </a>
            </li>
            <li class="nav-item">
                <a role="tab" class="nav-link <?php if(isset($tab2) && $tab2 == "tab2"){echo "active"; }?>" id="tab-1" data-toggle="tab" href="#tab-content-1">
                    <span>View </span>
                </a>
            </li>
            
        </ul>
     <div class="tab-content">
         @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
         @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
        <div class="tab-pane tabs-animation fade <?php if((isset($tab1) && $tab1 == "tab1") || (!isset($tab1) && !isset($tab2))){echo "show active"; }?> " id="tab-content-0" role="tabpanel">
            <div class="main-card mb-3 card">

                <div class="card-body"><h5 class="card-title">INWARD Stock</h5>
                    <form method="get" action="{{route('inward-inv-entry')}}" class="form-horizontal">
                        
                        <div class="form-row">
                            <div class="col-md-2">
                                <div class="position-relative form-group">
                                    <label>Brand</label>
                                    <input type="hidden" name="tab1" id="tab1" value="tab1">
                                    <select id="brand2" name="brand_id" onchange="get_product_detail('2',this.value)" class="form-control">
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
                                    <select id="product_id2" name="product" onchange="get_modelfirst('2',this.value)" class="form-control">
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
                                <div class="position-relative form-group"><label for="examplePassword11" class="">Part Code</label>
                                    <select  id="part_code2" name="part_code" class="form-control">
                                        <option value="">Select</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="position-relative form-group"><label for="examplePassword11" class="">Po Sr. No.</label>
                                    <input type="text" name="po_sr_no" id="po_sr_no" class="form-control" value="<?php echo $po_sr_no;?>"  placeholder="Po Sr. No.">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="position-relative form-group"><label for="examplePassword11" class="">Supplier Name</label>
                                    <select name="supplier_name" id="supplier_name" class="form-control" >
                                        <option value="">All</option>
                                        <?php foreach($supplier_master as $sup){ ?>
                                            <option value="<?php echo $sup->id; ?>" <?php if($supplier_name==$sup->supplier_name){ echo "selected"; } ?>><?php echo $sup->supplier_name; ?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="position-relative form-group">
                                    <label>From Date (Date of PO Raised)</label>
                                    <input name="from_date" autocomplete="off" id="from_date" placeholder="From" type="text" value="<?php echo $from_date; ?>" class="form-control datepicker">
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="position-relative form-group">
                                    <label>To Date (Date of PO Raised)</label>
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
                    <h5 class="card-title">View Inward</h5>

                    <table class="table" id="table1">
                        <thead>
                            <tr>
                                <th>Sr. No</th>
                                <th>Po Sr No.</th>
                                <!-- <th>Sr No.</th> -->
                                <th>Date of Po Raised</th>
                                <th>Name of Supplier</th>
                                <th>Brand</th>
                                <th>Product Category</th>
                                <th>Model No.</th>
                                <th>Part Name</th>
                                <th>Part Code</th>
                                <th>Color</th>
                                <th>Hsn Code</th>
                                <th>Remarks</th>
                                <!--<th>Voucher No.</th>
                                <th>Invoice Date</th>
                                <th>No. of Case</th>
                                <th>Vehicle / Docket No.</th>
                                <th>No. of Parts</th>-->
                                <th>Req Stock Qty</th>
                                <!-- <th>View</th> -->
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php   $srno = 1;
                                foreach($req_arr as $req)
                                {
                                    echo '<tr>';
                                        echo '<td>'.$srno++.'</td>';
                                        #echo '<td>'.$req->req_no.'</td>';
                                        echo '<td>'.$req->req_no.'</td>';
                                        
                                        echo '<td>';
                                        if(!empty($req->req_date))
                                        {
                                            echo date('d-m-Y', strtotime($req->req_date));
                                        }
                                        echo '</td>';
                                        echo '<td>'.$req->supplier_name.'</td>';
                                        echo '<td>'.$req->brand_name.'</td>';
                                        echo '<td>'.$req->category_name.'</td>';
                                        #echo '<td>'.$req->product_name.'</td>';
                                        echo '<td>'.$req->model_name.'</td>';
                                        echo '<td>'.$req->part_name.'</td>';
                                        echo '<td>'.$req->part_no.'</td>';
                                        echo '<td>'.$req->item_color.'</td>';
                                        echo '<td>'.$req->hsn_code.'</td>';
                                        echo '<td>'.$req->remarks.'</td>';
                                        //   echo '<td>'.$req->voucher_no.'</td>';
                                        //   echo '<td>'.$req->invoice_date.'</td>';
                                        //   echo '<td>'.$req->no_of_case.'</td>';
                                        //   echo '<td>'.$req->veh_doc_no.'</td>';
                                        //   echo '<td>'.$req->part_added.'</td>';
                                        echo '<td>'.$req->qty.'</td>';
                                        //echo '<td><a href="view-inw-inv-entry?inw_id='.base64_encode($req->req_id).'">View</a></td>';
                                        //echo '<td><a href="edit-inw-inv-entry?inw_id='.base64_encode($req->inw_id).'">Edit</a></td>';
                                        if($req->inward_status=='1')
                                        {
                                            echo '<td><a href="view-inw-inv-entry?inw_id='.base64_encode($req->inward_id).'">View</a></td>';
                                        }
                                        else
                                        {
                                            echo '<td>';
                                            echo '<a href="po-inward-inv-cancel?inw_id='.base64_encode($req->req_id).'">Cancel</a> || <a href="po-inward-inv-entry?req_id='.base64_encode($req->req_id).'">Inward</a></td>';
                                        }
                                        
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
        <div class="tab-pane tabs-animation fade <?php if(isset($tab2) && $tab2 == "tab2"){echo "show active"; }?>" id="tab-content-1" role="tabpanel">
            <div class="main-card mb-3 card">
                <form method="get" action="{{route('inward-inv-entry')}}">
                    <div class="card-body">                                        
                        <h5 class="card-title">Inwarded Stock</h5> 
                        <div class="form-row">
                            <div class="col-md-2">
                                <div class="position-relative form-group">
                                    <label>Brand</label>
                                    <input type="hidden" name="tab2" id="tab2" value="tab2">
                                    <select id="brand1" name="brand_id2" onchange="get_product_detail('1',this.value)" class="form-control">
                                        <option value="">Brand</option>
                                        <?php foreach($brand_arr as $brand) {?>       
                                            <option value="<?php echo $brand['brand_id']; ?>" <?php if($brand_id2==$brand['brand_id']){ echo "selected"; } ?>><?php echo $brand['brand_name']; ?></option>     
                                        <?php  }?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="position-relative form-group">
                                    <label>Product Category</label>
                                    <select id="product_category_id1" name="product_category2" onchange="get_product('1',this.value)" class="form-control">
                                        <option value="">Product Category</option>
                                        <option value="All" <?php echo ($product_category2 == 'All') ? 'selected' : ''; ?>>All</option>
                                        <?php foreach($category_master2 as $category) {?>       
                                            <option value="<?php echo $category->product_category_id; ?>" <?php if($product_category2==$category->product_category_id){ echo "selected"; } ?>><?php echo $category->category_name; ?></option>     
                                        <?php  }?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="position-relative form-group">
                                    <label>Product</label>
                                    <select id="product_id1" name="product2" onchange="get_modelfirst('1',this.value)" class="form-control">
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
                                    <select id="model_id1" name="model2" onchange="get_partcode('1',this.value)" class="form-control">
                                        <option value="">Model No.</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="position-relative form-group"><label for="examplePassword11" class="">Part Code</label>
                                    <select  id="part_code1" name="part_code2" class="form-control">
                                        <option value="">Select</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="position-relative form-group"><label for="examplePassword11" class="">Po Sr. No.</label>
                                    <input type="text" name="po_sr_no2" id="po_sr_no" class="form-control" value="<?php echo $po_sr_no;?>"  placeholder="Po Sr. No.">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="position-relative form-group"><label for="examplePassword11" class="">Supplier Name</label>
                                    <select name="supplier_name2" id="supplier_name" class="form-control" >
                                        <option value="">All</option>
                                        <?php foreach($supplier_master as $sup){ ?>
                                            <option value="<?php echo $sup->id; ?>" <?php if($supplier_name==$sup->supplier_name){ echo "selected"; } ?>><?php echo $sup->supplier_name; ?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="position-relative form-group">
                                    <label>From Date</label>
                                    <input name="from_date2" autocomplete="off" id="from_date1" placeholder="From" type="text" value="<?php echo $from_date2; ?>" class="form-control datepicker">
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="position-relative form-group">
                                    <label>To Date</label>
                                    <input name="to_date2" autocomplete="off" id="to_date1" placeholder="To" type="text" value="<?php echo $to_date2; ?>" class="form-control datepicker">
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
                             
                    </div> 
                    <div class="card-body">                                        
                    <h5 class="card-title">View Inwarded </h5>

                    <table class="table" id="table1">
                        <thead>
                            <tr>
                                <th>Sr. No.</th>
                                <th>Po Sr. No.</th>
                                <th>Date of Po Raised</th>
                                <th>Supplier Name</th>
                                <th>Brand</th>
                                <th>Product Category</th>
                                <th>Model No.</th>
                                <th>Part Name</th>
                                <th>Part Code</th>
                                <th>Color</th>
                                <th>Hsn Code</th>
                                <th>Remarks</th>
                                <th>Req Stock Qty</th>
                                <th>View</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php   $srno = 1;
                                foreach($req_arr3 as $req)
                                {
                                    echo '<tr>';
                                        echo '<td>'.$srno++.'</td>';
                                        echo '<td>'.$req->inw_no.'</td>';
                                        
                                        echo '<td>';
                                        if(!empty($req->inw_date))
                                        {
                                            echo date('d-m-Y', strtotime($req->inw_date));
                                        }
                                        echo '</td>';
                                        echo '<td>'.$req->supplier_name.'</td>';
                                        echo '<td>'.$req->brand_name.'</td>';
                                        echo '<td>'.$req->category_name.'</td>';
                                        #echo '<td>'.$req->product_name.'</td>';
                                        echo '<td>'.$req->model_name.'</td>';
                                        echo '<td>'.$req->part_name.'</td>';
                                        echo '<td>'.$req->part_no.'</td>';
                                        echo '<td>'.$req->item_color.'</td>';
                                        echo '<td>'.$req->hsn_code.'</td>';
                                        echo '<td>'.$req->remarks.'</td>';
                                        echo '<td>'.$req->item_qty.'</td>';
                                        echo '<td><a href="view-inw-inv-entry?inw_id='.base64_encode($req->inw_id).'">View</a></td>';
                                        //echo '<td><a href="edit-inw-inv-entry?inw_id='.base64_encode($req->inw_id).'">Edit</a></td>';
                                        echo '<td>
                                        <a href="mrf-pdf?type=preview&inw_id='.base64_encode($req->inw_id).'">View Pdf || </a>
                                        <a href="mrf-pdf?type=download&inw_id='.base64_encode($req->inw_id).'">Download Pdf</a>
                                        </td>';
                                       
                                        
                                    echo '</tr>';
                                }
                        ?>
                        </tbody>
                    </table>                                      

                </form>
            </div>
     </div>
         
    </div>
        </div>
    </div>  
</div> 

<script>
    
    function remove_row(tr_id,row_no)
    {
        $('#row'+tr_id).remove();
        var rows = document.querySelectorAll('#part_arr tr');
        
        for(var i=0;i<rows.length;i++){
          var row = rows[i];
          var cell = row.cells[0];
          cell.innerHTML = i+1;
          console.log(cell);
        }
        
        
    }
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
    
    
function get_model(div_id,brand_id)
{
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-model-by-brand-id',
              method: 'post',
              data: {
                 brand_id: brand_id
              },
              success: function(result){
                  $('#model'+div_id).html(result)
              }});
 }
 
 function get_partcode(div_id,model_id)
 {
    var brand_id = $('#brand'+div_id).val();
    var product_category_id = 'All';
    var product_id = 'All';
    
     
     
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
                    console.log('#part_code'+div_id);
                  $('#part_code'+div_id).html(result);
              }});
 }

function get_part_name(div_id,part_code)
 {
     
     var brand_id = $('#brand'+div_id).val();
     var product_category_id = 'All';
     var product_id = 'All';
     var model_id = $('#model'+div_id).val();
     
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-part-name-by-part-code',
              method: 'post',
              data: {
                 brand_id: brand_id,
                 product_category_id:product_category_id,
                 product_id:product_id,
                 model_id: model_id,
                 part_code: part_code
              },
              success: function(result){
                  //alert('#part_name'+div_id);
                  $('#part_name'+div_id).val(result);
              }});
 }
    
 


    
    
 function add_part()
 {
     var brand = $('#brand').val();
     var model = $('#model').val();
     var part_code = $('#part_code').val();
     var part_name = $('#part_name').val();
     var color = $('#color').val();
     var hsn_code = $('#hsn_code').val();
     var gst = $('#gst').val();
     var item_qty = $('#item_qty').val();
     var bin_no = $('#bin_no').val();
     var purchase_amt = $('#purchase_amt').val();
     var asc_amt = $('#asc_amt').val();
     var cust_amt = $('#cust_amt').val();
     var remarks = $('#remarks').val();
     
     if(brand==='')
     {
         alert("Please Select Brand");
         $('#brand').focus();
         return false;
     }
     else if(model==='')
     {
         alert("Please Select Model");
         $('#model').focus();
         return false;
     }
     else if(part_code==='')
     {
         alert("Please Select Part Code");
         $('#part_code').focus();
         return false;
     }
     
     else if(color==='')
     {
         alert("Please Fill Color");
         $('#color').focus();
         return false;
     }
     else if(hsn_code==='')
     {
         alert("Please Fill HSN Code");
         $('#hsn_code').focus();
         return false;
     }
     else if(gst==='')
     {
         alert("Please Fill GST");
         $('#gst').focus();
         return false;
     }
     else if(item_qty==='')
     {
         alert("Please Fill Item Qty.");
         $('#item_qty').focus();
         return false;
     }
     else if(bin_no==='')
     {
         alert("Please Fill Bin or Rack No.");
         $('#bin_no').focus();
         return false;
     }
     else if(purchase_amt==='')
     {
         alert("Please Fill Purchase Amount");
         $('#purchase_amt').focus();
         return false;
     }
     else if(asc_amt==='')
     {
         alert("Please Fill ASC Amount");
         $('#asc_amt').focus();
         return false;
     }
     else if(cust_amt==='')
     {
         alert("Please Fill Customer Amount");
         $('#cust_amt').focus();
         return false;
     }
     //var tbody =document.getElementById("part_arr").getElementsByTagName('tbody')[0];
     var rows = document.querySelectorAll('#part_arr tr');
     
     var row_no = 0;
     for(var i=0;i<rows.length;i++){
         row_no++;
    }
     //console.log(row_no);
     
     
     
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
          
            jQuery.ajax({
              url: 'get-add-inw-part',
              method: 'post',
              data:{row_no:row_no,brand_id:brand,model_id:model,part_code:part_code,part_name:part_name,color:color,
                  hsn_code:hsn_code,gst:gst,item_qty:item_qty,bin_no:bin_no,purchase_amt:purchase_amt,asc_amt:asc_amt,
                  cust_amt:cust_amt,remarks:remarks},
              
              success: function(result){
                  $('#part_arr').append(result);
                    $('#brand').val('');
                    $('#model').html('');
                    $('#part_code').html('');
                    $('#part_name').val('');
                    $('#color').val('');
                    $('#hsn_code').val('');
                    $('#gst').val('');
                    $('#item_qty').val('');
                    $('#bin_no').val('');
                    $('#purchase_amt').val('');
                    $('#asc_amt').val('');
                    $('#cust_amt').val('');
                    $('#remarks').val('');
              }});    
 }
 
 function del_part(del_div)
 {
     $('#'+del_div).remove();
 }
 
 
</script>

@endsection
