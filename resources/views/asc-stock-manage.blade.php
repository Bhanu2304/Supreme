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

    function part_defective(invoice_id,remarks)
    {
        $('#scc').html("");
        $('#err').html("");
        $.post('part-asc-stock',{invoice_id:invoice_id,remarks:remarks}, function(resp)
        {
            const obj = JSON.parse(resp);
            if(obj.resp_id==='1')
            {
                $('#scc').html('<h5><font color="green">Part No '+obj.part_no+' Defective Added.</font></h5>');
                //$('#td'+tagId).html('<a onclick="job_reject('+"'"+tagId+"'"+');" href="#">Reject</a>');
                $('#td'+invoice_id).html("Accepted");
                //$('#tr'+tagId).remove();
            }
            else
            { 
                $('#err').html('<h5><font color="red">Part No Defective Already Add</font></h5>');
            }
            
        });
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
                        <div class="card-body"><h5 class="card-title">Asc Stock Management</h5>
                            
                        @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                        @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                  
                        <form method="get" action="{{route('asc-stock')}}" class="form-horizontal">
                           
                            <div class="form-row">
                                <div class="col-md-2">
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
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="exampleAddress" class="">Product</label>
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
                                        <label for="exampleAddress" class="">Model</label>
                                        <select id="product_id1" name="product" onchange="get_partcode('1',this.value)" class="form-control" >
                                            <option value="">Product</option>
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

                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="exampleEmail11" class="">From Date</label>
                                        <input name="from_date" autocomplete="off" id="from_date" placeholder="From" type="text" value="<?php echo $from_date; ?>" class="form-control datepicker">
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">To Date</label>
                                        <input name="to_date" autocomplete="off" id="to_date" placeholder="To" type="text" value="<?php echo $to_date; ?>" class="form-control datepicker">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">Ticket No.</label>
                                        <input name="ticket_no" autocomplete="off" id="ticket_no" placeholder="Ticket No" type="text" value="<?php echo $ticket_no; ?>" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">Challan No.</label>
                                        <input name="challan_no" autocomplete="off" id="challan_no" placeholder="Challan No" type="text" value="<?php echo $challan_no; ?>" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">Sr No.</label>
                                        <input name="sr_no" autocomplete="off" id="sr_no" placeholder="Sr No." type="text" value="<?php echo $sr_no; ?>" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">Status</label>
                                        <select id="status" name="status" class="form-control" >
                                            <option value="">Select</option>
                                            <option value="Ok Return">Ok Return</option>
                                            <option value="In Stock">In Stock</option>
                                            <option value="Consumed">Consumed</option>
                                            <option value="Defective Returned">Defective Returned</option>
                                        </select>
                                    </div>
                                </div>
                                
 				                <div class="col-md-2">
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
                                        <th>Challan No.</th>
                                        <!-- <th>Stock Inward Date</th> -->
                                        <th>Po Sr. No.</th>
                                        <th>Brand</th>
                                        <th>Product</th>
                                        <th>Model</th>
                                        <th>Spare Part Name</th>
                                        <th>Part Code</th>
                                        <th>Hsn Code</th>
                                        <th>Asc Name</th>
                                        <th>Asc Code</th>
                                        <th>Serial Number</th>
                                        <th>Issued on</th>
                                        <th>Used on</th>
                                        <th>Old part serial number</th>
                                        <th>Courier Name</th>
                                        <th>Courier Id</th>
                                        <th>Ticket Number</th>
                                        <th>Date of Received</th>
                                        <th>Status</th>
                                        <th>Remarks</th>
                                        
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                 
                                <?php   $srno = 1;
                                    foreach($req_arr as $req)
                                    {   
                                        echo '<tr>';
                                        echo '<td>'.$srno++.'</td>';
                                        echo '<td>'.$req->po_no.'</td>';
                                        // echo '<td>'.date('d-m-Y',strtotime($req->po_date)).'</td>';
                                        echo '<td>'.$req->voucher_no.'</td>';
                                        echo '<td>'.$req->brand_name.'</td>';
                                        echo '<td>'.$req->product_name.'</td>';
                                        echo '<td>'.$req->model_name.'</td>';
                                        echo '<td>'.$req->part_name.'</td>';
                                        echo '<td>'.$req->part_no.'</td>';
                                        echo '<td>'.$req->hsn_code.'</td>';
                                        echo '<td>'.$req->asc_name.'</td>';
                                        echo '<td>'.$req->asc_code.'</td>';
                                        echo '<td>'.$req->serial_no.'</td>';
                                        echo '<td>';
                                            if (!empty($req->issue_date)) { 
                                                echo date('d-m-Y', strtotime($req->issue_date)); 
                                            }
                                        echo '</td>';
                                        echo '<td>';
                                        if (!empty($req->used_date)) { 
                                            echo date('d-m-Y', strtotime($req->used_date)); 
                                        }
                                        echo '</td>';
                                        echo '<td>'.$req->old_serial_no.'</td>';
                                        echo '<td>'.$req->courier_name.'</td>';
                                        echo '<td>'.$req->courier_id.'</td>';
                                        echo '<td>'.$req->job_no.'</td>';
                                        echo '<td>'.date('d-m-Y',strtotime($req->created_at)).'</td>';
                                         echo '<td>'.$req->status.'</td>';
                                        echo '<td>'.$req->remarks.'</td>';
                                        // echo '<td>'.$req->part_no.'</td>';
                                        // echo '<td><a href="view-inw-inv-entry?inw_id='.base64_encode($req->inw_id).'">View</a></td>';
                                        echo '<td id="td'.$req->invoice_id.'">';
                                         
                                        echo '<a onclick="part_defective(\'' . $req->invoice_id . '\', \'OK Return\');" href="#">OK Return</a> ||';
                                        echo '<a onclick="part_defective(\'' . $req->invoice_id . '\', \'In Stock\');" href="#">In Stock</a> ||';
                                        echo '<a onclick="part_defective(\'' . $req->invoice_id . '\', \'Defective Returned\');" href="#">Defective Returned</a>';
                                        
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
