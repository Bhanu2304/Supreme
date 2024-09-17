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
     <div class="tab-content">
        <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
            <div class="main-card mb-3 card">
                
                    <div class="card-body">                                        
                        <h5 class="card-title">View Center Inventory Request</h5>

                        <table class="table" id="table_id">
                            <thead>
                            <tr>
                                <th>Sr No.</th>
                                <th>Request No.</th>
                                <th>Center</th>
                                <th>Remarks</th>
                                <th>No. of Parts</th>
                                <th>Total Quantity</th>
                                <th>Total</th>
                                <th>Part Status</th>
                                
                            </tr>
                            </thead>
                            <tbody>
                            <?php   $srno = 1;
                                    foreach($req_arr as $req)
                                    {
                                        echo '<tr>';
                                            echo '<td>'.$srno++.'</td>';  
                                            echo '<td><a href="approve-req-inv?req_id='.base64_encode($req->req_id).'">'.$req->req_id.'</a></td>';
                                            echo '<td>'.$req->center_name.'</td>';
                                            echo '<td>'.$req->remarks.'</td>';
                                            echo '<td>'.$req->part_required.'</td>';
                                            echo '<td>'.$req->qty.'</td>';
                                            echo '<td>'.$req->total.'</td>';
                                            echo '<td>';
                                            if($req->part_status_pending=='1')
                                            {
                                                echo 'Pending';
                                            }
                                            else if($req->part_status_pending=='2')
                                            {
                                                echo 'Reject';
                                            }
                                            echo '</td>';
                                        echo '</tr>';
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



@endsection
