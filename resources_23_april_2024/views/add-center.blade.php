@extends('layouts.app')

@section('content')
<script>
    menu_select('{{$url}}');
</script>
<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
                            <li class="nav-item">
                                <a role="tab" class="nav-link active" id="tab-0" data-toggle="tab" href="#tab-content-0">
                                    <span>View </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a role="tab" class="nav-link" id="tab-1" data-toggle="tab" href="#tab-content-1">
                                    <span>Add </span>
                                </a>
                            </li>
                        </ul>
            <div class="tab-content">
                
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body"><h5 class="card-title">View Service Center</h5>
                            
                            @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                 
                            
                            <table class="table table-striped table-bordered " style="width:100%" id="table_id">
                            <thead>
                                <tr>
                                    <th>Sr.No</th>
                                    <th>ASC Code</th>
                                    <th>ASC Name</th>
                                    <th>Contact Person</th>
                                    <th>Contact No.</th>
                                    <th>Alternate No.</th>
                                    <th>Landline No.</th>
                                    <th>Email ID</th>
                                    <th>Billing Address</th>
                                    <th>Shipping Address</th>
                                    <th>Region</th>
                                    <th>District</th>
                                    <th>State</th>
                                    <th>Pincode</th>
                                    <th>Bank Name</th>
                                    <th>Bank Address</th>
                                    <th>Account No.</th>
                                    <th>IFSC Code</th>
                                    <th>PAN No.</th>
                                    <th>GST No.</th>
                                    <th>Image</th>
                                    <th>Status</th>
                                   <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1 @endphp
                                @foreach($DataArr as $data)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{$data->asc_code}}</td>
                                    <td>{{$data->center_name}}</td>
                                    <td>{{$data->person_name}}</td>
                                    <td>{{$data->contact_no}}</td>
                                    <td>{{$data->alt_no1}}</td>
                                    <td>{{$data->alt_no2}}</td>
                                    <td>{{$data->email_id}}</td>
                                    <td>{{$data->bill_add}}</td>
                                    <td>{{$data->ship_add}}</td>
                                    
                                    <td>{{$data->region_name}}</td>
                                    <td>{{$data->dist_name}}</td>
                                    <td>{{$data->state_name}}</td>
                                    <td>{{$data->pincode}}</td>
                                    
                                    <td>{{$data->bank_name}}</td>
                                    <td>{{$data->bank_add}}</td>
                                    <td>{{$data->acc_no}}</td>
                                    <td>{{$data->ifsc}}</td>
                                    <td>{{$data->pan_no}}</td>
                                    <td>{{$data->gst_no}}</td>
                                    <!--<td>{{$data->center_remark}}</td>-->
                                    <td>
                                    <?php $imageFound = false;for ($i = 1; $i <= 5; $i++) {
                                        $columnName = "upload_image" . $i;
                                        $imageUrl = $data->$columnName;
                                        if($imageUrl!= "")
                                        {   
                                            $imageFound = true;
                                            echo "<a class='fancybox' target='_blank' href='{$str_server}/storage/app/service_center/{$data->center_id}/{$imageUrl}'><img src='{$str_server}/storage/app/service_center/{$data->center_id}/{$imageUrl}' class='toggle-icon' data-image='{$imageUrl}' alt='Toggle Image'></a>";
                                        }
                                      
                                    }if (!$imageFound) {
                                        echo "No image found.";
                                    } ?>
                                    </td>
                                    <td>
                                        @if($data->sc_status =='1'){{'Active'}}@endif
                                        @if($data->sc_status =='0'){{'De-Active'}}@endif
                                    </td>
                                    <td class="Officer"><a href="edit-centre?center_id=<?php echo base64_encode($data->center_id); ?>" >Edit</a></td>
                                </tr> 
                       
                        @endforeach
                         
                      </tbody>
                        </table>
                            
                        </div>
                    </div>

                </div>
                <div class="tab-pane tabs-animation fade" id="tab-content-1" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body"><h5 class="card-title">Add Service Center</h5>
                            
                           
                                 <form method="post" class="needs-validation" action="save-centre" id="center_form" enctype="multipart/form-data">
                                
                                 
                                 <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="center_name" class="">Center Name <font color="red">*</font></label>
                                            <input name="center_name" id="center_name" placeholder="Center Name" type="center_name" class="form-control" required="">
                                            <div class="invalid-tooltip">
                                                Please Fill Center Name.
                                            </div>
                                        </div>
                                    </div>
                                     <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="person_name" class="">Person Name <font color="red">*</font></label>
                                            <input name="person_name" id="person_name" placeholder="Person Name" type="text" class="form-control" required="">
                                            <div class="invalid-tooltip">
                                                Please Fill Person Name.
                                            </div>
                                        </div>
                                    </div>
                                
                                 
                                
                                 
                                 
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="contact_no" class="">Contact No. <font color="red">*</font></label>
                                            <input name="contact_no" id="contact_no" placeholder="Contact No." type="text" class="form-control" onkeypress="return checkNumber(this.value,event)" required="">
                                            <div class="invalid-tooltip">
                                                Please Fill Contact No.
                                            </div>
                                        </div>
                                    </div>
                                     <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="alt_no1" class="">Alternate No.</label>
                                            <input name="alt_no1" id="alt_no1" placeholder="Alternate No." type="text" class="form-control" onkeypress="return checkNumber(this.value,event)">
                                            
                                        </div>
                                    </div>
                                     <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="alt_no2" class="">Landline No.</label>
                                            <input name="alt_no2" id="alt_no2" placeholder="Landline No." type="text" class="form-control" onkeypress="return checkNumber(this.value,event)">
                                        </div>
                                    </div>
