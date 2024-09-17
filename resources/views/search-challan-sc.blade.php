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

        $('#table2').DataTable({
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
function reloadPage(){
    location.reload(true);
}

function get_pincode(state_id){
    
    $.post('vendor-get-pin',{state_id:state_id}, function(data){
        $('#pincode').html(data);
    }); 
     
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
              }});
}

function get_product(div_id,product_category_id)
{
     var brand_id = $('#brand_id'+div_id).val();
     //console.log(brand_id);
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


function generate_challan(invoice_id,eWayNoId)
{
    $('#scc').html("");
    $('#err').html("");
    
    var e_way_no = $('#'+eWayNoId).val();
    $.post('generate-challan-sc',{invoice_id:invoice_id,e_way_no:e_way_no}, function(resp){
        const obj = JSON.parse(resp);
        if(obj.resp_id==='1')
        {
            $('#scc').html('<h5><font color="green">Challan '+obj.job_no+' Generated Successfully.</font></h5>');
            //$('#td'+tagId).html('<a onclick="job_reject('+"'"+tagId+"'"+');" href="#">Reject</a>');
            $('#td'+invoice_id).html("Accepted");
            $('#tr'+invoice_id).remove();
        }
        else
        { 
            $('#err').html('<h5><font color="red">Challan Already Generated</font></h5>');
        }
        
    }); 
}

</script>

<div class="app-main">
<div class="app-main__outer">
   <div class="app-main__inner">
      <ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">

         <li class="nav-item">
            <a role="tab" class="nav-link <?php if((isset($tab1) && $tab1 == "tab1") || (!isset($tab1) && !isset($tab2))){echo "active"; }?>" id="tab-0" data-toggle="tab" href="#tab-content-0">
            <span>Generate</span>
            </a>
         </li>
         <li class="nav-item">
            <a role="tab" class="nav-link <?php if(isset($tab2) && $tab2 == "tab2"){echo "active"; }?>" id="tab-1" data-toggle="tab" href="#tab-content-1">
            <span>View</span>
            </a>
         </li>
      </ul>
      <div class="tab-content">
         <div class="tab-pane tabs-animation fade <?php if((isset($tab1) && $tab1 == "tab1") || (!isset($tab1) && !isset($tab2))){echo "show active"; }?>" id="tab-content-0" role="tabpanel">
            <div class="main-card mb-3 card">
               <div class="card-body">
                  <h5 class="card-title">Genrate Challan</h5>
                 
                  <p id="scc">@if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif </p>
                  <p id="err">@if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif </p>
                  <form method="get" action="{{route('search-challan-sc')}}" class="form-horizontal">
                     <div class="form-row">
                        <div class="col-md-2">
                           <div class="position-relative form-group">
                                 <label>Brand</label>
                                 <select id="brand_id1" name="brand_id" onchange="get_product_detail('1',this.value)" class="form-control">
                                    <option value="">Brand</option>
                                    <?php foreach($brand_arr as $brand) {?>       
                                       <option value="<?php echo $brand['brand_id']; ?>" <?php if($brand_id==$brand['brand_id']){ echo "selected"; } ?>><?php echo $brand['brand_name']; ?></option>     
                                    <?php  }?>
                                 </select>
                                 <input type="hidden" name="tab1" id="tab1" value="tab1">
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
                                    <?php foreach($product_master as $model){ ?>
                                       <option value="<?php echo $model->product_id; ?>" <?php if($product==$model->product_id){ echo "selected"; } ?>><?php echo $model->product_name; ?></option>
                                    <?php }?>
                                 </select>
                           </div>
                        </div>

                        <div class="col-md-2">
                           <div class="position-relative form-group">
                              <label for="exampleAddress" class="">Model No.<font color="red">*</font></label>
                              <select id="model_id1" name="model" onchange="get_partcode('1',this.value)" class="form-control">
                                 <option value="">Model No.</option>
                                 <option value="All" <?php echo ($model_id == 'All') ? 'selected' : ''; ?>>All</option>
                                 <?php foreach($model_master as $model){ ?>
                                    <option value="<?php echo $model->model_id; ?>" <?php if($model_id==$model->model_id){ echo "selected"; } ?>><?php echo $model->model_name; ?></option>
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
                              <label for="exampleEmail11" class="">Ticket No.</label>
                              <input name="ticket_no" id="ticket_no" placeholder="Ticket No." type="text" value="<?php echo $ticket_no; ?>" class="form-control" >
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="position-relative form-group">
                              <label for="exampleEmail11" class="">System Sr. No.</label>
                              <input name="sr_no" id="sr_no" placeholder="System Sr. No." type="text" value="<?php echo $sr_no; ?>" class="form-control" >
                           </div>
                        </div>
                        
                        <div class="col-md-2">
                           <div class="position-relative form-group">
                              <label for="exampleEmail11" class="">From Date</label>
                              <input name="from_date" id="from_date" placeholder="From" type="text" value="<?php echo $from_date; ?>" class="form-control datepicker" >
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="position-relative form-group">
                              <label for="examplePassword11" class="">To Date</label>
                              <input name="to_date" id="to_date" placeholder="To" type="text" value="<?php echo $to_date; ?>" class="form-control datepicker" >
                           </div>
                        </div>
                        <!-- <div class="col-md-2">
                           <div class="position-relative form-group">
                              <label for="examplePassword11" class="">ASC Code</label>
                              <input class="form-control" type="text" id="asc_code" name="asc_code" value="<?php //echo $asc_code;?>" data-original-title="Mobile No." data-placement="top" placeholder="ASC Code">
                           </div>
                        </div> -->
                        <!-- <div class="col-md-2">
                           <div class="position-relative form-group">
                              <label for="examplePassword11" class=""> Challan no.</label>
                              <input class="form-control" type="text" id="challan_no" name="challan_no" value="<?php //echo $challan_no;?>" data-original-title="Mobile No." data-placement="top" placeholder="Challan No.">
                           </div>
                        </div> -->
                        <div class="col-md-4"></div>
                        <div class="col-md-2">
                           <label>&nbsp;</label>
                           <div class="position-relative form-group">
                              <a href="{{route('generate-manual-challan')}}" class="btn btn-warning">Generate Manual Challan</a>
                           </div>
                        </div>
                        <div class="col-md-8">
                           <div class="position-relative form-group">                         
                              <input type="submit" class="btn btn-success btn-grad" data-original-title="" title="" value="Search">
                              &nbsp;<a href="{{route('home')}}" class="btn btn-danger btn-grad" data-original-title="" title="">Exit</a>
                           </div>
                        </div>
                     </div>
                  </form>
               </div>
               <div class="card-body">
                  <h5 class="card-title">Generate Challan</h5>
                  <table id="table1" class="table table-striped table-bordered" style="width:100%">
                     <thead>
                        <tr>
                           <th>Sr.</th>
                           <th>Date Of Challan</th>
                           <th>Challan No.</th>
                           <th>Brand</th>
                           <th>Ticket No.</th>
                           <th>Job No.</th>
                           <th>Center</th>
                           <th>State</th>
                           <th>Pin Code</th>
                           <th>Parts</th>
                           <th>Qty</th>
                           <th>Grand Total</th>
                           <th>Invoice No</th>
                           <th>Eway Bill</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php $srno = 1;
                           foreach($DataArr2 as $record)
                           {
                              #print_r($record);
                              echo '<tr id="tr'.$record->invoice_id.'">';
                              echo '<td>';
                              # echo '<input type="checkbox" name="case[]" value="'.$record->TagId.'" />';
                              echo $srno++.'</td>';
                              echo '<td>'.date('d-m-Y',strtotime($record->create_date)).'</td>';
                              echo '<td>';
                              # echo '<a href="tax-invoice?TagId='.$record->TagId.'">'.$record->challan_no.'</a>&nbsp;';
                              echo $record->po_no;
                              echo '</td>';
                              echo '<td>'.$record->brand_name.'</td>';
                              echo '<td>'.$record->ticket_no.'</td>';
                              echo '<td>'.$record->job_no.'</td>';
                              echo '<td>'.$record->asc_name.'</td>';
                              echo '<td>'.$record->state_name.'</td>';
                              echo '<td>'.$record->pincode.'</td>';
                              echo '<td>'.$record->req_qty.'</td>';
                              echo '<td>'.$record->dispatch_qty.'</td>';
                              echo '<td>'.$record->net_bill.'</td>';
                              echo '<td>'.$record->invoice_no.'</td>';
                              $eWayNoId = 'e_way_no_' . $record->invoice_id;
                              echo '<td><input type="text" name="e_way_no" id="' . $eWayNoId . '" placeholder="Eway No"></td>';
                              echo '<td>';
                              if($record->status == 0)
                              {
                                 echo '<a onclick="generate_challan(\'' . $record->invoice_id . '\', \'' . $eWayNoId . '\');" href="#">Generate Challan</a>';
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
         <div class="tab-pane tabs-animation fade <?php if(isset($tab2) && $tab2 == "tab2"){echo "show active"; }?>" id="tab-content-1" role="tabpanel">
            <div class="main-card mb-3 card">
               <div class="card-body">
                  <h5 class="card-title"></h5>
                  @if(Session::has('message'))
                  <h5><font color="green"> {{ Session::get('message') }}</font></h5>
                  @endif
                  @if(Session::has('error'))
                  <h5><font color="red"> {{ Session::get('error') }}</font></h5>
                  @endif
                  <form method="get" action="{{route('search-challan-sc')}}" class="form-horizontal">
                     <div class="form-row">
                        <div class="col-md-2">
                           <div class="position-relative form-group">
                              <label>Brand</label>
                              <select id="brand_id2" name="brand_id2" onchange="get_product_detail('2',this.value)" class="form-control">
                                 <option value="">Brand</option>
                                 <?php foreach($brand_arr as $brand) {?>       
                                    <option value="<?php echo $brand['brand_id']; ?>" <?php if($brand_id2==$brand['brand_id']){ echo "selected"; } ?>><?php echo $brand['brand_name']; ?></option>     
                                 <?php  }?>
                              </select>
                              <input type="hidden" name="tab2" id="tab2" value="tab2">
                           </div>
                        </div>
                        
                        <div class="col-md-2">
                           <div class="position-relative form-group">
                                 <label>Product Category</label>
                                 <select id="product_category_id2" name="product_category2" onchange="get_product('2',this.value)" class="form-control">
                                    <option value="">Product Category</option>
                                    <option value="All" <?php echo ($product_category2 == 'All') ? 'selected' : ''; ?>>All</option>
                                    <?php foreach($category_master as $category) {?>       
                                       <option value="<?php echo $category->product_category_id; ?>" <?php if($product_category2==$category->product_category_id){ echo "selected"; } ?>><?php echo $category->category_name; ?></option>     
                                    <?php  }?>
                                 </select>
                           </div>
                        </div>
                        
                        <div class="col-md-2">
                           <div class="position-relative form-group">
                                 <label>Product</label>
                                 <select id="product_id2" name="product2" onchange="get_model('2',this.value)" class="form-control">
                                    <option value="">Product</option>
                                    <option value="All" <?php echo ($product2 == 'All') ? 'selected' : ''; ?>>All</option>
                                    <?php foreach($product_master as $model){ ?>
                                       <option value="<?php echo $model->product_id; ?>" <?php if($product2==$model->product_id){ echo "selected"; } ?>><?php echo $model->product_name; ?></option>
                                    <?php }?>
                                 </select>
                           </div>
                        </div>

                        <div class="col-md-2">
                           <div class="position-relative form-group">
                                 <label for="exampleAddress" class="">Model No.<font color="red">*</font></label>
                                 <select id="model_id2" name="model2" onchange="get_partcode('2',this.value)" class="form-control">
                                    <option value="">Model No.</option>
                                    <option value="All" <?php echo ($model_id2 == 'All') ? 'selected' : ''; ?>>All</option>
                                    <?php foreach($model_master as $model){ ?>
                                       <option value="<?php echo $model->model_id; ?>" <?php if($model_id2==$model->model_id){ echo "selected"; } ?>><?php echo $model->model_name; ?></option>
                                    <?php }?>
                                 </select>
                           </div>
                        </div>

                        <div class="col-md-2">
                           <div class="position-relative form-group"><label for="examplePassword11" class="">Part Code</label>
                                 <select  id="part_code2" name="part_code2" class="form-control">
                                    <option value="">Select</option>
                                 </select>
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="position-relative form-group">
                              <label for="exampleEmail11" class="">Ticket No.</label>
                              <input name="ticket_no2" id="ticket_no" placeholder="Ticket No." type="text" value="<?php echo $ticket_no2; ?>" class="form-control" >
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="position-relative form-group">
                              <label for="exampleEmail11" class="">System Sr. No.</label>
                              <input name="sr_no2" id="sr_no" placeholder="System Sr. No." type="text" value="<?php echo $sr_no2; ?>" class="form-control" >
                           </div>
                        </div>
                        
                        <div class="col-md-2">
                           <div class="position-relative form-group">
                              <label for="exampleEmail11" class="">From Date</label>
                              <input name="from_date2" id="from_date2" placeholder="From" type="text" value="<?php echo $from_date2; ?>" class="form-control datepicker" >
                           </div>
                        </div>
                        <div class="col-md-2">
                           <div class="position-relative form-group">
                              <label for="examplePassword11" class="">To Date</label>
                              <input name="to_date2" id="to_date2" placeholder="To" type="text" value="<?php echo $to_date2; ?>" class="form-control datepicker" >
                           </div>
                        </div>
                       
                        <div class="col-md-8">
                           <div class="position-relative form-group">                         
                              <input type="submit"  class="btn btn-success btn-grad" data-original-title="" title="" value="Search" >
                              &nbsp;<a href="{{route('home')}}" class="btn btn-danger btn-grad" data-original-title="" title="">Exit</a>
                           </div>
                        </div>
                     </div>
                  </form>
                    
               </div>
               <div class="card-body">
                  <h5 class="card-title">Challan Details</h5>
                  <table id="table2" class="table table-striped table-bordered" style="width:100%">
                     <thead>
                        <tr>
                           <th>Sr.</th>
                           <th>Date Of Challan</th>
                           <th>Challan No.</th>
                           <th>Brand</th>
                           <th>Ticket No.</th>
                           <th>Job No.</th>
                           <th>Center</th>
                           <th>State</th>
                           <th>Pin Code</th>
                           <th>Parts</th>
                           <th>Qty</th>
                           <th>Grand Total</th>
                           <th>Invoice No</th>
                           <th>Eway Bill</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php $srno = 1;
                           foreach($DataArr2_view as $record)
                           {
                               #print_r($record);
                               echo '<tr>';
                                   echo '<td>';
                                   # echo '<input type="checkbox" name="case[]" value="'.$record->TagId.'" />';
                                   echo $srno++.'</td>';
                                   echo '<td>'.date('d-m-Y',strtotime($record->create_date)).'</td>';
                                   echo '<td>';
                                   # echo '<a href="tax-invoice?TagId='.$record->TagId.'">'.$record->challan_no.'</a>&nbsp;';
                                   echo $record->po_no;
                                   echo '</td>';
                                   echo '<td>'.$record->brand_name.'</td>';
                                   echo '<td>'.$record->ticket_no.'</td>';
                                   echo '<td>'.$record->job_no.'</td>';
                                   echo '<td>'.$record->asc_name.'</td>';
                                   echo '<td>'.$record->state_name.'</td>';
                                   echo '<td>'.$record->pincode.'</td>';
                                   echo '<td>'.$record->req_qty.'</td>';
                                   echo '<td>'.$record->dispatch_qty.'</td>';
                                    //   echo '<td>'.$record->total.'</td>';
                                    //   echo '<td>'.$record->gst_amount.'</td>';
                                   echo '<td>'.$record->net_bill.'</td>';
                                   echo '<td>'.$record->invoice_no.'</td>';
                                   echo '<td>'.$record->eway_no.'</td>';
                                   echo '<td>';
                                  
                                   echo '<a href="generate-Challan-sc?invoice_id='.$record->invoice_id.'">View </a> ||';
                                   echo ' <a href="download-Challan-sc?invoice_id='.$record->invoice_id.'">PDF </a>';
                                   echo '</td>';
                               echo '</tr>';
                           }
                           foreach($manual_challan_arr as $manual)
                           {
                              echo '<tr>';
                                   echo '<td>';
                                   # echo '<input type="checkbox" name="case[]" value="'.$record->TagId.'" />';
                                   echo $srno++.'</td>';
                                   echo '<td>'.date('d-m-Y',strtotime($manual->created_at)).'</td>';
                                   echo '<td>';
                                   # echo '<a href="tax-invoice?TagId='.$record->TagId.'">'.$record->challan_no.'</a>&nbsp;';
                                   echo $manual->challan_no;
                                   echo '</td>';
                                   echo '<td>'.$manual->brand_name.'</td>';
                                   echo '<td>'.$manual->ticket_number.'</td>';
                                   echo '<td>'.$manual->job_number.'</td>';
                                   echo '<td>'.$manual->center_name.'</td>';
                                   echo '<td>'.$manual->state_name.'</td>';
                                   echo '<td>'.$manual->pincode.'</td>';
                                   echo '<td>'.$manual->issue_qty.'</td>';
                                   echo '<td>'.$manual->dispatch_qty.'</td>';
                                    //   echo '<td>'.$record->total.'</td>';
                                    //   echo '<td>'.$record->gst_amount.'</td>';
                                   echo '<td>'.$manual->grand_total.'</td>';
                                   echo '<td>'.$manual->invoice_no.'</td>';
                                   echo '<td>'.$manual->eway_bill.'</td>';
                                   echo '<td>';
                                  
                                   #echo '<a href="view-manual-Challan?invoice_id='.$manual->id.'">View </a> ||';
                                   echo ' <a href="view-manual-Challan?invoice_id='.$manual->id.'">PDF </a>';
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
<script>
    $('#table_id').DataTable( );

   function updateUrl(part, srno) {
      var link = document.getElementById('challan_link_'+srno);
      link.href = "download-Challan?challan_no=<?php echo $new_challan; ?>&selected_part=" + part;
   }
</script>
@endsection
