@extends('layouts.app')
@section('content')

<script>
menu_select('{{$url}}');  

function reloadPage(){
    location.reload(true);
}



function get_states(country_id,state_id){
    
    $.post('get-states',{country_id:country_id}, function(data){
        $('#'+state_id).html(data);
    }); 
     
}

function get_states_by_region(region_id,state_id){
    
    $.post('get-state-by-region-id',{region_id:region_id}, function(data){
        $('#'+state_id).html(data);
    }); 
     
}


function get_area(){
    var country_id = $('#country1').val();
    var state_id = $('#state_id1').val();
    var reg_man_id = $('#reg_man_id1').val();
    var region_id = $('#region_id1').val();
    
    $.post('get-rm-area',{region_id:region_id,country_id:country_id,reg_man_id:reg_man_id,state_id:state_id}, function(data){
        $('#area_view').html(data);
    }); 
     
}

function remove_area(reg_map_id){
    //var country_id = $('#country').val();
    $.post('remove-rm-area',{reg_map_id:reg_map_id}, function(data){
        if(data==1)
        {
            $('#succ1').html("Area Removed Successfully.");
        }
        else
        {
            $('#err1').html("Area not Removed.");
        }
        get_area();
    });   
}

function validate_area(){
    $("#msgerr").remove();
    
    var country_id  =   $("#country").val();
    var state_name       =   $.trim($("#state_id").val());
    var dist_id       =   $.trim($("#dist_id").val());
    var region_id       =   $.trim($("#region_id").val());
    
    var reg_man_id_type       =   $.trim($("#reg_man_id").val());
    var split_arr = reg_man_id_type.split("_");
    var reg_man_id = split_arr[0];
    var reg_man_type = split_arr[1];
    
    if(reg_man_type==='ASM')
    {
        if(reg_man_id ===""){
            $("#reg_man_id").focus();
            $("#reg_man_id").after("<span id='msgerr' style='color:red;'>Please Select Regional Manager.</span>");
            return false;
        }
        else if(country_id ===""){
            $("#country").focus();
            $("#country").after("<span id='msgerr' style='color:red;'>Please Select Country.</span>");
            return false;
        }
        else if(state_name ===""){
            $("#state_id").focus();
            $("#state_id").after("<span id='msgerr' style='color:red;'>Please Select State Name.</span>");
            return false;
        }
        
        else if(dist_id ==""){
            $("#dist_id").focus();
            $("#dist_id").after("<span id='msgerr' style='color:red;'>Please Select District.</span>");
            return false;
        }
    }
    else if(reg_man_type==='RSM')
    {
        if(reg_man_id ===""){
            $("#reg_man_id").focus();
            $("#reg_man_id").after("<span id='msgerr' style='color:red;'>Please Select Regional Manager.</span>");
            return false;
        }
        else if(region_id ==""){
            $("#region_id").focus();
            $("#region_id").after("<span id='msgerr' style='color:red;'>Please Select Region.</span>");
            return false;
        }
    }
    
    
    return true;    
}


function get_district(state_id,div_id){
    
    $.post('get-district-by-state-id',{state_id:state_id,all:1}, function(data){
        if(data!=='<option value="">No Records Found</option>')
        {
            data = '<option value="All">All</option>'+data;
        }
        $('#'+div_id).html(data);
    }); 
     
}

function get_map_fields(field_value)
{
    //alert(field_value);
    if(field_value=='')
    {
        $('#asm_disp').hide();
        $('#rsm_disp').hide();
    }
    
    var split_arr = field_value.split("_");
    //console.log(split_arr[1]);
    if(split_arr[1]==='ASM' || split_arr[1]==='Coordinator' || split_arr[1]==='Store' || split_arr[1]==='BSM' || split_arr[1]==='Account')
    {
        $('#asm_disp').show();
        $('#rsm_disp').hide();
    }
    else if(split_arr[1]==='RSM' ||split_arr[1]==='NSM' )
    {
        $('#rsm_disp').show();
        $('#asm_disp').hide();
    }
    else
    {
        $('#asm_disp').hide();
        $('#rsm_disp').hide();
    }
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
                            <h5 class="card-title">Map Region/Area</h5>
                            <h5 class="card-title" style="text-color:blue;">
                                <a href="#" onclick="form_toggle('map_form','table_id');" style="cursor: pointer;">Add</a> / <a href="#" onclick="form_toggle('table_id','map_form');" style="cursor: pointer;">View</a>
                            </h5> 
                            
                            <h5><font color="green" id="succ1">@if(Session::has('message')) {{ Session::get('message') }} @endif</font></h5>
                            <h5><font color="red" id="err1">@if(Session::has('error')) {{ Session::get('error') }} @endif</font></h5>
                                  
                                 <form id="map_form"  method="post" action="{{route('save-map-area')}}" class="form-horizontal" onSubmit="return validate_area()" style="display: none;">
                           
                            <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">Regional Manager</label>
                                            <select name="reg_man_id" id="reg_man_id" onchange="get_map_fields(this.value)" class="form-control" required="">
                                                <option value="">Select</option>
                                                @foreach($regional_man as $man)
                                                <option value="{{$man->reg_man_id}}_{{$man->user_type}}">{{$man->man_name}} ({{$man->user_type}})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                
                                    
                                
                                    
                                        <div id="rsm_disp" style="display:none" class="col-md-6">
                                            <div class="position-relative form-group">
                                                <label for="exampleEmail11" class="">Region</label>
                                                <select name="region_id" id="region_id" class="form-control"  >
                                                    <option value="">Select</option>
                                                    <option value="All">All</option>
                                                    @foreach($region_master as $region)
                                                    <option value="{{$region->region_id}}">{{$region->region_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    
                                
                            </div>
                                    <div class="form-row" id="asm_disp" style="display:none">
                                        <div class="col-md-6">
                                            <div class="position-relative form-group">
                                                <label for="exampleEmail11" class="">Country</label>
                                                <select name="country" id="country" onchange="get_states(this.value,'state_id')" class="form-control" >
                                                    <option value="">Select</option>
                                                    @foreach($countryArr as $country)
                                                    <option value="{{$country->country_id}}">{{$country->country_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            <div class="position-relative form-group">
                                                <label for="examplePassword11" class="">State</label>

                                                <select name="state_id" id="state_id" onchange="get_district(this.value,'dist_id')" class="form-control" >
                                                    <option value="">Select</option>

                                                </select>
                                            </div>
                                        </div>
                                <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            <div class="position-relative form-group">
                                                <label for="examplePassword11" class="">District</label>

                                                <select name="dist_id" id="dist_id"  class="form-control">
                                                    <option value="">Select</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                            
                            
                              
                            
                            <div class="form-row">
                                
                                
                                     <div class="col-md-6">
                                         <input type="submit"  class="btn btn-success btn-grad" onclick="return validate_area()" data-original-title="" title="" value="Save" >
                                &nbsp;<a href="map-region" class="btn btn-danger btn-grad" data-original-title="" title="">Exit</a>
                            </div>
                            </div> 
                        </form>
                                 <div id="table_id">
                                     <div class="form-row">
                                         <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">Regional Manager</label>
                                            <select name="reg_man_id1" id="reg_man_id1" class="form-control" required="">
                                                <option value="">Select</option>
                                                @foreach($regional_man as $man)
                                                <option value="{{$man->reg_man_id}}_{{$man->user_type}}">{{$man->man_name}} ({{$man->user_type}})</option>
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
                                                    <label for="examplePassword11" class="">Region</label>

                                                    <select name="region_id1" id="region_id1" onchange="get_states_by_region(this.value,'state_id1')" class="form-control"  >
                                                    <option value="">Select</option>
                                                    @foreach($region_master as $region)
                                                    <option value="{{$region->region_id}}">{{$region->region_name}}</option>
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
                                        <div class="col-md-3">
                                            <div class="position-relative form-group">
                                                <label for="examplePassword11" class="">&nbsp;</label><br>

                                                <button type="button"  class="btn btn-success btn-grad" data-original-title="" title="" onclick="get_area()" value="Search" > Search</button>
                                            </div>
                                            </div>        
                                              
                            </div>
                            
                            
                            
                                     <div id="area_view">
                                         
                                     </div>
                            
                            
                                 </div>         
                                 
                                 
                                 
                    </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
function form_toggle_flash()
{
    if('<?php echo Session::get('form_id');?>'=='1')
    {
        form_toggle('map_form','table_id');
       // console.log("1");
    }
}

form_toggle_flash();
</script>

@endsection
