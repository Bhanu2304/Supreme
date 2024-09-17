@extends('layouts.app')
@section('content')
    
    <style type="text/css">
        .content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 500px;
            height: 250px;
            text-align: center;
            background-color:#ffa500;
            box-sizing: border-box;
            padding: 10px;
            z-index: 100;
            display: none;
            /*to hide popup initially*/
        }
          
        .close-btn {
            position: absolute;
            right: 20px;
            top: 15px;
            background-color: black;
            color: white;
            border-radius: 50%;
            padding: 4px;
        }
    </style>
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
            $('#tr'+tagId).remove();
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
    $('.content').toggle();
    $('.content').append('<div class="card"><button type="button" class="close" onclick="closeForm()"><i class="fa fa-times-circle" style="float: right;"></i></button><div class="card-header">Pls. mention the reason for Return to HO</div><div class="card-body"><textarea name="reason" id="reason" cols="60" rows="2"></textarea></div><div class="card-footer"><div class="row"><div class="col-sm-6 text-left"><button type="button" id="reject_job" class="btn btn-primary">Apply</button></div><div class="col-sm-6 text-right"><button type="button" onclick="closeForm()" class="btn btn-secondary">Cancel</button></div></div></div></div>');
    $('#table_id').css('pointer-events', 'none');
   
    $("#reject_job").on("click", function () {

        var reason = $('#reason').val();
        if(reason== '')
            {
                alert('Please Fill the Reason');
                return false;
            }

            $.post('reject-job',{tagId:tagId,reason:reason}, function(resp){
            const obj = JSON.parse(resp);
            if(obj.resp_id==='1')
            {
                $('#scc').html('<h5><font color="green">Job Case '+obj.job_no+' Rejected Successfully.</font></h5>');
                $('#tr'+tagId).remove();
                $('#content').empty();
                document.getElementById("content").style.display = "none";
                $('#table_id').css('pointer-events', 'auto');
            }
            else
            {
                $('#err').html('<h5><font color="red"> Job Case Already Rejected</font></h5>');
                $('#content').empty();
                document.getElementById("content").style.display = "none";
                $('#table_id').css('pointer-events', 'auto');
            }
            
        }); 
   });
    
}
function closeForm() {
    $('#content').empty();
    document.getElementById("content").style.display = "none";
    $('#table_id').css('pointer-events', 'auto');
}
</script>

<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner" id="hide">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">

                    <div class="main-card mb-3 card">
                        <div class="card-body"><h5 class="card-title">ASC Reservation View</h5>
                            
                            <p id="scc">@if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif </p>
                                 <p id="err">@if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif </p>
                                 
                                 @if(Session::has('st')) <?php echo Session::get('st'); ?> @endif
                                  
                                 <form method="get" action="{{route('vendor-tag-view')}}" autocomplete="off" class="form-horizontal"  >
                           
                            <div class="form-row">
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">State</label>
                                            <select id="state_id" name="state_id" class="form-control" onchange="get_pincode(this.value)">
                                                <option value="">State</option>
                                                <option value="All">All</option>
                                                @foreach($state_master as $state_id=>$state_name)
                                                    <option value="{{$state_name}}" <?php if( $state_name==$state) 
                                                        { echo 'selected';} ?>>{{$state_name}}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">PinCode</label>
                                        <select id="pincode" name="pincode" class="form-control">
                                            <option value="">PinCode</option>
                                            @foreach($pin_master as $pin)
                                                <option value="{{$pin}}" <?php if( $pin==$pincode) 
                                                        { echo 'selected';} ?>>{{$pin}}</option>
                                            @endforeach
                                        </select>
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
                                        <label for="examplePassword11" class="">Contact No. / PinCode</label>
                                        <input class="form-control" type="text" id="contact_no" name="contact_no" value="<?php echo $contact_no;?>" autocomplete="off" data-original-title="Mobile No." data-placement="top" placeholder="Ph. No. / PinCode">
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

                    <div class="content" id = "content"></div>

                            <div class="card-body">
                                
                              
                                
                            <h5 class="card-title">Customer Details</h5>
                            <table id="table_id" class="table table-striped table-bordered" style="width:100%">
                              <thead>
                                 <tr>
                                    <th>Sr.</th>
                                    <th>Ticket No.</th>
                                    <th colspan="2">Action</th>
                                    <th>ASC Name</th>
                                    <th>Cust. Gr.</th>
                                    <th>Cust Name</th>
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
                                                echo '<td>';
                                                // if($record->job_accept=='0')
                                                // {
                                                //     echo '<a href="ho-tag-view?TagId='.$record->TagId.'">'.$record->ticket_no.'</a>';
                                                // }
                                                // else
                                                // {
                                                //     echo '<a href="vendor-observation?TagId='.$record->TagId.'">'.$record->ticket_no.'</a>';
                                                // }
                                                if($record->brand_id == '4')
                                                {
                                                    echo '<a href="ho-tag-view-cl?TagId='.$record->TagId.'">'.$record->ticket_no.'</a>';
                                                }else{
                                                    echo '<a href="ho-tag-view?TagId='.$record->TagId.'">'.$record->ticket_no.'</a>';
                                                }
                                                #echo "brand-id=>$record->brand_id";
                                                echo '</td>';
                                                echo '<td id="td'.$record->TagId.'">';
                                                if($record->job_accept=='0')
                                                {
                                                echo '<a onclick="job_accept('."'".$record->TagId."'".');" href="#">Accept</a>';
                                                }
                                                else
                                                {
                                                    echo 'Accepted';
                                                }
                                                echo '</td>';
                                                echo '<td><a onclick="ticket_reject('."'".$record->TagId."'".');" href="#">Return to HO</a></td>';
                                                echo '<td>'.$record->center_name.'</td>';
                                                echo '<td>'.$record->Customer_Group.'</td>';
                                                echo '<td>'.$record->Customer_Name.'</td>';
                                                echo '<td>'.$record->State.'</td>';
                                                echo '<td>'.$record->dist_name.'</td>';
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
  

<script>
    $('#table_id').DataTable( );
    </script>
@endsection
