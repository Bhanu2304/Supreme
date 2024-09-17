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
                        <div class="card-body"><h5 class="card-title">Edit ASC Manager</h5>
                            <form method="post" action="man-update">
                                @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">Email <span style="color: #f00;">*</span></label>
                                            <input name="email" id="email" placeholder="Email" type="email" value="<?php echo $data['email']; ?>" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">New Password</label>
                                            <input name="pass" id="pass" placeholder="New Password" type="password" value=""  class="form-control" >
                                        </div>
                                    </div>
                                </div>  

                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Name</label>
                                            <input name="man_name" id="man_name" placeholder="Display Name" type="text" value="<?php echo $data['man_name']; ?>" class="form-control" required="">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Mobile No. <span style="color: #f00;">*</span></label>
                                            <input name="phone" id="phone" minlength="10" maxlength="10" onkeypress="return checkNumber(this.value,event)" placeholder="Mobile" type="text" value="<?php echo $data['phone']; ?>" class="form-control" required="">
                                        </div>
                                    </div>
                                </div>

                                 <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Service Center <span style="color: #f00;">*</span></label>
                                            <select id="center_id" name="center_id" class="form-control">
                                                <option value="">Select</option>
                                                @foreach($center_master as $region)
                                                <option value="{{$region->center_id}}" <?php if($region->center_id==$data['center_id']) { echo 'selected'; }  ?>>{{$region->center_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                 <div class="form-row">
                                     <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">Status</label>
                                            <select id="man_status" name="man_status" class="form-control" required="">
                                                <option value="1" <?php if($data['man_status']=='1') { echo 'selected';} ?>>Active</option>
                                                <option value="0" <?php if($data['man_status']=='0') { echo 'selected';} ?>>De-Active</option>
                                            </select>
                                        </div>
                                     </div>
                                 </div>
                                 <input type="hidden" id="se_id" name="man_id" value="<?php echo $data['man_id']; ?>" />
                                 <a href="add-man" class="btn btn-danger btn-grad btnr1" data-original-title="" title="">Back</a>
                                <button type="submit" onclick="return validate_user()" class="mt-2 btn btn-primary">Update</button>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div> 
 
<script>
    function validate_user()
 {
     var re = /\S+@\S+\.\S+/;
     var email = document.getElementById("email");
     var phone = document.getElementById("mobile").value;
     
     if(false==re.test(email.value))
        {
            alert("Please Fill Valid Email Id");
            return false;
        }
      else if(phone.length!=10)
      {
        alert("Please Fill Valid Mobile No.");
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
 
 
</script>

@endsection
