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

function toggleAscName() 
{
   var partyType = $('#party_type').val();
   if (partyType === 'Asc') {
      $('#asc_name_div').show();
   } else {
      $('#asc_name_div').hide();
   }
}

   function calculateGrandTotal() {
        var issueQty = parseFloat($('#issue_qty').val()) || 0;
        var rate = parseFloat($('#rate').val()) || 0;
        var gstPercentage = parseFloat($('#gst').val()) || 0;
        var subtotal = issueQty * rate;
        var gstAmount = (subtotal * gstPercentage) / 100;
        var total = subtotal + gstAmount;
        $('#grand_total').val(total.toFixed(2));
    }

    function calculateTotal() {
        var issueQty = parseFloat($('#issue_qty').val()) || 0;
        var rate = parseFloat($('#rate').val()) || 0;

        var subtotal = issueQty * rate;

        $('#total').val(subtotal.toFixed(2));
    }

    $(document).ready(function() {
        $('#issue_qty, #rate, #gst').on('input', function() {
            calculateGrandTotal();
            calculateTotal();
        });
    });


    function center_detail(center_id)
   {
      $.ajaxSetup({
               headers: {
                     'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                  }
            });
      jQuery.ajax({
               url: 'get-center-detail',
               method: 'post',
               data: {
                  center_id: center_id
               },
               success: function(result){
                  
                     $('#to').val(result);
               }});
   }    
</script>

<div class="app-main">
<div class="app-main__outer">
   <div class="app-main__inner">
      
      <div class="tab-content">
         <div class="tab-pane tabs-animation fade show active" id="tab-content-1" role="tabpanel">
            <div class="main-card mb-3 card">
               
               <div class="card-body">
               <h5 class="card-title">Generate Challan</h5>
               @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
               <div id="Product_Details" class="tabFormcontent">
               <form action="generate-manual-challan" method="post">
                        <div class="form-row">
                        
                           <div class="col-md-4">
                              <div class="position-relative form-group"><label>Brand<span style="color: #f00;">*</span></label>
                                 <select name="Brand" id="Brand" class="form-control" onchange="get_product_category('',this.value)" required>
                                    <option value="">Select</option>
                                    <?php foreach($brand_master as $brand_id=>$brand_name){
                                          echo '<option value="'.$brand_id.'">'.$brand_name.'</option>';
                                    }?>
                              </select>
                              </div>
                           </div>

                           <div class="col-md-4">
                              <div class="position-relative form-group"><label>Party Type <span style="color: #f00;">*</span></label>
                                 <select  id="party_type" name="party_type" class="form-control" required="" onchange="toggleAscName()">
                                       <option value="">Select</option>
                                       <option value="Asc">Asc</option>
                                       <option value="Others">Others</option>
                                 </select>
                              </div>
                           </div>

                           <div class="col-md-4" id="asc_name_div" style="display: none;">
                                 <div class="position-relative form-group"><label>Asc Name<span style="color: #f00;">*</span></label>
                                    <select id="asc_code" name="asc_code" class="form-control" onchange="center_detail(this.value)">
                                       <option value="">Select</option>
                                       @foreach($asc_master as $asc)
                                             <option value="{{$asc->center_id}}" <?php if( $asc_code==$asc->center_id) 
                                                { echo 'selected';} ?>>{{$asc->center_name}} - {{$asc->asc_code}}</option>
                                       @endforeach
                                    </select>
                                 </div>
                           </div>

                           <div class="col-md-4">
                              <div class="position-relative form-group"><label>To<span style="color: #f00;">*</span></label>
                                 <input  name="to" id="to" placeholder="(Party Name,Address,Phone No.)" type="text" class="form-control" required>
                              </div>
                           </div>

                           <div class="col-md-4">
                              <div class="position-relative form-group"><label>Contact Person Name<span style="color: #f00;">*</span></label>
                                 <input form="save_tagging_cl" name="Customer_Name" id="Customer_Name_CL" placeholder="Contact Person" type="text" class="form-control" required>
                              </div>
                           </div>

                           <div class="col-md-4">
                              <div class="position-relative form-group"><label for="examplePassword11" class="">Contact No.<span style="color: #f00;">*</span></label>
                                    <input form="save_tagging_cl" name="Contact_No" id="Contact_No_CL" placeholder="Contact No." type="text" class="form-control" onkeypress="return checkNumber(this.value,event)" required=""  maxlength="10">
                                    <span class="error" id="errornameccl" style="color:red"></span>
                              </div>
                           </div>

                           <div class="col-md-4">
                              <div class="position-relative form-group"><label>Item Description</label>
                              <input  name="man_ser_no" id="man_ser_no" placeholder="Item Description" type="text" class="form-control" ></div>
                           </div>

                           <div class="col-md-4">
                              <div class="position-relative form-group"><label>Spare Part Name<span style="color: #f00;">*</span></label>
                              <input  name="part_name" id="part_name" placeholder="Spare Part Name" type="text" class="form-control" ></div>
                           </div>

                           <div class="col-md-4">
                              <div class="position-relative form-group"><label>Spare Part Number <span style="color: #f00;">*</span></label>
                              <input  name="part_number" id="part_number" placeholder="Spare Part Number" type="text" class="form-control" ></div>
                           </div>

                           <div class="col-md-4">
                              <div class="position-relative form-group"><label>Ticket Number <span style="color: #f00;">*</span></label>
                              <input  name="ticket_number" id="ticket_number" placeholder="Ticket Number" type="text" class="form-control" ></div>
                           </div>

                           <div class="col-md-4">
                              <div class="position-relative form-group"><label>Job Number <span style="color: #f00;">*</span></label>
                              <input  name="job_number" id="job_number" placeholder="Job Number" type="text" class="form-control" ></div>
                           </div>

                           <div class="col-md-4">
                              <div class="position-relative form-group"><label>System Sr. No. <span style="color: #f00;">*</span></label>
                              <input  name="sr_no" id="sr_no" placeholder="System Sr. No." type="text" class="form-control" ></div>
                           </div>


                           <div class="col-md-4">
                              <div class="position-relative form-group"><label>Type Of Part<span style="color: #f00;">*</span></label>
                                 <select  id="type_of_part" name="type_of_part" class="form-control"  required="">
                                       <option value="">Select</option>
                                       <option value="Fresh">Fresh</option>
                                       <option value="Defective">Defective</option>
                                 </select>
                              </div>
                           </div>

                           <div class="col-md-4">
                              <div class="position-relative form-group"><label>Issue Qty<span style="color: #f00;">*</span></label>
                              <input  name="issue_qty" id="issue_qty" placeholder="Issue Qty" type="text" class="form-control" ></div>
                           </div>

                           <div class="col-md-4">
                              <div class="position-relative form-group"><label>Rate<span style="color: #f00;">*</span></label>
                              <input  name="rate" id="rate" placeholder="Rate" type="text" class="form-control" ></div>
                           </div>

                           <div class="col-md-4">
                              <div class="position-relative form-group"><label>Total<span style="color: #f00;">*</span></label>
                              <input  name="total" id="total" placeholder="Total" type="text" class="form-control" ></div>
                           </div>

                           <div class="col-md-4">
                              <div class="position-relative form-group"><label>Gst %<span style="color: #f00;">*</span></label>
                              <input  name="gst" id="gst" placeholder="Gst %" type="text" class="form-control" ></div>
                           </div>

                           

                           <div class="col-md-4">
                              <div class="position-relative form-group"><label>Grand Total<span style="color: #f00;">*</span></label>
                              <input  name="grand_total" id="grand_total" placeholder="Grand Total" type="text" class="form-control" ></div>
                           </div>

                           <div class="col-md-4">
                              <div class="position-relative form-group"><label>Eway Bill No.<span style="color: #f00;">*</span></label>
                              <input  name="eway_bill" id="eway_bill" placeholder="Eway Bill No." type="text" class="form-control" ></div>
                           </div>

                           <div class="col-md-4">
                              <div class="position-relative form-group"><label>Remarks<span style="color: #f00;">*</span></label>
                              <input  name="remarks" id="remarks" placeholder="Remarks" type="text" class="form-control" ></div>
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
