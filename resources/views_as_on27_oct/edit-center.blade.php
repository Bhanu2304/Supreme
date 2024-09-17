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
                        <div class="card-body"><h5 class="card-title">Edit Service Center</h5>
                            
                            <form method="post" class="needs-validation" action="update-centre" id="center_form" >
                                @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                 
                                 <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="center_name" class="">Center Name <font color="red">*</font></label>
                                            <input name="center_name" id="center_name" placeholder="Center Name" type="center_name" value="<?php echo $data_sc->center_name; ?>" class="form-control" required="">
                                            <div class="invalid-tooltip">
                                                Please Fill Center Name.
                                            </div>
                                        </div>
                                    </div>
                                     <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="person_name" class="">Person Name <font color="red">*</font></label>
                                            <input name="person_name" id="person_name" placeholder="Person Name" type="text" class="form-control" value="<?php echo $data_sc->person_name; ?>" required="">
                                            <div class="invalid-tooltip">
                                                Please Fill Person Name.
                                            </div>
                                        </div>
                                    </div>
                                
                                 
                                
                                 
                                
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="contact_no" class="">Contact No. <font color="red">*</font></label>
                                            <input name="contact_no" id="contact_no" placeholder="Contact No." type="text" class="form-control" value="<?php echo $data_sc->contact_no; ?>" onkeypress="return checkNumber(this.value,event)" required="">
                                            <div class="invalid-tooltip">
                                                Please Fill Contact No.
                                            </div>
                                        </div>
                                    </div>
                                    
                                     <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Alternate No. 1</label>
                                            <input name="alt_no1" id="alt_no1" placeholder="Alternate No. 1" value="<?php echo $data_sc->alt_no1; ?>" type="text" class="form-control" onkeypress="return checkNumber(this.value,event)">
                                        </div>
                                    </div>
                                     <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Alternate No. 2</label>
                                            <input name="alt_no2" id="alt_no2" placeholder="Alternate No. 2" value="<?php echo $data_sc->alt_no2; ?>" type="text" class="form-control" onkeypress="return checkNumber(this.value,event)">
                                        </div>
                                    </div>
                                     <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Alternate No. 3</label>
                                            <input name="alt_no3" id="alt_no3" placeholder="Alternate No. 3" value="<?php echo $data_sc->alt_no3; ?>" type="text" class="form-control" onkeypress="return checkNumber(this.value,event)">
                                        </div>
                                    </div>
                                     
                                     <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="email_id" class="">Email <font color="red">*</font></label>
                                            <input name="email_id" id="email_id" placeholder="Email" type="email" class="form-control" value="<?php echo $data_sc->email_id; ?>" required="">
                                            <div class="invalid-tooltip">
                                                Please Fill Email Id.
                                            </div>
                                        </div>
                                    </div>
                                
                                 
                                 
                                
                                 

                                
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">New Password <font color="red">*</font></label>
                                            <input name="password" id="password" placeholder="Password" type="password" class="form-control" >
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="asc_code" class="">ASC Code <font color="red">*</font></label>
                                            <input name="asc_code" id="asc_code" placeholder="ASC Code" type="text" value="<?php echo $data_sc->asc_code; ?>" class="form-control" required="">
                                        </div>
                                    </div>
                                

                                
                                 
                                
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="region" class="">Region <font color="red">*</font></label>
                                            <select id="region" name="region" class="form-control" required="">
                                                <option value="">Region</option>
                                                <?php
                                                        foreach($region_master as $region)
                                                        {
                                                            ?>       <option value="<?php echo $region['region_id']; ?>" 
                                                                        <?php if($region['region_id']==$data_sc->region) { echo 'selected';} ?>
                                                                    ><?php echo $region['region_name']; ?></option>     
                                                <?php   }
                                                ?>
                                            </select>
                                            <div class="invalid-tooltip">
                                                Please Fill Region.
                                            </div>
                                        </div>
                                    </div>
                                     
                                     <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="State" class="">State  <font color="red">*</font></label>
                                            
                                            <select id="state" name="state" class="form-control" required="" onchange="get_district(this.value)">
                                                <option value="">State</option>
                                                <?php
                                                        foreach($state_master as $state)
                                                        {
                                                            ?>       <option value="<?php echo $state['state_id']; ?>"
                                                                    <?php if($state['state_id']==$data_sc->state) { echo 'selected';} ?>
                                                                    ><?php echo $state['state_name']; ?></option>     
                                                <?php   }
                                                ?>
                                            </select>
                                            <div class="invalid-tooltip">
                                                Please Fill State.
                                            </div>
                                        </div>
                                    </div>
                                     
                                     <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="dist_id" class="">District  <font color="red">*</font></label>
                                            <select name="dist_id" id="dist_id" class="form-control" onchange="get_pincode(this.value);" required="">
                                                <option value="">Select</option>
                                                <?php
                                                        foreach($district_master as $dist)
                                                        {
                                                            ?>       <option value="<?php echo $dist['dist_id']; ?>"
                                                                    <?php if($dist['dist_id']==$data_sc->dist_id) { echo 'selected';} ?>
                                                                    ><?php echo $dist['dist_name']; ?></option>     
                                                <?php   }
                                                ?>
                                            </select>
                                            <div class="invalid-tooltip">
                                                Please Fill District.
                                            </div>
                                        </div>
                                    </div>
                                     
                                     <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="address" class="">Address <font color="red">*</font></label>
                                            <input name="address" id="address" placeholder="Address" type="text" value="<?php echo $data_sc->address; ?>" class="form-control" required="">
                                            <div class="invalid-tooltip">
                                                Please Fill Address.
                                            </div>
                                        </div>
                                    </div>
                                
                                

                                
                                 
                                
                                    
                                     
                                
                                
                                
                                 
                                
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="pincode" class="">Pincode  <font color="red">*</font></label>
                                            <select name="pincode" id="pincode" class="form-control" required="">
                                                <option value="">Pincode</option>
                                                <?php
                                                        foreach($data_pin as $pin)
                                                        {
                                                            ?>       <option value="<?php echo $pin->pincode; ?>"
                                                                    <?php if($pin->pincode==$data_sc->pincode) { echo 'selected';} ?>
                                                                    ><?php echo $pin->pincode; ?></option>     
                                                <?php   }
                                                ?>
                                            </select><div class="invalid-tooltip">
                                                Please Select Pincode.
                                            </div>
                                        </div>
                                    </div>
                                     
                                 </div>     
                                 Bank Details :
                                <hr>
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="bank_name" class="">Bank Name  <font color="red">*</font></label>
                                            <input name="bank_name" id="bank_name" value="<?php echo $data_sc->bank_name; ?>" placeholder="Bank Name" type="text" class="form-control"  required="">
                                            <div class="invalid-tooltip">
                                                Please Fill Bank Name.
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="bank_add" class="">Bank Address  <font color="red">*</font></label>
                                            <input name="bank_add" id="bank_add" value="<?php echo $data_sc->bank_add; ?>" placeholder="Bank Address" type="text" class="form-control"  required="">
                                            <div class="invalid-tooltip">
                                                Please Fill Bank Address.
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="acc_no" class="">Account No.  <font color="red">*</font></label>
                                            <input name="acc_no" id="acc_no" value="<?php echo $data_sc->acc_no; ?>" placeholder="Account No." type="text" class="form-control"  required="">
                                            <div class="invalid-tooltip">
                                                Please Fill Account No.
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="ifsc" class="">IFSC Code  <font color="red">*</font></label>
                                            <input name="ifsc" id="ifsc" value="<?php echo $data_sc->ifsc; ?>" placeholder="IFSC Code" type="text" class="form-control"  required="">
                                            <div class="invalid-tooltip">
                                                Please Fill IFSC Code.
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="pan_no" class="">PAN No.  <font color="red">*</font></label>
                                            <input name="pan_no" id="pan_no" maxlength="10" onblur="check_pan_no(this.value);" value="<?php echo $data_sc->pan_no; ?>" placeholder="PAN No." type="text" class="form-control"  required="">
                                            <div class="invalid-feedback" id="disp_pan_no">
                                                Please Fill Right PAN No.
                                            </div>
                                        </div>
                                    </div>  
                                     
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">GST No.  <font color="red">*</font></label>
                                            <input name="gst_no" id="gst_no" placeholder="GST No." value="<?php echo $data_sc->gst_no; ?>" type="text" class="form-control"  required="">
                                        </div>
                                    </div>
                                
                                 
                                     <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Remark  </label>
                                            <input name="center_remark" id="center_remark" placeholder="Remarks" type="text" value="<?php echo $data_sc->center_remark; ?>" class="form-control"  >
                                        </div>
                                    </div>
                                     <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="exampleEmail11" class="">Status</label>
                                        <select id="sc_status" name="sc_status" class="form-control" required="">
                                            <option value="1" <?php if($data_sc->sc_status=='1') { echo 'selected';} ?>>Active</option>
                                            <option value="0" <?php if($data_sc->sc_status=='0') { echo 'selected';} ?>>De-Active</option>
                                        </select>
                                    </div>
                                     </div>
                                 </div>
                                 
                                 <input type="hidden" id="center_id" name="center_id" value="<?php echo $data_sc->center_id; ?>" />
                                <button type="submit" onclick="return validate_user()" class="mt-2 btn btn-primary">Save</button>
                                <a href="{{route('home')}}" class="mt-2 btn btn-danger"  title="home">Exit</a>
                            </form>
                            
                            
                            
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
 
