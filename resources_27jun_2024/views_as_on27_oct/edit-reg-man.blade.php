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
                        <div class="card-body"><h5 class="card-title">Region Manager</h5>
                            
                            <h5 class="card-title" style="text-color:blue;">
                                <a href="#" onclick="form_toggle('man_form','table_id');" style="cursor: pointer;">Add</a> / <a href="#" onclick="form_toggle('table_id','man_form');" style="cursor: pointer;">View</a>
                            </h5> 
                            
                             @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                            
                            <form id="man_form" method="post" action="update-reg-man">
                               
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">Email <span style="color: #f00;">*</span></label>
                                            <input name="email" id="email" placeholder="Email" type="email" value="<?php echo $data['email']; ?>" class="form-control" autocomplete="off" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">Password </label>
                                            <input name="pass" id="pass" placeholder="Password" type="password" class="form-control" >
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Name</label>
                                            <input name="man_name" id="man_name" value="<?php echo $data['man_name']; ?>" placeholder="Display Name" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Mobile No. <span style="color: #f00;">*</span></label>
                                            <input name="phone" id="phone" value="<?php echo $data['phone']; ?>" placeholder="Mobile" type="text" onkeypress="return checkNumber(this.value,event)" class="form-control" required="">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">User Type <span style="color: #f00;">*</span></label>
                                            <select id="user_type" name="user_type" class="form-control" >
                                                <option value="">Select</option>
                                                <?php foreach($ut_master as $ut) { ?>
                                                <option value="<?php echo $ut->user_type;?>" <?php if($data['user_type']==$ut->user_type) {echo 'selected';} ?>><?php echo $ut->user_type;?></option>
                                                <?php } ?>
                                                
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Status <span style="color: #f00;">*</span></label>
                                            <select id="man_status" name="man_status" class="form-control" >
                                                <option value="">Select</option>
                                                <option value="1" <?php if($data['man_status']=='1') {echo 'selected';} ?>>Active</option>
                                                <option value="0" <?php if($data['man_status']=='0') {echo 'selected';} ?>>De-Active</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                               
                                <input type="hidden" name="reg_man_id" value="<?php echo $data['reg_man_id']; ?>" /> 
                                 
                                <button type="submit" onclick="return validate_user()" class="mt-2 btn btn-primary">Save</button>
                                <a href="add-reg-man" class="mt-2 btn btn-danger"  title="view">Exit</a>
                            </form>
                            
                            
                            
                            
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
 
<script>
    function show_region(user_type)
    {
        if(user_type=='ASM')
        {
            $('#region_disp').show();
        }
        else if(user_type=='RSM')
        {
            $('#region_disp').show();
        }
        else
        {
            $('#region_disp').hide();
        }
    }
    
    
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
        if(val.length>10)
        {
            return false;
        }
	return true;
}
 
 function form_toggle(first,second)
{
    $('#'+first).show();
    $('#'+second).hide();
}
 
 
</script>

@endsection
