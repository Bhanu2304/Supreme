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
            $('#product_category_id'+div_id).html(result);
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

function canbalize(return_id)
{
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });
    jQuery.ajax({
        url: 'get-canbalize',
        method: 'post',
        data: {
        return_id: return_id
        },
        success: function(dispatch_det){
            $('#details_part').html(dispatch_det);
            $('.hover_bkgr_fricc').show();
        }
    });
}


function save_canbalized_part(return_id)
{
     
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });
    
    jQuery.ajax({
        type: "POST",
        url: 'canbalize-save',
        data: {return_id: return_id},
        success: function(data)
        {
            alert(data);
            canbalize(return_id);
        }
    });

    return false;
}


function scrap(return_id)
{
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });
    jQuery.ajax({
        url: 'get-scrap',
        method: 'post',
        data: {
        return_id: return_id
        },
        success: function(dispatch_det){
            $('#details_part').html(dispatch_det);
            $('.hover_bkgr_fricc').show();
        }
    });
}


function save_scrap_part(return_id)
{
     
    var scrap_date = $('#scrap_date').val();
    if(scrap_date == "")
    {
        alert('Please Select Scrap Date');
        return false;
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });
    
    jQuery.ajax({
        type: "POST",
        url: 'scrap-save',
        data: {return_id: return_id,scrap_date:scrap_date},
        success: function(data)
        {
            alert(data);
            scrap(return_id);
        }
    });

    return false;
}


function def_return(return_id)
{
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });
    jQuery.ajax({
        url: 'get-def-return',
        method: 'post',
        data: {
        return_id: return_id
        },
        success: function(dispatch_det){
            $('#details_part').html(dispatch_det);
            $('.hover_bkgr_fricc').show();
        }
    });
}


