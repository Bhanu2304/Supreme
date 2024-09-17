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
                    <span>View Return PO</span>
                </a>
            </li>
            
            
        </ul>
     <div class="tab-content">
         <h5 id="succ" style="display:none;"><font color="green"> Part Return Successfully.</font></h5> 
                                 <h5 id="error" style="display:none;"><font color="red">Part Return Failed. </font></h5> 
         <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
             <div class="main-card mb-3 card">
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
                                <th>Color</th>
                                <th>Requested Qty</th>
                                <th>Issued Qty</th>
                                <th>Type of SRN</th>
                                <th>Remarks</th>
                                <th>Status</th>
                            </tr>
                            
                            <tbody>
                            <?php   
                                if(!empty($po_inv_arr))
                                {
                                    $srno = 1;
                                    foreach($po_inv_arr as $po_job)
                                    { $part_id = $po_job->part_allocate_id;
                                        echo '<tr id="tr'."$part_id".'">';
                                            echo '<td align="center">'.$srno++.'</td>';
                                            echo '<td align="center">'.date('d-m-y',strtotime($po_job->part_po_date)).'</td>';
                                            echo '<td align="center">'.$po_job->part_po_no.'</td>';
                                            echo '<td align="center">'.$po_job->po_type.'</td>';
                                            echo '<td align="center">'.$po_job->job_no.'</td>';
                                            echo '<td align="center">'.$po_job->center_name.'</td>';
                                            echo '<td align="center">'.$po_job->asc_code.'</td>';
                                            echo '<td align="center">'.$po_job->Brand.'</td>';
                                            echo '<td align="center">'.$po_job->Model.'</td>';
                                            echo '<td align="center">'.$po_job->part_no.'</td>';
                                            echo '<td align="center">'.$po_job->part_name.'</td>';
                                            echo '<td align="center">'.$po_job->color.'</td>';
                                            echo '<td align="center">'.$po_job->part_required.'</td>';
                                            echo '<td align="center">'.$po_job->part_allocated.'</td>';
                                            echo '<td align="center">'.$po_job->srn_type.'</td>';
                                            echo '<td align="center">'.$po_job->srn_remarks.'</td>';
                                            echo '<td align="center"><a href="#" onclick="return apply_srn('."'".$po_job->part_allocate_id."'".')">Apply</a></td>';
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
    
    function apply_srn(part_id)
    {
        
        
        $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'save-return-sc-ob',
              method: 'post',
              data: {
                 part_id: part_id
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
