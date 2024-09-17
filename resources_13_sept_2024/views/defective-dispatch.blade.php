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
        <ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
            <!-- <li class="nav-item">
                <a role="tab" class="nav-link active" id="tab-0" data-toggle="tab" href="#tab-content-0">
                    <span>Dispatch</span>
                </a>
            </li> -->
            <li class="nav-item">
                <a role="tab" class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-content-1">
                    <span>View Defective Part Dispatch</span>
                </a>
            </li>
            
        </ul>
     <div class="tab-content">
         
            <h5 id="succ" style="display:none;"><font color="green"> </font></h5> 
            <h5 id="error" style="display:none;"><font color="red"> </font></h5> 
            
        
         <div class="tab-pane tabs-animation fade show active" id="tab-content-1" role="tabpanel">
             <div class="main-card mb-3 card">

                    <div class="card-body"><h5 class="card-title">View Defective Part Dispatch</h5>
                        <form method="get" action="{{route('defective-part-dispatch')}}" class="form-horizontal">
                            
                            <div class="form-row">
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Job Number</label>
                                        <input type="text" name="job_no" id="job_no" class="form-control" value="<?php echo $job_no;?>"  placeholder="Job Number">
                                    </div>
                                </div>

                                <!-- <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Po Status</label>
                                        <select  id="po_status" name="po_status" class="form-control">
                                            <option value="">Select</option>
                                            <option value="Inwarded" <?php if($po_status=="Inwarded"){ echo "selected"; } ?> >Inwarded</option>
                                            <option value="Pending" <?php if($po_status=="Pending"){ echo "selected"; } ?>>Pending</option>
                                            <option value="Cancelled" <?php if($po_status=="Cancelled"){ echo "selected"; } ?>>Cancelled</option>
                                        </select>
                                    </div>
                                </div> -->


                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>From Date</label>
                                        <input name="from_date" autocomplete="off" id="from_date" placeholder="From" type="text" value="<?php echo $from_date; ?>" class="form-control datepicker">
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>To Date</label>
                                        <input name="to_date" autocomplete="off" id="to_date" placeholder="To" type="text" value="<?php echo $to_date; ?>" class="form-control datepicker">
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                    <div class="position-relative form-group">
                                        <input type="submit" class="btn btn-primary" value="Search">
                                        &nbsp;<a href="{{route('home')}}" class="btn btn-danger">Exit</a>
                                        <!-- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input role="tab" type="submit" value="Raise Po Request" class="btn btn-primary" id="tab-1" data-toggle="tab" href="#tab-content-1"> -->
                                    </div>
                                    
                                </div>
                            </div>
                        </form>
                    </div>
                
                    <div class="card-body">                                        
                        <h5 class="card-title">View Dispatch</h5>

                        <table border="1" style="font-size:13px;" id="table_id">
                            
                            <tr>
                                <th style="text-align:center;">#</th>
                                <th style="text-align:center;">Create Date</th>
                                <th style="text-align:center;">Part PO Number</th>
                                <th style="text-align:center;">Part Name</th>
                                <th style="text-align:center;">Eway bill Number</th>
                                <th style="text-align:center;">ASC Name</th>
                                <th style="text-align:center;">Job No.</th>
                                <th style="text-align:center;">Courier Name & Docket Number</th>
                                <th style="text-align:center;">Transporter Name & Vehicle Number</th>
                                <th style="text-align:center;">Transportation Charges</th>
                                <th style="text-align:center;">By Hand - Person Name & Contact Number</th>
                                <th style="text-align:center;">Number of cases/boxes</th>
                                <th style="text-align:center;">Customer Amount</th>
                                <th style="text-align:center;">Comments</th>
                                <th style="text-align:center;">Remarks</th>
                                <th style="text-align:center;">Action</th>

                            </tr>
                            
                            <tbody>
                            <?php   $srno = 1;
                                    foreach($dispatch_master as $dispatch)
                                    {
                                        #print_r($dispatch);
                                        echo '<tr>';
                                            echo '<td>'.$srno++.'</td>';
                                            echo '<td style="text-align:center;">'.date('d-m-y',strtotime($dispatch->part_po_date)).'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->part_po_no.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->part_name.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->eway_bill_no.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->center_name.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->job_no.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->doc_no.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->veh_doc_no.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->transportation_charge.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->dispatch_ref_no.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->no_of_cases.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->customer_price.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->dispatch_comments.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->remarks.'</td>';
                                            //echo '<td>Pending</td>';
                                            #echo '<td style="text-align:center;"><a href="view-dispatch?dispatch_id='.$dispatch->dpart_id.'">View</td>';
                                            echo '<td style="text-align:center;">';
                                            if($dispatch->request_to_ho == 0)
                                            {
                                                if(strtolower($UserType)!=strtolower('Admin'))
                                                {
                                                    echo '<a href="edit-defective-dispatch?dispatch_id='.$dispatch->dpart_id.'">Send';
                                                }else{
                                                    echo "Pending";
                                                }
                                                
                                            }else{
                                                echo "Dispatched";
                                            }
                                            
                                            
                                            echo '</td>';
                                            //echo '<td style="text-align:center;">View</td>';
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
