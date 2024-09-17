@extends('layouts.app')
@section('content')

<script>
                                    

menu_select('{{$url}}');                                                             
</script>
<script>


function reloadPage(){
    location.reload(true);
}

function get_pincode(state_name)
 {
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-pincode-by-state-name',
              method: 'post',
              data: {
                 state_name: state_name 
              },
              success: function(result){
                  $('#pincode').html(result);
              }});
 }

function get_state(region_id)
 {
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-state-by-region-id',
              method: 'post',
              data: {
                 region_id: region_id 
              },
              success: function(result){
                  $('#state_id').html(result);
              }});
 }

function get_asc_code(asc_id)
 {
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-asc-code-by-asc-id',
              method: 'post',
              data: {
                 asc_id: asc_id 
              },
              success: function(result){
                  $('#asc_name').val(result);
              }});
 }

function get_ticket_date(ticket_no)
 {
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-ticket-date-by-ticket-no',
              method: 'post',
              data: {
                 ticket_no: ticket_no 
              },
              success: function(result){
                  $('#ticket_date').val(result);
              }});
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

function job_accept(tagId)
{
    $('#scc').html("");
    $('#err').html("");
    $.post('accept-job',{tagId:tagId}, function(resp){
        const obj = JSON.parse(resp);
        if(obj.resp_id==='1')
        {
            $('#scc').html('<h5><font color="green">Job Case '+obj.job_no+' Accepted Successfully.</font></h5>');
            //$('#td'+tagId).html('<a onclick="job_reject('+"'"+tagId+"'"+');" href="#">Reject</a>');
            $('#td'+tagId).html("Accepted");
        }
        else
        {
            $('#err').html('<h5><font color="red"> Job Case Already Accepted</font></h5>');
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

function assign(se_no,case_id){
    
    var se_id = $('#se_id'+se_no+'_'+case_id).val();
    $.post('ho-allocate-se',{case_id:case_id,se_id:se_id,se_no:se_no}, function(data){
        alert(data);
    }); 
     
}


</script>

<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body"><h5 class="card-title">Allocate To Engineer</h5>
                            
                            <p id="scc">@if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif </p>
                                 <p id="err">@if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif </p>
                                 
                                 @if(Session::has('st')) <?php echo Session::get('st'); ?> @endif
                                  
                                 <form method="get" action="{{route('ho-alloc-se-view')}}" autocomplete="off" class="form-horizontal"  >
                           
                            <div class="form-row">
                                    <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">Region</label>
                                            <select id="region_id" name="region_id" class="form-control" onchange="get_state(this.value)">
                                                <option value="">Region</option>
                                                <?php if(count($region_list)>1) {   ?>
                                                <option value="All">All</option>
                                                <?php } ?>
                                                @foreach($region_list as $region)
                                                    <option value="{{$region->region_id}}" <?php if( $region->region_id==$region_id) 
                                                        { echo 'selected';} ?>>{{$region->region_name}}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                    </div>
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">State</label>
                                            <select id="state_id" name="state_id" class="form-control" onchange="get_pincode(this.value)">
                                                <option value="">State</option>
                                                <?php if(count($state_master)>1) {   ?>
                                                <option value="All">All</option>
                                                <?php } ?>
                                                @foreach($state_master as $state_id=>$state_name)
                                                    <option value="{{$state_name}}" <?php if( $state_name==$state) 
                                                        { echo 'selected';} ?>>{{$state_name}}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                </div>
                                
                                
                                
                                
                                                <div class="col-md-3">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Service Type </label>
                                                        <select id="service_type" name="service_type" class="form-control"  >
                                                            <option value="">Select</option>
                                                            <option value="Home Service">Home Service</option>
                                                            <option value="Walk in Service">Walk in Service</option>
                                                            <option value="Refurbrish">Refurbrish</option>
                                                            <option value="Demo & Installation">Demo & Installation</option>
                                                        </select>
                                                    </div>
                                                </div>
                                    <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">Service Center Name </label>
                                        <select id="asc_code" name="asc_code" onchange="get_asc_code(this.value)" class="form-control" >
                                                <option value="All">All</option>
                                                @foreach($asc_master as $asc)
                                                    <option value="{{$asc->center_id}}" <?php if( $center_id==$asc->center_id) 
                                                        { echo 'selected';} ?>>{{$asc->center_name}}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                </div>
                                    <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">Service Center Code </label>
                                        <input type="text" id="asc_name" name="asc_name" class="form-control" >
                                                
                                            
                                    </div>
                                </div>
                                    
                                <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">Ticket No.</label>
                                            <input class="form-control" type="text" id="ticket_no" onblur="get_ticket_date(this.value);" name="ticket_no" value="<?php echo $ticket_no;?>" placeholder="Ticket No.">
                                        </div>
                                    </div>
                                    
                                <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">Ticket Created Date</label>
                                            <input class="form-control" type="text" id="ticket_date" name="ticket_date" value="<?php echo $ticket_date;?>" placeholder="Ticket Created Date">
                                        </div>
                                    </div>    
                                    
                                                
                                <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">Customer Address </label>
                                            <input class="form-control" type="text" id="cust_add" name="cust_add" value="<?php echo $cust_add;?>" placeholder="Customer Address">
                                        </div>
                                    </div>
                                

                                
                                
                                
                                <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">Phone Number </label>
                                            <input class="form-control" type="text" id="contact_no" name="contact_no" value="<?php echo $contact_no;?>" placeholder="Phone No.">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">Pin code </label>
                                            <input class="form-control" type="text" id="pincode" name="pincode" value="<?php echo $pincode;?>"  placeholder="Pin Code">
                                        </div>
                                    </div>
                                <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">From Date</label>
                                            <input name="from_date" id="from_date" placeholder="From" type="text" value="<?php echo $from_date; ?>" autocomplete="off" class="form-control datepicker" >
                                        </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">To Date</label>
                                        <input name="to_date" id="to_date" placeholder="To" type="text" value="<?php echo $to_date; ?>" autocomplete="off" class="form-control datepicker" >
                                    </div>
                                </div>
                                
                                    
                                
                                    
                                
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">&nbsp;</label>
                                            <br>                   
                                            <input type="submit" name="submit" class="btn btn-primary btn-grad" data-original-title="" title="" value="Search" >
                                            <button type="reset" id="reset" class="btn btn-primary btn-grad"  >Reset</button>
                                            <input type="submit" name="submit" class="btn btn-primary btn-grad" data-original-title="" title="" value="Download In Excel" >
                                        </div>
                                    </div>

 

                                 </div>


                        </form>
                    </div>
                        
                            
                            <div class="card-body">
                                
                                
                                
                                    
                                
                                
                            <h5 class="card-title">Customer Details</h5>
                            <table id="table_id" class="table table-striped table-bordered" style="width:100%">
                              <thead>
                                  <tr>
                                      <th colspan="8">Job Details</th> 
                                      <th rowspan="2">Allocate To</th>
                                      <th></th>
                                      <th rowspan="2">Reallocate To</th>
                                      <th></th>
                                  </tr>
                                 <tr>
                                    <th>Sr.</th>
                                    <th>Ticket No.</th>
                                    <th>Job No.</th>
                                    <th>Center</th>
                                    <th>Cust. Gr.</th>
                                    <th>Name</th>
                                    <th>State</th>
                                    
                                    <th>Pincode</th>
                                    
                                   <th></th>
                                   <th></th>
                                 </tr>
                              </thead>
                              <tbody>
                                 
                                 <?php $srno = 1;
                                        foreach($DataArr as $record)
                                        {
                                            echo '<tr>';
                                            echo '<td>';
                                            echo $srno++.'</td>';
                                                echo '<td><a href="ho-tag-view?TagId='.$record->TagId.'&back='."$back&whereTag=$whereTag".'">'.$record->ticket_no.'</a></td>';
                                                echo '<td>'.$record->job_no.'</td>';
                                                
                                                echo '<td>'.$record->center_name.'</td>';
                                                echo '<td>'.$record->Customer_Group.'</td>';
                                                echo '<td>'.$record->Customer_Name.'</td>';
                                                
                                                echo '<td>'.$record->State.'</td>';
                                                
                                                echo '<td>'.$record->Pincode.'</td>';
                                                
                                                
                                                ?>
                              <td>
                                                <select id="se_id1_<?php echo $record->TagId; ?>" name="se_id<?php echo $record->TagId; ?>" class="form-control" required=""> 
                                                    <option value="">Select</option>
                                                @foreach($se_arr as $se)
                                                    <!-- <option value="{{$se->se_id}}" <?php //if($se->se_id==$record->se_id) { echo 'selected';} ?> >{{$se->se_name}}</option> -->
                                                    <option value="{{$se->se_id}}" <?php if($se->center_id==$record->center_id) { echo 'selected';}else{echo 'hidden';} ?> >{{$se->se_name}}</option>
                                                @endforeach
                                                </select>
                              </td>
                              <td><a href="#" onclick="assign('1','<?php echo $record->TagId; ?>')">Assign</a></td>
                              <td>
                                                <select id="se_id2_<?php echo $record->TagId; ?>" name="se_id2_<?php echo $record->TagId; ?>" class="form-control" required=""> 
                                                    <option value="">Select</option>
                                                @foreach($se_arr as $se)
                                                    <!-- <option value="{{$se->se_id}}" >{{$se->se_name}}</option> -->
                                                    <option value="{{$se->se_id}}" <?php if($se->se_id==$record->se_id) { echo 'hidden';}?> >{{$se->se_name}}</option>
                                                @endforeach
                                                </select>
                              </td>
                              <td><a href="#" onclick="assign('2','<?php echo $record->TagId; ?>')">Assign</a></td>
                              
                              
                                    <?php 
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
<script>
    $('#table_id').DataTable( );
    </script>
@endsection
