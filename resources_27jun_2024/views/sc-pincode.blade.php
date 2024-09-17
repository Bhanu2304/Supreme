@extends('layouts.app')
@section('content')

<script src= "https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> 

<script src= "https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script> 
<link rel="stylesheet" href= "https://cdnjs.cloudflare.com/ajax/libs/chosen/1.4.2/chosen.min.css" /> 
<script src= "https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script> 
<link rel="stylesheet" href= "https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" /> 
<script>
    jQuery(document).ready(function($) {
    // Use $ for jQuery code here
        $("#pincode").select2(); 
        
    });
</script>
<script>
menu_select('{{$url}}');  

function reloadPage(){
    location.reload(true);
}

function check_record(country_id,state_name,place,pincode)
{
    
$.post('pin-exist',{country_id:country_id,state_name:state_name,place:place,pincode:pincode}, function(data){
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

function get_states(country_id,state_id){
    
    $.post('get-states',{country_id:country_id}, function(data){
        $('#'+state_id).html(data);
    }); 
     
}
function get_pins(){
    var country_id = $('#country1').val();
    var state_id = $('#state_id1').val();
    var center_id = $('#center_id1').val();
    var pin = $('#pin1').val();
    
    $.post('get-sc-pincode',{country_id:country_id,center_id:center_id,state_id:state_id,pincode:pin}, function(data){
        $('#pincode_view').html(data);
        get_mpins();
    }); 
     
}

function validate_state(){
    $("#msgerr").remove();
    
    var country_id  =   $("#country").val();
    var state_name       =   $.trim($("#state_id").val());
    var center       =   $.trim($("#center_id").val());
    var pincode       =   $.trim($("#pincode").val());
     
    
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
    else if(center ==""){
        $("#center_id").focus();
        $("#center_id").after("<span id='msgerr' style='color:red;'>Please Select Service Center.</span>");
        return false;
    }
    else if(pincode ==""){
        $("#pincode").focus();
        $("#pincode").after("<span id='msgerr' style='color:red;'>Please Select Pincode.</span>");
        return false;
    }
    
    
    return true;    
}
function get_pincode(dist_id){
    
    $.post('get-pincode-by-dist-id',{dist_id:dist_id}, function(data){
        $('#pincode').html(data);
    }); 
     
}

function remove_pincode(sc_pin_id){
    //var country_id = $('#country').val();
    $.post('remove-pincode',{sc_pin_id:sc_pin_id}, function(data){
        if(data==1)
        {
            $('#succ1').html("Pincode Removed Successfully.");
        }
        else
        {
            $('#err1').html("Pincode not Removed.");
        }
        get_pins();
    });   
}



function get_district(state_id,div_id){
    
    $.post('get-district-by-state-id-map',{state_id:state_id}, function(data){
        $('#'+div_id).html(data);
    });
    
    // if(state_id == 'All')
    // {
    //     var data_all = '<option value="All">All</option>';
    //     $('#pincode').html(data_all);
    // }
     
}

function get_mpins(){
    
    var checkboxes = document.getElementsByName('chk[]');
    var dist_ids = "";
    for (var i=0, n=checkboxes.length;i<n;i++) 
    {
        if (checkboxes[i].checked) 
        {
            dist_ids += checkboxes[i].value+",";
        }
    }
    $.post('get-pincode-by-mdist-id',{dist_ids:dist_ids}, function(data){
        $('#pincode').html(data);
    });
     
}

function form_toggle(first,second)
{
    $('#'+first).show();
    $('#'+second).hide();
}


</script>

<script>
    
    setTimeout(function() {
        var successMessage = $('#succ1');
        if (successMessage.length > 0) {
            successMessage.remove();
        }
        
        var errorMessage = $('#err1');
        if (errorMessage.length > 0) {
            errorMessage.remove();
        }
    }, 1000);
</script>

<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title">Map Pincode</h5>
                            <h5 class="card-title" style="text-color:blue;">
                                <a href="#" onclick="form_toggle('pin_form','table_id');" style="cursor: pointer;">Add</a> / <a href="#" onclick="form_toggle('table_id','pin_form');" style="cursor: pointer;">View</a>
                            </h5>
                            <h5><font color="green" id="succ1">@if(Session::has('message')) {{ Session::get('message') }}@endif</font></h5> 
                            <h5><font color="red" id="err1">@if(Session::has('error')){{ Session::get('error') }} @endif</font></h5>
                                  
                            <form id="pin_form"  method="post" action="{{route('save-map-pincode')}}" class="form-horizontal" onSubmit="return validate_pincode()" style="display: none;">
                           
                                <div class="form-row">
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">Service Center</label>
                                            <select name="center_id" id="center_id"  class="form-control chzn-select chzn-rtl" tabindex="9" required="">
                                                <option value="">Select</option>
                                                @foreach($center_arr as $center)
                                                <option value="{{$center->center_id}}">{{$center->center_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">Country</label>
                                            <select name="country" id="country" onchange="get_states(this.value,'state_id')" class="form-control chzn-select chzn-rtl" tabindex="9" required="">
                                                <option value="">Select</option>
                                                @foreach($countryArr as $country)
                                                <option value="{{$country->country_id}}">{{$country->country_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                
                                    <div class="col-md-3">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">State</label>
                                
                                            <select name="state_id" id="state_id" onchange="get_district(this.value,'dist_id')" class="form-control" required="">
                                                <option value="">Select</option>
                                                
                                            </select>
                                        </div>
                                    </div>
								
                                
                                </div>
                            
                                <div class="form-row" id="dist_id"></div>
                            
                                <div class="form-row">
                                    <div class="col-md-3">
                                        <div class="position-relative form-group">
                                            <label for="pincode" class="">Pincode</label><br>
                                            <!-- <select class="form-control" id="pincode" onfocus="get_mpins();" name="pincode[]"  required="" multiple>
                                                <option value="">Select</option>
                                            </select> -->
                                            <select style="width:100%;" name="pincode[]" id="pincode" multiple="multiple" onfocus="get_mpins();">
                                                <option value="">Select</option>
                                            </select>
                                        </div>     
                                    </div>
                                 
                                
                                    <?php //if(!empty($vendorArr))
                                    // { ?>
                                    <!-- <table id="table1" class="table table-striped table-bordered" style="width:100%">
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
                                            <td><input type="checkbox" name="vendor[]" class="" value="<?php //echo $vendor->id; ?>" ><?php //echo $srno++; ?></td>
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
                                        <input type="submit"  class="btn btn-success btn-grad" data-original-title="" title="" value="Save" >
                                        &nbsp;<a href="map-pincode" class="btn btn-danger btn-grad" data-original-title="" title="">Exit</a>
                                    </div>
                                </div> 
                            </form>
                                <div id="table_id">
                                     <div class="form-row">
                                         <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">Service Center</label>
                                            <select name="center_id" id="center_id1"  class="form-control">
                                                <option value="">Select</option>
                                                @foreach($center_arr as $center)
                                                <option value="{{$center->center_id}}">{{$center->center_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">Country</label>
                                            <select name="country" id="country1" onchange="get_states(this.value,'state_id1')" class="form-control" >
                                                <option value="">Select</option>
                                                @foreach($countryArr as $country)
                                                <option value="{{$country->country_id}}">{{$country->country_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                
                                    <div class="col-md-3">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">State</label>
                                
                                            <select name="state_id" id="state_id1" class="form-control" >
                                                <option value="">Select</option>
                                                
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">Pincode</label>
                                            <input type="text" name="pin1" id="pin1" class="form-control" minlength="6" maxlength="6">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                         <label for="examplePassword11" class="">&nbsp;</label><br>
                                         
                                         <button type="button"  class="btn btn-success btn-grad" data-original-title="" title="" onclick="get_pins()" value="Search"> Search</button>
                                        </div>
                                    </div>        
                                              
                                </div>
                            
                            
                            
                                     <div id="pincode_view">
                                         
                                     </div>
                            
                            
                                 </div>         
                                 
                                 
                                 
                    </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection
