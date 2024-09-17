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



<div class="app-main"> 
    <div class="app-main__outer">
        <div class="app-main__inner">
        <ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
            <li class="nav-item">
                <a role="tab" class="nav-link active" id="tab-0" data-toggle="tab" href="#tab-content-0">
                    <span>View </span>
                </a>
            </li>
            <li class="nav-item">
                <a role="tab" class="nav-link"       id="tab-1" data-toggle="tab" href="#tab-content-1">
                    <span>Add </span>
                </a>
            </li>
        </ul>
     <div class="tab-content">
         @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
         @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
         <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
             <div class="main-card mb-3 card">
                
                    <div class="card-body">                                        
                        <h5 class="card-title">View Inward</h5>

                        <table class="table" id="table_id">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Sr No.</th>
                                    <th>Supplier Name</th>
                                    <th>Voucher No.</th>
                                    <th>Invoice Date</th>
                                    <th>No. of Case</th>
                                    <th>Vehicle / Docket No.</th>
                                    <th>No. of Parts</th>
                                    <th>Tot. Qty</th>
                                    <th>View</th>
                                    <th>Edit</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php   $srno = 1;
                                    foreach($req_arr as $req)
                                    {
                                        echo '<tr>';
                                            echo '<td>'.$srno++.'</td>';
                                            echo '<td>'.$req->inw_no.'</td>';
                                            echo '<td>'.$req->supplier_name.'</td>';
                                            echo '<td>'.$req->voucher_no.'</td>';
                                            echo '<td>'.$req->invoice_date.'</td>';
                                            echo '<td>'.$req->no_of_case.'</td>';
                                            echo '<td>'.$req->veh_doc_no.'</td>';
                                            echo '<td>'.$req->part_added.'</td>';
                                            echo '<td>'.$req->qty.'</td>';
                                            echo '<td><a href="view-inw-inv-entry?inw_id='.base64_encode($req->inw_id).'">View</a></td>';
                                            echo '<td><a href="edit-inw-inv-entry?inw_id='.base64_encode($req->inw_id).'">Edit</a></td>';
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
        <div class="tab-pane tabs-animation fade " id="tab-content-1" role="tabpanel">
            <div class="main-card mb-3 card">
                <form method="post" action="save-inward-entry" >
                    <div class="card-body">                                        
                        <h5 class="card-title">Inward Stock</h5> 
                                 
                                <div class="form-row">
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Supplier Name (Party Name) <font color="red">*</font></label>
                                            <input autocomplete="off" type="text" id="supplier_name" name="supplier_name" value="" class="form-control"  required="" />
                                        </div>
                                    </div>
                                
                                    <div class="col-md-1">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Voucher / Invoice No. <font color="red">*</font></label>
                                            <input autocomplete="off" type="text" id="voucher_no" name="voucher_no"  value="" class="form-control" required="" />
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-1">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Invoice Date <font color="red">*</font></label>
                                            <input autocomplete="off" type="text" id="invoice_date" readonly="" name="invoice_date" value="" class="form-control datepicker" required="" />
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
                                             <th>GST <font color="red">*</font></th>
                                             <th>Item Qty. <font color="red">*</font></th>
                                             <th>Bin or Rack No. <font color="red">*</font></th>
                                             <th>Purchase Amount <font color="red">*</font></th>
                                             <th>ASC Amount <font color="red">*</font></th>
                                             <th>Customer Amount <font color="red">*</font></th>
                                             <th>Remarks </th>
                                             <th>Action </th>
                                         </tr>
                                     
                                     
                                        <?php for($b=1;$b<=1;$b++) { ?> 
                                         <tr>
                                                     <th><?php echo $a;?></th>
                                                     <td><select autocomplete="off" id="brand<?php echo $a;?>"   onchange="get_model('<?php echo $a;?>',this.value)">
                                                             <option value="">Select</option>
                                                             <?php
                                                        foreach($brand_arr as $brand)
                                                        {
                                                            ?>       <option value="<?php echo $brand['brand_id']; ?>"><?php echo $brand['brand_name']; ?></option>     
                                                <?php   }
                                                ?>
                                                 </select></td>
                                             <td><select autocomplete="off" id="model<?php echo $a;?>"   onchange="get_partcode('<?php echo $a;?>',this.value)">
                                                             <option value="">Select</option>
                                                 </select></td>
                                             <td><select autocomplete="off" id="part_code<?php echo $a;?>"   onchange="get_part_name('<?php echo $a;?>',this.value)">
                                                             <option value="">Select</option> 
                                                 </select> 
                                             </td>
                                             <td><input autocomplete="off" type="text" id="part_name<?php echo $a;?>"  placeholder="Part Name" ></td>
                                             <td><input autocomplete="off" type="text" id="color<?php echo $a;?>"  placeholder="Color" ></td>
                                             <td><input autocomplete="off" type="text" id="hsn_code<?php echo $a;?>"  placeholder="HSN" ></td>
                                             <td><input autocomplete="off" type="text" maxlength="2" id="gst<?php echo $a;?>"  placeholder="GST %" ></td>
                                             <td><input autocomplete="off" type="text" onkeypress="return checkNumber(this.value,event)" id="item_qty<?php echo $a;?>"  placeholder="Qty." ></td>
                                             <td><input autocomplete="off" type="text" id="bin_no<?php echo $a;?>"  placeholder="Rack No." ></td>
                                             <td><input autocomplete="off" type="text" onkeypress="return checkNumber(this.value,event)" id="purchase_amt<?php echo $a;?>"  placeholder="Amount" ></td>
                                             <td><input autocomplete="off" type="text" onkeypress="return checkNumber(this.value,event)" id="asc_amt<?php echo $a;?>"  placeholder="Amount" ></td>
                                             <td><input autocomplete="off" type="text" onkeypress="return checkNumber(this.value,event)" id="cust_amt<?php echo $a;?>"  placeholder="Amount" ></td>
                                             <td><input autocomplete="off" type="text"  id="remarks<?php echo $a;?>"  placeholder="Remarks" ></td>
                                             <td><button type="button" onclick="add_part()" id="btn" value="Add">Add</button></td>
                                         </tr>
                                        <?php } ?> 
                                         </thead>
                                         <tbody id="part_arr">
                                     </tbody>
                                 </table>
      

      
                                 <br>
                                 <br>

                            

                        <div class="form-row">
                            <div class="col-md-5">
                                <div class="position-relative form-group">
                                    <button type="reset" class="mt-2 btn btn-primary">Reset Details</button>
                                     <button type="submit"  class="mt-2 btn btn-primary" >Save Invoice</button>
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
