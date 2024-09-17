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
                    <span>View Dispatch</span>
                </a>
            </li>
            
            
        </ul>
     <div class="tab-content">
         <h5 id="succ" style="display:none;"><font color="green"> </font></h5> 
                                 <h5 id="error" style="display:none;"><font color="red"> </font></h5> 
         <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
             <div class="main-card mb-3 card">
                
                <h5>Invoice No.: <?php echo $invoice_arr[0]->invoice_no;?></h5>
                 <h5>Eway Bill No.: <?php echo $dispatch_det->eway_bill_no;?></h5>
                 <h5>ASC Name: <?php echo $dispatch_det->asc_name;?></h5>
                    <div class="card-body">                                        
                        
                            <table border="1" style="font-size:13px;text-align:center;" id="table_id">
                            
                            <tr>
                                <th>Sr No.</th>
                                <th>PO Request Date</th>
                                <th>Part PO Number</th>
                                <th>PO Type</th>
                                <th>Job No.</th>
                                <th>ASC Name</th>
                                <th>ASC Code</th>
                                <th>Brand Name</th>
                                <th>Model No.</th>
                                <th>Part Code</th>
                                <th>Part Name</th>
                                <th>Colour</th>
                                <th>HSN Code</th>
                                <th>GST Rate</th>
                                <th>ASC Amount</th>
                                <th>Customer Amount</th>
                                <th>Requested Qty</th>
                                <th>Issued Qty</th>
                                <th>Discount</th>
                                <th>Remarks</th>
                                
                            </tr>
                            
                            <tbody>
                            <?php   
                                if(!empty($invoice_arr))
                                {
                                    $srno = 1;
                                    foreach($invoice_arr as $po_job)
                                    {
                                        echo '<tr>';
                                            echo '<td align="center">'.$srno++.'</td>';
                                            echo '<td align="center">'.date('d-m-y',strtotime($po_job->po_date)).'</td>';
                                            echo '<td align="center">'.$po_job->po_no.'</td>';
                                            echo '<td align="center">'.$po_job->po_type.'</td>';
                                            echo '<td align="center">'.$po_job->job_no.'</td>';
                                            echo '<td align="center">'.$po_job->asc_name.'</td>';
                                            echo '<td align="center">'.$po_job->asc_code.'</td>';
                                            echo '<td align="center">'.$po_job->brand_name.'</td>';
                                            echo '<td align="center">'.$po_job->model_name.'</td>';
                                            echo '<td align="center">'.$po_job->part_no.'</td>';
                                            echo '<td align="center">'.$po_job->part_name.'</td>';
                                            echo '<td align="center">'.$po_job->color.'</td>';
                                            echo '<td align="center">'.$po_job->hsn_code.'</td>';
                                            echo '<td align="center">'.$po_job->gst.'</td>';
                                            echo '<td align="center">'.$po_job->asc_amount.'</td>';
                                            echo '<td align="center">'.$po_job->customer_amount.'</td>';
                                            echo '<td align="center">'.$po_job->req_qty.'</td>';
                                            echo '<td align="center">'.$po_job->issued_qty.'</td>';
                                            echo '<td align="center">'.$po_job->discount.'</td>';
                                            echo '<td align="center">'.$po_job->remarks.'</td>';
                                        echo '</tr>';
                                    }
                                }
                                else
                                {
                                    echo '<tr><td colspan="21">No Records Found.</td></tr>';
                                }
                            ?>
                            </tbody>
                        </table>   
                           <div class="form-row">
                            <div class="col-md-5">
                                <div class="position-relative form-group">
                                    <a href="ho-dispatch-po" class="mt-2 btn btn-primary">Back</a>
                                </div>
                            </div>
                        </div>  
                            
                        
                            
                        
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
    
    function approve(part_id)
    {
        var eway_bill_no = $('#eway_bill_no'+part_id).val();
        var doc_no = $('#doc_no'+part_id).val();
        var veh_doc_no = $('#veh_doc_no'+part_id).val();
        var dispatch_ref_no = $('#dispatch_ref_no'+part_id).val();
        var no_of_cases = $('#no_of_cases'+part_id).val();
        var remarks = $('#remarks'+part_id).val();
        
        $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'save-dispatch',
              method: 'post',
              data: {
                 eway_bill_no: eway_bill_no,
                 invoice_id:part_id,
                 remarks:remarks,
                 doc_no:doc_no,
                 veh_doc_no:veh_doc_no,
                 dispatch_ref_no:dispatch_ref_no,
                 no_of_cases:no_of_cases
              },
              success: function(result){
                  if(result==='1')
                  {
                      $('#tr'+part_id).remove();
                      $('#succ').show();
                      $('#error').hide();
                  }
                  
                  else
                  {
                      $('#succ').hide();
                      $('#error').show();
                  }    
              }});
    }
    
    
    
 


    
    
 
 
 
</script>

@endsection
