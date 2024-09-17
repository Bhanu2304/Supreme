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
                        <div class="card-body"><h5 class="card-title">Edit Supplier</h5>
                            <form method="post">
                                @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Supplier Name <font color="red">*</font></label>
                                            <input name="supplier_name" id="supplier_name" placeholder="Supplier Name" type="text" value="<?php echo $data['supplier_name']; ?>" class="form-control" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Address <font color="red">*</font></label>
                                            <input name="address" id="address" placeholder="Address" type="text" value="<?php echo $data['address']; ?>"  class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">State <font color="red">*</font></label>
                                            <select id="state" name="state" class="form-control" required="">
                                                <option value="">State</option>
                                                <?php foreach($state_master as $state){ ?>       
                                                    <option value="<?php echo $state['state_id']; ?>" <?php if($state['state_id'] == $data['state']){ echo "selected";} ?>><?php echo $state['state_name']; ?></option>     
                                                <?php   } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Phone No <font color="red">*</font></label>
                                            <input name="phone_no" id="phone_no" placeholder="Phone No" type="text" value="<?php echo $data['phone_no']; ?>" class="form-control" onkeypress="return checkNumber(this.value,event)" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Email <font color="red">*</font></label>
                                            <input name="email" id="email" placeholder="Email" type="text" class="form-control" value="<?php echo $data['email']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">GST No. <font color="red">*</font></label>
                                            <input name="gst_no" id="gst_no" placeholder="GST No." type="text" class="form-control" value="<?php echo $data['gst_code']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">Status <font color="red">*</font></label>
                                            <select id="status" name="status" class="form-control" required="">
                                                <option value="1" <?php if($data['active_status']=='1') { echo 'selected';} ?>>Active</option>
                                                <option value="0" <?php if($data['active_status']=='0') { echo 'selected';} ?>>De-Active</option>
                                            </select>
                                        </div>
                                     </div>
                                </div>
                                <!-- <div class="form-row">
                                     
                                </div> -->
                                <input type="hidden" id="se_id" name="se_id" value="<?php echo $data['id']; ?>" />
                                <a href="supplier" class="btn btn-danger btn-grad btnr1" data-original-title="" title="">Back</a>
                                <button type="submit" onclick="return validate_user()" class="btn btn-primary">Update</button>
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
