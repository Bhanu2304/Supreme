@extends('layouts.app')
@section('content')

<script>

menu_select('{{$url}}'); 
function reloadPage(){
    location.reload(true);
}

function check_record(country_id,state_name){
    
        $.post('state-exist',{country_id:country_id,state_name:state_name}, function(data){
            if(data==2)
            {
                return true;
            }
            else 
            {
                 $("#state_name").focus();
        $("#state_name").after("<span id='msgerr' style='color:red;'>State Name Allready Exist.</span>");
                return false;
            }
        }); 
     
}

function validate_state(){
    $("#msgerr").remove();
    
    var country_id  =   $("#country").val();
    var state_name       =   $.trim($("#state_name").val());
     
    
    if(country_id ==""){
        $("#country_id").focus();
        $("#country_id").after("<span id='msgerr' style='color:red;'>Please Select Country.</span>");
        return false;
    }
    else if(state_name ==""){
        $("#state_name").focus();
        $("#state_name").after("<span id='msgerr' style='color:red;'>Please enter State Name.</span>");
        return false;
    }
    
    check_record(country_id,state_name);
    return true;    
}

function get_district(country_id){
    
    $.post('get-district',{state_id:country_id}, function(data){
        $('#state_view').html(data);
    }); 
     
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
                        <div class="card-body"><h5 class="card-title"> District Management</h5>
                            <h5 class="card-title" style="text-color:blue;">
                                <a href="#" onclick="form_toggle('state_form','table_id');" style="cursor: pointer;">Add</a> / <a href="#" onclick="form_toggle('table_id','state_form');" style="cursor: pointer;">View</a>
                            </h5> 
                            
                                @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                  
                                 <form id="state_form" method="post" action="{{route('save-district')}}" class="form-horizontal"  style="display:none;">
                           
                            <div class="form-row">
                                    <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">State <span style="color: #f00;">*</span></label>
                                            <select name="country" id="country" data-placeholder="" class="form-control" tabindex="9" required="">
                                                <option value="">Select</option>
                                                
                                                @foreach($stateArr as $state)
                                                <option value="{{$state->state_id}}">{{$state->state_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                
                                <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">District <span style="color: #f00;">*</span></label>
                                
                                            <input class="form-control" type="text" id="dist_name" name="dist_name" data-original-title="District name" data-placement="top" placeholder="District Name" required="">
                                        </div>
                                </div>
                            </div>
                            <div class="form-row">
                                     <div class="col-md-6">
                                <input type="submit"  class="btn btn-success btn-grad" data-original-title="" title="" value="Save" >
                                &nbsp;<a href="dashboard" class="btn btn-danger btn-grad" data-original-title="" title="">Exit</a>
                            </div>
                            </div> 
                        </form>
                                 
                                 <div id="table_id">
                                     <div class="form-group">
                                <label for="text1" class="control-label col-lg-2">Select State</label>
                                <div class="col-lg-4">
                                    <select name="country" id="country" onchange="get_district(this.value)" data-placeholder="" class="form-control chzn-select chzn-rtl multiselect" tabindex="9">
                                        <option value="">Select</option>
                                        <option value="All">All</option>
                                        @foreach($stateArr as $state)
                                                <option value="{{$state->state_id}}">{{$state->state_name}}</option>
                                                @endforeach
                                    </select> <font color="red">Note=></font> Please Select State to See District
                                </div>
                            </div>
                            
                            <span id="state_view"></span>
                            
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
