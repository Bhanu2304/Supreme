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
                        <div class="card-body"><h5 class="card-title">View Complaint</h5>
                            
                            <p id="scc">@if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif </p>
                                 <p id="err">@if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif </p>
                                 
                                 @if(Session::has('st')) <?php echo Session::get('st'); ?> @endif
                                  
                                 <form method="get" action="{{route('vendor-view-complaint')}}" autocomplete="off" class="form-horizontal"  >
                           
                            <div class="form-row">
                                
                                
                                <div class="col-md-3">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Warranty Category </label>
                                        <select id="warranty_category" name="warranty_category" class="form-control" >
                                            <option value="">Select</option>
                                            <option value="Standard Warranty" <?php if($warranty_category=='Standard Warranty') { echo 'selected';}?> >Standard Warranty</option>
                                            <option value="Out Warranty" <?php if($warranty_category=='Out Warranty') { echo 'selected';}?>>Out Warranty</option>
                                            <option value="Extended" <?php if($warranty_category=='Extended') { echo 'selected';}?>>Extended</option>
                                            <option value="International" <?php if($warranty_category=='International') { echo 'selected';}?>>International</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Service Type </label>
                                        <select id="service_type" name="service_type" class="form-control"  >
                                            <option value="">Select</option>
                                            <!-- <option value="Home Service" <?php //if($service_type=='Home Service') { echo 'selected';}?>>Home Service</option>
                                            <option value="Walk in Service" <?php //if($service_type=='Walk in Service') { echo 'selected';}?>>Walk in Service</option>
                                            <option value="Refurbrish" <?php //if($service_type=='Refurbrish') { echo 'selected';}?>>Refurbrish</option>
                                            <option value="Demo & Installation" <?php //if($service_type=='Demo & Installation') { echo 'selected';}?>>Demo & Installation</option> -->

                                            <option value="Demo & Installation" <?php if($service_type=='Demo & Installation') { echo 'selected';}?> >Demo & Installation</option>
                                            <option value="Online" <?php if($service_type=='Online') { echo 'selected';}?> >Online</option>
                                            <option value="Refurbished" <?php if($service_type=='Refurbished') { echo 'selected';}?> >Refurbished</option>
                                            <option value="Site Visit" <?php if($service_type=='Site Visit') { echo 'selected';}?> >Site Visit</option>
                                            <option value="Walk in" <?php if($service_type=='Walk in') { echo 'selected';}?> >Walk in</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- <div class="col-md-3">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Job Status </label>
                                        <select id="job_status" name="job_status" class="form-control"  >
                                            <option value="">Select</option>
                                            <option value="Open" <?php if($job_status=='Open') { echo 'selected';}?>>Open</option>
                                            <option value="Close" <?php if($job_status=='Close') { echo 'selected';}?>>Close</option>
                                            <option value="Part Pending" <?php if($job_status=='Part Pending') { echo 'selected';}?>>Part Pending</option>
                                            <option value="Reschedule" <?php if($job_status=='Reschedule') { echo 'selected';}?>>Reschedule</option>
                                        </select>
                                    </div>
                                </div> -->
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">Pin Code</label>
                                        <input type="text" maxlength="6" id="pincode" name="pincode" placeholder="Pincode" value="<?php echo $pincode; ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">Job No.</label>
                                        <input class="form-control" type="text" id="job_no" name="job_no" value="<?php echo $job_no;?>" placeholder="Job No.">
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">Ticket No.</label>
                                            <input class="form-control" type="text" id="ticket_no" name="ticket_no" value="<?php echo $ticket_no;?>" placeholder="Ticket No.">
                                        </div>
                                </div>
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
                                        <label for="examplePassword11" class="">Contact No. </label>
                                        <input class="form-control" maxlength="10" type="text" id="contact_no" name="contact_no" value="<?php echo $contact_no;?>" autocomplete="off" data-original-title="Mobile No." data-placement="top" placeholder="Contact No.">
                                    </div>
                                </div>
                                
 				<div class="col-md-2">
                                    
                                        <div class="position-relative form-group">
                                            <br>
                                            
                                            <label for="examplePassword11" class="">&nbsp;</label>   
                                           
                                            <input type="submit"  class="mt-2 btn btn-primary" value="Search" >
                                &nbsp;<a href="{{route('se-dash')}}" class="mt-2 btn btn-danger" >Exit</a>
                                        </div>
                                </div>



                                 </div>


                        </form>
                    </div>
                        
                            
                        <div class="card-body">
                                
                            <h5 class="card-title">Compliant Details</h5>
                            <table id="table_id" class="table table-striped table-bordered" style="width:100%">
                              <thead>
                                 <tr>
                                    <th>Sr.</th>
                                    <th>Job No.</th>
                                    <th>Ticket No.</th>
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
                                 if(empty($DataArr))
                                 {
                                     echo '<th colspan="10" class="text-center">No record found!</th>';
                                     
                                 }
                                        foreach($DataArr as $record)
                                        {
                                            echo '<tr id="tr'.$record->TagId.'">';
                                            echo '<td>';
                                            echo $srno++.'</td>';
                                                
                                                echo '<td>';
                                                if($record->brand_id == '4'){
                                                    echo '<a href="vendor-observation-cl?whereTag='.$whereTag.'&TagId='.$record->TagId.'">'.$record->job_no.'</a>';
                                                }else{
                                                    echo '<a href="vendor-observation?whereTag='.$whereTag.'&TagId='.$record->TagId.'">'.$record->job_no.'</a>';
                                                }
                                                echo '</td>'; 
                                                
                                                echo '<td>'.$record->ticket_no.'</td>';
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
