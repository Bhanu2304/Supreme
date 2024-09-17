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
                        <div class="card-body"><h5 class="card-title">Edit Closure</h5>
                            <form method="post">
                                @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Closure Code</label>
                                            <textarea name="closure_code" id="closure_code" cols="30" rows="10" class="form-control" placeholder="Enter Closure Code" required><?php echo $data['closure_code']; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                     <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="exampleEmail11" class="">Status</label>
                                        <select id="status" name="status" class="form-control" required="">
                                            <option value="1" <?php if($data['status']=='1') { echo 'selected';} ?>>Active</option>
                                            <option value="0" <?php if($data['status']=='0') { echo 'selected';} ?>>De-Active</option>
                                        </select>
                                    </div>
                                     </div>
                                </div>
                                <input type="hidden" id="se_id" name="se_id" value="<?php echo $data['id']; ?>" />
                                <a href="closure-code" class="btn btn-danger btn-grad btnr1" data-original-title="" title="">Back</a>
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
