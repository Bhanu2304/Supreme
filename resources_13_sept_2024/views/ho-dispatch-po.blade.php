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
            <li class="nav-item">
                <a role="tab" class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-content-1">
                    <span>View Dispatch</span>
                </a>
            </li>
            <li class="nav-item">
                <a role="tab" class="nav-link " id="tab-0" data-toggle="tab" href="#tab-content-0">
                    <span>Dispatch</span>
                </a>
            </li>
            
            
        </ul>
     <div class="tab-content">
         
         <h5 id="succ" style="display:none;"><font color="green"> </font></h5> 
            <h5 id="error" style="display:none;"><font color="red"> </font></h5> 
         <div class="tab-pane tabs-animation fade" id="tab-content-0" role="tabpanel">
             <div class="main-card mb-3 card">
                
                    <div class="card-body">                                        
                        <h5 class="card-title">Create Dispatch</h5>
                        
                        <table border="1" style="font-size:13px;text-align:center;" id="table_id">
                            
                            <tr>
                                <th>#</th>
                                <th>Invoice Number</th>
                                <th>Eway bill Number</th>
                                <th>ASC Name</th>
                                <th>Courier Name & Docket Number</th>
                                <th>Transporter Name & Vehicle Number</th>
                                <th>Transportion Charges</th>
                                <th>By Hand - Person Name & Contact Number</th>
                                <th>Number of cases/boxes</th>
                                <th>Comments</th>
                                <th colspan="2">Action</th>
                            </tr>
                            
                            <tbody>
                            <?php   $srno = 1;
                                    foreach($invoice_master as $dispatch)
                                    {
                                        echo '<tr id="tr'.$dispatch->dispatch_id.'">';
                                            echo '<td>'.$srno++.'</td>';
                                            echo '<td>'.$dispatch->invoice_no.'</td>';
                                            echo '<td>';?>
                                                <input type="text" autocomplete="off" maxlength="20" id="eway_bill_no<?php echo $dispatch->dispatch_id; ?>" name="eway_bill_no<?php echo $dispatch->dispatch_id; ?>"  />
                                            <?php echo '</td>';
                                            echo '<td>'.$dispatch->asc_name.'</td>';
                                            echo '<td>';?>
                                                <input type="text" autocomplete="off" maxlength="20" id="doc_no<?php echo $dispatch->dispatch_id; ?>" name="doc_no<?php echo $dispatch->dispatch_id; ?>"  />
                                            <?php echo '</td>';
                                            echo '<td>';?>
                                                <input type="text" autocomplete="off" maxlength="20" id="veh_doc_no<?php echo $dispatch->dispatch_id; ?>" name="veh_doc_no<?php echo $dispatch->dispatch_id; ?>"  />
                                            <?php echo '</td>';
                                            echo '<td>';?>
                                            <input type="text" autocomplete="off" maxlength="20" id="transportation_charge<?php echo $dispatch->dispatch_id; ?>" name="transportation_charge<?php echo $dispatch->dispatch_id; ?>"  />
                                        <?php echo '</td>';
                                            echo '<td>';?>
                                                <input type="text" autocomplete="off" maxlength="20" id="dispatch_ref_no<?php echo $dispatch->dispatch_id; ?>" name="dispatch_ref_no<?php echo $dispatch->dispatch_id; ?>"  />
                                            <?php echo '</td>';
                                            
                                            echo '<td>';?>
                                            <input type="text" onkeypress="return checkNumber(this.value,event)" autocomplete="off" id="no_of_cases<?php echo $dispatch->dispatch_id; ?>" name="no_of_cases<?php echo $dispatch->dispatch_id; ?>"  />
                                            <?php echo '</td>';
                                            
                                            
                                            echo '<td>';?>
                                            <input autocomplete="off" type="text" id="remarks<?php echo $dispatch->dispatch_id; ?>" name="remarks<?php echo $dispatch->dispatch_id; ?>"  />
                                            <?php echo '</td>';
                                            echo '<td><a href="#" onclick="approve('."'{$dispatch->dispatch_id}'".')">Dispatch</a></td>';
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
        
         <div class="tab-pane tabs-animation fade show active" id="tab-content-1" role="tabpanel">
             <div class="main-card mb-3 card">

                    <div class="card-body"><h5 class="card-title">View Dispatch</h5>
                        <form method="get" action="{{route('ho-dispatch-po')}}" class="form-horizontal">
                            
                            <div class="form-row">
                                
                                <!-- <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Challan No.</label>
                                        <input type="text" name="challan_no" id="challan_no" class="form-control" value="<?php //echo $challan_no;?>"  placeholder="Challan No.">
                                    </div>
                                </div> -->
      
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

                        <table class="table table-striped table-bordered" style="font-size:13px;" id="table_id">
                            
                            <tr>
                                <th style="text-align:center;">#</th>
                                <th style="text-align:center;">Invoice Number</th>
                                <th style="text-align:center;">PO Request Date</th>
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
                                <th style="text-align:center;">ASC Amount</th>
                                <th style="text-align:center;">Customer Amount</th>
                                <th style="text-align:center;">Comments</th>
                                <th style="text-align:center;">Discount</th>
                                <th style="text-align:center;">Remarks</th>
                                
                                <th style="text-align:center;">View</th>
                                <th style="text-align:center;">Edit</th>

                            </tr>
                            
                            <tbody>
                            <?php   $srno = 1;
                                    foreach($dispatch_master as $dispatch)
                                    {
                                        echo '<tr>';
                                            echo '<td>'.$srno++.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->invoice_no.'</td>';
                                            echo '<td style="text-align:center;">'.date('d-m-y',strtotime($dispatch->po_date)).'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->po_no.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->part_name.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->eway_bill_no.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->asc_name.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->job_no.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->doc_no.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->veh_doc_no.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->transportation_charge.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->dispatch_ref_no.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->no_of_cases.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->asc_amount.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->customer_amount.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->dispatch_comments.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->discount.'</td>';
                                            echo '<td style="text-align:center;">'.$dispatch->remarks.'</td>';
                                            //echo '<td>Pending</td>';
                                            echo '<td style="text-align:center;"><a href="view-dispatch?dispatch_id='.$dispatch->dispatch_id.'">View</td>';
                                            echo '<td style="text-align:center;"><a href="edit-dispatch?dispatch_id='.$dispatch->dispatch_id.'">Edit</td>';
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

<script>
    
    function approve(part_id)
    {
        var eway_bill_no = $('#eway_bill_no'+part_id).val();
        var doc_no = $('#doc_no'+part_id).val();
        var veh_doc_no = $('#veh_doc_no'+part_id).val();
        var dispatch_ref_no = $('#dispatch_ref_no'+part_id).val();
        var transportation_charges = $('#transportation_charge'+part_id).val();
        var no_of_cases = $('#no_of_cases'+part_id).val();
        var remarks = $('#remarks'+part_id).val();
        
        if(eway_bill_no==='')
        {
            alert('Please Fill Eway Bill No.');
            $('#eway_bill_no').focus();
            return false;
        }
        else if(doc_no==='')
        {
            alert('Please Fill Courier Name & Docket Number');
            $('#doc_no').focus();
            return false;
        }
        else if(veh_doc_no==='')
        {
            alert('Please Fill Transporter Name & Vehicle Number.');
            $('#veh_doc_no').focus();
            return false;
        }
        else if(transportation_charges==='')
        {
            alert('Please Fill Transportation Charges.');
            $('#transportation_charges').focus();
            return false;
        }
        else if(dispatch_ref_no==='')
        {
            alert('Please Fill By Hand - Person Name & Contact Number.');
            $('#dispatch_ref_no').focus();
            return false;
        }
        else if(no_of_cases==='')
        {
            alert('Please Fill Number of cases/boxes');
            $('#no_of_cases').focus();
            return false;
        }
        else if(remarks==='')
        {
            alert('Please Fill Comments');
            $('#remarks').focus();
            return false;
        }
        $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'save-dispatch',
              method: 'post',
              data: {
                 eway_bill_no: eway_bill_no,
                 dispatch_id:part_id,
                 remarks:remarks,
                 doc_no:doc_no,
                 veh_doc_no:veh_doc_no,
                 transportation_charges:transportation_charges,
                 dispatch_ref_no:dispatch_ref_no,
                 no_of_cases:no_of_cases
              },
              success: function(result){
                  if(result==='1')
                  {
                      $('#tr'+part_id).remove();
                      $('#succ').show();
                      $('#succ').html("Inventory Dispatched Successfully.");
                      $('#error').hide();
                  }
                  
                  else
                  {
                      $('#succ').hide();
                      $('#error').html("Inventory Dispatched Failed.");
                      $('#error').show();
                  }    
              }});
    }
    
 
 function del_part(del_div)
 {
     $('#'+del_div).remove();
 }
 
 
</script>

@endsection
