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
                    <span>Jobsheet Creation</span>
                </a>
                
            </li>
            <li class="nav-item">
            <a role="tab" class="nav-link" id="tab-1" data-toggle="tab" href="#tab-content-1">
                    <span>Jobsheet View</span>
                </a>
            </li>
        </ul>
     <div class="tab-content">
         @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
         @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
         <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
            <div class="main-card mb-3 card">
                <form id="job_apply_form" method="post" action="job-sheet-apply-save">
                    <div class="card-body">                                        
                        <h5 class="card-title">Jobsheet Creation</h5>
                        <table border="1" style="font-size: 13px;" id="table_id">
                            <tr>
                                <th>#</th>
                                <th>ASC Name</th>
                                <th>Job No.</th>
                                <th>Brand Name</th>
                                <th>Model No.</th>
                                <th>Serial No.</th>
                                <th>Warranty Type</th>
                                <th>Part Name</th>
                                <th>Part No.</th>
                                <th>Qty</th>
                                <th>Part Amount</th>
                                <th>Labour Amount</th>
                                <th>PO No.</th>
                                <th>Claim Type</th>
                                <th>Jobsheet Apply</th>
                            </tr>
                            
                            <tbody>
                            <?php   
                                if(!empty($job_sheet_arr))
                                {
                                    $srno = 1;
                                    foreach($job_sheet_arr as $po_job)
                                    {
                                        echo '<tr>';
                                            echo '<td align="center">'.$srno++.'</td>';
                                            echo '<td align="center">'.$po_job['asc_name'].'</td>';
                                            echo '<td align="center">'.$po_job['job_no'].'</td>';
                                            echo '<td align="center">'.$po_job['brand_name'].'</td>';
                                            echo '<td align="center">'.$po_job['model_name'].'</td>';
                                            
                                            echo '<td align="center">'.$po_job['Serial_No'].'</td>';
                                            echo '<td align="center">'.$po_job['warranty_type'].'</td>';
                                            echo '<td align="center">'.$po_job['part_name'].'</td>';
                                            echo '<td align="center">'.$po_job['part_no'].'</td>';
                                            
                                            echo '<td align="center">'.$po_job['qty'].'</td>';
                                            echo '<td align="center">'.$po_job['part_amount'].'</td>';
                                            echo '<td align="center">'.$po_job['labour_amt'].'</td>';
                                            echo '<td align="center">'.$po_job['po_no'].'</td>';
                                            echo '<td align="center">'.$po_job['claim_type'].'</td>';
                                            
                                            echo '<td align="center">';
                                            if($po_job['claim_type']=='Part')
                                            {
                                                
                                            }
                                            else
                                            {
                                                echo '<input type="checkbox" id="'.$po_job['job_id'].'" name="chk_job[]" value="'.$po_job['js_id'].'"></td>';
                                            }
                                            echo '</td>';
                                        echo '</tr>';
                                    }
                                }
                                else
                                {
                                    echo '<tr><td colspan="16">No Records Found.</td></tr>';
                                }
                            ?>
                            </tbody>
                        </table>
                        <br>
                        <table style="width:100%">
                            <tr>
                                <td id="disp_job_no">
                                    
                                </td>
                                <td>
                                    <div style="text-align: right;">
                                        <button type="submit" name="job_apply" id="job_apply" value="job_apply" class="btn btn-primary">Apply for Jobsheet Settlement</button>
                                        <button type="button" onclick="show_reason()" class="btn btn-primary trigger_popup_fricc">Apply for special Approval <br/>(Jobsheet misplaced or XYZ Reason) </button>
                                    </div>    
                                </td>
                            </tr>
                        </table>
                        
                        
                              
                    </div>     
                     
                </form>
            </div>
         </div>
        
        <div class="tab-pane tabs-animation fade " id="tab-content-1" role="tabpanel">
            <div class="main-card mb-3 card">
                        <table border="1" style="font-size: 13px;" id="table_id">
                            <tr>
                                <th>#</th>
                                <th>ASC Name</th>
                                <th>Job No.</th>
                                <th>Brand Name</th>
                                <th>Model No.</th>
                                <th>Serial No.</th>
                                <th>Warranty Type</th>
                                <th>Part Name</th>
                                <th>Part No.</th>
                                <th>Qty</th>
                                <th>Part Amount</th>
                                <th>Labour Amount</th>
                                <th>PO No.</th>
                                <th>Claim Type</th>
                                <th>Jobsheet Status</th>
                            </tr>
                            
                            <tbody>
                            <?php   
                                if(!empty($jobsheet_status_arr))
                                {
                                    $srno = 1;
                                    foreach($jobsheet_status_arr as $po_job)
                                    {
                                        echo '<tr>';
                                            echo '<td align="center">'.$srno++.'</td>';
                                            echo '<td align="center">'.$po_job->asc_name.'</td>';
                                            echo '<td align="center">'.$po_job->job_no.'</td>';
                                            echo '<td align="center">'.$po_job->brand_name.'</td>';
                                            echo '<td align="center">'.$po_job->model_name.'</td>';
                                            
                                            echo '<td align="center">'.$po_job->serial_no.'</td>';
                                            echo '<td align="center">'.$po_job->warranty_type.'</td>';
                                            echo '<td align="center">'.$po_job->part_name.'</td>';
                                            echo '<td align="center">'.$po_job->part_no.'</td>';
                                            
                                            echo '<td align="center">'.$po_job->qty.'</td>';
                                            echo '<td align="center">'.$po_job->part_amt.'</td>';
                                            echo '<td align="center">'.$po_job->labour_amt.'</td>';
                                            echo '<td align="center">'.$po_job->po_no.'</td>';
                                            echo '<td align="center">'.$po_job->claim_type.'</td>';
                                            
                                            echo '<td align="center">';
                                            if($po_job->job_apply=='1' && $po_job->special_approval=='1')
                                            {
                                                echo 'Pending For Special Approval';
                                            }
                                            else if($po_job->job_apply=='1')
                                            {
                                                echo 'Pending For Approval';
                                            }
                                            else if($po_job->job_apply=='2' && $po_job->special_approval=='1')
                                            {
                                                echo 'Rejected';
                                            }
                                            echo '</td>';
                                        echo '</tr>';
                                    }
                                }
                                else
                                {
                                    echo '<tr><td colspan="16">No Records Found.</td></tr>';
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
    width: 60%;
    position: relative;
    border-radius: 8px;
    padding: 15px 5%;
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
                <div class="form-row">
                    <div class="col-md-4">
                        <div class="position-relative form-group"><label for="examplePassword11" class="">Reason<span style="color: #f00;">*</span></label>
                            <select form="job_apply_form" name="reason" id="reason" class="form-control">
                                <option value="Reason1">Reason1</option>
                                <option value="Reason2">Reason2</option>
                                <option value="Reason3">Reason3</option>
                                <option value="Reason4">Reason4</option>
                            </select>
                        </div>
                    </div> 
                    
                    <div class="col-md-6">
                        <div class="position-relative form-group"><label for="examplePassword11" class="">Remarks<span style="color: #f00;">*</span></label>
                            <textarea form="job_apply_form" name="remarks" id="remarks" class="form-control" placeholder="Remarks"></textarea>
                        </div>
                    </div>  
                    <div class="col-md-2">
                        <div class="position-relative form-group">
                            <br><br>
                            <button form="job_apply_form" name="job_apply" id="special_apply" value="special_approval"  class="mt-2 btn btn-success">Special Approval</button>
                        </div>
                    </div>
                </div>
            </div>
        
        
    </div>
</div>


<script>
    
    function show_reason()
    {
        $('.hover_bkgr_fricc').show();
    }
    
 $(window).load(function () {
    $('.popupCloseButton').click(function(){
        $('.hover_bkgr_fricc').hide();
    });
});
</script>

@endsection
