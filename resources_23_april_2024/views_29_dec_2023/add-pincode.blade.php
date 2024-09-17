@extends('layouts.app')
@section('content')

<script>
menu_select('{{$url}}');  

function reloadPage(){
    location.reload(true);
}

function check_record(country_id,state_name,place,pincode)
{
    
$.post('pin-exist',{country_id:country_id,state_name:state_name,place:place,pincode:pincode}, function(data){
if(data===2)
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

function get_district(state_id,div_id){   
    //var state_id = $('#state_id').val();
    $.post('get-district-by-state-id',{state_id:state_id}, function(data){
        $('#'+div_id).html(data);
    });      
}



function get_pincode(state_id){
    //var country_id = $('#country').val();
    $.post('get-pincode',{state_id:state_id}, function(data){
        $('#pincode_view').html(data);
    });   
}



function validate_state(){
    $("#msgerr").remove();
    
    var country_id  =   $("#country").val();
    var state_name       =   $.trim($("#state_name").val());
    var place       =   $.trim($("#place").val());
    var pincode       =   $.trim($("#pincode").val());
     
    
    if(country_id ===""){
        $("#country_id").focus();
        $("#country_id").after("<span id='msgerr' style='color:red;'>Please Select Country.</span>");
        return false;
    }
    else if(state_name ===""){
        $("#state_name").focus();
        $("#state_name").after("<span id='msgerr' style='color:red;'>Please Select State Name.</span>");
        return false;
    }
    else if(place ===""){
        $("#place").focus();
        $("#place").after("<span id='msgerr' style='color:red;'>Please Fill Place.</span>");
        return false;
    }
    else if(pincode ===""){
        $("#pincode").focus();
        $("#pincode").after("<span id='msgerr' style='color:red;'>Please Fill Pincode.</span>");
        return false;
    }
    
    check_record(country_id,state_name,place,pincode);
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

function form_toggle(first,second)
{
    $('#'+first).show();
    $('#'+second).hide();
}
</script>

<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title"> Pincode</h5>
                            <h5 class="card-title" style="text-color:blue;">
                                <a href="#" onclick="form_toggle('pin_form','table_id');" style="cursor: pointer;">Add</a> / <a href="#" onclick="form_toggle('table_id','pin_form');" style="cursor: pointer;">View</a>
                            </h5> 
                            
                                @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                  
                                 <form id="pin_form"  method="post" action="{{route('save-pincode')}}" class="form-horizontal" onSubmit="return validate_pincode()" style="display: none;">
                           
                            <div class="form-row">
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
                                    
                                    <div class="col-md-3">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">District</label>
                                
                                            <select name="dist_id" id="dist_id" class="form-control" required="">
                                                <option value="">Select</option>
                                                
                                            </select>
                                        </div>
                                    </div>
                                    
                            </div>
                            
                            <div class="form-row">
                                
                                <div class="col-md-3">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">Place</label>
                                            <input class="form-control" type="text" id="place" name="place" data-original-title="place" data-placement="top" placeholder="Place" required="">
                                        </div>    
                                    </div>
                                    <div class="col-md-3">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">Pincode</label>
                                            <input class="form-control" type="text" id="pincode" name="pincode" data-original-title="Pincode" data-placement="top" placeholder="Pincode" onKeyPress="return checkPinNumber(this.value,event)" required="">
                                        </div>    
                                    </div>
                            </div>
                            
                            <div class="form-row">
                                
                                 
                                
                                <?php //if(!empty($vendorArr))
                               // { ?>
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
                                &nbsp;<a href="{{route('home')}}" class="btn btn-danger btn-grad" data-original-title="" title="">Exit</a>
                            </div>
                            </div> 
                        </form>
                                 <div id="table_id">
                                     <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">Country</label>
                                            <select name="country" id="country" onchange="get_states(this.value,'state_id1')" class="form-control chzn-select chzn-rtl" tabindex="9" required="">
                                                <option value="">Select</option>
                                                @foreach($countryArr as $country)
                                                <option value="{{$country->country_id}}">{{$country->country_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                
                                <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">State</label>
                                
                                            <select name="state_id" id="state_id1" onchange="get_pincode(this.value)" data-placeholder="" class="form-control chzn-select chzn-rtl multiselect" tabindex="9" required="">
                                                <option value="">Select</option>
                                                
                                            </select>
                                        </div>
                                </div>
                            </div>
                            <font color="red">Note =></font> Please choose Country and State to View Pincode
                            
                            
                            <span id="pincode_view"></span>
                            
                            <div class="form-row">
                                     <div class="col-md-6">
                                &nbsp;<a href="dashboard" class="btn btn-danger btn-grad" data-original-title="" title="">Exit</a>
                            </div>
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
