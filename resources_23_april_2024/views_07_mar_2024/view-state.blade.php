@extends('layouts.app')
@section('content')

<script>


function reloadPage(){
    location.reload(true);
}

function get_state(country_id){
    
    $.post('get-state',{country_id:country_id}, function(data){
        $('#state_view').html(data);
    }); 
     
}




</script>

<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body"><h5 class="card-title">View State</h5>
                        
                            <div class="form-group">
                                <label for="text1" class="control-label col-lg-2">Select Country</label>
                                <div class="col-lg-4">
                                    <select name="country" id="country" onchange="get_state(this.value)" data-placeholder="" class="form-control chzn-select chzn-rtl multiselect" tabindex="9">
                                        <option value="">Select</option>
                                        @foreach($countryArr as $country)
                                        <option value="{{$country->country_id}}">{{$country->country_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <span id="state_view"></span>
                            
                            <div class="form-row">
                                     <div class="col-md-6">
                                &nbsp;<a href="{{route('home')}}" class="btn btn-danger btn-grad" data-original-title="" title="">Exit</a>
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
