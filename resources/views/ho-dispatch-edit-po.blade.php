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
                    <span>Edit Dispatch</span>
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
                                <th>Invoice Number</th>
                                <!-- <th>Eway bill Number</th> -->
                                <th>ASC Name</th>
                                <th>Courier Name & Docket Number</th>
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
                                            echo '<td>'.$dispatch_det->invoice_no.'</td>';
                                            echo '<td>';?>
                                            <!-- <input type="text" autocomplete="off" maxlength="20" value="<?php //echo $dispatch_det->eway_bill_no; ?>" id="eway_bill_no" name="eway_bill_no"  /> -->
                                            <?php echo '</td>';
                                            echo '<td>'.$dispatch_det->asc_name.'</td>';
                                            echo '<td>';?>
                                                <input type="text" autocomplete="off" maxlength="20" value="<?php echo $dispatch_det->doc_no; ?>" id="doc_no" name="doc_no"  />
                                            <?php echo '</td>';
                                            echo '<td>';?>
                                                <input type="text" autocomplete="off" maxlength="20" value="<?php echo $dispatch_det->veh_doc_no; ?>" id="veh_doc_no" name="veh_doc_no"  />
                                            <?php echo '</td>';
                                            echo '<td>';?>
                                                <input type="text" autocomplete="off" maxlength="20" value="<?php echo $dispatch_det->dispatch_ref_no; ?>" id="dispatch_ref_no" name="dispatch_ref_no"  />
                                            <?php echo '</td>';
                                            
                                            echo '<td>';?>
                                            <input type="text" value="<?php echo $dispatch_det->no_of_cases; ?>" onkeypress="return checkNumber(this.value,event)" autocomplete="off" id="no_of_cases" name="no_of_cases"  />
                                            <?php echo '</td>';
                                            
                                            
                                            echo '<td>';?>
                                            <input autocomplete="off" value="<?php echo $dispatch_det->dispatch_comments; ?>" type="text" id="remarks" name="remarks"  />
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
                                    <a href="ho-dispatch-po" class="mt-2 btn btn-primary">Back</a>
                                     <button type="submit"  class="mt-2 btn btn-primary" >Update</button>
                                </div>
                            </div>
                        </div>  
                            
                            <input type="hidden" id="dispatch_id" name="dispatch_id" value="<?php echo $dispatch_det->dispatch_id; ?>">
                            
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
    
    function approve(part_id)
    {
        var eway_bill_no = $('#eway_bill_no'+part_id).val();
        var doc_no = $('#doc_no'+part_id).val();
        var veh_doc_no = $('#veh_doc_no'+part_id).val();
        var dispatch_ref_no = $('#dispatch_ref_no'+part_id).val();
        var no_of_cases = $('#no_of_cases'+part_id).val();
        var remarks = $('#remarks'+part_id).val();
        
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
                 invoice_id:part_id,
                 remarks:remarks,
                 doc_no:doc_no,
                 veh_doc_no:veh_doc_no,
                 dispatch_ref_no:dispatch_ref_no,
                 no_of_cases:no_of_cases
              },
              success: function(result){
                  if(result==='1')
                  {
                      $('#tr'+part_id).remove();
                      $('#succ').show();
                      $('#error').hide();
                  }
                  
                  else
                  {
                      $('#succ').hide();
                      $('#error').show();
                  }    
              }});
    }
    
    
    
 


    
    
 
 
 
</script>

@endsection
