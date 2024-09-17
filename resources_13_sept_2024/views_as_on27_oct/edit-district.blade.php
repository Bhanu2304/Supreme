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
    var state_id       =   $.trim($("#state_id").val());
    
    check_record(country_id,state_name,state_id);
    
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
                        <div class="card-body"><h5 class="card-title">Edit District</h5>
                        <form method="post" action="{{route('update-district')}}" class="form-horizontal"  >
                            @if(Session::has('error'))
                            <p class="alert {{Session::get('alert-class')}}">{{ Session::get('error') }}</p>
                            @endif
                            
                            @if(Session::has('message'))
                            <p class="alert {{Session::get('alert-class')}}">{{ Session::get('message') }}</p>
                            @endif
                            
                            <div class="form-group">
                                <label for="text1" class="control-label col-lg-2"> State</label>
                                <div class="col-lg-4">
                                    <select name="state_id" id="state_id" data-placeholder="" class="form-control" tabindex="9" required="">
                                        @foreach($stateArr as $country)
                                        <option value="{{$country->state_id}}" <?php if($country->state_id==$data_state_record['state_id']) { echo 'selected'; }  ?>>{{$country->state_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <label for="pass1" class="control-label col-lg-2">District Name</label>
                                <div class="col-lg-4">
                                    <input class="form-control" type="text" id="dist_name" name="dist_name" value="<?php echo $data_state_record['dist_name']; ?>"  placeholder="District Name" required="">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                
                                <div class="col-lg-4">
                                    <input type="submit"  class="btn btn-success btn-grad" data-original-title="" title="" value="Update" >
                                    &nbsp;<a href="add-district" class="btn btn-danger btn-grad" data-original-title="" title="">Exit</a>
                                </div>
                                
                            </div>
                            <input type="hidden" id="dist_id" name="dist_id" value="<?php echo $data_state_record['dist_id'];  ?>" /> 
                        </form>
                    </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection
