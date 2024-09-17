@extends('layouts.app')

@section('content') 

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

<?php $tab = Session::get('tab'); ?>

<div class="app-main"> 
    <div class="app-main__outer">
        <div class="app-main__inner">
        
     <div class="tab-content">
         @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
         @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
        
        <div class="tab-pane tabs-animation fade show active " id="tab-content-1" role="tabpanel">
            <div class="main-card mb-3 card">
                <form method="post" action="save-issue-without-po" >
                    <div class="card-body">                                        
                        <h5 class="card-title">Issue Part Without PO</h5> <h5 class="card-title">Date : <?php echo date('d-m-y'); ?></h5> 
                                 
                                <div class="form-row">

                                    
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">PO Type <font color="red">*</font></label>
                                            <select id="po_type" name="po_type" class="form-control" required="">
                                                <option value="">PO Type</option>
                                                <option value="FOC">FOC</option>
                                                <option value="Paid">Paid</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Job No. </label>
                                            <input type="text" id="job_no" name="job_no" class="form-control" >
                                                
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Service Center<font color="red">*</font></label>
                                            <select id="asc_code" name="asc_code" class="form-control" required="">
                                                <option value="">Center Name</option>
                                                <?php if(Session::get('UserType')=='Admin'){ echo '<option value="All">All</option>'; }?>
                                                
                                                @foreach($asc_master as $asc)
                                                        <option value="{{$asc->center_id}}" <?php if( $asc_code==$asc->center_id) 
                                                            { echo 'selected';} ?>>{{$asc->center_name}} {{$asc->asc_code}} {{$asc->city}} {{$asc->state_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Brand <font color="red">*</font></label>
                                            <select id="brand_id1" name="brand" onchange="get_product_detail('1',this.value)" class="form-control" required="">
                                                <option value="">Brand</option>
                                                <?php foreach($brand_arr as $brand)  { ?>      
                                                    <option value="<?php echo $brand['brand_id']; ?>"><?php echo $brand['brand_name']; ?></option>
                                                <?php  } ?>
                                            </select>
                                        </div>
                                    </div>
                                
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Product <font color="red">*</font></label>
                                            <select id="product_category_id1" name="product_category" onchange="get_product('1',this.value)" class="form-control" required="">
                                                <option value="">Product </option>
                                                
                                            </select>
                                        </div>
                                        
                                    </div>
                                
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Model <font color="red">*</font></label>
                                            <select id="product_id1" name="product" onchange="get_model('1',this.value)" class="form-control" required="">
                                                <option value="">Model</option>
                                                
                                            </select>
                                        </div>
                                    </div>
                                
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Model No.<font color="red">*</font></label>
                                            <select id="model_id1" name="model" onchange="get_partcode('1',this.value)" class="form-control" required="">
                                                <option value="">Model No.</option>
                                            </select>
                                        </div>
                                    </div>
                                
                                    <div class="col-md-2">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Part Code <font color="red">*</font></label>
                                            <select id="part_code1" name="part_code" class="form-control" onchange="get_srno('1',this.value);get_hsn_code('1',this.value);" >
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">HSN Code</label>
                                        <select id="hsn_code1" name="hsn_code"  class="form-control">
                                            <option value="">Select</option>
                                        </select>    
                                    </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="position-relative form-group"><label for="examplePassword11" class="">Sr. No. <font color="red">*</font></label>
                                            <select id="srno1" name="srno" class="form-control"  onchange="get_part_detail(this.value);">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                            
                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Color </label>
                                        <input type="text" id="color" name="color" placeholder="Color"  class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Customer Amount </label>
                                        <input type="text"  onkeypress="return checkNumber(this.value,event)" id="customer_amount" name="customer_amount" placeholder="Customer Amount"  class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">ASC Amount </label>
                                        <input type="text" onblur="get_total('')" onkeypress="return checkNumber(this.value,event)" id="asc_amount" name="asc_amount" placeholder="ASC Amount"  class="form-control">
                                    </div>
                                </div>
                                  
                                    <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Discount </label>
                                        <input type="text" onblur="get_total('')" onkeypress="return checkNumber(this.value,event)" id="discount" name="discount" placeholder="Discount"  class="form-control">
                                    </div>
                                </div> 
                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Total </label>
                                        <input type="text"  id="total" name="total" placeholder="Total"  class="form-control" readonly="">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">GST </label>
                                        <input type="text" onblur="get_total('')" onkeypress="return checkNumber(this.value,event)" id="gst" name="gst" placeholder="GST"  class="form-control">
                                    </div>
                                </div>     
                                <div class="col-md-2">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Net Total </label>
                                        <input type="text"  id="net_total" name="net_total" placeholder="Net Total"  class="form-control" readonly="">
                                    </div>
                                </div>    
                            </div>
                                                      
                        <div class="form-row">
                                <div class="col-md-6">
                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Remarks <font color="red">*</font></label>
                                        <textarea id="remarks" name="remarks" placeholder="Remarks" required="" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>

                        <div class="form-row">
                            <div class="col-md-2">
                                <div class="position-relative form-group">
                                    <a href="dashboard"  class="mt-2 btn btn-danger">Back</a>
                                     <button type="submit"  class="mt-2 btn btn-primary" >Issue</button>
                                </div>
                            </div>
                        </div>       
                    </div>                                      



                    <div class="app-wrapper-footer">
                        <div class="app-footer">

                        </div>   
                    </div>
                </form>
        </div>
     </div>
         
    </div>
        </div>
    </div>  
</div> 

<script>
    
    function get_total(div_id)
    {
        
        var asc_amount = $('#asc_amount'+div_id).val();
        var gst = $('#gst'+div_id).val();
        var discount = $('#discount'+div_id).val();
        var disc = discount*asc_amount/100;
        var total = asc_amount-disc;
        $('#total'+div_id).val(total);
        var tax = gst*total/100;
        var net_total = total + tax;
        $('#net_total'+div_id).val(net_total);
    }
    
    
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
                 model_id: model_id
                  
              },
              success: function(result){
                  $('#part_code'+div_id).html(result);
              }});
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
              url: 'get-part-rate-sc',
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
    
    
 function get_srno(div_id,spare_id)
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
              url: 'get-sr-no',
              method: 'post',
              data: {
                 brand_id: brand_id,
                 product_category_id:product_category_id,
                 product_id:product_id,
                 model_id: model_id,
                 spare_id:spare_id 
              },
              success: function(result){
                  $('#srno'+div_id).html(result);
              }});
     
        
    
 }

 
 function get_part_detail(sr_no)
 {

     var part_code = $('#part_code1').val();     
     
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-part-detail-using-srno',
              method: 'post',
              data: {
                 sr_no: sr_no,
                 part_code:part_code 
              },
              success: function(result){
                $('#color').val(result.item_color);
                $('#asc_amount').val(result.asc_amount);
                $('#customer_amount').val(result.customer_amount);
              }});
     
        
    
 }
 
 function del_part(del_div)
 {
     $('#'+del_div).remove();
 }
 
 
</script>

@endsection
