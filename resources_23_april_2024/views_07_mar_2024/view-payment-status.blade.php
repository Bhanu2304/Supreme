@extends('layouts.app')
@section('content')

<script>

menu_select('{{$url}}');
function reloadPage(){
    location.reload(true);
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
                        <div class="card-body"><h5 class="card-title">Payment View</h5>
                            
                                @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                 
                                 @if(Session::has('st')) {{ Session::get('st') }} @endif
                                  
                        <form method="get" action="{{route('view-invoice')}}" class="form-horizontal"  >
                           
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
                                
                                <div class="col-md-1">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">From Date</label>
                                            <input name="from_date" id="from_date" placeholder="From" type="text" value="<?php echo $from_date; ?>" class="form-control datepicker" >
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">To Date</label>
                                            <input name="to_date" id="to_date" placeholder="To" type="text" value="<?php echo $to_date; ?>" class="form-control datepicker" >
                                        </div>
                                    </div>
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">Contact No. / PinCode</label>
                                        <input class="form-control" type="text" id="contact_no" name="contact_no" value="<?php echo $contact_no;?>" data-original-title="Mobile No." data-placement="top" placeholder="Mobile No. / Pincode">
                                    </div>
                                </div>
                                
 				<div class="col-md-8">
                                        <div class="position-relative form-group">
                                                                           
                                            <input type="submit"  class="btn btn-success btn-grad" data-original-title="" title="" value="Search" >
                                &nbsp;<a href="{{route('home')}}" class="btn btn-danger btn-grad" data-original-title="" title="">Exit</a>
                                        </div>
                                </div>



                                 </div>


                        </form>
                    </div>
                        
                            
                            <div class="card-body">
                                
                                
                                
                                   
                                
                                
                            <h5 class="card-title">Invoice Details</h5>
                            <table id="table_id" class="table table-striped table-bordered" style="width:100%">
                              <thead>
                                 <tr>
                                    <th>Sr.</th>
                                    <th>Job No.</th>
                                    <th>Action </th>
                                    <th>Total Invoice </th>
                                    <th>SGST </th>
                                    <th>CGST </th>
                                    <th>IGST </th>
                                    <th>Total Payable </th>
                                    <th>Name</th>
                                    <th>Mobile No.</th>
                                    <th>Product</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 
                                 <?php $srno = 1;
                                        foreach($DataArr as $record)
                                        {
                                            echo '<tr>';
                                            echo '<td>';
                                            
                                            echo $srno++.'</td>';
                                                echo '<td>'.$record->TagId.'</td>';
                                                echo '<td>';
                                                
                                                    echo '<a href="update-payment?TagId='.$record->TagId."&whereTag=$whereTag".'">Update Payment</a>';
                                                
                                                echo '</td>';
                                                
                                                echo '<td>'.$record->total_taxable_value.'</td>';
                                                echo '<td>'.$record->total_cgst.'</td>';
                                                echo '<td>'.$record->total_sgst.'</td>';
                                                echo '<td>'.$record->total_igst.'</td>';
                                                echo '<td>'.$record->total_payable.'</td>';
                                                echo '<td>'.$record->Customer_Name.'</td>';
                                                echo '<td>'.$record->Contact_No.'</td>';
                                                echo '<td>'.$record->Product.'</td>';
                                                
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
<script>
    $('#table_id').DataTable( );
    </script>
@endsection
