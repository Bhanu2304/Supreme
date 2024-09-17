@extends('layouts.app')
@section('content')
<script src= "https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> 
<script src = "https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script> 
<link rel = "stylesheet" href = "https://cdnjs.cloudflare.com/ajax/libs/chosen/1.4.2/chosen.min.css" /> 
<script src = "https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script> 
<link rel = "stylesheet" href = "https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" /> 
<script>
    jQuery(document).ready(function($) {
    // Use $ for jQuery code here
        $("#state_id").select2(); 
        $("#dist_id").select2();
        
    });
</script>

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


function get_asc_retainer(type)
{

    var region_id = $('#region_id1').val();
    var center_id = $('#center_id').val();

    $.post('get-asc-retainer',{region_id:region_id,center_id:center_id,type:type}, function(data){
        if(type== "Search")
        {
            $('#area_view').html(data);
        }else{
            var blob = new Blob([data]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = "retainer-ship.xls";
            link.click();
        }
        
    });
     
}

function update_amount(center_id)
{

    var retainership_amount = $('#retainership_amount').val();
    
    $.post('update-amount',{center_id:center_id,retainership_amount:retainership_amount}, function(data)
    {
        if(data==1)
        {
            alert("Amount Update Successfully.");
        }
        else
        {
            alert("Amount Update not Successfully.");
        }
        get_asc_retainer('Search');
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

function get_asc(region_id)
{
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });
    jQuery.ajax({
        url: 'get-asc-name',
        method: 'post',
        data: {
            region_id: region_id,

        },
        success: function(result){
            $('#center_id').html(result)
        }
    });
}

    
setTimeout(function() {
    var successMessage = $('#succ1');
    if (successMessage.length > 0) {
        successMessage.remove();
    }
    
    var errorMessage = $('#err1');
    if (errorMessage.length > 0) {
        errorMessage.remove();
    }
}, 10000);



</script>


<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title">Retainership</h5>   
                            <h5><font color="green" id="succ1">@if(Session::has('message')) {{ Session::get('message') }} @endif</font></h5>
                            <h5><font color="red" id="err1">@if(Session::has('error')) {{ Session::get('error') }} @endif</font></h5>
                                
                                <div id="table_id">
                                    <div class="form-row">
                                        <div class="col-md-3">
                                            <div class="position-relative form-group">
                                                <label for="examplePassword11" class="">Region</label>
                                                <select name="region_id1" id="region_id1"  class="form-control"  onchange="get_asc(this.value)">
                                                    <option value="">Select</option>
                                                    @foreach($region_master as $region)
                                                    <option value="{{$region->region_id}}">{{$region->region_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="position-relative form-group">
                                                <label for="examplePassword11" class="">Asc Name</label>
                                                <select id="center_id" name="center_id" class="form-control">
                                                    <option value="">Select</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="position-relative form-group">
                                                <label for="examplePassword11" class="">&nbsp;</label><br>
                                                <button type="button"  class="btn btn-success btn-grad" data-original-title="" title="" onclick="get_asc_retainer('Search')" value="Search"> Search</button>
                                                <button type="button"  class="btn btn-success btn-grad" data-original-title="" title="" onclick="get_asc_retainer('Download')" value="Download"> Download Excel</button>
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
