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
            <a role="tab" class="nav-link active" id="tab-1" data-toggle="tab" href="#tab-content-1">
            <span>Generate</span>
            </a>
         </li>
      </ul>
      <div class="tab-content">
         <div class="tab-pane tabs-animation fade show active" id="tab-content-1" role="tabpanel">
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
                           <th>Asc Name</th>
                           <th>Part Name</th>
                           <th>Hsn Code</th>
                           <th>Qty</th>
                           <th>Amount</th>
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
                                    echo '<select name="center_id" id="center_id">';
                                    
                                    foreach($asc_master as $asc){ ?>
                                          <option value="<?php echo $asc->center_id ; ?>" <?php if( $asc_code==$asc->center_id) 
                                             { echo 'selected';} ?>><?php echo $asc->center_name ; ?></option>
                                    <?php }
                                   
                                   
                                 echo '</select></td>';
                                 echo '<td>';
                                   echo '<input type="text" name="part_name" id="part_name" placeholder="Part Name">';
                                 echo '</td>';

                                 echo '<td>';
                                   echo '<input type="text" name="hsn_code" id="hsn_code" placeholder="Hsn Code">';
                                 echo '</td>';

                                 echo '<td>';
                                   echo '<input type="text" name="qty" id="qty" placeholder="Qty">';
                                 echo '</td>';

                                 echo '<td>';
                                   echo '<input type="text" name="amount" id="amount" placeholder="Amount">';
                                 echo '</td>';
                                 
                                 echo '<td>';
                                 
                                 #echo ' <a href="download-Challan?challan_no='.$new_challan.'" onclick="window.location.reload();">Generate Challan</a>';
                                 echo '<a id="challan_link_'.$srno.'" href="#" onclick="generateChallanUrl(event, \'' . $new_challan . '\')">Generate Challan</a>';
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

   function generateChallanUrl(event, challanNo) {
      event.preventDefault();

      //var challanNo = '<?php echo $new_challan; ?>';
      var centerId = $('#center_id').val();
      var partName = $('#part_name').val();
      var hsnCode = $('#hsn_code').val();
      var qty = $('#qty').val();
      var amount = $('#amount').val();


      var url = 'download-Challan?challan_no=' + challanNo +
                  '&center_id=' + centerId +
                  '&part_name=' + encodeURIComponent(partName) +
                  '&hsn_code=' + encodeURIComponent(hsnCode) +
                  '&qty=' + encodeURIComponent(qty) +
                  '&amount=' + encodeURIComponent(amount);

                  // console.log(url);
                  // return false;
      window.location.href = url;
   }
</script>
@endsection
