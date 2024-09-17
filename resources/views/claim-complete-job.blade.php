@extends('layouts.app')
@section('content')
<script src= "https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> 
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<!-- DataTables JavaScript -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<!-- TableExport JavaScript -->
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.1.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.1.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script>
    jQuery(document).ready(function($)
    {
        // Use $ for jQuery code here
        $('#table1').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Download Excel', 
                    className: 'btn btn-warning', 
                    
                   
                }
            ]
        });
        
    });
</script>
<script>
                                    

menu_select('{{$url}}');                                                             
</script>
<script>


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

function get_product_detail(div_id,brand_id)
{
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });
    jQuery.ajax({
        url: 'get-product-category-by-brand-id',
        method: 'post',
        data: {
            brand_id: brand_id,
            all:'1'
        },
        success: function(result){
            $('#product_category_id'+div_id).html(result)
        }
    });
}

function get_product(div_id,product_category_id)
{
    var brand_id = $('#brand_id'+div_id).val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });
    jQuery.ajax({
        url: 'get-product-by-brand-id',
        method: 'post',
        data: {
            brand_id: brand_id,
            product_category_id:product_category_id,
            all:'1'
        },
        success: function(result){
            $('#product_id'+div_id).html(result);
        }
    });
}
 
function get_model(div_id,product_id)
{
    var brand_id = $('#brand_id'+div_id).val();
    var product_category_id = $('#product_category_id'+div_id).val();
     
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });
    jQuery.ajax({
        url: 'get-model-by-product-id',
        method: 'post',
        data: {
            brand_id: brand_id,
            product_category_id:product_category_id,
            product_id: product_id,
            all:'1'
        },
        success: function(result){
            $('#model_id'+div_id).html(result);
        }
    });
}

function part_defective(tagId)
{
    $('#scc').html("");
    $('#err').html("");
    $.post('part-defective',{tagId:tagId}, function(resp)
    {
        const obj = JSON.parse(resp);
        if(obj.resp_id==='1')
        {
            $('#scc').html('<h5><font color="green">Part No '+obj.part_no+' Defective Added.</font></h5>');
            //$('#td'+tagId).html('<a onclick="job_reject('+"'"+tagId+"'"+');" href="#">Reject</a>');
            $('#td'+tagId).html("Accepted");
            //$('#tr'+tagId).remove();
        }
        else
        { 
            $('#err').html('<h5><font color="red">Part No Defective Already Add</font></h5>');
        }
        
    });
}

</script>

