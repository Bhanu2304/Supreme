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

</script>

<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body"><h5 class="card-title">Allocate  Service Engineer</h5>
                            
                                @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                 
                                 @if(Session::has('st')) <?php echo Session::get('st'); ?> @endif
                                  
                        <form method="get" action="{{route('allocate-se')}}" class="form-horizontal"  >
                           
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
                                        <label for="examplePassword11" class="">Pin Code</label>
                                        <select id="pincode" name="pincode" class="form-control">
                                            <option value="">Pincode</option>
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
                                            <input name="from_date" id="from_date" autocomplete="off" placeholder="From" type="text" value="<?php echo $from_date; ?>" class="form-control datepicker" >
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">To Date</label>
                                            <input name="to_date" id="to_date" autocomplete="off" placeholder="To" type="text" value="<?php echo $to_date; ?>" class="form-control datepicker" >
                                        </div>
                                    </div>
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">Contact No. / PinCode</label>
                                        <input class="form-control" type="text" id="contact_no" name="contact_no" value="<?php echo $contact_no;?>" data-original-title="Mobile No." data-placement="top" placeholder="Ph. No. / Pin">
                                    </div>
                                </div>
                                
 				<div class="col-md-2">
                                     <br>
                                        <div class="position-relative form-group">
                                           
                                                     <label for="examplePassword11" class="">&nbsp;</label>                       
                                            <input type="submit"  class="btn btn-primary" value="Search" >
                                &nbsp;<a href="{{route('home')}}" class="btn btn-danger" >Exit</a>
                                        </div>
                                </div>



                                 </div>


                        </form>
                    </div>
                        
                            
                            <div class="card-body">
                                
                                <form method="POST" action="se-allocate"  >
                                
                                <div class="form-row">
                                    <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">Service Engineer</label>
                                        <select id="se_id" name="se_id" class="form-control" required=""> 
                                            <option value="">Select</option>
                                            @foreach($se_arr as $se)
                                                <option value="{{$se->se_id}}" >{{$se->se_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <label for="examplePassword11" class="">&nbsp;</label>
                                        <div class="position-relative form-group">
                                            
                                            
                                             
                                            <input type="submit"  class="btn btn-primary"  value="Allocate" >
                                
                                        </div> 
                                    </div>
                                    
                                </div>    
                                
                                
                            <h5 class="card-title">Customer Details</h5>
                            <table id="table_id" class="table table-striped table-bordered" style="width:100%">
                              <thead>
                                 <tr>
                                    <th>Sr.</th>
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
                                            echo '<tr>';
                                            echo '<td>';
                                            echo '<input type="checkbox" name="case[]" value="'.$record->TagId.'" />';
                                            echo $srno++.'</td>';
                                                echo '<td>'.$record->ticket_no.'</a></td>';
                                                echo '<td>'.$record->Customer_Group.'</td>';
                                                echo '<td>'.$record->Customer_Name.'</td>';
                                                echo '<td>'.$record->Customer_Address.'</td>';
                                                echo '<td>'.$record->State.'</td>';
                                                echo '<td>'.$record->Contact_No.'</td>';
                                                echo '<td>'.$record->Pincode.'</td>';
                                                echo '<td>'.$record->Product.'</td>';
                                                
                                            echo '</tr>';
                                        }
                                 ?>
                                  
                              </tbody>
                           </table>
                            
                            
                            <input type="hidden" name="whereTag" value="<?php echo $whereTag; ?>" >
                                </form>
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
