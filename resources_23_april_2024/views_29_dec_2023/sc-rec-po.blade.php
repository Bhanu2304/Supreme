@extends('layouts.app')

@section('content') 

<script>
                                    

menu_select('{{$url}}');
</script>
<script>
function checkNumber(val,evt,part_id)
{
    var charCode = (evt.which) ? evt.which : event.keyCode;
	
	if (charCode> 31 && (charCode < 48 || charCode > 57) )
        {            
            return false;
        }
        if(val.length>6)
        {
            return false;
        }
        
        
        
	return true;
}

function validate_parts(part_id)
{
    var avail_parts = $('#dispatch_qty'+part_id).val();
    var avail_parts_int = parseInt(avail_parts);

    var noofparts = parseInt($('#miss_qty'+part_id).val());
    var no_of_part_int = parseInt(noofparts);

    if(avail_parts_int<no_of_part_int)
    {
        alert("No. of Parts shoud not more than Issued Qty.");
    }
}
</script>



<div class="app-main"> 
    <div class="app-main__outer">
        <div class="app-main__inner">
            <ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
                <li class="nav-item">
                    <a role="tab" class="nav-link active" id="tab-0" data-toggle="tab" href="#tab-content-0">
                        <span>Receive PO</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a role="tab" class="nav-link" id="tab-1" data-toggle="tab" href="#tab-content-1">
                        <span>View Received PO</span>
                    </a>
                </li>
            </ul>
            <div class="tab-content">

             <h5 id="succ" style="display:none;"><font color="green"> </font></h5> 
             <h5 id="error" style="display:none;"><font color="red"> </font></h5> 
             <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                <div class="main-card mb-3 card">

                        <div class="card-body">                                        
                            <h5 class="card-title">Received PO</h5>

                            <table border="1" style="font-size:13px;" id="table_id">

                                <tr>
                                    <th style="text-align:center;">#</th>
                                    <th style="text-align:center;">Invoice Number</th>
                                    <th style="text-align:center;">Eway bill Number</th>
                                    <th style="text-align:center;">ASC Name</th>
                                    <th style="text-align:center;">Courier Name & Docket Number</th>
                                    <th style="text-align:center;">Transporter Name & Vehicle Number</th>
                                    <th style="text-align:center;">By Hand - Person Name & Contact Number</th>
                                    <th style="text-align:center;">Number of cases/boxes</th>
                                    <th style="text-align:center;">Comments</th>
                                    <th style="text-align:center;">View</th>
                                    <th style="text-align:center;" colspan="2">Action</th>
    <!--                                <th style="text-align:center;">View</th>-->
                                </tr>

                                <tbody>
                                <?php   $srno = 1;
                                        foreach($dispatch_master as $dispatch)
                                        {
                                            echo '<tr '.'id="tr'.$dispatch->dispatch_id.'">';
                                                echo '<td>'.$srno++.'</td>';
                                                echo '<td style="text-align:center;">'.$dispatch->invoice_no.'</td>';
                                                echo '<td style="text-align:center;">'.$dispatch->eway_bill_no.'</td>';
                                                echo '<td style="text-align:center;">'.$dispatch->asc_name.'</td>';
                                                echo '<td style="text-align:center;">'.$dispatch->doc_no.'</td>';
                                                echo '<td style="text-align:center;">'.$dispatch->veh_doc_no.'</td>';
                                                echo '<td style="text-align:center;">'.$dispatch->dispatch_ref_no.'</td>';
                                                echo '<td style="text-align:center;">'.$dispatch->no_of_cases.'</td>';
                                                echo '<td style="text-align:center;">'.$dispatch->dispatch_comments.'</td>';
                                                echo '<td style="text-align:center;"><a href="view-dispatch-sc?dispatch_id='.$dispatch->dispatch_id.'">View</a></td>';
                                                echo '<td style="text-align:center;"><a href="#" onclick="save_inward('.$dispatch->dispatch_id.');">Accept</a></td>';
                                                echo '<td style="text-align:center;"><a href="#" class="trigger_popup_fricc" onclick="cancel_dispatch('.$dispatch->dispatch_id.')">Cancel</a></td>';
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
             <div class="tab-pane tabs-animation fade" id="tab-content-1" role="tabpanel">
                 <div class="main-card mb-3 card">

                        <div class="card-body">                                        
                            <h5 class="card-title">View Received PO's</h5>

                            <table border="1" style="font-size:13px;" id="table_id">

                                <tr>
                                    <th style="text-align:center;">#</th>
                                    <th style="text-align:center;">Invoice Number</th>
                                    <th style="text-align:center;">Eway bill Number</th>
                                    <th style="text-align:center;">ASC Name</th>
                                    <th style="text-align:center;">Courier Name & Docket Number</th>
                                    <th style="text-align:center;">Transporter Name & Vehicle Number</th>
                                    <th style="text-align:center;">By Hand - Person Name & Contact Number</th>
                                    <th style="text-align:center;">Number of cases/boxes</th>
                                    <th style="text-align:center;">Comments</th>
                                    <th style="text-align:center;">View</th>
                                    <th style="text-align:center;">Status</th>
                                    <th style="text-align:center;">SRN</th>
    
                                </tr>

                                <tbody>
                                <?php   $srno = 1;
                                        foreach($receive_master as $dispatch)
                                        {
                                            echo '<tr '.'id="tr'.$dispatch->dispatch_id.'">';
                                                echo '<td>'.$srno++.'</td>';
                                                echo '<td style="text-align:center;">'.$dispatch->invoice_no.'</td>';
                                                echo '<td style="text-align:center;">'.$dispatch->eway_bill_no.'</td>';
                                                echo '<td style="text-align:center;">'.$dispatch->asc_name.'</td>';
                                                echo '<td style="text-align:center;">'.$dispatch->doc_no.'</td>';
                                                echo '<td style="text-align:center;">'.$dispatch->veh_doc_no.'</td>';
                                                echo '<td style="text-align:center;">'.$dispatch->dispatch_ref_no.'</td>';
                                                echo '<td style="text-align:center;">'.$dispatch->no_of_cases.'</td>';
                                                echo '<td style="text-align:center;">'.$dispatch->dispatch_comments.'</td>';
                                                echo '<td style="text-align:center;"><a href="view-dispatch-sc?dispatch_id='.$dispatch->dispatch_id.'">View</a></td>';
                                                echo '<td style="text-align:center;">';
                                                if($dispatch->dispatch=='2')
                                                {
                                                    echo 'Approved';
                                                }
                                                else
                                                {
                                                    echo 'Cancelled';
                                                }
                                                        echo '</td>';
                                                echo '<td style="text-align:center;"><a href="#" class="trigger_popup_fricc" onclick="srn_dispatch('.$dispatch->dispatch_id.')">Apply</a></td>';
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

<style>
    /* Popup box BEGIN */
.hover_bkgr_fricc{
    background:rgba(0,0,0,.4);
    cursor:pointer;
    display:none;
    height:100%;
    position:fixed;
    text-align:center;
    top:0;
    width:100%;
    z-index:10000;
}
.hover_bkgr_fricc .helper{
    display:inline-block;
    height:100%;
    vertical-align:middle;
}
.hover_bkgr_fricc > div {
    background-color: #fff;
    box-shadow: 10px 10px 60px #555;
    display: inline-block;
    height: auto;
    max-width: 2000px;
    min-height: 400px;
    vertical-align: middle;
    width: 80%;
    position: relative;
    border-radius: 8px;
    padding: 15px 1%;
}
.popupCloseButton {
    background-color: #fff;
    border: 3px solid #999;
    border-radius: 50px;
    cursor: pointer;
    display: inline-block;
    font-family: arial;
    font-weight: bold;
    position: absolute;
    top: -20px;
    right: -20px;
    font-size: 25px;
    line-height: 30px;
    width: 30px;
    height: 30px;
    text-align: center;
}
.popupCloseButton:hover {
    background-color: #ccc;
}
.trigger_popup_fricc {
    cursor: pointer;
    font-size: 13px;
    margin: 10px;
    display: inline-block;
    font-weight: bold;
}
</style>

<div class="hover_bkgr_fricc">
    <span class="helper"></span>
    <div>
        <div class="popupCloseButton">&times;</div>
        
            <div id="dispatch_det">
                
            </div>
        
        
    </div>
</div>



<script>
    
    function save_inward(dispatch_id)
    {
        
        $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'sc-save-dispatch',
              method: 'post',
              data: {
                 dispatch_id: dispatch_id
              },
              success: function(result){
                  if(result==='1')
                  {
                      $('#tr'+dispatch_id).remove();
                      $('#succ').show();
                      $('#succ').html("Inventory Received and Inward Successfully.");
                      $('#error').hide();
                  }
                  else
                  {
                      $('#succ').hide();
                      $('#error').html("Inventory Received and Inward Failed.");
                      $('#error').show();
                  }    
              }});
    }
    
 function save_cancel_dispatch(id,dispatch_id)
 {
     
    //e.preventDefault(); // avoid to execute the actual submit of the form.
    $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    var form = $("#form"+id);
    var url = form.attr('action');
    
    jQuery.ajax({
           type: "POST",
           url: url,
           data: form.serialize(), // serializes the form's elements.
           success: function(data)
           {
               alert(data); // show response from the php script.
               cancel_dispatch(dispatch_id);
           }
         });    

    return false;
 }
 
 function cancel_dispatch(dispatch_id)
 {
    $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
     jQuery.ajax({
              url: 'get-sc-dispatch-det',
              method: 'post',
              data: {
                 dispatch_id: dispatch_id
              },
              success: function(dispatch_det){
                  $('#dispatch_det').html(dispatch_det);
                  $('.hover_bkgr_fricc').show();
              }});
 }
 
 
 
 
 function srn_dispatch(dispatch_id)
 {
    $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
     jQuery.ajax({
              url: 'get-sc-srn-det',
              method: 'post',
              data: {
                 dispatch_id: dispatch_id
              },
              success: function(dispatch_det){
                  $('#dispatch_det').html(dispatch_det);
                  $('.hover_bkgr_fricc').show();
              }});
 }
 
 function save_srn_dispatch(id,dispatch_id)
 {
     
    //e.preventDefault(); // avoid to execute the actual submit of the form.
    $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    var form = $("#form"+id);
    var formData = new FormData(form[0]);
    
    var url = form.attr('action');
    
    $.ajax({
           type: "POST",
           url: url,
           data: formData,            
           enctype: 'multipart/form-data',
           cache:false,
           processData: false,
           contentType: false,
           success: function(data)
           {
               alert(data); // show response from the php script.
               srn_dispatch(dispatch_id);
           }
         });    

    return false;
 }
 
 
 function view_cancel_det(dispatch_id)
 {
    $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
     jQuery.ajax({
              url: 'get-sc-cancel-dispatch-det',
              method: 'post',
              data: {
                 dispatch_id: dispatch_id
              },
              success: function(dispatch_det){
                  $('#dispatch_det').html(dispatch_det);
                  $('.hover_bkgr_fricc').show();
              }});
 }
 
 $(window).load(function () {
    $('.popupCloseButton').click(function(){
        $('.hover_bkgr_fricc').hide();
    });
});
</script>

@endsection