<script>
    function check_pan_no(pan_no)
    {
        if(pan_no.length!==10)
        {
            $('#disp_pan_no').show();
            $('#pan_no').css('border-color','#d92550');
        }
        else
        {
            $('#disp_pan_no').hide();
            $('#pan_no').css('border-color','#ced4da');
        }
    }
    function validate_user()
    {
     $('#center_form').addClass("was-validated");     
     
     var re = /\S+@\S+\.\S+/;
     var email = document.getElementById("email_id");
     var phone = document.getElementById("contact_no").value;
     var pan_no = document.getElementById("pan_no").value;
     
    
    if(contact_no.length==='')
    {
        alert("Please Fill Contact No.");
        return false;
    }
    
     else if(false===re.test(email.value))
        {
            alert("Please Fill Valid Email Id");
            return false;
        }
      else if(phone.length<10)
      {
        alert("Please Fill Valid Contact No.");
        return false;
      }
     else if(pan_no!=='' && pan_no.length!==10)
      {
        alert("Please Fill Right PAN No.");
        document.getElementById("pan_no").focus=true;
        return false;
      }
        
    return true;    
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
 
 
 function checkPinNumber(val,evt)
{    
    var charCode = (evt.which) ? evt.which : event.keyCode
	
    if (charCode> 31 && (charCode < 48 || charCode > 57)  || (val=='e' || val.length>=6))
    {            
            return false;
    }
    return true;
}
 
 function get_district(state_id)
 {
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-district-by-state-id',
              method: 'post',
              data: {
                 state_id: state_id 
              },
              success: function(result){
                  $('#dist_id').html(result)
              }});
 }
 
 function get_pincode(dist_id)
 {
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-pincode-by-dist-id',
              method: 'post',
              data: {
                 dist_id: dist_id 
              },
              success: function(result){
                  $('#pincode').html(result)
              }});
 }
 
 
 
 
</script>

@endsection
