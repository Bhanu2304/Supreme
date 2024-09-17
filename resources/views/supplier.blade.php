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
<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body"><h5 class="card-title"> Supplier</h5>
                            
                            <h5 class="card-title" style="text-color:blue;">
                                <a href="#" onclick="form_toggle('se_form','table_div');" style="cursor: pointer;">Add</a> / <a href="#" onclick="form_toggle('table_div','se_form');" style="cursor: pointer;">View</a>
                            </h5> 
                            @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                            @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                            
                            <form id="se_form" method="post"  style="display:none;">
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Supplier Name <font color="red">*</font></label>
                                            <input name="supplier_name" id="supplier_name" placeholder="Supplier Name" type="text" class="form-control" required>
                                            <!-- <textarea name="closure_code" id="closure_code" cols="30" rows="10" class="form-control" placeholder="Enter Closure Code" required></textarea> -->
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Address <font color="red">*</font></label>
                                            <input name="address" id="address" placeholder="Address" type="text" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">State <font color="red">*</font></label>
                                            <select id="state" name="state" class="form-control" required="">
                                                <option value="">State</option>
                                                <?php foreach($state_master as $state){ ?>       
                                                    <option value="<?php echo $state['state_id']; ?>"><?php echo $state['state_name']; ?></option>     
                                                <?php   } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Phone No <font color="red">*</font></label>
                                            <input name="phone_no" id="phone_no" placeholder="Phone No" type="text" class="form-control" onkeypress="return checkNumber(this.value,event)" required>
                                            <!-- <textarea name="closure_code" id="closure_code" cols="30" rows="10" class="form-control" placeholder="Enter Closure Code" required></textarea> -->
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Email <font color="red">*</font></label>
                                            <input name="email" id="email" placeholder="Email" type="text" class="form-control" required>
                                            <!-- <textarea name="closure_code" id="closure_code" cols="30" rows="10" class="form-control" placeholder="Enter Closure Code" required></textarea> -->
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">GST No. <font color="red">*</font></label>
                                            <input name="gst_no" id="gst_no" placeholder="GST No." type="text" class="form-control" required>
                                            <!-- <textarea name="closure_code" id="closure_code" cols="30" rows="10" class="form-control" placeholder="Enter Closure Code" required></textarea> -->
                                        </div>
                                    </div>
                                    
                                </div>

                                <button type="submit"  class="mt-2 btn btn-primary">Save</button>
                                <a href="#" onclick="form_toggle('table_id','se_form');" class="mt-2 btn btn-danger"  title="view">Exit</a>
                            </form>
                                 <div id="table_div">
                                     <table id="table_id" class="table table-striped table-bordered" style="width:100%">
                              <thead>
                                 <tr>
                                    <th>Sr.No</th>
                                    <th>Supplier Code</th>
                                    <th>Supplier Name</th>
                                    <th>Address</th> 
                                    <th>State</th> 
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Gst</th>
                                    <th>Create Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                 </tr>
                              </thead>
                              <tbody>
                                  @php $i = 0; @endphp
                                    @foreach($DataArr as $Data)
                                  
                                  
                                 <tr>
                                    <td>{{++$i}}</td>
                                    <td>{{$Data->supplier_code}}</td>
                                    <td>{{$Data->supplier_name}}</td>
                                    <td>{{$Data->address}}</td>
                                    <td>{{$Data->state_name}}</td>
                                    <td>{{$Data->phone_no}}</td>
                                    <td>{{$Data->email}}</td>
                                    <td>{{$Data->gst_code}}</td>
                                    
                                    <td>{{ date('d-m-Y', strtotime($Data->created_at)) }}</td>

                                    <td class="Status">@if($Data->active_status=='1') {{'Active'}} @else {{'De-Active'}} @endif</td>
                                    <td class="Officer"><a href="edit-supplier?supplier_id=<?php echo base64_encode($Data->id); ?>" >Edit</a></td>
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



    
function form_toggle(first,second)
{
    $('#'+first).show();
    $('#'+second).hide();
}
 $('#table_id').DataTable( );
 
</script>

@endsection
