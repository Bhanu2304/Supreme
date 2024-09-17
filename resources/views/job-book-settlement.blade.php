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
    jQuery(document).ready(function($) {
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

function part_defective(tagId,type)
{
    if(type == "Approved")
    {
        $('#scc').html("");
        $('#err').html("");
        $.post('claim-approval',{tagId:tagId}, function(resp)
        {
            //const obj = JSON.parse(resp);
            if(resp==='1')
            {
                alert("Claim is Approved");
                window.location.reload();
            }
            else
            { 
                alert("Please try Again!");
                window.location.reload();
            }
            
        });
    }else{
        
        $("#reject_tag_id").val(tagId);
        $("#reject_type").val(type);
        $('.hover_bkgr_fricc').show();
    }
    
}

function reject_claim()
{
    var reject_tag_id = $('#reject_tag_id').val();
    var reject_type = $('#reject_type').val();
    var remarks = $('#remarks').val();

    $('#scc').html("");
    $('#err').html("");
    $.post('claim-reject',{tag_id:reject_tag_id,reject_type:reject_type,remarks:remarks}, function(resp)
    {
        alert(resp);
        window.location.reload();
        
    });
    
}

function apply_jobs()
{
    var selected = [];
    $('input[name="apply[]"]:checked').each(function() {
        selected.push($(this).val());
    });

    //console.log(selected);
    
    if (selected.length === 0) {
        alert('Please select at least one.');
        return false;
    }

    $.ajax({
        url: 'save-claim-jobs',
        type: 'POST',
        data: {apply: selected},
        success: function(response) {
            alert('Update successful');
            window.location.reload();
        },
        error: function(xhr, status, error) {
            alert('An error occurred: ' + xhr.responseText);
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
                        <div class="card-body">
                            <h5 class="card-title">Job Book Settlement</h5>
                            
                            @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                            @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                            <form method="get" action="{{route('job-book-settlement')}}" class="form-horizontal">
                                <div class="form-row">
                                    <div class="col-md-3">
                                        <div class="position-relative form-group">
                                            <label>ASC Name</label>
                                            <select id="center_id" name="center_id" class="form-control" >
                                                <option value="">Select</option>
                                                <option value="All" <?php if($center_id == 'All') { echo "selected"; } ?>>All</option>
                                                <?php foreach($asc_master as $asc) { ?>       
                                                    <option value="<?php echo $asc->center_id; ?>" <?php if($center_id==$asc->center_id){ echo "selected"; } ?>><?php echo $asc->center_name.' - '.$asc->asc_code.' - '.$asc->city; ?></option>
                                                <?php  }?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2"></div>
                                    
                                    <div class="col-md-3">
                                        <label>&nbsp;</label>
                                        <div class="position-relative form-group">  
                                            <input type="submit"  class="btn btn-primary" value="Search">
                                            &nbsp;<a href="{{route('home')}}" class="btn btn-danger">Exit</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row">
                                <div class="col-md-10"></div>
                                <div class="col-md-2">
                                    <a href="{{route('issue-job-book')}}" class="btn btn-warning">Issue Job Sheet</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            
                            <table id="table1" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Sr No.</th>
                                        <th>Brand</th>
                                        <th>Zone</th>
                                        <th>State</th>
                                        <th>Asc Name</th>
                                        <th>Asc Code</th>
                                        <th>Last Job Book Sent On</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php   $srno = 1;
                                        foreach($req_arr as $req)
                                        {   #print_r($req);
                                            echo '<tr>';
                                            echo '<td>'.$srno++.'</td>';
                                            echo '<td>'.$req->brand_name.'</td>';
                                            echo '<td>'.$req->region_name.'</td>';
                                            echo '<td>'.$req->state_name.'</td>';
                                            echo '<td>' . $req->center_name . ' - ' . $req->city . '</td>';
                                            echo '<td>'.$req->asc_code.'</td>';
                                            echo '<td>'.date('d-m-Y',strtotime($req->created_at)).'</td>';
                                            echo '<td>'.$req->remarks.'</td>';
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


</script>

@endsection
