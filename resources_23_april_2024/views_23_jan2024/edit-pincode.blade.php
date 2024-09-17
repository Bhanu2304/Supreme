@extends('layouts.app')
@section('content')

<script>
                                    

menu_select('{{$url}}');                                                              
</script>
<script>


function reloadPage(){
    location.reload(true);
}

function check_record(country_id,state_name,place,pincode,pin_id){
    
        $.post('pincode-exist-update',{country_id:country_id,state_name:state_name,place:place,pincode:pincode,pin_id:pin_id}, function(data){
            if(data==2)
            {
                return true;
            }
            else 
            {
                 $("#pincode").focus();
        $("#pincode").after("<span id='msgerr' style='color:red;'>Pincode Allready Exist.</span>");
                return false;
            }
        }); 
     
}

function get_states(country_id){
    
    $.post('get-states',{country_id:country_id}, function(data){
        $('#state_id').html(data);
    }); 
     
}

function get_district(state_id,div_id){
    
    $.post('get-district-by-state-id',{state_id:state_id}, function(data){
        $('#'+div_id).html(data);
    }); 
     
}

function validate_state(){
    $("#msgerr").remove();
    
    var country_id  =   $("#country").val();
    var state_name       =   $.trim($("#state_name").val());
    var place       =   $.trim($("#place").val());
    var pincode       =   $.trim($("#pincode").val());
    var pin_id       =   $.trim($("#pin_id").val());
     
    
    if(country_id ==""){
        $("#country_id").focus();
        $("#country_id").after("<span id='msgerr' style='color:red;'>Please Select Country.</span>");
        return false;
    }
    else if(state_name ==""){
        $("#state_name").focus();
        $("#state_name").after("<span id='msgerr' style='color:red;'>Please Select State Name.</span>");
        return false;
    }
    else if(place ==""){
        $("#place").focus();
        $("#place").after("<span id='msgerr' style='color:red;'>Please Fill Place.</span>");
        return false;
    }
    else if(pincode ==""){
        $("#pincode").focus();
        $("#pincode").after("<span id='msgerr' style='color:red;'>Please Fill Pincode.</span>");
        return false;
    }
    
    check_record(country_id,state_name,place,pincode,pin_id);
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

</script>

<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body"><h5 class="card-title">Edit Pincode</h5>
                            
                                @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                  
                        <form method="post" action="{{route('update-pincode')}}" class="form-horizontal" onSubmit="return validate_pincode()" >
                           
                            <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">Country</label>
                                            <select name="country_id" id="country" onchange="get_states(this.value)" class="form-control chzn-select chzn-rtl" tabindex="9" required="">
                                                <option value="">Select</option>
                                                @foreach($countryArr as $country)
                                                <option value="{{$country->country_id}}" <?php if($country->country_id==$data_pin_record['Country_Id']) { echo 'selected'; }  ?>>{{$country->country_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                
                                <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">State</label>
                                
                                            <select name="state_id" id="state_id" onchange="get_district(this.value,'dist_id')" class="form-control chzn-select chzn-rtl" tabindex="9" required="">
                                                <option value="">Select</option>
                                                @foreach($state_master as $state)
                                                <option value="{{$state->state_id}}" <?php if($state->state_id==$data_pin_record['state_id']) { echo 'selected'; }  ?>>{{$state->state_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">District</label>
                                        <select name="dist_id" id="dist_id" data-placeholder="" class="form-control" tabindex="9" required="">
                                            <option value="">Select</option>
                                            @foreach($dist_master as $dist)
                                                <option value="{{$dist->dist_id}}" <?php if($dist->dist_id==$data_pin_record['dist_id']) { echo 'selected'; }  ?>>{{$dist->dist_name}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">Place</label>
                                            <input class="form-control" type="text" id="place" name="place" data-original-title="place" data-placement="top" placeholder="Place" value="<?php echo $data_pin_record['place']; ?>" required="">
                                        </div>    
                                </div>
                                
                                     
                            </div>
                            
                            <div class="form-row">
                                <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">Pincode</label>
                                            <input class="form-control" type="text" id="pincode" name="pincode" data-original-title="Pincode" data-placement="top" placeholder="Pincode" value="<?php echo $data_pin_record['pincode']; ?>" onKeyPress="return checkPinNumber(this.value,event)" required="">
                                        </div>    
                                </div>
                                
                                
                                <?php //if(!empty($vendorArr))
                                //{ ?>
<!--                                <table id="table1" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                           <th>Sr.No</th>
                                           <th>Vendor Name</th>
                                           <th>Vendor Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php //$srno=1; foreach($vendorArr as $vendor) { ?>
                                        <tr>
                                        <td><input type="checkbox" name="vendor[]" class="" value="<?php //echo $vendor->id; ?>"  <?php //if(in_array($vendor->id,$vendorList)) { echo 'checked';} ?>><?php //echo $srno++; ?></td>
                                        <td><?php //echo $vendor->name; ?></td>
                                        <td><?php //echo $vendor->email; ?></td>
                                        </tr>
                                        <?php //} ?>
                                    </tbody>
                              </thead>
                                </table>  -->
                                <?php //}
                                ?>
                            </div> 
                            
                            <div class="form-row">
                                
                                
                                     <div class="col-md-6">
                                <input type="submit"  class="btn btn-success btn-grad" data-original-title="" title="" value="Update" >
                                &nbsp;<a href="view-pincode" class="btn btn-danger btn-grad" data-original-title="" title="">Back</a>
                            </div>
                            </div> 
                            <input type="hidden" id="pin_id" name="pin_id" value="<?php echo $data_pin_record['Pin_Id'];  ?>" />
                        </form>
                    </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection
