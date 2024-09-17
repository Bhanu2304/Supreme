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
                        <div class="card-body"><h5 class="card-title">Apply Jobs</h5>
                            
                        @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                        @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                  
                        <form method="get" action="{{route('account-statement')}}" class="form-horizontal">
                           
                            <div class="form-row">

                                
                                
                                <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="exampleAddress" class="">Brand</label>
                                        <select id="brand_id1" name="brand" onchange="get_product_detail('1',this.value)" class="form-control" >
                                            <option value="">Brand</option>
                                            <?php foreach($brand_arr as $brand) {?>       
                                                <option value="<?php echo $brand['brand_id']; ?>" <?php if($brand_id==$brand['brand_id']){ echo "selected"; } ?>><?php echo $brand['brand_name']; ?></option>     
                                            <?php  }?>
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
                                
                                <table id="table1" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Brand</th>
                                            <th>Amount Dispersed</th>
                                            <th>Dispersed on (Date)</th>
                                            <th>Transaction Id</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    
                                        <?php   $srno = 1;
                                            foreach($req_arr as $req)
                                            {   
                                                echo '<tr>';
                                                echo '<td>'.$srno++.'</td>';
                                                echo '<td>'.$req->Brand.'</td>';
                                                echo '<td>'.$req->disperse_amount.'</td>';
                                                echo '<td>'.(!empty($req->disperse_date) ? date('d-m-Y', strtotime($req->disperse_date)) : '').'</td>';
                                                echo '<td>'.$req->transaction_id.'</td>';
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