<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body"><h5 class="card-title">Complete Job</h5>
                            
                        @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                        @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                  
                        <form method="get" action="{{route('claim-complete-job')}}" class="form-horizontal">
                           
                            <div class="form-row">

                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">Ticket No.</label>
                                        <input class="form-control" type="text" id="ticket_no" name="ticket_no" value="<?php echo $ticket_no;?>" placeholder="Ticket No.">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">Job No.</label>
                                        <input class="form-control" type="text" id="job_no" name="job_no" value="<?php echo $job_no;?>" placeholder="Job No.">
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="exampleAddress" class="">Brand</label>
                                        <select id="brand_id1" name="brand" onchange="get_product_detail('1',this.value)" class="form-control">
                                            <option value="">Brand</option>
                                            <option value="All">All</option>
                                            <?php foreach($brand_arr as $brand) {?>       
                                                <option value="<?php echo $brand['brand_id']; ?>" <?php if($brand_id==$brand['brand_id']){ echo "selected"; } ?>><?php echo $brand['brand_name']; ?></option>     
                                            <?php  }?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="exampleAddress" class="">Product</label>
                                        <select id="product_category_id1" name="product_category" class="form-control">
                                            <option value="">Product</option>
                                            <option value="All" <?php echo ($product_category == 'All') ? 'selected' : ''; ?>>All</option>
                                            <?php foreach($category_master as $category) {?>       
                                                <option value="<?php echo $category->product_category_id; ?>" <?php if($product_category==$category->product_category_id){ echo "selected"; } ?>><?php echo $category->category_name; ?></option>     
                                            <?php  }?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Type Of Service</label>
                                        <select id="service_type" name="service_type" class="form-control"  >
                                            <option value="">Select</option>
                                            <option value="All">All</option>
                                            <option value="Demo & Installation" <?php if($service_type=='Demo & Installation') { echo 'selected';}?> >Demo & Installation</option>
                                            <option value="Online" <?php if($service_type=='Online') { echo 'selected';}?> >Online</option>
                                            <option value="Refurbished" <?php if($service_type=='Refurbished') { echo 'selected';}?> >Refurbished</option>
                                            <option value="Site Visit" <?php if($service_type=='Site Visit') { echo 'selected';}?> >Site Visit</option>
                                            <option value="Walk in" <?php if($service_type=='Walk in') { echo 'selected';}?> >Walk in</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">ASC Name</label>
                                            <select id="center_name" name="center_name" class="form-control">
                                            <option value="">Select</option>
                                            @foreach($asc_master as $asc)
                                                <option value="{{$asc->center_id}}" <?php if( $asc_code==$asc->center_id) { echo 'selected';} ?>>{{$asc->center_name}} - {{$asc->asc_code}}</option>
                                            @endforeach
                                            </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">Closure Code</label>
                                        <select id="closure_code" name="closure_code" class="form-control">
                                            <option value="">Select</option>
                                            
                                            @foreach($closure_master as $clm)
                                                <option value="{{$clm->id}}" <?php if( $closure_code==$clm->id) { echo 'selected';} ?>>{{$clm->closure_code}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Warranty Type</label>
                                        <select id="warranty_category" name="warranty_category" class="form-control" >
                                            <option value="">Select</option>
                                            <option value="Standard Warranty" <?php if($warranty_category=='Standard Warranty') { echo 'selected';}?> >Standard Warranty</option>
                                            <option value="Out Warranty" <?php if($warranty_category=='Out Warranty') { echo 'selected';}?>>Out Warranty</option>
                                            <option value="Extended" <?php if($warranty_category=='Extended') { echo 'selected';}?>>Extended</option>
                                            <option value="International" <?php if($warranty_category=='International') { echo 'selected';}?>>International</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Status </label>
                                        <select id="job_status" name="job_status" class="form-control"  >
                                            <option value="">Select</option>
                                            <option value="1" <?php if($claim_status=='1') { echo 'selected';}?>>Pending For Approval</option>
                                            <option value="2" <?php if($claim_status=='2') { echo 'selected';}?>>Correction</option>
                                            <option value="3" <?php if($claim_status=='3') { echo 'selected';}?>>Cancelled</option>
                                        </select>
                                        
                                    </div>
                                </div>
                            

                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="exampleEmail11" class="">From Date</label>
                                        <input name="from_date" autocomplete="off" id="from_date" placeholder="From" type="text" value="<?php echo $from_date; ?>" class="form-control datepicker">
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">To Date</label>
                                        <input name="to_date" autocomplete="off" id="to_date" placeholder="To" type="text" value="<?php echo $to_date; ?>" class="form-control datepicker">
                                    </div>
                                </div>
                                
 				                <div class="col-md-3">
                                    <label for="examplePassword11" class="">&nbsp;</label>
                                    <div class="position-relative form-group">  
                                        <input type="submit"  class="btn btn-primary" value="Search">
                                        &nbsp;<a href="{{route('home')}}" class="btn btn-danger">Exit</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                        
                            
                            <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <h5 class="card-title">Details</h5>
                                </div>
                                <div class="col-md-8"></div>
                                <div class="col-md-3">
                                    <!-- <a href="{{route('fresh-stock-download')}}" class="card-title btn btn-warning">Download</a> -->
                                    <!-- <a href="#" id="export-excel" class="card-title btn btn-warning">Download Excel</a> -->
                                </div>
                            </div>
                            
                            <table id="table1" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Sr No.</th>
                                        <th>Claim Generate Date</th>
                                        <th>Ticket No.</th>
                                        <th>Asc Name</th>
                                        <th>Asc Code</th>
                                        <th>JOb No.</th>
                                        <th>Brand</th>
                                        <th>Product</th>
                                        <th>Model</th>
                                        <th>Serial No.</th>
                                        <th>Closure Code</th>
                                        <th>Remarks</th>
                                        <th>Job Amount</th>
                                        <th>Accounts Remarks</th>
                                        <th>View Pdf</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                 
                                <?php   $srno = 1;
                                    foreach($req_arr as $req)
                                    {   
                                        echo '<tr>';
                                        echo '<td>'.$srno++.'</td>';
                                        echo '<td>'.(!empty($req->claim_gen_date) ? date('d-m-Y', strtotime($req->claim_gen_date)) : '').'</td>';
                                        echo '<td>'.$req->ticket_no.'</td>';
                                        echo '<td>'.$req->center_name.'</td>';
                                        echo '<td>'.$req->asc_code.'</td>';
                                        echo '<td>'.$req->job_no.'</td>';
                                        echo '<td>'.$req->brand_name.'</td>';
                                        echo '<td>'.$req->product_name.'</td>';
                                        echo '<td>'.$req->model_name.'</td>';
                                        echo '<td>'.$req->Serial_No.'</td>';
                                        echo '<td>'.$req->closure_name.'</td>';
                                        echo '<td>'.$req->remarks.'</td>';
                                        echo '<td>'.$req->closure_amount.'</td>';
                                        echo '<td>'.$req->claim_remarks.'</td>';
                                        #echo '<td>'.$req->closure_amount.'</td>';

                                        echo '<td><a href="view-generate-pdf?TagId='.$req->TagId.'" target="_blank">View </a>';
                                        echo '<a href="generate-pdf?TagId='.$req->TagId.'">PDF </a></td>';

                                        #echo '<td>'.date('d-m-Y',strtotime($req->created_at)).'</td>';
                                        // echo '<td>'.$req->part_no.'</td>';
                                        // echo '<td><a href="view-inw-inv-entry?inw_id='.base64_encode($req->inw_id).'">View</a></td>';
                                        echo '<td>';
                                        // o for approved ,2 for correction,3 for rejection
                                        if($req->claim_status == 1)
                                        {   
                                            echo 'Pending For Approval';
                                        }else if($req->claim_status == 2){
                                            echo 'Correction';
                                        }else if($req->claim_status == 3){

                                            echo 'Cancelled';
                                        }
                                        
                                        echo '</td>';
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

@endsection
