@extends('layouts.app')
@section('content')

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

</script>

<div class="app-main">
<div class="app-main__outer">
   <div class="app-main__inner">
      <ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
         <li class="nav-item">
            <a role="tab" class="nav-link active" id="tab-0" data-toggle="tab" href="#tab-content-0">
            <span>View</span>
            </a>
         </li>
         <li class="nav-item">
            <a role="tab" class="nav-link" id="tab-1" data-toggle="tab" href="#tab-content-1">
            <span>Generate</span>
            </a>
         </li>
      </ul>
      <div class="tab-content">
         <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
            <div class="main-card mb-3 card">
               <div class="card-body">
                  <h5 class="card-title">View Challan</h5>
                  @if(Session::has('message'))
                  <h5><font color="green"> {{ Session::get('message') }}</font></h5>
                  @endif
                  @if(Session::has('error'))
                  <h5><font color="red"> {{ Session::get('error') }}</font></h5>
                  @endif
                  <form method="get" action="{{route('search-challan')}}" class="form-horizontal">
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
                                    <?php foreach($product_master as $model){ ?>
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
                                    <option value="All" <?php echo ($model_id == 'All') ? 'selected' : ''; ?>>All</option>
                                    <?php foreach($model_master as $model){ ?>
                                       <option value="<?php echo $model->model_id; ?>" <?php if($model_id==$model->model_id){ echo "selected"; } ?>><?php echo $model->model_name; ?></option>
                                    <?php }?>
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
                              <input class="form-control" type="text" id="asc_code" name="asc_code" value="<?php echo $asc_code;?>" data-original-title="Mobile No." data-placement="top" placeholder="ASC Code">
                           </div>
                        </div> -->
                        <!-- <div class="col-md-2">
                           <div class="position-relative form-group">
                              <label for="examplePassword11" class=""> Challan no.</label>
                              <input class="form-control" type="text" id="challan_no" name="challan_no" value="<?php //echo $challan_no;?>" data-original-title="Mobile No." data-placement="top" placeholder="Challan No.">
                           </div>
                        </div> -->
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
                           <!-- <th>Total</th>
                           <th>Tax</th> -->
                           <th>Grand Total</th>
                           <th>Invoice No</th>
                           <th>Eway Bill</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php $srno = 1;
                           foreach($DataArr as $record)
                           {
                               echo '<tr>';
                                   echo '<td>';
                                   # echo '<input type="checkbox" name="case[]" value="'.$record->TagId.'" />';
                                   echo $srno++.'</td>';
                                   echo '<td>'.date('d-m-Y',strtotime($record->create_date)).'</td>';
                                   echo '<td>';
                                   # echo '<a href="tax-invoice?TagId='.$record->TagId.'">'.$record->challan_no.'</a>&nbsp;';
                                   echo $record->challan_no;
                                   echo '</td>';
                                   echo '<td>'.$record->brand_name.'</td>';
                                   echo '<td></td>';
                                   echo '<td></td>';
                                   echo '<td>'.$record->center_name.'</td>';
                                   echo '<td>'.$record->state.'</td>';
                                   echo '<td>'.$record->pincode.'</td>';
                                   echo '<td>'.$record->part_required.'</td>';
                                   echo '<td>'.$record->qty.'</td>';
                                 //   echo '<td>'.$record->total.'</td>';
                                 //   echo '<td>'.$record->total_tax.'</td>';
                                   echo '<td>'.$record->net_total.'</td>';
                                   echo '<td>';
                                   echo '<a href="generate-Challan?approve_id='.$record->approve_id.'">View </a> ||';
                                   echo ' <a href="download-Challan?approve_id='.$record->approve_id.'">PDF </a>';
                                   echo '</td>';
                               echo '</tr>';
                           }
                           foreach($DataArr2 as $record)
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
                                   echo '<td>'.$record->eway_bill_no.'</td>';
                                   echo '<td>';
                                   echo '<a href="generate-Challan?invoice_id='.$record->invoice_id.'">View </a> ||';
                                   echo ' <a href="download-Challan?invoice_id='.$record->invoice_id.'">PDF </a>';
                                   echo '</td>';
                               echo '</tr>';
                           }
                           ?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
         <div class="tab-pane tabs-animation fade" id="tab-content-1" role="tabpanel">
            <div class="main-card mb-3 card">
               <div class="card-body">
                  <h5 class="card-title"></h5>
                  @if(Session::has('message'))
                  <h5><font color="green"> {{ Session::get('message') }}</font></h5>
                  @endif
                  @if(Session::has('error'))
                  <h5><font color="red"> {{ Session::get('error') }}</font></h5>
                  @endif
                    
               </div>
               <div class="card-body">
                  <h5 class="card-title">Generate Challan</h5>
                  <table id="table1" class="table table-striped table-bordered" style="width:100%">
                     <thead>
                        <tr>
                           <th>Sr.</th>
                           <th>Brand</th>
                           <th>Challan No.</th>
                           <th>Parts</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php $srno = 1;
                           foreach($brand_arr as $record)
                           {  
                              echo '<tr>';
                                 echo '<td>';
                                 # echo '<input type="checkbox" name="case[]" value="'.$record->TagId.'" />';
                                 echo ($srno + 1).'</td>';
                                 echo '<td>'.$record['brand_name'].'</td>';
                                 echo '<td>';
                                 # echo '<a href="tax-invoice?TagId='.$record->TagId.'">'.$record->challan_no.'</a>&nbsp;';
                                 $brand_ser_name = strtoupper(substr($record['brand_name'], 0, 2));
                                 $new_challan = $brand_ser_name.'-'.$new_challan_no;
                                 echo $new_challan;
                                 echo '</td>';
                                 echo '<td>';
                                    echo '<select name="selected_parts['.$srno.']" onchange="updateUrl(this.value, '.$srno.')">';
                                    echo '<option value="1">Doc 1</option>';
                                    echo '<option value="2">Doc 2</option>'; 
                                    echo '<option value="3">Doc 3</option>'; 
                                    echo '<option value="4">Doc 4</option>'; 
                                    echo '<option value="5">Doc 5</option>'; 
                                   
                                 echo '</select>';
                                 
                                 echo '<td>';
                                 
                                 #echo ' <a href="download-Challan?challan_no='.$new_challan.'" onclick="window.location.reload();">Generate Challan</a>';
                                 echo '<a id="challan_link_'.$srno.'" href="download-Challan?challan_no='.$new_challan.'&selected_part=1">Generate Challan</a>';
                                 # echo '<a href="download-Challan?challan_no='.$new_challan.'" onclick="setTimeout(function(){ location.reload(); }, 100);">Generate Challan</a>';
                                 echo '</td>';
                              echo '</tr>';
                              $srno++;
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
