@extends('layouts.app')

@section('content')

<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body"><h5 class="card-title">Edit User</h5>
                            <form method="post" action="update-user">
                                @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">Email</label>
                                            <input name="email" id="email" placeholder="Email" type="email" value="<?php echo $data['email']; ?>" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">New Password</label>
                                            <input name="password" id="password" placeholder="New Password" type="password" value=""  class="form-control" >
                                        </div>
                                    </div>
                                </div>  

                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Name</label>
                                            <input name="name" id="name" placeholder="Display Name" type="text" value="<?php echo $data['UserName']; ?>" class="form-control" required="">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Mobile No.</label>
                                            <input name="mobile" id="mobile" placeholder="Mobile" type="text" value="<?php echo $data['mobile']; ?>" class="form-control" required="">
                                        </div>
                                    </div>
                                </div>


                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group"><label for="exampleAddress" class="">User Type</label>
                                            <Select name="UserType" id="UserType" class="form-control" required="">
                                                <option value="Admin" <?php if($data['UserType']=='Admin') echo 'selected'; ?> >Admin</option>
                                                <option value="Vendor <?php if($data['UserType']=='Vendor') echo 'selected'; ?>">Vendor</option>
                                                <option value="Agent" <?php if($data['UserType']=='Agent') echo 'selected'; ?>>Agent</option> 
                                            </select>
                                        </div>
                                    </div>
                                    
                                </div>
                                 <div class="form-row">
                                     <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="exampleEmail11" class="">Status</label>
                                        <select id="user_status" name="user_status" class="form-control" required="">
                                            <option value="1" <?php if($data['user_status']=='1') { echo 'selected';} ?>>Active</option>
                                            <option value="0" <?php if($data['user_status']=='0') { echo 'selected';} ?>>De-Active</option>
                                        </select>
                                    </div>
                                     </div>
                                 </div>
                                 <input type="hidden" id="UserId" name="UserId" value="<?php echo $data['UserId']; ?>" />
                                 <a href="view-user" class="btn btn-danger btn-grad btnr1" data-original-title="" title="">Back</a>
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
</script>

@endsection
