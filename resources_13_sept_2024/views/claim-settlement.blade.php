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


function validate_state(){
    $("#msgerr").remove();
    
    var country_id   =   $("#country").val();
    var state_name   =   $.trim($("#state_id").val());
    var center       =   $.trim($("#center_id").val());
    var pincode      =   $.trim($("#pincode").val());
    
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


function get_asc_retainer(type)
{
    if(type== "Search")
    {

        var region_id = $('#region_id1').val();
        var center_id = $('#center_id').val();

        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();

        $.post('get-job-claim',{region_id:region_id,center_id:center_id,from_date:from_date,to_date:to_date,type:type}, function(data){
            
            $('#area_view').html(data);

        }); 
    }else if(type== "Disperse")
    {
        var region_id = $('#region_id2').val();
        var center_id = $('#center_id1').val();

        var from_date = $('#from_date1').val();
        var to_date = $('#to_date1').val();
        var disperse_on = $('#disperse_on').val();
        var transaction_id = $('#transaction_id').val();

        $.post('get-disperse-claim',{region_id:region_id,center_id:center_id,from_date:from_date,to_date:to_date,disperse_on:disperse_on,transaction_id:transaction_id}, function(data){
            
            $('#pincode_view').html(data);
            //$('.datepicker').datepicker();

            jQuery(document).ready(function($) {
                $( ".datepicker" ).datepicker();
                
            });

        });
    }else if(type == "Download")
    {
        $.post('disperse-export',{region_id:region_id,center_id:center_id,from_date:from_date,to_date:to_date,disperse_on:disperse_on,transaction_id:transaction_id}, function(data){
            
            var blob = new Blob([data]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = "disperse.xls";
            link.click();

        });
            
    }
 
}

function form_toggle(first,second)
{
    $('#'+first).show();
    $('#'+second).hide();
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

function update_amount(tag_id)
{

    var disperse_amount = $('#disperse_amount').val();
    var disperse_date = $('#disperse_date').val();
    var transaction_id = $('#transaction_id_input').val();
    console.log(transaction_id);


    if (disperse_amount === '') {
        alert('Please fill the disperse amount.');
        return false;
    }
    if (disperse_date === '') {
        alert('Please fill the Disperse Date.');
        return false;
    }
    if (transaction_id === '') {
        alert('Please fill the Transaction ID.');
        return false;
    }
    
    $.post('claim-disperse',{tag_id:tag_id,disperse_amount:disperse_amount,disperse_date:disperse_date,transaction_id:transaction_id}, function(data){
        if(data==1)
        {
            alert("Disperse Update Successfully.");
        }
        else
        {
            alert("Disperse Update not Successfully.");
        }
        get_asc_retainer("Disperse");
    });
}


</script>

<script>

    function showAlert() {
        alert("Claim Generated Successfully");
        return true;
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
    }, 1000);

    function get_asc(div,region_id)
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
                $('#center_id'+div).html(result)
            }
        });
    }
</script>

