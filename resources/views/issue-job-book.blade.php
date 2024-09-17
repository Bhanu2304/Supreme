@extends('layouts.app')
@section('content')

<script>

menu_select('{{$url}}');
function reloadPage(){
    location.reload(true);
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

function get_pincode(state_id){
    
    $.post('vendor-get-pin',{state_id:state_id}, function(data){
        $('#pincode').html(data);
    }); 
     
}



function checkPinNumber(val,evt)
{    
   var charCode = (evt.which) ? evt.which : event.keyCode

   if(charCode> 31 && (charCode < 48 || charCode > 57)  || (val=='e' || val.length>=6))
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

function get_state(region_id)
{
   $.ajaxSetup({
      headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
      }
   });
   jQuery.ajax({
      url: 'get-state-by-region',
      method: 'post',
      data: {
            region_id: region_id,

      },
      success: function(result){
         $('#state_id').html(result);
      }
   });
}


function get_asc(state_id)
{
   $.ajaxSetup({
      headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
      }
   });
   jQuery.ajax({
      url: 'get-asc-name-by-state',
      method: 'post',
      data: {
         state_id: state_id,

      },
      success: function(result){
         $('#center_id').html(result);
      }
   });
}




</script>

<div class="app-main">
<div class="app-main__outer">
   <div class="app-main__inner">
      
      <div class="tab-content">
         <div class="tab-pane tabs-animation fade show active" id="tab-content-1" role="tabpanel">
            <div class="main-card mb-3 card">
               
               <div class="card-body">
               <h5 class="card-title">Issue Job Sheet</h5>
               @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
               <div id="Product_Details" class="tabFormcontent">
                  <form action="issue-job-book" method="post">
                        <div class="form-row">
                        
                           <div class="col-md-4">
                              <div class="position-relative form-group"><label>Brand<span style="color: #f00;">*</span></label>
                                 <select name="Brand" id="Brand" class="form-control" onchange="get_product_category('',this.value)" required>
                                    <option value="">Select</option>
                                    <?php foreach($brand_arr as $brand){
                                          echo '<option value="'.$brand['brand_id'].'">'.$brand['brand_name'].'</option>';
                                    }?>
                              </select>
                              </div>
                           </div>

                           <div class="col-md-4">
                              <div class="position-relative form-group"><label>Zone<span style="color: #f00;">*</span></label>
                                 <select  id="region_id" name="region_id" class="form-control" required="" onchange="get_state(this.value)">
                                       <option value="">Select</option>
                                       <?php foreach($region_master as $region){
                                          echo '<option value="'.$region['region_id'].'">'.$region['region_name'].'</option>';
                                       }?>
                                 </select>
                              </div>
                           </div>

                           <div class="col-md-4">
                              <div class="position-relative form-group"><label>State<span style="color: #f00;">*</span></label>
                                 <select id="state_id" name="state_id" class="form-control" onchange="get_asc(this.value)" required>
                                    <option value="">Select</option>
                                 </select>
                              </div>
                           </div>

                           <div class="col-md-4">
                              <div class="position-relative form-group"><label>ASC Name<span style="color: #f00;">*</span></label>
                                 <select id="center_id" name="center_id" class="form-control" required>
                                    <option value="">Select</option>
                                 </select>
                              </div>
                           </div>
                           <div class="col-md-4">
                              <div class="position-relative form-group">
                                 <label>Remarks<span style="color: #f00;">*</span></label>
                                 <!-- <input  name="remarks" id="remarks" placeholder="Remarks" type="text" class="form-control" > -->
                                 <textarea name="remarks" id="remarks" class="form-control" placeholder="Remarks" required></textarea>
                              </div>
                           </div>

                           

                     </div>
                     <div class="form-row">
                           <div class="col-md-4"></div>
                           <div class="col-md-4">
                              <div class="position-relative form-group">
                                 <input type="submit" class="mt-2 btn btn-success" value="Save">
                                 <!-- <button type="button" style="float:right;" onclick="openTab1( 'Product_Details','Estimated_Cost');" class="mt-2 btn btn-success" >Save</button> -->
                              </div>
                           </div>
                     </div>
                  </form>
                  </div>
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
