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
                    <span>SRN Dispatched Cases</span>
                </a>
            </li>
            <li class="nav-item">
                <a role="tab" class="nav-link" id="tab-1" data-toggle="tab" href="#tab-content-1">
                    <span>View SRN Dispatched Cases</span>
                </a>
            </li>
            
        </ul>
     <div class="tab-content">
         
         <h5 id="succ" style="display:none;"><font color="green"> </font></h5> 
                                 <h5 id="error" style="display:none;"><font color="red"> </font></h5> 
         <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
             <div class="main-card mb-3 card">
                
                    <div class="card-body">                                        
                        <h5 class="card-title">SRN Dispatched Cases</h5>
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
                                <th style="text-align:center;">Part Type</th>
                                <th style="text-align:center;">Part Status</th>
                                <th style="text-align:center;">Bin or Rack No.</th>
                                <th style="text-align:center;">Accept</th>
<!--                                <th style="text-align:center;">Return</th>-->
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
                                            echo '<td>'.$po_job->return_type.'</td>';
                                            echo '<td>';?>
                            <select name="part_status<?php echo $po_job->return_id; ?>" id="part_status<?php echo $po_job->return_id; ?>" >
                                <option value="Defective" <?php if($po_job->return_type=='Faulty') {echo 'selected';} ?>>Defective</option>
                                <option value="Mismatch" <?php if($po_job->return_type=='Mismatch') {echo 'selected';} ?>>Mismatch</option>
                                <option value="Good Stock">Good Stock</option>
                            </select>
                                            <?php echo '</td>';
                                            echo '<td>';
                                        ?>
                                        <input type="text" name="bin_no<?php echo $po_job->return_id; ?>" id="bin_no<?php echo $po_job->return_id; ?>" value="" placeholder="Bin or Rack No." >
                                        <?php echo '</td>';
                                            echo '<td><a href="#" onclick="approve_srn('."'{$po_job->return_id}'".')">Accept</a></td>';
                                           // echo '<td><a href="#" onclick="cancel_short('."'{$po_job->return_id}'".')">cancel</a></td>';                                            
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
                        <h5 class="card-title">View SRN Dispatched Cases</h5>

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
                                <th style="text-align:center;">Part Status</th>
                                <th style="text-align:center;">Bin or Rack No.</th>

                            </tr>
                            
                            <tbody>
                            <?php   $srno = 1;
                                    foreach($srn_master_view as $po_job)
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
                                            echo '<td>'.$po_job->part_status.'</td>';
                                            echo '<td>'.$po_job->bin_no.'</td>';
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
    
    function approve_srn(part_id)
    {
        var part_status = $('#part_status'+part_id).val();
        var bin_no = $('#bin_no'+part_id).val();
        
        $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'ho-return-approve-srn',
              method: 'post',
              data: {
                 return_id:part_id,
                 part_status:part_status,
                 bin_no:bin_no
              },
              success: function(result){
                  if(result==='1')
                  {
                      $('#tr'+part_id).remove();
                      $('#succ').show();
                      $('#succ').html("Inventory Added Successfully.");
                      $('#error').hide();
                  }
                  
                  else
                  {
                      $('#succ').hide();
                      $('#error').html("Inventory Addition Failed.");
                      $('#error').show();
                  }    
              }});
    }
    
    
    
</script>

@endsection
