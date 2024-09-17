@extends('layouts.app')
@section('content')

<script>

menu_select('{{$url}}');
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
                        <div class="card-body"><h5 class="card-title">Challan Search</h5>
                            
                                @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                 
                                
                                  
                        <form method="get" action="{{route('search-challan')}}" class="form-horizontal"  >
                           
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
                                            <input name="from_date" id="from_date" placeholder="From" type="text" value="<?php echo $from_date; ?>" class="form-control datepicker" >
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">To Date</label>
                                            <input name="to_date" id="to_date" placeholder="To" type="text" value="<?php echo $to_date; ?>" class="form-control datepicker" >
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">ASC Code</label>
                                            <input class="form-control" type="text" id="asc_code" name="asc_code" value="<?php echo $asc_code;?>" data-original-title="Mobile No." data-placement="top" placeholder="ASC Code">
                                        </div>
                                    </div>
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class=""> Challan no.</label>
                                        <input class="form-control" type="text" id="challan_no" name="challan_no" value="<?php echo $challan_no;?>" data-original-title="Mobile No." data-placement="top" placeholder="Challan No.">
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
                                
                               
                                
                                
                                
                                
                            <h5 class="card-title">Challan Details</h5>
                            <table id="table1" class="table table-striped table-bordered" style="width:100%">
                              <thead>
                                 <tr>
                                    <th>Sr.</th>
                                    <th>Challan No.</th>
                                    <th>Print</th>
                                    <th>Center</th>
                                    <th>State</th>
                                    <th>Pin Code</th>
                                    <th>Parts</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                    <th>Tax</th>
                                    <th>Net Total</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 
                                 <?php $srno = 1;
                                        foreach($DataArr as $record)
                                        {
                                            echo '<tr>';
                                                echo '<td>';
                                                //echo '<input type="checkbox" name="case[]" value="'.$record->TagId.'" />';
                                                echo $srno++.'</td>';
                                                echo '<td>';
                                                //echo '<a href="tax-invoice?TagId='.$record->TagId.'">'.$record->TagId.'</a>&nbsp;';
                                                echo $record->challan_no;
                                                echo '</td>';
                                                echo '<td>';
                                                echo '<a href="generate-Challan?approve_id='.$record->approve_id.'">View </a></td>';
                                                echo '<td>'.$record->center_name.'</td>';
                                                echo '<td>'.$record->state.'</td>';
                                                echo '<td>'.$record->pincode.'</td>';
                                                echo '<td>'.$record->part_required.'</td>';
                                                echo '<td>'.$record->qty.'</td>';
                                                echo '<td>'.$record->total.'</td>';
                                                echo '<td>'.$record->total_tax.'</td>';
                                                echo '<td>'.$record->net_total.'</td>';
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
