@extends('layouts.app')
@section('content')

<script>
                                    

menu_select('{{$url}}');                                                              
</script>

<script>
    
    
    
function submitForm(form,path,id){

    $("#msgerr").remove();
    var formData    =   $(form).serialize();
    var project_id  =   $("#project_id").val();
    var user_rights =   $("#user_rights").val();
    var designation =   $("#designation").val();
    var department  =   $("#department").val();
    
    if(project_id ==""){
        $("#project_id").focus();
        $("#project_id").after("<span id='msgerr' style='color:red;font-size:11px;'>Please select project name.</span>");
        return false;
    }
    else if(user_rights ==""){
        $("#user_rights").focus();
        $("#user_rights").after("<span id='msgerr' style='color:red;font-size:11px;'>Please select user rights.</span>");
        return false;
    }
    else if(designation ==""){
        $("#designation").focus();
        $("#designation").after("<span id='msgerr' style='color:red;font-size:11px;'>Please select designation.</span>");
        return false;
    }
    else if(department ==""){
        $("#department").focus();
        $("#department").after("<span id='msgerr' style='color:red;font-size:11px;'>Please select department.</span>");
        return false;
    }
    else{
        $.post(path,formData).done(function(data){
            $("#"+id).after('<p class="alert alert-info" id="msgerr">'+data+'</p>');
            //alert(data);
        });
        return true;
    }  
}

function reloadPage(){
    location.reload(true);
}
</script>
<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body"><h5 class="card-title">View Service Center</h5>
                            
                                @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                        

                     </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection