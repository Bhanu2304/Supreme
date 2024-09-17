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

function set_tagId(TagId)
{
    $('#job_tag_id').val(TagId);
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
                        <div class="card-body"><h5 class="card-title">Se Job View</h5>
                            
                            <p id="scc">@if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif </p>
                                 <p id="err">@if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif </p>
                                 
                                 @if(Session::has('st')) <?php echo Session::get('st'); ?> @endif
                                  
                                 <form method="get" action="{{route('se-job-view')}}" autocomplete="off" class="form-horizontal"  >
                           
                            <div class="form-row">
                                
                                
                                
                                
                                <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">From Date</label>
                                            <input name="from_date" id="from_date" autocomplete="off" placeholder="From" type="text" value="<?php echo $from_date; ?>" autocomplete="off" class="form-control datepicker" >
                                        </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">To Date</label>
                                        <input name="to_date" id="to_date" autocomplete="off" placeholder="To" type="text" value="<?php echo $to_date; ?>" autocomplete="off" class="form-control datepicker" >
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">Contact No. / PinCode</label>
                                        <input class="form-control" type="text" id="contact_no" name="contact_no" value="<?php echo $contact_no;?>" autocomplete="off" data-original-title="Mobile No." data-placement="top" placeholder="Ph. No. / Pin">
                                    </div>
                                </div>
                                
 				<div class="col-md-2">
                                    
                                        <div class="position-relative form-group">
                                            <br>
                                            <label for="examplePassword11" class="">&nbsp;</label>   
                                           
                                            <input type="submit"  class="btn btn-primary" value="Search" >
                                &nbsp;<a href="{{route('home')}}" class="btn btn-danger" >Exit</a>
                                        </div>
                                </div>



                                 </div>


                        </form>
                    </div>
                        
                            
                            <div class="card-body">
                                
                                
                                
                                    
                                
                                
                            <h5 class="card-title">Job Details</h5>
                            <table id="table_id" class="table table-striped table-bordered" style="width:100%">
                              <thead>
                                 <tr>
                                    <th>Sr.</th>
                                    <th>Job No.</th>
                                    <th colspan="2">Call Type</th>
                                    
                                    <th>Ticket No.</th>
                                    <th>Cust. Gr.</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>State</th>
                                    <th>Mobile No.</th>
                                    <th>Pincode</th>
                                    <th>Product</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 
                                 <?php $srno = 1;
                                        foreach($DataArr as $record)
                                        {
                                            echo '<tr id="tr'.$record->TagId.'">';
                                            echo '<td>';
                                            echo $srno++.'</td>';
                                                echo '<td><a href="se-job-ob?TagId='.$record->TagId.'">'.$record->ticket_no.'</a></td>';
                                                echo '<td>'.$record->entry_type.'</td>';
                                                //echo '<td><a onclick="ob_view('."'".$record->TagId."'".');" href="#">View</a></td>';
                                                echo '<td id="td'.$record->TagId.'">';
                                                $job_id = "'$record->TagId'";
                                                if($record->entry_type=='calling' && $record->se_sdl_job=='0')
                                                {
                                                    echo '<a class="btn mr-2 mb-2 btn-primary" onclick="set_tagId('.$job_id.')" data-toggle="modal" data-target="#jobShedule" href="#">job</a>';
                                                    ?>
                                        
                                                    <?php
                                                }
                                                else
                                                {
                                                    echo $record->job_date;
                                                }
                                                echo '</td>';
                                                
                                                echo '<td>'.$record->job_no.'</td>';
                                                echo '<td>'.$record->Customer_Group.'</td>';
                                                echo '<td>'.$record->Customer_Name.'</td>';
                                                echo '<td>'.$record->Customer_Address.'</td>';
                                                echo '<td>'.$record->State.'</td>';
                                                echo '<td><a href="tel:'.$record->Contact_No.'">'.$record->Contact_No.'</td>';
                                                echo '<td>'.$record->Pincode.'</td>';
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