<!--                                     <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="alt_no3" class="">Alternate No. 3</label>
                                            <input name="alt_no3" id="alt_no3" placeholder="Alternate No. 3" type="text" class="form-control" onkeypress="return checkNumber(this.value,event)">
                                        </div>
                                    </div>-->
                                     <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="email_id" class="">Email <font color="red">*</font></label>
                                            <input name="email_id" id="email_id" placeholder="Email" type="email" class="form-control" required="">
                                            <div class="invalid-tooltip">
                                                Please Fill Email Id.
                                            </div>
                                        </div>
                                    </div>
                                
                                 
                                 
                                
                                 

                               
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="password" class="">Password <font color="red">*</font></label>
                                            <input name="password" id="password" placeholder="Password" type="password" class="form-control" required="">
                                            <div class="invalid-tooltip">
                                                Please Fill Password.
                                            </div>
                                        </div>
                                    </div>
                                    
                               

                                
                                 
                               
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="region" class="">Region <font color="red">*</font></label>
                                            <select id="region" name="region" class="form-control" onchange="get_states_by_region(this.value,'state')" required="">
                                                <option value="">Region</option>
                                                <?php
                                                        foreach($region_master as $region)
                                                        {
                                                            ?>       <option value="<?php echo $region['region_id']; ?>"><?php echo $region['region_name']; ?></option>     
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
                                            <label for="state" class="">State  <font color="red">*</font></label>
                                            
                                            <select id="state" name="state" class="form-control" required=""  onchange="get_district(this.value);">
                                                <option value="">State</option>
                                                <?php
                                                        foreach($state_master as $state)
                                                        {
                                                            ?>       <option value="<?php echo $state['state_id']; ?>"><?php echo $state['state_name']; ?></option>     
                                                <?php   }
                                                ?>
                                            </select><div class="invalid-tooltip">
                                                Please Fill State.
                                            </div>
                                        </div>
                                    </div> 
                                     <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="dist_id" class="">District  <font color="red">*</font></label>
                                            <select name="dist_id" id="dist_id" class="form-control" onchange="get_pincode(this.value)" required="">
                                                <option value="">Select</option>
                                            </select>
                                            <div class="invalid-tooltip">
                                                Please Fill District.
                                            </div>
                                        </div>
                                    </div>
                                     
                                     <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="address" class="">Address <font color="red">*</font></label>
                                            <input name="address" id="address" placeholder="Address" type="text" class="form-control" required="">
                                            <div class="invalid-tooltip">
                                                Please Fill Address.
                                            </div>
                                        </div>
                                    </div>                                
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="pincode" class="">Pincode  <font color="red">*</font></label>
<!--                                            <input type="text" name="pincode" id="pincode" class="form-control" required="" minlength="6" maxlength="6">-->
                                            <select name="pincode" id="pincode" class="form-control" required="">
                                                <option value="">Pincode</option>
                                            </select>
                                            <div class="invalid-tooltip">
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
                                            <input name="bank_name" id="bank_name" placeholder="Bank Name" type="text" class="form-control"  required="">
                                            <div class="invalid-tooltip">
                                                Please Fill Bank Name.
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="bank_add" class="">Bank Address  <font color="red">*</font></label>
                                            <input name="bank_add" id="bank_add" placeholder="Bank Address" type="text" class="form-control"  required="">
                                            <div class="invalid-tooltip">
                                                Please Fill Bank Address.
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="acc_no" class="">Account No.  <font color="red">*</font></label>
                                            <input name="acc_no" id="acc_no" placeholder="Account No." type="text" class="form-control"  required="">
                                            <div class="invalid-tooltip">
                                                Please Fill Account No.
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="ifsc" class="">IFSC Code  <font color="red">*</font></label>
                                            <input name="ifsc" id="ifsc" placeholder="IFSC Code" type="text" class="form-control"  required="">
                                            <div class="invalid-tooltip">
                                                Please Fill IFSC Code.
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="pan_no" class="">PAN No.  </label>
                                            <input name="pan_no" id="pan_no" placeholder="PAN No." onblur="check_pan_no(this.value);" maxlength="10" type="text" class="form-control"  >
                                            <div class="invalid-feedback" id="disp_pan_no">
                                                Please Fill Right PAN No.
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="gst_no" class="">GST No.  </label>
                                            <input name="gst_no" id="gst_no" placeholder="GST No." type="text" class="form-control"  >
                                            
                                        </div>
                                    </div>
                                
                                 <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="bill_add" class="">Billing Address  </label>
                                            <textarea name="bill_add" id="bill_add" placeholder="Billing Address" type="text" class="form-control"  ></textarea>
                                            
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="ship_add" class="">Shipping Address  </label>
                                            <textarea name="ship_add" id="ship_add" placeholder="Shipping Address" type="text" class="form-control"  ></textarea>
                                            
                                        </div>
                                    </div>
                                
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Upload Image 1</label>
                                            <input name="upload_image1" id="upload_image1" placeholder="Image" type="file" class="form-control" onchange="validateFile(this)" accept="image/jpeg, image/png">
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Upload Image 2</label>
                                            <input name="upload_image2" id="upload_image2" placeholder="Image" type="file" class="form-control" onchange="validateFile(this)" accept="image/jpeg, image/png">
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Upload Image 3</label>
                                            <input name="upload_image3" id="upload_image3" placeholder="Image" type="file" class="form-control" onchange="validateFile(this)" accept="image/jpeg, image/png">
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Upload Image 4</label>
                                            <input name="upload_image4" id="upload_image4" placeholder="Image" type="file" class="form-control" onchange="validateFile(this)" accept="image/jpeg, image/png">
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Upload Image 5</label>
                                            <input name="upload_image5" id="upload_image5" placeholder="Image" type="file" class="form-control" onchange="validateFile(this)" accept="image/jpeg, image/png">
                                            
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Remark  </label>
                                            <input name="center_remark" id="center_remark" placeholder="Remarks" type="text" class="form-control"  >
                                            
                                        </div>
                                    </div>
                                    
                                </div>
                                 
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
    
    function get_states_by_region(region_id,state_id){
    
        $.post('get-state-id-by-region-id',{region_id:region_id}, function(data)
        {
            $('#'+state_id).html(data);
            $('#dist_id').html('');
        }); 
     
    }
    // function numberOfImages(inputElement, maxFiles)
    // {   
    //     var numberOfFiles = inputElement.files.length;

    //     //console.log(numberOfFiles);
    //     if (numberOfFiles > maxFiles) {
    //         alert('You can only upload a maximum of ' + maxFiles + ' files.');
    //         inputElement.value = ''; 
    //     }

    // }

    function validateFile(input) 
    {
        const maxFileSize = 1024 * 1024;

        for (let i = 0; i < input.files.length; i++) {
            const file = input.files[i];
            // if (file.size > maxFileSize) {
            //     alert('File size exceeds the maximum limit of 1 MB.');
            //     input.value = '';
            //     return;
            // }
        }
    }
    
    
    function validate_user()
    {
     $('#center_form').addClass("was-validated");     
     
     var re = /\S+@\S+\.\S+/;
     var email = document.getElementById("email_id");
     var phone = document.getElementById("contact_no").value;
     var pan_no = document.getElementById("pan_no").value;
     var pincode = document.getElementById("pincode").value;
     
    
    if(phone.length==='')
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
    else if(pincode.length<6)
    {
    alert("Please Fill Valid Pincode");
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
        if(val.length>9)
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
 
 
 
 $('#table_id').DataTable( );
</script>

@endsection
