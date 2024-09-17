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
                    <span>View PO Request</span>
                </a>
            </li>
           <li class="nav-item">
                <a role="tab" class="nav-link"       id="tab-1" data-toggle="tab" href="#tab-content-1">
                    <span>View  Approved PO</span>
                </a>
            </li>
        </ul>
     <div class="tab-content">
         @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
         <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
             <div class="main-card mb-3 card">
                
                    <div class="card-body">                                        
                        <h5 class="card-title">PO Request</h5>

                        <table class="table" id="table_id">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Job No.</th>
                                <th>PO Date</th>
                                <th>Type</th>
                                <th>Brand</th>
                                <th>Part Pending</th>
                                <th>View</th>
                                
                            </tr>
                            </thead>
                            <tbody>
                            <?php   
                                $srno = 1;
                                foreach($po_job_arr as $po_job)
                                {
                                    echo '<tr>';
                                        echo '<td>'.$srno++.'</td>';
                                        echo '<td>'.$po_job->job_no.'</td>';
                                        echo '<td>'.date('d-m-Y',strtotime($po_job->created_at)).'</td>';
                                        echo '<td>Job Case</td>';
                                        echo '<td>'.$brand_master[$po_job->brand_id].'</td>';
                                        echo '<td>'.$po_job->part_pending.'</td>';

                                        echo '<td><a href="outward-job-part-po?tag_id='.base64_encode($po_job->TagId).'">View</a></td>';
                                    echo '</tr>';
                                }
                                foreach($po_sc_arr as $po_job)
                                {
                                    echo '<tr>';
                                        echo '<td>'.$srno++.'</td>';
                                        echo '<td>'.$po_job->req_no.'</td>';
                                        echo '<td>'.date('d-m-Y',strtotime($po_job->created_at)).'</td>';
                                        echo '<td>PO</td>';
                                        echo '<td>'.$brand_master[$po_job->brand_id].'</td>';
                                        echo '<td>'.$po_job->qty_pending.'</td>';

                                        echo '<td><a href="outward-sc-part-po?req_id='.base64_encode($po_job->req_id).'">View</a></td>';
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
                
                    <div class="card-body">                                        
                        <h5 class="card-title">View Approve PO</h5>

                        <table class="table" id="table_id">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>PO Request Date</th>
                                <th>Part PO No.</th>
                                <th>PO Type</th>
                                <th>Job No.</th>
                                <th>ASC Name</th>
                                <th>ASC Code</th>
                                <th>Brand</th>
                                <th>Model</th>
                                <th>Part Code</th>
                                <th>Part Name</th>
                                <th>Color</th>
                                <th>HSN Code</th>
                                <th>GST Rate</th>
                                <th>ASC Amount</th>
                                <th>Customer Amount</th>
                                <th>Request Qty.</th>
                                <th>Issued Qty.</th>
                                <th>Reject Qty.</th>
                                <th>Discount</th>
                                <th>Remarks</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php   $srno = 1;
                                    
                                    foreach($data_arr as $po_job)
                                    {
                                        echo '<tr id="tr'.$po_job->part_id.'">';
                                            echo '<td>'.$srno++.'</td>';
                                            echo '<td>'.date('d-m-y',strtotime($po_job->po_date)).'</td>';
                                            echo '<td>'.$po_job->po_no.'</td>';
                                            echo '<td>'.$po_job->po_type.'</td>';
                                            echo '<td>'.$po_job->job_no.'</td>';
                                            echo '<td>'.$po_job->asc_name.'</td>';
                                            echo '<td>'.$po_job->asc_code.'</td>';
                                            echo '<td>'.$po_job->brand_name.'</td>';
                                            echo '<td>'.$po_job->model_name.'</td>';
                                            echo '<td>'.$po_job->part_no.'</td>';
                                            echo '<td>'.$po_job->part_name.'</td>';
                                            echo '<td>'.$po_job->color.'</td>';
                                            echo '<td>'.$po_job->hsn_code.'</td>';
                                            echo '<td>'.$po_job->gst.'</td>';
                                            echo '<td>'.$po_job->asc_amount.'</td>';
                                            echo '<td>'.$po_job->customer_amount.'</td>';
                                            echo '<td>'.$po_job->req_qty.'</td>';
                                            echo '<td>'.$po_job->issued_qty.'</td>';
                                            echo '<td>';
                                            if($po_job->reject=='1')
                                            {
                                               echo $po_job->req_qty;
                                            }
                                            echo '</td>';
                                            echo '<td>'.$po_job->discount.'</td>';
                                            echo '<td>'.$po_job->remarks.'</td>';
                                            
                                           
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
