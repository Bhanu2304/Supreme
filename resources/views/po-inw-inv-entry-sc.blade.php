@extends('layouts.app')

@section('content') 

<style>
    /* Style to make the select element look read-only */
    select[readonly] {
        pointer-events: none;
        /* background-color: #e9ecef;
        color: #6c757d; */
        border: 1px solid #ced4da;
        padding: 0.375rem 0.75rem;
    }
</style>
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

    function checkNumber_new(val,evt)
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
        //console.log(val);
        //updateItemList(val, evt.target.id);
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
        }
    });
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
        }
    });
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


function updateTableRows(value, index,maxQty) 
{
    const tableRows = document.querySelectorAll(`.table-row-${index}`);
    const qty = parseInt(value);

    console.log("max qty --"+maxQty);
    console.log(" qty --"+qty);
    if (qty > maxQty) {
        //qty = maxQty;
        document.getElementById(`item_qty${index}`).value = maxQty; // Update the input field to the max value
        alert(`The maximum allowed quantity is ${maxQty}.`);
    }
    
    tableRows.forEach((row, idx) => {
        if (idx < qty) {
            row.style.display = 'table-row';
        } else {
            row.style.display = 'none';
        }
    });
}


</script>



<div class="app-main"> 
    <div class="app-main__outer">
        <div class="app-main__inner">
        <ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
            <li class="nav-item">
                <a role="tab" class="nav-link active" id="tab-0" data-toggle="tab" href="#tab-content-0">
                    <span>Inward Inventory </span>
                </a>
            </li>
            
        </ul>
     <div class="tab-content">
         @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
         @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
         
        <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
            <div class="main-card mb-3 card">
                <form method="post" action="save-inward-entry-sc" >
                    <div class="card-body">                                        
                        <h5 class="card-title">Inward Stock</h5> 
                                <div class="form-row">
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Supplier Name (Party Name) <font color="red">*</font></label>
                                            <select name="supplier_name" id="supplier_name" class="form-control" readonly>
                                            <?php foreach($supplier_master as $sup){ ?>
                                                <option value="<?php echo $sup->id; ?>" <?php if($po_inv->supplier_id==$sup->supplier_id){ echo "selected"; } ?>><?php echo $sup->supplier_name; ?></option>
                                            <?php }?>
                                            </select>
                                        </div>
                                    </div>
                                
                                    
                                    <div class="col-md-1">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Voucher/Invoice No. <font color="red">*</font></label>
                                            <input autocomplete="off" type="text" id="voucher_no" name="voucher_no"  value="" class="form-control" required="" />
                                        </div>
                                    </div>

                                    <div class="col-md-1">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Invoice Date<font color="red">*</font></label>
                                            <input autocomplete="off" type="text" id="invoice_date" name="invoice_date"  value="" class="form-control datepicker" required="" />
                                        </div>
                                    </div>

                                    <div class="col-md-1">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">No. of cases/boxes <font color="red">*</font></label>
                                            <input autocomplete="off" type="text" id="no_of_case" name="no_of_case" onkeypress="return checkNumber(this.value,event)" value="" class="form-control" required="" />
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-1">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Vehicle / Docket No. <font color="red">*</font></label>
                                            <input autocomplete="off" type="text" id="veh_doc_no" name="veh_doc_no" value="" class="form-control" required="" />
                                        </div>
                                    </div>
                                </div>
                        
  
                                    <table  border="1" style="font-size:13px;">
                                        <thead>
                                            <tr>
                                                <th>Sr. No.</th>
                                                <th>Brand <font color="red">*</font></th>
                                                <th>Model <font color="red">*</font></th>
                                                <th>Part Code <font color="red">*</font></th>
                                                <th>Part Name <font color="red">*</font></th>
                                                <th>Item Color <font color="red">*</font></th>
                                                <th>HSN Code <font color="red">*</font></th>                                                
                                                <th>Item Qty. <font color="red">*</font></th>
                                                <th>GST <font color="red">*</font></th>
                                                <th>Bin or Rack No. <font color="red">*</font></th>
                                                <th>Purchase Amount <font color="red">*</font></th>
                                                <th>ASC Amount <font color="red">*</font></th>
                                                <th>Customer Amount <font color="red">*</font></th>
                                                <th>Remarks </th>
                                                <!--<th>Action</th>-->
                                            </tr>
                                     
                                            <?php
                                            $b = 1;
                                            foreach($po_inv_parts as $po_inv_part) { ?> 
                                             <tr>
                                                         <th><?php echo $b++; $a=$po_inv_part->req_part_id;?>.</th>
                                                        <td>
                                                            <select autocomplete="off" id="brand<?php echo $a;?>" name="part[brand<?php echo $a;?>]"  readonly>
                                                                 <option value="">Select</option>
                                                                 <?php
                                                                        foreach($brand_arr as $brand)
                                                                        {
                                                                            ?>       <option value="<?php echo $brand['brand_id']; ?>" <?php if($brand['brand_id']==$po_inv_part->brand_id) {echo 'selected';} ?>><?php echo $brand['brand_name']; ?></option>     
                                                                <?php   }
                                                                ?>
                                                            </select>
                                                        </td>
                                                 <td>
                                                    <select autocomplete="off" id="model<?php echo $a;?>" name="part[model<?php echo $a;?>]"  readonly>
                                                        <option value="">Select</option>
                                                        <?php foreach($po_inv_part->mod_list as $mod) { ?>       
                                                            <option value="<?php echo $mod->model_id; ?>" <?php if($mod->model_id==$po_inv_part->model_id) {echo 'selected';} ?>><?php echo $mod->model_name; ?></option>     
                                                        <?php   }?> 
                                                    </select>
                                                 </td>
                                                 <td>
                                                    <select autocomplete="off" id="part_code<?php echo $a;?>"  name="part[part_code<?php echo $a;?>]" readonly>
                                                        <option value="">Select</option> 
                                                        <?php foreach($po_inv_part->part_list as $mod){?>       
                                                            <option value="<?php echo $mod->spare_id; ?>" <?php if($mod->spare_id==$po_inv_part->spare_id) {echo 'selected';} ?>><?php echo $mod->part_no; ?></option>     
                                                        <?php   }?>
                                                    </select> 
                                                 </td>
                                                 <td><input autocomplete="off" type="text" name="part[part_name<?php echo $a;?>]" id="part_name<?php echo $a;?>" value="<?php echo $po_inv_part->part_name; ?>" placeholder="Part Name" readonly></td>
                                                 <td><input autocomplete="off" type="text" name="part[color<?php echo $a;?>]" id="color<?php echo $a;?>"  placeholder="Color" value="<?php echo $po_inv_part->color; ?>" readonly></td>
                                                 <td><input autocomplete="off" type="text" name="part[hsn_code<?php echo $a;?>]" id="hsn_code<?php echo $a;?>"  placeholder="HSN" value="<?php echo $po_inv_part->hsn_code; ?>" ></td>
                                                 <!-- oninput="updateItemList(this.value,event)" -->
                                                 <td><input autocomplete="off" type="text" name="part[item_qty<?php echo $a;?>]" oninput="updateTableRows(this.value, <?php echo $a; ?>,<?php echo $po_inv_part->req_qty; ?>)"  onkeypress="return checkNumber_new(this.value,event)" id="item_qty<?php echo $a;?>" value="<?php echo $po_inv_part->req_qty; ?>"  placeholder="Qty." ></td>
                                                 <td><input autocomplete="off" type="text" name="part[gst<?php echo $a;?>]" maxlength="2" id="gst<?php echo $a;?>"  placeholder="GST %" ></td>                                                 
                                                 <td><input autocomplete="off" type="text" name="part[bin_no<?php echo $a;?>]" id="bin_no<?php echo $a;?>"  placeholder="Rack No." ></td>
                                                 <td><input autocomplete="off" type="text" name="part[purchase_amt<?php echo $a;?>]" onkeypress="return checkNumber(this.value,event)" id="purchase_amt<?php echo $a;?>" value="<?php echo $po_inv_part->curr_amt; ?>" placeholder="Amount"></td>
                                                 <td><input autocomplete="off" type="text" name="part[asc_amt<?php echo $a;?>]" onkeypress="return checkNumber(this.value,event)" id="asc_amt<?php echo $a;?>"  placeholder="Amount" ></td>
                                                 <td><input autocomplete="off" type="text" name="part[cust_amt<?php echo $a;?>]" onkeypress="return checkNumber(this.value,event)" id="cust_amt<?php echo $a;?>"  placeholder="Amount" ></td>
                                                 <td><input autocomplete="off" type="text"  name="part[remarks<?php echo $a;?>]"  id="remarks<?php echo $a;?>"  placeholder="Remarks" ></td>
                                                 <!--<td><button type="button" onclick="add_part()" id="btn" value="Add">Add</button></td>-->
                                             </tr>
                                            <?php } ?> 
                                        
                                         </thead>
                                         
                                 </table>
                        <br/>
                        <table border="1">
                            <thead>
                                <th colspan="5" style="text-align: center;">Add SrNo.</th>
                                <tr>
                                    <th>Items</th>
                                    <th>Brand</th>
                                    <th>Model</th>
                                    <th>Part code</th>
                                    <th>Sr. No.</th>
                                </tr>
                            </thead>
                            <tbody>
                              <tr  id="row<?php echo $a; ?>"></tr>
    <?php 
    
    $b = 1;
    foreach($po_inv_parts as $po_inv_part) 
    { 
        $item_qty = $po_inv_part['req_qty'];
        $brand_id = $po_inv_part['brand_id'];
        $model_id = $po_inv_part['model_id'];
        $spare_id = $po_inv_part['spare_id'];
        $part_no = $po_inv_part['part_no'];
        $mod_list = $po_inv_part['mod_list'];
        $part_list = $po_inv_part['part_list'];
    
        for ($i = 0; $i < $item_qty; $i++) {?>

            <tr id="row<?php echo $b; ?>" class="table-row-<?php echo $a; ?>">
            <!-- <tr  id="row<?php //echo $b; ?>" id="item_list_<?php //echo $b; ?>"> -->
                <td><?php echo $b++; ?>.</td>
                <td>
                    <?php foreach($brand_arr as $brand){     
                        if($brand['brand_id']==$brand_id) { echo $brand['brand_name'];}
                    }?>
                </td>
                <td>
                    <?php foreach($mod_list as $model){
                        if($model->model_id==$model_id) { echo $model->model_name;}
                    }?>
                </td>
                <td>
                    <?php foreach($part_list as $part){
                        if($part->spare_id==$spare_id) { echo $part->part_no;}
                    } ?>
                </td>
                <td>
                    <input type="text" name='Itemlist<?php echo $spare_id;?>[<?php echo $i;?>]' placeholder="Sr No."/>
                </td>
            </tr>
        <?php } 
        } ?>
        </tbody>
            </table>

            <br>
            <br>

            <div class="form-row">
                <div class="col-md-5">
                    <div class="position-relative form-group">
                        <button type="reset" class="mt-2 btn btn-primary">Reset Details</button>
                        <button type="submit" class="mt-2 btn btn-primary">Save Invoice</button>
                    </div>
                </div>
            </div>
        </div>                                      

            <div class="app-wrapper-footer">
                <div class="app-footer"></div>   
            </div>
            <input type="hidden" name="req_id" value="<?php echo $req_id; ?>" />
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
