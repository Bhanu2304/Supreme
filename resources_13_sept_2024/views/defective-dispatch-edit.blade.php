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
                <a role="tab" class="nav-link active" id="tab-0" data-toggle="tab" href="#tab-content-0">
                    <span>Edit Defective Dispatch</span>
                </a>
            </li>
            
            
        </ul>
     <div class="tab-content">
         <h5 id="succ" style="display:none;"><font color="green"> </font></h5> 
        <h5 id="error" style="display:none;"><font color="red"> </font></h5> 
         <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
             <div class="main-card mb-3 card">
                
                    <div class="card-body">                                        
                        <form action="update-dispatch">
                            <table border="1" style="font-size:13px;text-align:center;" id="table_id">
                            
                            <tr>
                                <th>#</th>
                                <th>Part Po Number</th>
                                <th>Eway bill Number</th>
                                <th>ASC Name</th>
                                <th>Courier Name & Docket Number</th>
                                <th>Transportion Charges</th>
                                <th>Transporter Name & Vehicle Number</th>
                                <th>By Hand - Person Name & Contact Number</th>
                                <th>Number of cases/boxes</th>
                                <th>Comments</th>
                                
                            </tr>
                            
                            <tbody>
                            <?php   $srno = 1;
                                    //foreach($invoice_master as $dispatch)
                                    //{
                                        echo '<tr>';
                                            echo '<td>'.$srno++.'</td>';
                                            echo '<td>'.$dispatch_det[0]->part_po_no.'</td>';
                                            echo '<td>';?>
                                            <input type="text" autocomplete="off" maxlength="20" value="<?php echo $dispatch_det[0]->eway_bill_no; ?>" id="eway_bill_no" name="eway_bill_no"  />
                                            <?php echo '</td>';
                                            echo '<td>'.$dispatch_det[0]->center_name.'</td>';
                                            echo '<td>';?>
                                                <input type="text" autocomplete="off" maxlength="20" value="<?php echo $dispatch_det[0]->doc_no; ?>" id="doc_no" name="doc_no"  />
                                            <?php echo '</td>';
                                             echo '<td>';?>
                                             <input type="text" autocomplete="off" maxlength="20" value="<?php echo $dispatch_det[0]->transportation_charge; ?>" id="transportation_charge" name="transportation_charge"  />
                                            <?php echo '</td>';
                                            echo '<td>';?>
                                                <input type="text" autocomplete="off" maxlength="20" value="<?php echo $dispatch_det[0]->veh_doc_no; ?>" id="veh_doc_no" name="veh_doc_no"  />
                                            <?php echo '</td>';
                                            echo '<td>';?>
                                                <input type="text" autocomplete="off" maxlength="20" value="<?php echo $dispatch_det[0]->dispatch_ref_no; ?>" id="dispatch_ref_no" name="dispatch_ref_no"  />
                                            <?php echo '</td>';
                                            
                                            echo '<td>';?>
                                            <input type="text" value="<?php echo $dispatch_det[0]->no_of_cases; ?>" onkeypress="return checkNumber(this.value,event)" autocomplete="off" id="no_of_cases" name="no_of_cases"  />
                                            <?php echo '</td>';
                                            
                                            
                                            echo '<td>';?>
                                            <input autocomplete="off" value="<?php echo $dispatch_det[0]->dispatch_comments; ?>" type="text" id="remarks" name="remarks"  />
                                            <?php echo '</td>';
                                            //echo '<td><a href="#" onclick="approve('."'{$dispatch_det->dispatch_id}'".')">Update</a></td>';
                                        echo '</tr>';
                                    //}
                            ?>
                            </tbody>
                        </table>   
                           <div class="form-row">
                            <div class="col-md-5">
                                <div class="position-relative form-group">
                                    <a href="defective-part-dispatch" class="mt-2 btn btn-primary">Back</a>
                                     <button type="button" onclick="approve()" class="mt-2 btn btn-primary" >Save</button>
                                </div>
                            </div>
                        </div>  
                            
                            <input type="hidden" id="dispatch_id" name="dispatch_id" value="<?php echo $dispatch_det[0]->dpart_id; ?>">
                            
                        </form>    
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
    
    function approve()
    {
        
        var dispatch_id = $('#dispatch_id').val();
        var eway_bill_no = $('#eway_bill_no').val();
        var doc_no = $('#doc_no').val();
        var transportation_charge = $('#transportation_charge').val();
        var veh_doc_no = $('#veh_doc_no').val();
        var dispatch_ref_no = $('#dispatch_ref_no').val();
        var no_of_cases = $('#no_of_cases').val();
        var remarks = $('#remarks').val();
        
        $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
        jQuery.ajax({
              url: 'save-defective-dispatch',
              method: 'post',
              data: {
                 eway_bill_no: eway_bill_no,
                 invoice_id:dispatch_id,
                 remarks:remarks,
                 doc_no:doc_no,
                 transportation_charge:transportation_charge,
                 veh_doc_no:veh_doc_no,
                 dispatch_ref_no:dispatch_ref_no,
                 no_of_cases:no_of_cases
              },
              success: function(result){
                  if(result==='1')
                  {
                    
                     alert('Dispatched Successfully');
                     window.history.back();
                     
                  }
                  
                  else
                  {
                    alert('Failed to Dispatch');
                     window.history.back();
                  }    
              }});
    }
    
    
    
 


    
    
 
 
 
</script>

@endsection
