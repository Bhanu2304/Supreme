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
                <a role="tab" class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-content-1">
                    <span>Invoice View</span>
                </a>
            </li>
            <li class="nav-item">
                <a role="tab" class="nav-link" id="tab-0" data-toggle="tab" href="#tab-content-0">
                    <span>Make Invoice Copy</span>
                </a>
                
            </li>
        </ul>
     <div class="tab-content">
        @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
        @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
        <div class="tab-pane tabs-animation fade " id="tab-content-0" role="tabpanel">
             <div class="main-card mb-3 card">
                    <div class="card-body"><h5 class="card-title">View Invoice</h5>
                        <form method="get" action="{{route('po-invoice')}}" class="form-horizontal">
                            
                            <div class="form-row">
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Job Number</label>
                                        <input type="text" name="job_number" id="job_number" class="form-control" value="<?php echo $job_number;?>"  placeholder="Job Number">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Ticket Number</label>
                                        <input type="text" name="ticket_number" id="ticket_number" class="form-control" value="<?php echo $ticket_number;?>"  placeholder="Ticket Number">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>Brand</label>
                                        <select id="brand_id1" name="brand_id" onchange="get_product_detail('1',this.value)" class="form-control">
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
                                        <select id="product_category_id1" name="product_category" onchange="get_product('1',this.value)" class="form-control">
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
                                        <select id="product_id1" name="product" onchange="get_model('1',this.value)" class="form-control">
                                            <option value="">Product</option>
                                            <option value="All" <?php echo ($product == 'All') ? 'selected' : ''; ?>>All</option>
                                            <?php foreach($model_master as $model){ ?>
                                                <option value="<?php echo $model->product_id; ?>" <?php if($product==$model->product_id){ echo "selected"; } ?>><?php echo $model->product_name; ?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Warranty Type</label>
                                        <select id="warranty_type" name="warranty_type" class="form-control" >
                                            <option value="">All</option>
                                            <option value="In" <?php if( $warranty_type=="In") { echo 'selected';} ?>>In</option>
                                            <option value="Out" <?php if( $warranty_type=="Out") { echo 'selected';} ?>>Out</option>
                                            
                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Asc Name</label>
                                        <select id="asc_name" name="asc_name" class="form-control" >
                                            <option value="All">All</option>
                                            @foreach($asc_master as $asc)
                                                <option value="{{$asc->center_id}}" <?php if( $center_id==$asc->center_id) 
                                                    { echo 'selected';} ?>>{{$asc->center_name}}</option>
                                            @endforeach
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
                        <h5 class="card-title">View Invoice</h5>
                        <table class="table table-striped table-bordered" id="table1">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Invoice No.</th>
                                <th>PO Request Date</th>
                                <th>Part PO Number</th>
                                <th>PO Type</th>
                                <th>Ticket No.</th>
                                <th>Job No.</th>
                                <th>ASC Name</th>
                                <th>ASC Code</th>
                                <th>Brand Name</th>
                                <th>Model No.</th>
                                <th>Part Code</th>
                                <th>Part Name</th>
                                <th>Colour</th>
                                <th>HSN Code</th>
                                <th>GST Rate</th>
                                <th>ASC Amount</th>
                                <th>Request Qty</th>
                                <th>Issued Qty</th>
                                <th>Discount</th>
                                <th>Remarks</th>
                                <th>PDF</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php   $srno = 1;
                                foreach($invoice_arr2 as $po_job)
                                {
                                    echo '<tr>';
                                    echo '<td align="center">'.$srno++.'</td>';
                                    echo '<td align="center">'.$po_job->invoice_no.'</td>';
                                    echo '<td align="center">'.date('d-m-y',strtotime($po_job->po_date)).'</td>';
                                    echo '<td align="center">'.$po_job->po_no.'</td>';
                                    echo '<td align="center">'.$po_job->po_type.'</td>';
                                    echo '<td align="center">'.$po_job->ticket_no.'</td>';
                                    echo '<td align="center">'.$po_job->job_no.'</td>';
                                    echo '<td align="center">'.$po_job->asc_name.'</td>';
                                    echo '<td align="center">'.$po_job->asc_code.'</td>';
                                    echo '<td align="center">'.$po_job->brand_name.'</td>';
                                    echo '<td align="center">'.$po_job->model_name.'</td>';
                                    echo '<td align="center">'.$po_job->part_no.'</td>';
                                    echo '<td align="center">'.$po_job->part_name.'</td>';
                                    echo '<td align="center">'.$po_job->color.'</td>';
                                    echo '<td align="center">'.$po_job->hsn_code.'</td>';
                                    echo '<td align="center">'.$po_job->gst_amount.'</td>';
                                    echo '<td align="center">'.$po_job->asc_amount.'</td>';
                                    echo '<td align="center">'.$po_job->req_qty.'</td>';
                                    echo '<td align="center">'.$po_job->issued_qty.'</td>';
                                    echo '<td align="center">'.$po_job->discount.'</td>';
                                    echo '<td align="center">'.$po_job->remarks.'</td>';
                                    echo '<td align="center">';
                                    echo '<a href=po-invoice-pdf?type=preview&invoice_id='.$po_job->invoice_id.'>Pi  /';
                                    echo '<a href=generate-TXInvoice?TagId='.$po_job->TagId.'> Ti ';
                                    echo '</td>';
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
        
         <div class="tab-pane tabs-animation fade show active" id="tab-content-1" role="tabpanel">
            <div class="main-card mb-3 card">

                    <div class="card-body"><h5 class="card-title">View Invoice</h5>
                        <form method="get" action="{{route('po-invoice')}}" class="form-horizontal">
                            
                            <div class="form-row">

                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Invoice No.</label>
                                        <input type="text" name="invoice_no" id="invoice_no" class="form-control" value="<?php echo $invoice_no;?>"  placeholder="Invoice No.">
                                    </div>
                                </div>

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
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Po Type</label>
                                        <select id="po_type" name="po_type" class="form-control" >
                                            <option value="">All</option>
                                            <option value="FOC" <?php if( $po_type=="FOC") { echo 'selected';} ?>>FOC</option>
                                            <option value="Paid" <?php if( $po_type=="Paid") { echo 'selected';} ?>>Paid</option>
                                            
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Type of Invoice</label>
                                        <select id="type_of_inv" name="type_of_inv" class="form-control" >
                                            <option value="">All</option>
                                            <option value="Performa" <?php if( $type_of_inv=="Performa") { echo 'selected';} ?>>Performa</option>
                                            <option value="Tax" <?php if( $type_of_inv=="Tax") { echo 'selected';} ?>>Tax</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Asc Name</label>
                                        <select id="asc_name" name="asc_name" class="form-control" >
                                            <option value="All">All</option>
                                            @foreach($asc_master as $asc)
                                                <option value="{{$asc->center_id}}" <?php if( $center_id==$asc->center_id) 
                                                    { echo 'selected';} ?>>{{$asc->center_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Job Number</label>
                                        <input type="text" name="job_number" id="job_number" class="form-control" value="<?php echo $job_number;?>"  placeholder="Job Number">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Ticket Number</label>
                                        <input type="text" name="ticket_number" id="ticket_number" class="form-control" value="<?php echo $ticket_number;?>"  placeholder="Ticket Number">
                                    </div>
                                </div>


                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>From Date</label>
                                        <input name="from_date" autocomplete="off" id="from_date1" placeholder="From" type="text" value="<?php echo $from_date; ?>" class="form-control datepicker">
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label>To Date</label>
                                        <input name="to_date" autocomplete="off" id="to_date1" placeholder="To" type="text" value="<?php echo $to_date; ?>" class="form-control datepicker">
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
                        <h5 class="card-title">View Invoice</h5>

                        <table class="table table-striped table-bordered" id="table1">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Invoice No.</th>
                                <th>PO Request Date</th>
                                <th>Part PO Number</th>
                                <th>PO Type</th>
                                <th>Ticket No.</th>
                                <th>Job No.</th>
                                <th>ASC Name</th>
                                <th>ASC Code</th>
                                <th>Brand Name</th>
                                <th>Model No.</th>
                                <th>Part Code</th>
                                <th>Part Name</th>
                                <th>Colour</th>
                                <th>HSN Code</th>
                                <th>GST Rate</th>
                                <th>ASC Amount</th>
                                <th>Request Qty</th>
                                <th>Issued Qty</th>
                                <th>Discount</th>
                                <th>Remarks</th>
                                <th>PDF</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php   $srno = 1;
                                    
                                    foreach($invoice_arr as $po_job)
                                    {
                                        echo '<tr>';
                                            echo '<td align="center">'.$srno++.'</td>';
                                            echo '<td align="center">'.$po_job->invoice_no.'</td>';
                                            echo '<td align="center">'.date('d-m-y',strtotime($po_job->po_date)).'</td>';
                                            echo '<td align="center">'.$po_job->po_no.'</td>';
                                            echo '<td align="center">'.$po_job->po_type.'</td>';
                                            echo '<td align="center">'.$po_job->ticket_no.'</td>';
                                            echo '<td align="center">'.$po_job->job_no.'</td>';
                                            echo '<td align="center">'.$po_job->asc_name.'</td>';
                                            echo '<td align="center">'.$po_job->asc_code.'</td>';
                                            echo '<td align="center">'.$po_job->brand_name.'</td>';
                                            echo '<td align="center">'.$po_job->model_name.'</td>';
                                            echo '<td align="center">'.$po_job->part_no.'</td>';
                                            echo '<td align="center">'.$po_job->part_name.'</td>';
                                            echo '<td align="center">'.$po_job->color.'</td>';
                                            echo '<td align="center">'.$po_job->hsn_code.'</td>';
                                            echo '<td align="center">'.$po_job->gst_amount.'</td>';
                                            echo '<td align="center">'.$po_job->asc_amount.'</td>';
                                            echo '<td align="center">'.$po_job->req_qty.'</td>';
                                            echo '<td align="center">'.$po_job->issued_qty.'</td>';
                                            echo '<td align="center">'.$po_job->discount.'</td>';
                                            echo '<td align="center">'.$po_job->remarks.'</td>';
                                            echo '<td align="center">';
                                            echo '<a href=po-invoice-pdf?type=preview&invoice_id='.$po_job->invoice_id.'>Preview </a> || ';
                                            echo '<a href=po-invoice-pdf?invoice_id='.$po_job->invoice_id.'> Pdf</td>';
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

<script>
    
    function remove_row(tr_id,row_no)
    {
        $('#row'+tr_id).remove();
        var rows = document.querySelectorAll('#part_arr tr');
        
        for(var i=0;i<rows.length;i++){
          var row = rows[i];
          var cell = row.cells[0];
          cell.innerHTML = i+1;
          console.log(cell);
        }
        
        
    }
        
    function create_invoices() 
    {
        $('#disp_inv_no').html('');
        //alert(chk_inv.length);
        $.ajaxSetup({
                 headers: {
                     'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                 }
             });
             
        var chk_inv = document.querySelectorAll('[name="chk_inv[]"]:checked');     
        for(var i=0; i<chk_inv.length; i++)       
        {
            if(chk_inv[i].type==='checkbox' && chk_inv[i].checked===true)
            {
                var out_id = chk_inv[i].value;
                  task(out_id,i);
            }
        }    
    }

     function task(out_id,i) {
  setTimeout(function() {
      jQuery.ajax({
                url: 'ho-create-invoice', 
                method: 'post',
                data:{out_id:out_id},

                   success: function(result){
                       $('#disp_inv_no').append(result);
                   }});  
  }, 500*i );
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
 
 
</script>

@endsection
