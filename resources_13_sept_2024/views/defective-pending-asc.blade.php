@extends('layouts.app')

@section('content') 
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,400i,700,700i">
<script src= "https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> 
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<!-- DataTables JavaScript -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<!-- TableExport JavaScript -->
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.1.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.1.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.70/vfs_fonts.js"></script>
<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script> -->

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
                    
                   
                },
                {
                    extend: 'pdfHtml5',
                    text: 'Download PDF', 
                    className: 'btn btn-danger', 
                    customize: function(doc) {
                        doc.pageSize = 'A2';
                    }
                }
            ],
            ordering: false
        });
        
    });
</script>

<script>
                                    

menu_select('{{$url}}');
</script>
<script>

    function checkNumber(val,evt)
    {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        
        if (charCode> 31 && (charCode < 48 || charCode > 57))
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

<?php $tab = Session::get('tab'); ?>

<div class="app-main"> 
    <div class="app-main__outer">
        <div class="app-main__inner">
        <ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
            <li class="nav-item">
                <a role="tab" class="nav-link <?php if($tab!=='1') { ?>active <?php } ?>" id="tab-0" data-toggle="tab" href="#tab-content-0">
                    <span>View </span>
                </a>
            </li>
            
        </ul>
        <div class="tab-content">
         <h5 id="succ" style="display:none;"><font color="green"> </font></h5> 
         <h5 id="error" style="display:none;"><font color="red"> </font></h5> 
         <div class="tab-pane tabs-animation fade <?php if($tab!=='1') { ?>show active <?php } ?>" id="tab-content-0" role="tabpanel">
             <div class="main-card mb-3 card"> 

                    <div class="card-body"><h5 class="card-title">View Defective Parts Pending at Asc</h5>
                        <form method="get" action="{{route('defective-pending-asc')}}" class="form-horizontal">
                            
                            <div class="form-row">
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>Brand</label>
                                        <select id="brand_id2" name="brand_id" onchange="get_product_detail('2',this.value)" class="form-control">
                                            <option value="">Brand</option>
                                            <?php foreach($brand_arr as $brand) {?>       
                                                <option value="<?php echo $brand['brand_id']; ?>" <?php if($brand_id==$brand['brand_id']){ echo "selected"; } ?>><?php echo $brand['brand_name']; ?></option>     
                                            <?php  }?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>Product Category</label>
                                        <select id="product_category_id2" name="product_category" onchange="get_product('2',this.value)" class="form-control">
                                            <option value="">Product Category</option>
                                            <option value="All" <?php echo ($product_category == 'All') ? 'selected' : ''; ?>>All</option>
                                            <?php foreach($category_master as $category) {?>       
                                                <option value="<?php echo $category->product_category_id; ?>" <?php if($product_category==$category->product_category_id){ echo "selected"; } ?>><?php echo $category->category_name; ?></option>     
                                            <?php  }?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>Product</label>
                                        <select id="product_id2" name="product" onchange="get_model('2',this.value)" class="form-control">
                                            <option value="">Product</option>
                                            <option value="All" <?php echo ($product == 'All') ? 'selected' : ''; ?>>All</option>
                                            <?php foreach($model_master as $model){ ?>
                                                <option value="<?php echo $model->product_id; ?>" <?php if($product==$model->product_id){ echo "selected"; } ?>><?php echo $model->product_name; ?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="exampleAddress" class="">Model No.<font color="red">*</font></label>
                                        <select id="model_id2" name="model" onchange="get_partcode('2',this.value)" class="form-control">
                                            <option value="">Model No.</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Part Code</label>
                                        <select  id="part_code2" name="part_code" class="form-control">
                                            <option value="">Select</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Po No.</label>
                                        <input type="text" name="po_no" id="po_no" class="form-control" value="<?php echo $po_sr_no;?>"  placeholder="Po No.">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Status</label>
                                        <select  id="po_status" name="po_status" class="form-control">
                                            <option value="">Select</option>
                                            <option value="Approved" <?php if($po_status=="Approved"){ echo "selected"; } ?> >Approved</option>
                                            <option value="Pending" <?php if($po_status=="Pending"){ echo "selected"; } ?>>Pending</option>
                                        </select>
                                    </div>
                                </div>

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
                        <!-- <div class="row">
                            <div class="col-md-6">
                                <h5 class="card-title">View PO Request</h5>
                            </div>
                            <div class="col-md-3"></div>
                            <div class="col-md-3">
                                <h5 class="card-title" style='float: right;'><a href="req-inv-pdf" role="tab" class="btn btn-primary"><span>Download Pdf </span></a></h5>
                            </div>
                        </div> -->
                        <h5 class="card-title">View Defective Parts</h5>
                        <table class="table" id="table1">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Po No.</th>
                                <th>Date</th>
                                <th>Ticket No.</th>
                                <th>Job No.</th>
                                <th>Brand</th>
                                <th>Product Category</th>
                                <th>Product</th>
                                <th>Model No.</th>
                                <th>Part Name</th>
                                <th>Part Code</th>
                                <th>Color</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php   $srno = 1;
                                    foreach($req_arr as $req)
                                    {
                                        #print_r($req);
                                        echo '<tr '.'id="tr'.$req->id.'">';
                                            echo '<td>'.$srno++.'</td>';
                                            echo '<td>'.$req->part_po_no.'</td>';
                                            echo '<td>'.date('d-m-Y', strtotime($req->created_at)).'</td>';
                                            echo '<td>'.$req->ticket_no.'</td>';
                                            echo '<td>'.$req->job_no.'</td>';
                                            echo '<td>'.$req->brand_name.'</td>';
                                            echo '<td>'.$req->category_name.'</td>';
                                            echo '<td>'.$req->product_name.'</td>';
                                            echo '<td>'.$req->model_name.'</td>';
                                            echo '<td>'.$req->part_name.'</td>';
                                            echo '<td>'.$req->part_no.'</td>';
                                            echo '<td>'.$req->color.'</td>';
                                            echo '<td>';
                                            if($req->pending_status==1)
                                            {
                                                #echo '<a href="#" onclick="save_def_pending('.$req->dpart_id.');">Accept</a>';
                                                echo 'Pending'; 

                                            }else {
                                                echo $req->part_status;
                                            }
                                            echo '</td>';
                                            
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
</div> 

<script>
    
    function set_value_by_id(div_id,value)
    {
        $('#'+div_id).val(value);
    }
    function get_value_by_id(div_id)
    {
        var div_value = $('#'+div_id).val();
        if(div_value==='')
        {
            return 0;
        }
        else
        {
            return div_value;
        }
    }
    function cal_tot_val(srno)
    {
        var qty = get_value_by_id('qty'+srno);
        var rate = get_value_by_id('rate'+srno);
        var total = qty*rate;
        set_value_by_id('total'+srno,total.toFixed(2));
        
        
        get_total_summary();
    }
    function get_total_summary()
    {
        var rate_arr = document.getElementsByName('SparePart[rate][]');
        var qty_arr = document.getElementsByName('SparePart[qty][]');
        
        var total = 0;
        for (var i = 0; i <rate_arr.length; i++) {
            var rate=rate_arr[i].value;
            //console.log("parePart[rate]["+i+"].value="+inp.value);
            var qty = qty_arr[i].value;
            total += parseFloat(rate)*parseFloat(qty);
            //console.log(total);
        }
        //alert(total);
        document.getElementById('total').value = total.toFixed(2);


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
              }});
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
              }});
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
              }});
 }

function get_part_name(div_id,part_code)
 {
     
     var brand_id = $('#brand_id'+div_id).val();
     var product_category_id = $('#product_category_id'+div_id).val();
     var product_id = $('#product_id'+div_id).val();
     var model_id = $('#model_id'+div_id).val();
     
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-part-name',
              method: 'post',
              data: {
                 brand_id: brand_id,
                 product_category_id:product_category_id,
                 product_id:product_id,
                 model_id: model_id,
                 part_code: part_code
              },
              success: function(result){
                  $('#part_name'+div_id).html(result)
              }});
 }
    
 function get_partcode(div_id,model_id)
 {
    var brand_id = $('#brand_id'+div_id).val();
    var product_category_id = $('#product_category_id'+div_id).val();
    var product_id = $('#product_id'+div_id).val();
    
     
     
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

function get_hsn_code(div_id,part_no)
 {
     var brand_id = $('#brand_id'+div_id).val();
    var product_category_id = $('#product_category_id'+div_id).val();
    var product_id = $('#product_id'+div_id).val();
    var model_id = $('#model_id'+div_id).val();
     var part_name = $('#part_name'+div_id).val();
     
     
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-hsn-code',
              method: 'post',
              data: {
                  brand_id: brand_id,
                 product_category_id:product_category_id,
                 product_id:product_id,
                 model_id: model_id,
                 part_name: part_name,
                 part_no: part_no 
              },
              success: function(result){
                  $('#hsn_code'+div_id).html(result);
              }});
 }
    
function get_rate(div_id,index)
 {
     
     var brand_id = $('#brand_id'+index).val();
    var product_category_id = $('#product_category_id'+index).val();
    var product_id = $('#product_id'+index).val();
    var model_id = $('#model_id'+index).val();
     var part_name = $('#part_name'+index).val();
     var part_no = $('#part_no'+index).val();
     var hsn_code = $('#hsn_code'+index).val();
     
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-part-rate',
              method: 'post',
              data: {
                  brand_id: brand_id,
                 product_category_id:product_category_id,
                 product_id:product_id,
                 model_id: model_id,
                 part_name: part_name,
                 part_no: part_no,
                 hsn_code: hsn_code
              },
              success: function(result){
                  $('#'+div_id).val(result);
                  var qty = $('#qty'+index).val();
                  var total = qty*parseFloat(result);
                  $('#total'+index).val(total);
                  
              }});
 }    
    
    
 function add_part(div_id,index)
 {
     //var div_id = '1';
     var brand_id = $('#brand_id'+div_id).val();
     var product_category_id = $('#product_category_id'+div_id).val();
     var product_id = $('#product_id'+div_id).val();
     var model_id = $('#model_id'+div_id).val();
     
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
          
          $('#part_arr').html('');
    for (var option of document.getElementById('part_code'+div_id).options)
    {
        if (option.selected) {
            var sp_id = option.value;
            jQuery.ajax({
              url: 'get-add-req-part',
              method: 'post',
              data:{sp_id:sp_id,brand_id:brand_id,product_category_id:product_category_id,product_id:product_id,model_id:model_id},
              
              success: function(result){
                  $('#part_arr').append(result);
              }});
        }
        
    }

 }
 
 function del_part(del_div)
 {
     $('#'+del_div).remove();
 }

function save_def_pending(dispatch_id)
{
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });
    jQuery.ajax({
        url: 'def-pending-accept',
        method: 'post',
        data: {
            dispatch_id: dispatch_id
        },
        success: function(result){
            if(result==='1')
            {
                $('#tr'+dispatch_id).remove();
                $('#succ').show();
                $('#succ').html("Approved Successfully.");
                $('#error').hide();
            }
            else
            {
                $('#succ').hide();
                $('#error').html("Approved Failed.");
                $('#error').show();
            }    
        }
    });
}
 
 
</script>

@endsection
