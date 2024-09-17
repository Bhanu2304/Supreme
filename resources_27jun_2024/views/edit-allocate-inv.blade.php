@extends('layouts.app')

@section('content')

<script>
                                    

menu_select('{{$url}}');                                                             
</script>

<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title">Edit Inventory Allocation</h5>
                            
                            
                            @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                            <form id="spare_form" method="post" action="update-allocate-inv" >
                                <div class="form-row">
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Part Name <font color="red">*</font></label>
                                            <select id="part_name" name="part_name" class="form-control" onchange="get_partno(this.value)" required="">
                                                <option value="">Select</option>
                                                <?php
                                                        foreach($part_arr as $part)
                                                        {
                                                            ?>       <option value="<?php echo $part->part_name; ?>"
                                                                    <?php if($part->part_name==$data['part_name']) { echo 'selected';} ?>
                                                                    ><?php echo $part->part_name; ?></option>     
                                                <?php   }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Part No. <font color="red">*</font></label>
                                            <select id="part_no" name="part_no" onchange="get_hsn_code(this.value)" class="form-control" required="">
                                                <option value="">Select</option>
                                                <?php
                                                        foreach($part_no_arr as $part_no)
                                                        {
                                                            ?>       <option value="<?php echo $part_no->part_no; ?>"
                                                                    <?php if($part_no->part_no==$data['part_no']) { echo 'selected';} ?>
                                                                    ><?php echo $part_no->part_no; ?></option>     
                                                <?php   }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">HSN Code <font color="red">*</font></label>
                                            <select id="hsn_code" name="hsn_code" class="form-control" required="">
                                                <option value="">Select</option>
                                                <?php
                                                        foreach($hsn_no_arr as $hsn)
                                                        {
                                                            ?>       <option value="<?php echo $hsn->hsn_code; ?>"
                                                                    <?php if($hsn->hsn_code==$data['hsn_code']) { echo 'selected';} ?>
                                                                    ><?php echo $hsn->hsn_code; ?></option>     
                                                <?php   }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                 <div class="form-row">
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Available Quantity <font color="red">*</font></label>
                                            <input name="stock_qty" id="stock_qty" value="<?php echo $stock_qty; ?>" placeholder="Stock Quantity" type="number" onkeypress="return checkNumber(this.value,event)" class="form-control" required="">
                                        </div>
                                    </div>
                                </div>                                
                                <div class="form-row">
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Allocate Quantity <font color="red">*</font></label>
                                            <input name="move_qty" id="move_qty" placeholder="Quantity" type="number" value="<?php echo $data['stock_qty']; ?>" onkeypress="return checkNumber(this.value,event)" class="form-control" required="">
                                        </div>
                                    </div>
                                </div>                                                                
                                <div class="form-row">
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Center <font color="red">*</font></label>
                                            <select id="center_id" name="center_id" class="form-control"  required="">
                                                <option value="">Select</option>
                                                <?php
                                                        foreach($center_arr as $center)
                                                        {
                                                            ?>       <option value="<?php echo $center->center_id; ?>"
                                                                    <?php if($center->center_id==$data['center_id']) { echo 'selected';} ?>
                                                                    ><?php echo $center->center_name; ?></option>     
                                                <?php   }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>  
                                
                                <input type="hidden" id="inv_id" name="inv_id" value="<?php echo $data['inv_id'];  ?>" />
                                <button type="submit"  class="mt-2 btn btn-primary">Update</button>
                                <a href="allocate-inv" class="mt-2 btn btn-danger"   title="Cancel">Cancel</a>
                            </form>
                                
                        </div>    
                           
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 <script>
    
    function get_mol(balance)
    {
        var mol = parseFloat(balance*1.5);
        document.getElementById('mol').value = mol;
    }
    
function form_toggle(first,second)
{
    $('#'+first).show();
    $('#'+second).hide();
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

function get_partno(part_name)
 {
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-part-no',
              method: 'post',
              data: {
                 part_name: part_name 
              },
              success: function(result){
                  $('#part_no').html(result)
              }});
 }

function get_hsn_code(part_no)
 {
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-hsn-code',
              method: 'post',
              data: {
                 part_no: part_no 
              },
              success: function(result){
                  $('#hsn_code').html(result)
              }});
 }
</script>

@endsection
