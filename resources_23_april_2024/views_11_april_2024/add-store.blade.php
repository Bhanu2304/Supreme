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
                        <div class="card-body"><h5 class="card-title"> Store</h5>
                            
                            <h5 class="card-title" style="text-color:blue;">
                                <a href="#" onclick="form_toggle('se_form','table_div');" style="cursor: pointer;">Add</a> / <a href="#" onclick="form_toggle('table_div','se_form');" style="cursor: pointer;">View</a>
                            </h5> 
                            @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                            
                            <form id="se_form" method="post" action="save-store" style="display:none;">
                                
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">Email <span style="color: #f00;">*</span></label>
                                            <input name="email" id="email" placeholder="Email" type="email" class="form-control" required="">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">Password <span style="color: #f00;">*</span></label>
                                            <input name="pass" id="pass" placeholder="Password" type="password" class="form-control" required="" minlength="8">
                                        </div>
                                    </div>
                                </div>  

                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Name</label>
                                            <input name="se_name" id="se_name" placeholder="Display Name" type="text" class="form-control">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Mobile No. <span style="color: #f00;">*</span></label>
                                            <input name="phone" id="phone" placeholder="Mobile" type="text" class="form-control" required="">
                                        </div>
                                    </div>
                                </div>


                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group"><label for="exampleAddress" class="">Service Center <span style="color: #f00;">*</span></label>
                                            <Select name="vendor_id" id="vendor_id" class="form-control" required="">
                                                <option value="">Select</option>
                                                <?php foreach($vendor_arr as $vendor)
                                                        {?>
                                                <option value="<?php echo $vendor->center_id; ?>"><?php echo $vendor->center_name; ?> - <?php echo $vendor->state_name; ?> - <?php echo $vendor->city; ?> - <?php echo $vendor->pincode; ?></option>
                                                <?php    
                                                        }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>  
                                 
                                <button type="submit" onclick="return validate_user()" class="mt-2 btn btn-primary">Save</button>
                                <a href="#" onclick="form_toggle('table_id','se_form');" class="mt-2 btn btn-danger"  title="view">Exit</a>
                            </form>
                                 <div id="table_div">
                                     <table id="table_id" class="table table-striped table-bordered" style="width:100%">
                              <thead>
                                 <tr>
                                    <th>Sr.No</th>
                                    <th>Name</th>
                                    <th>Service Center</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    
                                    <th>Status</th>
                                    <th>Action</th>
                                 </tr>
                              </thead>
                              <tbody>
                                  @php $i = 0; @endphp
                                    @foreach($DataArr as $Data)
                                  
                                  
                                 <tr>
                                    <td>{{++$i}}</td>
                                    <td>{{$Data->se_name}}</td>
                                    <td>{{$Data->center_name}}</td>
                                    <td class="emailid">{{$Data->email}}</td>
                                    <td class="emailid">{{$Data->phone}}</td>
                                    
                                    
                                    <td class="Status">@if($Data->se_status=='1') {{'Active'}} @else {{'De-Active'}} @endif</td>
                                    <td class="Officer"><a href="store-edit?se_id=<?php echo base64_encode($Data->se_id); ?>" >Edit</a></td>
                                 </tr>
                                 @endforeach
                                
                              </tbody>
                           </table>     
                                 </div>   
                            
                                 
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
     var vendor_id = document.getElementById("vendor_id").value;
     var password = document.getElementById("pass").value;
     

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
     else if (password.length < 8) {
        alert("Password must be at least 8 characters long");
        return false;
     }
     else if(vendor_id=='')
      {
        alert("Please Select Vendor");
        return false;
      }
        
    return true;    
 }
 
 function form_toggle(first,second)
{
    $('#'+first).show();
    $('#'+second).hide();
}
 $('#table_id').DataTable( );
 
</script>

@endsection
