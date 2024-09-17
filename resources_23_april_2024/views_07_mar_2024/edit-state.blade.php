@extends('layouts.app')
@section('content')


<?php //print_r($data_state_record); exit;  ?>

<script>
                                    

menu_select('{{$url}}');                                                              
</script>

<script>


function reloadPage(){
    location.reload(true);
}

function check_record(country_id,state_name,state_id)
{
    
        $.post('state-exist-update',{country_id:country_id,state_name:state_name,state_id:state_id}, function(data){
            if(data===2)
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
    var state_id       =   $.trim($("#state_id").val());
    
    check_record(country_id,state_name,state_id);
    
    if(country_id ===""){
        $("#country_id").focus();
        $("#country_id").after("<span id='msgerr' style='color:red;'>Please Select Country.</span>");
        return false;
    }
    else if(state_name ===""){
        $("#state_name").focus();
        $("#state_name").after("<span id='msgerr' style='color:red;'>Please enter State Name.</span>");
        return false;
    }
    
    check_record(country_id,state_name,state_id);
    return true;    
}


</script>

<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body"><h5 class="card-title">Edit State</h5>
                        <form method="post" action="{{route('update-state')}}" class="form-horizontal" onSubmit="return validate_state()" >
                            @if(Session::has('error'))
                            <p class="alert {{Session::get('alert-class')}}">{{ Session::get('error') }}</p>
                            @endif
                            
                            @if(Session::has('message'))
                            <p class="alert {{Session::get('alert-class')}}">{{ Session::get('message') }}</p>
                            @endif
                            
                            <div class="form-group">
                                <label for="text1" class="control-label col-lg-2"> Country</label>
                                <div class="col-lg-4">
                                    <select name="country_id" id="country_id" data-placeholder="" class="form-control" tabindex="9" required="">
                                        @foreach($countryArr as $country)
                                        <option value="{{$country->country_id}}" <?php if($country->country_id==$data_state_record['country_id']) { echo 'selected'; }  ?>>{{$country->country_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-4">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">Region</label>
                                            <select name="region_id" id="region_id" data-placeholder="" class="form-control" tabindex="9" required="">
                                                <option value="">Select</option>
                                                @foreach($region_master as $region)
                                                <option value="{{$region->region_id}}" <?php if($region->region_id==$data_state_record['region_id']) { echo 'selected'; }  ?>>{{$region->region_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                </div>
                                
                                <label for="pass1" class="control-label col-lg-2">State</label>
                                <div class="col-lg-4">
                                    <input class="form-control" type="text" id="state_name" name="state_name" value="<?php echo $data_state_record['state_name']; ?>"  placeholder="State Name" required="">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <input type="submit"  class="btn btn-success btn-grad" data-original-title="" title="" value="Update" >
                                &nbsp;<a href="add-state" class="btn btn-danger btn-grad" data-original-title="" title="">Exit</a>
                            </div>
                            <input type="hidden" id="state_id" name="state_id" value="<?php echo $data_state_record['state_id'];  ?>" /> 
                        </form>
                    </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection
