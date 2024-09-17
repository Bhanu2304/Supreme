@extends('layouts.app')
@section('content')

<script>


function reloadPage(){
    location.reload(true);
}



function get_states(country_id){
    
    $.post('get-states',{country_id:country_id}, function(data){
        $('#state_id').html(data);
    }); 
     
}

function get_pincode(state_id){
    var country_id = $('#country').val();
    $.post('get-pincode',{country_id:country_id,state_id:state_id}, function(data){
        $('#pincode_view').html(data);
    }); 
     
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
                        <div class="card-body"><h5 class="card-title">View Pincode</h5>
                            
                                @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                  
                        
                           
                            <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">Country</label>
                                            <select name="country" id="country" onchange="get_states(this.value)" class="form-control chzn-select chzn-rtl" tabindex="9" required="">
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
                                
                                            <select name="state_id" id="state_id" onchange="get_pincode(this.value)" data-placeholder="" class="form-control chzn-select chzn-rtl multiselect" tabindex="9" required="">
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
@endsection
