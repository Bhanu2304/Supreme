@extends('layouts.app')
@section('content')

<script>
                                    
  
menu_select('{{$url}}');                                                             
</script>
<script>


function reloadPage(){
    location.reload(true);
}

function get_pincode(state_id){
    
    $.post('vendor-get-pin',{state_id:state_id}, function(data){
        $('#pincode').html(data);
    }); 
     
}



function checkPinNumber(val,evt)
{    
    var charCode = (evt.which) ? evt.which : event.keyCode
	
    if (charCode> 31 && (charCode < 48 || charCode > 57)  || (val=='e' || val.length>=6))
    {            
            return false;
    }
    return true;
}  

function set_tagId(TagId,visit_type)
{
    $('#job_tag_id').val(TagId);
    $('#first_date').hide();
    if(visit_type==='first_visit')
    {
        $('#exampleModalLabel').html('Job Schedule');
    }
    else
    {
        $('#first_date').val($('#td_'+TagId).html());
        $('#first_date').show();
        $('#exampleModalLabel').html('Job Reschedule');
    }
}

function save_shd_time()
{
    $('#scc').html("");
    $('#err').html("");
    var tagId = $('#job_tag_id').val();
    var job_date = $('#job_date').val();
    var job_remarks = $('#job_remarks').val();
    $.post('se-job-save',{tagId:tagId,job_date:job_date,job_remarks:job_remarks}, function(resp){
        const obj = JSON.parse(resp);
        if(obj.resp_id==='1')
        {
            $('#aj_resp').html('<h5><font color="green">Job Case '+obj.job_no+' Sheduled Successfully.</font></h5>');
            //$('#td'+tagId).html('<a onclick="job_reject('+"'"+tagId+"'"+');" href="#">Reject</a>');
            $('#td'+tagId).html(job_date);
        }
        else
        {
            $('#aj_resp').html('<h5><font color="red"> Job Case Already Sheduled.</font></h5>');
        }
        
    }); 
}

function ticket_reject(tagId)
{
    $('#scc').html("");
    $('#err').html("");
    $.post('reject-job',{tagId:tagId}, function(resp){
        const obj = JSON.parse(resp);
        if(obj.resp_id==='1')
        {
            $('#scc').html('<h5><font color="green">Job Case '+obj.job_no+' Rejected Successfully.</font></h5>');
            $('#tr'+tagId).remove();
        }
        else
        {
            $('#err').html('<h5><font color="red"> Job Case Already Rejected</font></h5>');
        }
        
    }); 
}

</script>

<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        
                        
                            
                            <div class="card-body">
                                
                                
                                
                                    
                                
                                
                            <h5 class="card-title">Compliant Details</h5>
                            <table id="table_id" class="table table-striped table-bordered" style="width:100%">
                              <thead>
                                 <tr>
                                    <th>Sr.</th>
                                    <th>Job ID</th>
                                    <th>Center</th>
                                    <th>Cust. Gr.</th>
                                    <th>Cust. Name</th>
                                    <th>State</th>
                                    <th>District</th>
                                    <th>Pincode</th>
                                    <th>Brand</th>
                                    <th>Model No.</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 
                                 <?php $srno = 1;
                                        foreach($DataArr as $record)
                                        {
                                            echo '<tr id="tr'.$record->TagId.'">';
                                            echo '<td>';
                                            echo $srno++.'</td>';
                                                
                                                echo '<td><a href="se-job-detail?back_url='.$back_url.'&whereTag='.$whereTag.'&TagId='.base64_encode($record->TagId).'">'.$record->job_no.'</a></td>'; 
                                                echo '<td>'.$record->center_name.'</td>';
                                                echo '<td>'.$record->Customer_Group.'</td>';
                                                echo '<td>'.$record->Customer_Name.'</td>';
                                                
                                                echo '<td>'.$record->State.'</td>';
                                                echo '<td>';
                                                //echo '<a href="tel:'.$record->Contact_No.'">'.$record->Contact_No;
                                                echo $record->dist_name;
                                                echo '</td>';
                                                echo '<td>'.$record->Pincode.'</td>';
                                                echo '<td>'.$record->Brand.'</td>';
                                                echo '<td>'.$record->Product.'</td>';
                                            echo '</tr>';
                                        }
                                 ?>
                                  
                              </tbody>
                           </table>
                            
                            
                            <input type="hidden" name="whereTag" value="<?php echo $whereTag; ?>" >
                                
                        </div>
                        
                        

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>


@endsection
