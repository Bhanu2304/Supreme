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

/*function ticket_reject(tagId)
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
}*/


function raise_po(part_id)
{
    var po_type = $('#po_type'+part_id).val();
    var color = $('#color'+part_id).val();
    var remarks = $('#remarks'+part_id).val();
    var pending_parts = $('#pending_parts'+part_id).val();
    var pending_parts_int = parseInt(pending_parts);
    
    
    if(pending_parts==='')
    {
        alert("No. of Pending Parts should not be empty.");
        $('#pending_parts'+part_id).focus();
        return false;
    }
    
    if(remarks==='')
    {
        alert("Remarks should not be empty.");
        $('#remarks'+part_id).focus();
        return false;
    }
    
    
    if(pending_parts_int<=0)
    {
        alert("No. of Pending Parts not less than 1");
        $('#pending_parts'+part_id).focus();
        return false;
    }
    
    $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
          
          
    jQuery.ajax({
              url: 'se-raise-po',
              method: 'post',
              data: {
                 part_id: part_id,
                 po_type:po_type,
                 color:color,
                 pending_parts:pending_parts,
                 remarks:remarks
              },
              success: function(result){
                  //alert(result);
                  const obj = JSON.parse(result);
                  if(obj.status==='1')
                  {
                      $('#tr'+part_id).remove();
                      $('#scc').html(obj.job_remarks);
                      $('#scc').show();
                      $('#err').hide();
                  }
                  else 
                  {
                      $('#scc').hide();
                      $('#err').html(obj.job_remarks);
                      $('#err').show();
                  }
                  
                  
              }});      
    
    
}

</script>

