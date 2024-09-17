@extends('layouts.app')
@section('content')

<?php //print_r($pageArr); exit; ?>




<?php

function print_tree($parent,$pageArr,$pageMap)
{
    
   // echo '<li>';
            //print_r($pageMap[$parent]); exit;
    if(!empty($pageMap[$parent]))
    {
        echo "<ol class='user-tree'>";
        foreach($pageMap[$parent] as $page)
        {
            print_tree($page,$pageArr,$pageMap);   
        }
        echo '</ol>';
    }
    else
    {
        //echo '<li>'.$pageArr[$parent]['page_name'].'</li>';
        echo "<li><div class=\"checkbox-primary\"><label><input class=\".checkbox-info\" type=\"checkbox\" name=\"selectAll[]\" id=\"" . $parent . "\"  value=\"" . $parent . "\"> " . $pageArr[$parent]['page_name'] . "</label></div></li>";
    }
    
    //echo '</li>';
}

?>
<script>
                                    

menu_select('{{$url}}');                                                             
</script>
<link href="./mystyle.css" rel="stylesheet">
<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body"><h5 class="card-title">Manage Access</h5>
                            
                                @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                  
                        
                           <div id="status" style="visibility:hidden;"><span id="status_span" style="color:green; display:block;padding-top:5px;padding-bottom:5px;margin-left:200px;">Updated Successfully</span></div>
                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="exampleEmail11" class="">User</label>
                                        <select name="selected_user" id="selected_user" data-placeholder="" class="form-control" tabindex="9" required="">
                                            <option value="">Select</option>
                                            @foreach($userArr as $user)
                                            <option value="{{$user->id}}">{{$user->email}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                           <div class="box" id="panel_box"  >
                               <div  style="color:#828282;margin-top:20px;visibility:hidden;" id="menu_tree">
                                   
                               </div>    
                           </div> 
                            
                            <div class="form-row">
                                <div class="col-md-6" id="update_btn" style="visibility:hidden;margin-top:15px;">
                                <input type="submit" id="update_btn_main" onclick="check()" class="btn btn-success btn-grad" data-original-title="" title="" value="Save" >
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
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> 
<script>
function show_child(id)
{
    if(document.getElementById(id).checked == true)
    {
        $("#a"+id+" input:checkbox").prop("checked", true);
    }
    else
    {
        $("#a"+id+" input:checkbox").prop("checked", false);
    }
}  

function auto_fill_all()
{
    //$("#main_container input:checkbox").prop("checked", true);
    if(document.getElementById('all_check').checked == true)
    {
        $("#main_container input:checkbox").prop("checked", true);
    }
    else
    {
        $("#main_container input:checkbox").prop("checked", false);
        var sel = $('#selected_user').val();
            $("#status").css({"visibility": "hidden"});
        if(sel=="none"){
            
            //$("#panel_box").css({"height": "300",});
            $("#menu_tree").css({"visibility": "hidden",});
            $("#update_btn").css({"visibility": "hidden"});console.log("hidden");
        }else{
        $("#main_container input:checkbox").prop("checked", false); 
        
        $.getJSON("<?php echo 'get-access'; ?>", {user: $('#selected_user').val()}, function (data) {
            if(Object.keys(data).length === 0)
            {
               // $("#main_container input:checkbox").prop("checked", true);
                
            }
            else
            {
            $.each(data, function (key, val) {
                $.each(val, function (key1, val1) { 
                    var acces = val1.access;
                    var p_acces = val1.parent_access;
                    var acces_arr = acces.split(',');
                    $.each(acces_arr, function (index, value) {
                        
                        $('#' + value).prop('checked', true);
                        
                    });
                    
                    var p_acces_arr = p_acces.split(',');
                    $.each(p_acces_arr, function (index, value) { 
                        $('#' + value).prop('checked', true);
                        
                    });
                    
                }); 

            });
            
        }
        });
                    $("#status_span").html("Updated Successfully");
                    $("#menu_tree").css({"visibility": "visible"});
                    $("#update_btn").css({"visibility": "visible"});
                    //$("#panel_box").css({"height": "500px",});                    
    }
        
        
        
    }
}
 
function check() {
        var sel = $('#selected_user').val();
        if(sel=="none"){
            $("#status").css({"visibility": "visible"});
            $("#status_span").html("Please Select User");
        }else{
        
        var ride = "";
        $('input:checked').each(function () {
            ride = ride + $(this).val() + ",";

        });
        ride = ride.slice(0, ride.length - 1); 
        
        
        $.get("save-access", {rides: ride,user: $('#selected_user').val()}, function (data) {
            console.log(data);
            if (data = "save") {
                $("#main_container input:checkbox").prop("checked", false);
                $("#status").css({"visibility": "visible"});
                $('#selected_user').val("none");
            }
        });
        }
    }

</script>    
  
<script>
    $('#selected_user').change(function () {
            var sel = $('#selected_user').val();
            $("#status").css({"visibility": "hidden"});
        if(sel=="none"){
            
            //$("#panel_box").css({"height": "300",});
            $("#menu_tree").css({"visibility": "hidden",});
            $("#update_btn").css({"visibility": "hidden"});;
        }else{
            
                
            $.post('get-pages',{user: $('#selected_user').val()}, function(menu){
            $("#menu_tree").html(menu);
            $("#panel_box input:checkbox").prop("checked", false); 
            
            $.getJSON("<?php echo 'get-access'; ?>", {user: $('#selected_user').val()}, function (data) {
            if(Object.keys(data).length === 0)
            {
               // $("#main_container input:checkbox").prop("checked", true);
                
            }
            else
            {
            $.each(data, function (key, val) {
                $.each(val, function (key1, val1) { 
                    var acces = val1.access;
                    var p_acces = val1.parent_access;
                    var acces_arr = acces.split(',');
                    
                    
                    
                    $.each(acces_arr, function (index, value) {
                        //alert(value);
                        try{
                            $('#' + value).prop('checked', true);
                        }
                        catch(err)
                        {
                            
                        }
                        
                        
                    });
                    
                    var p_acces_arr = p_acces.split(',');
                    $.each(p_acces_arr, function (index, value) {
                        try{
                        $('#' + value).prop('checked', true);
                        }
                        catch(err)
                        {
                            
                        }
                    });
                    
                }); 

            });
            
        }
        });
            
            
        }); 
            
            
        
        
        
                    $("#status_span").html("Updated Successfully");
                    $("#menu_tree").css({"visibility": "visible"});
                    $("#update_btn").css({"visibility": "visible"});
                    //$("#panel_box").css({"height": "500px",});                    
    }}); 
</script>  


@endsection

  