function save_def_amount(return_id)
{
     
    var def_amount_item = $('#def_amount_item').val();
    if(def_amount_item == "")
    {
        alert('Please Select Defective Amount');
        return false;
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });
    
    jQuery.ajax({
        type: "POST",
        url: 'defective-save',
        data: {return_id: return_id,def_amount_item:def_amount_item},
        success: function(data)
        {
            alert(data);
            def_return(return_id);
        }
    });

    return false;
}

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
function get_partcode(div_id,model_id)
{
    var brand_id = $('#brand_id'+div_id).val();
    var product_category_id = $('#product_category_id'+div_id).val();
    var product_id = $('#product_id'+div_id).val();
    if(div_id=='1')
   {
       model_id = 'All';
   }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });
    jQuery.ajax({
        url: 'get-part-no',
        method: 'post',
        data: {
            brand_id: brand_id,
            product_category_id:product_category_id,
            product_id:product_id,
            model_id: model_id,
            div_id:div_id
        },
        success: function(result){
            
            console.log('#part_code'+div_id);
            $('#part_code'+div_id).html(result);
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
                        <div class="card-body"><h5 class="card-title">Fresh Defective Stock Management</h5>
                            
                        @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                        @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                  
                        <form method="get" action="{{route('fresh-defective-stock')}}" class="form-horizontal">
                           
                            <div class="form-row">
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>Brand</label>
                                        <select id="brand_id1" name="brand" onchange="get_product_detail('1',this.value)" class="form-control" >
                                            <option value="">Brand</option>
                                            <?php foreach($brand_arr as $brand) {?>       
                                                <option value="<?php echo $brand['brand_id']; ?>" <?php if($brand_id==$brand['brand_id']){ echo "selected"; } ?>><?php echo $brand['brand_name']; ?></option>     
                                            <?php  }?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>Product</label>
                                        <select id="product_category_id1" name="product_category" onchange="get_product('1',this.value)" class="form-control" >
                                            <option value="">Product</option>
                                            <option value="All" <?php echo ($product_category == 'All') ? 'selected' : ''; ?>>All</option>
                                            <?php foreach($category_master as $category) {?>       
                                                <option value="<?php echo $category->product_category_id; ?>" <?php if($product_category==$category->product_category_id){ echo "selected"; } ?>><?php echo $category->category_name; ?></option>     
                                            <?php  }?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>Model</label>
                                        <select id="product_id1" name="product" onchange="get_partcode('1',this.value)" class="form-control">
                                            <option value="">Model</option>
                                            <option value="All" <?php echo ($product == 'All') ? 'selected' : ''; ?>>All</option>
                                            <?php foreach($model_master as $model){ ?>
                                                <option value="<?php echo $model->product_id; ?>" <?php if($product==$model->product_id){ echo "selected"; } ?>><?php echo $model->product_name; ?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Part Code</label>
                                            <select  id="part_code1" name="part_code" class="form-control">
                                                <option value="">Select</option>
                                            </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>Defective Sr. No.</label>
                                        <input name="sr_no" autocomplete="off" id="sr_no" placeholder="Defective Sr. No." type="text" value="<?php echo $sr_no; ?>" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Defective Received From</label>
                                            <select  id="part_code1" name="part_code" class="form-control">
                                                <option value="">All</option>
                                                <option value="ASC">ASC</option>
                                                <option value="Engineer">Engineer</option>
                                                <option value="Fresh Defective">Fresh Defective</option>
                                            </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>Defective Received From Date</label>
                                        <input name="from_date" autocomplete="off" id="from_date" placeholder="From" type="text" value="<?php echo $from_date; ?>" class="form-control datepicker">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>Defective Received To Date</label>
                                        <input name="to_date" autocomplete="off" id="to_date" placeholder="To" type="text" value="<?php echo $to_date; ?>" class="form-control datepicker">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>Ticket No.</label>
                                        <input name="ticket_no" autocomplete="off" id="ticket_no" placeholder="Ticket No." type="text" value="<?php echo $ticket_no; ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>Job No.</label>
                                        <input name="job_no" autocomplete="off" id="job_no" placeholder="Job No." type="text" value="<?php echo $job_no; ?>" class="form-control">
                                    </div>
                                </div>
                                
 				                <div class="col-md-2">
                                    <label>&nbsp;</label>
                                    <div class="position-relative form-group">
                                        <input type="submit" class="btn btn-primary" value="Search">
                                        &nbsp;<a href="{{route('home')}}" class="btn btn-danger">Exit</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                        
                            
                            <div class="card-body">
                            <div class="row">
                                <div class="col-md-2">
                                    <h5 class="card-title">Details</h5>
                                </div>
                                <div class="col-md-8"></div>
                                <div class="col-md-2">
                                    <!-- <a href="{{route('fresh-stock-download')}}" class="card-title btn btn-warning">Download</a> -->
                                    <!-- <a href="#" id="export-excel" class="card-title btn btn-warning">Download Excel</a> -->
                                </div>
                            </div>
                            
                            <table id="table1" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Sr No.</th>
                                        <th>Date of Def. Recd</th>
                                        <th>Name of Asc / Engineer</th>
                                        <th>Asc / Engineer Code</th>
                                        <th>Brand</th>
                                        <th>Product</th>
                                        <th>Model</th>
                                        <th>Part Code</th>
                                        <th>Ticket No.</th>
                                        <th>Job No.</th>
                                        <th>Defective Sr No.</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                 
                                <?php   $srno = 1;
                                    foreach($req_arr as $req)
                                    {   #print_r($req);
                                        echo '<tr>';
                                        echo '<td>'.$srno++.'</td>';
                                        echo '<td>'.date('d-m-Y',strtotime($req->part_po_date)).'</td>';
                                        // echo '<td></td>';
                                        echo '<td>'.$req->center_name.'</td>';
                                        echo '<td>'.$req->asc_code.'</td>';
                                        echo '<td>'.$req->brand_name.'</td>';
                                        echo '<td>'.$req->model_name.'</td>';
                                        echo '<td>'.$req->part_name.'</td>';
                                        echo '<td>'.$req->part_no.'</td>';
                                        echo '<td>'.$req->ticket_no.'</td>';
                                        echo '<td>'.$req->job_no.'</td>';
                                        echo '<td>'.$req->part_po_no.'</td>';
                                        // echo '<td>'.date('d-m-Y',strtotime($req->created_at)).'</td>';
                                        // echo '<td>'.$req->part_no.'</td>';
                                        // echo '<td><a href="view-inw-inv-entry?inw_id='.base64_encode($req->inw_id).'">View</a></td>';
                                        echo '<td id="td'.$req->dpart_id.'">';
                                            echo '<button style="width:85px;" type="submit" class="trigger_popup_fricc" onclick="canbalize('."'".$req->dpart_id."'".')" class="btn btn-success">Canbalize</button>';
                                            echo '<button style="width:85px;" type="submit" class="trigger_popup_fricc" onclick="def_return('."'".$req->dpart_id."'".')" class="mt-2 btn btn-primary">Defective Return</button>';
                                            echo '<button style="width:85px;" type="submit" class="trigger_popup_fricc" onclick="scrap('."'".$req->dpart_id."'".')" class="mt-2 btn btn-warning">Scrap</button>';
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
        <div id="details_part"></div>
    </div>
</div>


<script>

    $('.popupCloseButton').click(function(){
        $('.hover_bkgr_fricc').hide();
    });

</script>


@endsection