<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
                <li class="nav-item">
                    <a role="tab" class="nav-link active" id="tab-0" data-toggle="tab" href="#tab-content-0">
                        <span>Raise PO Request</span>
                    </a>
                </li>
               <li class="nav-item">
                    <a role="tab" class="nav-link"       id="tab-1" data-toggle="tab" href="#tab-content-1">
                        <span>View  PO Request</span>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body"><h5 class="card-title">Raise PO</h5>
                            
                            <h5 id="scc"><font color="green"> {{ Session::get('message') }}</font></h5>  
                                 <h5 id="err"><font color="red"> {{ Session::get('error') }}</font></h5>  
                                 
                                 @if(Session::has('st')) <?php echo Session::get('st'); ?> @endif
                                  
                                 <form method="get" action="{{route('se-raise-po')}}" autocomplete="off" class="form-horizontal"  >
                           
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
                                
                                <div class="col-md-3">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Job Status </label>
                                        <select id="job_status" name="job_status" class="form-control"  >
                                            <option value="">Select</option>
                                            <option value="Open" <?php if($job_status=='Open') { echo 'selected';}?>>Open</option>
                                            <option value="Close" <?php if($job_status=='Close') { echo 'selected';}?>>Close</option>
                                            <option value="Part Pending" <?php if($job_status=='Part Pending') { echo 'selected';}?>>Part Pending</option>
                                            <option value="Reschedule" <?php if($job_status=='Reschedule') { echo 'selected';}?>>Reschedule</option>
                                        </select>
                                    </div>
                                </div>
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
                                           
                                            <input type="submit" name="search" class="mt-2 btn btn-primary" value="Search" >
                                            <input type="reset"  class="mt-2 btn btn-primary" value="Reset" >
                               
                                        </div>
                                </div>



                                 </div>


                        </form>
                    </div>
                        
                            
                            <div class="card-body">
                            <h5 class="card-title">Part Request</h5>
                            <table id="table_id" border="1">
                              <thead>
                                 <tr>
                                    <th>Sr.</th>
                                    <th>Job ID</th>
                                    <th>View</th>
                                    <th>Center</th>
                                    <th>Cust. Gr.</th>
                                    <th>Name</th>
                                    <th>State</th>
                                    <th>Mobile No.</th>
                                    <th>Pincode</th>
                                    <th>Brand</th>
                                    <th>Model</th>
                                    <th>Part Code</th>
                                    <th>Part Name</th>
                                    <th>PO Type</th>
                                    <th>No. of Pending Parts</th>
                                    <th>Color</th>
                                    <th>Remarks</th>
                                    <th>Action</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 
                                 <?php $srno = 1;
                                        foreach($DataArr as $record)
                                        {
                                            echo '<tr id="tr'.$record->part_id.'">';
                                            echo '<td>';
                                            echo $srno++.'</td>';
                                                echo '<td>'.$record->job_no.'</td>';
                                                echo '<td><a href="se-job-detail?whereTag='.$whereTag.'&back_url='.$back_url.'&TagId='.base64_encode($record->TagId).'">View</td>';
                                                echo '<td>'.$record->center_name.'</td>';
                                                echo '<td>'.$record->Customer_Group.'</td>';
                                                echo '<td>'.$record->Customer_Name.'</td>';
                                                
                                                echo '<td>'.$record->State.'</td>';
                                                echo '<td>';
                                                //echo '<a href="tel:'.$record->Contact_No.'">'.$record->Contact_No;
                                                echo $record->Contact_No;
                                                echo '</td>';
                                                echo '<td>'.$record->Pincode.'</td>';
                                                echo '<td>'.$record->Brand.'</td>';
                                                echo '<td>'.$record->Model.'</td>';
                                                echo '<td>'.$record->part_no.'</td>';
                                                echo '<td>'.$record->part_name.'</td>';
                                                ?>
                                                <td><select name="po_type<?php echo $record->part_id;?>" id="po_type<?php echo $record->part_id;?>">
                                                        <option value="FOC">PAID</option>
                                                        <option value="FOC">FOC</option>    
                                                </select></td>
                                                <td><input onkeypress="return checkNumber(this.value,event)" autocomplete="off" type="text" name="pending_parts<?php echo $record->part_id;?>" id="pending_parts<?php echo $record->part_id;?>" value="<?php echo $record->pending_parts;?>"  placeholder="Pending Parts" ></td>
                                                <td><input autocomplete="off" type="text" name="color<?php echo $record->part_id;?>" id="color<?php echo $record->part_id;?>"  placeholder="Color" ></td>
                                                <td><input autocomplete="off" type="text" name="remarks<?php echo $record->part_id;?>" id="remarks<?php echo $record->part_id;?>"  placeholder="Remarks" ></td>
                                                <?php 
                                                
                                                echo '<td><a href="#" onclick="raise_po('."'".$record->part_id."'".')">Raise PO</td>';
                                            echo '</tr>';
                                        }
                                 ?>
                              </tbody>
                           </table>
                            <input type="hidden" name="whereTag" value="<?php echo $whereTag; ?>" >
                        </div>
                        
                        

                    </div>

                </div>

                <div class="tab-pane tabs-animation fade" id="tab-content-1" role="tabpanel">
                    <div class="main-card mb-3 card">
                        
                        <div class="card-body">
                             <form method="get" action="{{route('se-raise-po')}}" autocomplete="off" class="form-horizontal"  >
                           
                            <div class="form-row">
                                
                                
                                <div class="col-md-3">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Warranty Category </label>
                                        <select id="warranty_category1" name="warranty_category" class="form-control" >
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
                                        <select id="service_type1" name="service_type" class="form-control"  >
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
                                
                                <div class="col-md-3">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Job Status </label>
                                        <select id="job_status1" name="job_status" class="form-control"  >
                                            <option value="">Select</option>
                                            <option value="Open" <?php if($job_status=='Open') { echo 'selected';}?>>Open</option>
                                            <option value="Close" <?php if($job_status=='Close') { echo 'selected';}?>>Close</option>
                                            <option value="Part Pending" <?php if($job_status=='Part Pending') { echo 'selected';}?>>Part Pending</option>
                                            <option value="Reschedule" <?php if($job_status=='Reschedule') { echo 'selected';}?>>Reschedule</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">Pin Code</label>
                                        <input type="text" maxlength="6" id="pincode1" name="pincode" placeholder="Pincode" value="<?php echo $pincode; ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">Job No.</label>
                                        <input class="form-control" type="text" id="job_no1" name="job_no" value="<?php echo $job_no;?>" placeholder="Job No.">
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">PO No.</label>
                                            <input class="form-control" type="text" id="po_no1" name="po_no" value="<?php echo $po_no;?>" placeholder="PO No.">
                                        </div>
                                </div>
                                <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">From Date</label>
                                            <input name="from_date" id="from_date1" autocomplete="off" placeholder="From" type="text" value="<?php echo $from_date; ?>" autocomplete="off" class="form-control datepicker" >
                                        </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">To Date</label>
                                        <input name="to_date" id="to_date1" autocomplete="off" placeholder="To" type="text" value="<?php echo $to_date; ?>" autocomplete="off" class="form-control datepicker" >
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">Contact No. </label>
                                        <input class="form-control" maxlength="10" type="text" id="contact_no1" name="contact_no" value="<?php echo $contact_no;?>" autocomplete="off" data-original-title="Mobile No." data-placement="top" placeholder="Contact No.">
                                    </div>
                                </div>
                                
 				<div class="col-md-2">
                                    
                                        <div class="position-relative form-group">
                                            <br>
                                            
                                            <label for="examplePassword11" class="">&nbsp;</label>   
                                           
                                            <input type="submit" name="search_po" class="mt-2 btn btn-primary" value="Search" >
                                &nbsp;<a href="{{route('se-dash')}}" class="mt-2 btn btn-danger" >Exit</a>
                                        </div>
                                </div>



                                 </div>


                        </form>
                        </div>
                        
                        <div class="card-body">
                            <h5 class="card-title">View PO Request</h5>
                            <table id="table_view" border="1">
                              <thead>
                                 <tr>
                                    <th>Sr.</th>
                                    <th>PO No.</th>
                                    <th>PO Date</th>
                                    <th>Job ID</th>
                                    <th>View</th>
                                    <th>Center</th>
                                    <th>Cust. Gr.</th>
                                    <th>Name</th>
                                    <th>State</th>
                                    <th>Mobile No.</th>
                                    <th>Pincode</th>
                                    <th>Brand</th>
                                    <th>Model</th>
                                    <th>Part Code</th>
                                    <th>Part Name</th>
                                    <th>PO Type</th>
                                    <th>Color</th>
                                    <th>No. of Pending Parts</th>
                                    <th>Remarks</th>
                                    <th> Status</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 
                                 <?php $srno = 1;
                                        foreach($po_data_arr as $record)
                                        {
                                            echo '<tr id="tr'.$record->part_id.'">'; 
                                            echo '<td>';echo $srno++.'</td>';
                                            echo '<td>'.$record->part_po_no.'</td>';
                                            echo '<td>'.$record->part_po_date.'</td>';
                                                echo '<td>'.$record->job_no.'</td>';
                                                echo '<td><a href="se-job-detail?whereTag='.$whereTag.'&back_url='.$back_url.'&TagId='.base64_encode($record->TagId).'">View</td>';
                                                echo '<td>'.$record->center_name.'</td>';
                                                echo '<td>'.$record->Customer_Group.'</td>';
                                                echo '<td>'.$record->Customer_Name.'</td>';
                                                
                                                echo '<td>'.$record->State.'</td>';
                                                echo '<td>';
                                                //echo '<a href="tel:'.$record->Contact_No.'">'.$record->Contact_No;
                                                echo $record->Contact_No;
                                                echo '</td>';
                                                echo '<td>'.$record->Pincode.'</td>';
                                                echo '<td>'.$record->Brand.'</td>';
                                                echo '<td>'.$record->Model.'</td>';
                                                echo '<td>'.$record->part_no.'</td>';
                                                echo '<td>'.$record->part_name.'</td>';
                                                echo '<td>'.$record->po_type.'</td>';
                                                echo '<td>'.$record->color.'</td>';
                                                echo '<td>'.$record->pending_parts.'</td>';
                                                echo '<td>'.$record->remarks.'</td>';
                                                echo '<td>'.$record->part_status.'</td>';
                                                //echo '<td><a href="#" onclick="raise_po('."'".$record->part_id."'".')">Raise PO</td>';
                                            echo '</tr>';
                                        }
                                 ?>
                              </tbody>
                           </table>
                        </div>    
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
