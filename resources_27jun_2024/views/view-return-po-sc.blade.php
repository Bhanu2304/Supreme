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
                    <span>Short Part Cases</span>
                </a>
            </li>
            <li class="nav-item">
                <a role="tab" class="nav-link" id="tab-1" data-toggle="tab" href="#tab-content-1">
                    <span>Faulty or Mismatch Part Cases</span>
                </a>
            </li>
            
        </ul>
     <div class="tab-content">
         
         <h5 id="succ" style="display:none;"><font color="green"> </font></h5> 
                                 <h5 id="error" style="display:none;"><font color="red"> </font></h5> 
         <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
             <div class="main-card mb-3 card">
                
                    <div class="card-body">                                        
                        <h5 class="card-title">Short Part Cases</h5>
                        <table border="1" style="font-size:13px;text-align:center;" id="table_id">
                            
                            <tr>
                                <th style="text-align:center;">#</th>
                                <th style="text-align:center;">Invoice No.</th>
                                <th style="text-align:center;">PO Type</th>
                                <th style="text-align:center;">ASC Name</th>
                                <th style="text-align:center;">Job No.</th>
                                <th style="text-align:center;">Brand Name</th>
                                <th style="text-align:center;">Model Number</th>
                                <th style="text-align:center;">Part Code</th>
                                <th style="text-align:center;">Part Name</th>
                                <th style="text-align:center;">Color</th>
                                <th style="text-align:center;">HSN Code</th>
                                <th style="text-align:center;">GST Rate</th>
                                <th style="text-align:center;">ASC Amount</th>
                                <th style="text-align:center;">Customer Amount</th>
                                <th style="text-align:center;">Request Qty</th>
                                <th style="text-align:center;">Issued Qty</th>
                                <th style="text-align:center;">Discount</th>
                                <th style="text-align:center;">Remarks</th>
                                <th style="text-align:center;">Accept</th>
                                <th style="text-align:center;">Return</th>
                            </tr>
                            
                            <tbody>
                            <?php   $srno = 1;
                                    foreach($short_return_master as $po_job)
                                    {
                                        
                                        echo '<tr id="tr'.$po_job->return_id.'">';
                                            echo '<td>'.$srno++.'</td>';
                                            echo '<td>'.$po_job->invoice_no.'</td>';
                                            echo '<td>'.$po_job->po_type.'</td>';
                                            echo '<td>'.$po_job->asc_name.'</td>';
                                            echo '<td>'.$po_job->job_no.'</td>';
                                            
                                            //echo '<td>'.$po_job->asc_code.'</td>';
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
                                            echo '<td>'.$po_job->discount.'</td>';
                                            echo '<td>'.$po_job->remarks.'</td>';
                                            echo '<td><a href="#" onclick="approve_short('."'{$po_job->return_id}'".')">Accept</a></td>';
                                            echo '<td><a href="#" onclick="cancel_short('."'{$po_job->return_id}'".')">cancel</a></td>';                                            
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
        
         <div class="tab-pane tabs-animation fade" id="tab-content-1" role="tabpanel">
             <div class="main-card mb-3 card">
                
                    <div class="card-body">                                        
                        <h5 class="card-title">Faulty or Mismatch Part Cases</h5>

                        <table border="1" style="font-size:13px;" id="table_id">
                            
                            <tr>
                                <th style="text-align:center;">#</th>
                                <th style="text-align:center;">Invoice No.</th>
                                <th style="text-align:center;">PO Type</th>
                                <th style="text-align:center;">ASC Name</th>
                                <th style="text-align:center;">Job No.</th>
                                <th style="text-align:center;">Brand Name</th>
                                <th style="text-align:center;">Model Number</th>
                                <th style="text-align:center;">Part Code</th>
                                <th style="text-align:center;">Part Name</th>
                                <th style="text-align:center;">Color</th>
                                <th style="text-align:center;">HSN Code</th>
                                <th style="text-align:center;">GST Rate</th>
                                <th style="text-align:center;">ASC Amount</th>
                                <th style="text-align:center;">Customer Amount</th>
                                <th style="text-align:center;">Request Qty</th>
                                <th style="text-align:center;">Issued Qty</th>
                                <th style="text-align:center;">Discount</th>
                                <th style="text-align:center;">Remarks</th>
                                <th style="text-align:center;">Accept</th>
                                <th style="text-align:center;">Return</th>

                            </tr>
                            
                            <tbody>
                            <?php   $srno = 1;
                                    foreach($fault_return_master as $po_job)
                                    {
                                        echo '<tr id="tr'.$po_job->return_id.'">';
                                            echo '<td>'.$srno++.'</td>';
                                            echo '<td>'.$po_job->invoice_no.'</td>';
                                            echo '<td>'.$po_job->po_type.'</td>';
                                            echo '<td>'.$po_job->asc_name.'</td>';
                                            echo '<td>'.$po_job->job_no.'</td>';
                                            
                                            //echo '<td>'.$po_job->asc_code.'</td>';
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
                                            echo '<td>'.$po_job->discount.'</td>';
                                            echo '<td>'.$po_job->remarks.'</td>';
                                            echo '<td><a href="#" onclick="approve_fault('."'{$po_job->return_id}'".')">Accept</a></td>';
                                            echo '<td><a href="#" onclick="cancel_fault('."'{$po_job->return_id}'".')">cancel</a></td>';                                            
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
    
    function approve_short(part_id)
    {
        
        $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'ho-return-approve-short',
              method: 'post',
              data: {
                 return_id:part_id
              },
              success: function(result){
                  if(result==='1')
                  {
                      $('#tr'+part_id).remove();
                      $('#succ').show();
                      $('#succ').html("Short Case Request Accepts Successfully.");
                      $('#error').hide();
                  }
                  
                  else
                  {
                      $('#succ').hide();
                      $('#error').html("Short Case Request Failed.");
                      $('#error').show();
                  }    
              }});
    }
    
    function cancel_short(part_id)
    {
        
        $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'ho-return-cancel-short',
              method: 'post',
              data: {
                 return_id:part_id
              },
              success: function(result){
                  if(result==='1')
                  {
                      $('#tr'+part_id).remove();
                      $('#succ').show();
                      $('#succ').html("Short Case Request Cancelled Successfully.");
                      $('#error').hide();
                  }
                  
                  else
                  {
                      $('#succ').hide();
                      $('#error').html("Short Case Request Failed.");
                      $('#error').show();
                  }    
              }});
    }
    
    function approve_fault(part_id)
    {
        
        $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'ho-return-approve-fault',
              method: 'post',
              data: {
                 return_id:part_id
              },
              success: function(result){
                  if(result==='1')
                  {
                      $('#tr'+part_id).remove();
                      $('#succ').show();
                    $('#succ').html("Part Moved To SRN Dispatched cases Successfully.");
                      $('#error').hide();
                  }
                  
                  else
                  {
                      $('#succ').hide();
                      $('#error').html("Part Moved To SRN Dispatched cases Failed.");
                      $('#error').show();
                  }    
              }});
    }
 
    function cancel_fault(part_id)
    {
        
        $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'ho-return-cancel-fault',
              method: 'post',
              data: {
                 return_id:part_id
              },
              success: function(result){
                  if(result==='1')
                  {
                      $('#tr'+part_id).remove();
                      $('#succ').show();
                      $('#succ').html("Fault/Mismatch Case Request Cancelled Successfully.");
                      $('#error').hide();
                  }
                  
                  else
                  {
                      $('#succ').hide();
                      $('#error').html("Fault/Mismatch Request Failed.");
                      $('#error').show();
                  }    
              }});
    }
 
</script>

@endsection