<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title">Claim Settlement</h5>
                            <h5 class="card-title" style="text-color:blue;">
                                <a href="#" onclick="form_toggle('pin_form','table_id');" style="cursor: pointer;">Generate</a> / <a href="#" onclick="form_toggle('table_id','pin_form');" style="cursor: pointer;">Disperse</a>
                            </h5>
                            <h5><font color="green" id="succ1">@if(Session::has('message')) {{ Session::get('message') }}@endif</font></h5> 
                            <h5><font color="red" id="err1">@if(Session::has('error')){{ Session::get('error') }} @endif</font></h5>
                                  
                            <form id="pin_form"  method="post" action="{{route('save-map-pincode')}}" class="form-horizontal" onSubmit="return validate_pincode()" >
                           
                                <div class="form-row">
                                    <div class="col-md-3">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">Region</label>
                                            <select name="region_id1" id="region_id1"  class="form-control" onchange="get_asc('1',this.value)" >
                                                <option value="">Select</option>
                                                <option value="All">All</option>
                                                @foreach($region_master as $region)
                                                <option value="{{$region->region_id}}">{{$region->region_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">ASC Name</label>

                                            <select id="center_id1" name="center_id" class="form-control">
                                            <option value="">Select</option>
                                            <!-- @foreach($asc_master as $asc)
                                                <option value="{{$asc->center_id}}" <?php //if( $asc_code==$asc->center_id) 
                                                        //{ echo 'selected';} ?>>{{$asc->center_name}} - {{$asc->asc_code}}</option>
                                            @endforeach -->
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">From Date</label>
                                            <input name="from_date" autocomplete="off" id="from_date" placeholder="From" type="text" value="<?php echo $from_date; ?>" class="form-control datepicker">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">To Date</label>
                                            <input name="to_date" autocomplete="off" id="to_date" placeholder="To" type="text" value="<?php echo $to_date; ?>" class="form-control datepicker">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">&nbsp;</label><br>
                                            <button type="button"  class="btn btn-success btn-grad" data-original-title="" title="" onclick="get_asc_retainer('Search')" value="Search">Search</button>
                                            <!-- <button type="button"  class="btn btn-success btn-grad" data-original-title="" title="" onclick="get_asc_retainer('Download')" value="Download"> Download Excel</button> -->
                                        </div>
                                    </div>
                                
                                
                                </div>
                            
                                <div class="form-row" id="area_view"></div>
                                
                            </form>
                                <div id="table_id" style="display: none;">
                                    <div class="form-row">
                                        <div class="col-md-3">
                                            <div class="position-relative form-group">
                                                <label for="examplePassword11" class="">Region</label>
                                                <select name="region_id1" id="region_id2"  class="form-control" onchange="get_asc('2',this.value)" >
                                                    <option value="">Select</option>
                                                    <option value="All">All</option>
                                                    @foreach($region_master as $region)
                                                    <option value="{{$region->region_id}}">{{$region->region_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="position-relative form-group">
                                                <label for="examplePassword11" class="">Asc Name</label>

                                                <select id="center_id2" name="center_id" class="form-control">
                                                <option value="">Select</option>
                                                <!-- @foreach($asc_master as $asc)
                                                    <option value="{{$asc->center_id}}" <?php //if( $asc_code==$asc->center_id) 
                                                            //{ echo 'selected';} ?>>{{$asc->center_name}} - {{$asc->asc_code}}</option>
                                                @endforeach -->
                                                </select>
                                            </div>
                                        </div>
                                
                                        <div class="col-md-3">
                                            <div class="position-relative form-group">
                                                <label for="exampleEmail11" class="">From Date</label>
                                                <input name="from_date" autocomplete="off" id="from_date1" placeholder="From" type="text" value="<?php echo $from_date; ?>" class="form-control datepicker">
                                            </div>
                                        </div>
                                    
                                        <div class="col-md-3">
                                            <div class="position-relative form-group">
                                                <label for="examplePassword11" class="">To Date</label>
                                                <input name="to_date" autocomplete="off" id="to_date1" placeholder="To" type="text" value="<?php echo $to_date; ?>" class="form-control datepicker">
                                            </div>
                                        </div>
                                                     
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-3">
                                            <div class="position-relative form-group">
                                                <label for="examplePassword11" class="">Dispersed On</label>
                                                <input name="disperse_on" autocomplete="off" id="disperse_on" placeholder="To" type="text" value="<?php echo $disperse_on; ?>" class="form-control datepicker">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="position-relative form-group">
                                                <label for="examplePassword11" class="">Transaction Id</label>
                                                <input name="transaction_id" autocomplete="off" id="transaction_id" placeholder="Transaction Id" type="text" value="<?php echo $transaction_id; ?>" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">&nbsp;</label><br>
                                            
                                            <button type="button"  class="btn btn-success btn-grad" data-original-title="" title="" onclick="get_asc_retainer('Disperse')" value="Search">Search</button>&nbsp;&nbsp;&nbsp;
                                            <button type="button"  class="btn btn-success btn-grad" data-original-title="" title="" onclick="get_asc_retainer('Download')" value="Download"> Download Excel</button>
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
