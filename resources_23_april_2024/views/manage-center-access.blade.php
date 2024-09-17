@extends('layouts.app')
@section('content')

<?php //print_r($pageArr); exit; ?>





<script>
                                    

menu_select('{{$url}}');                                                             
</script>

<style>

* { margin: 0; padding: 0; }

#page-wrap {
  margin: auto 0;
}

.treeview {
  margin: 10px 0 0 20px;
}

ul { 
  list-style: none;
}

.treeview li {
  
  padding: 2px 0 2px 16px;
}

.treeview > li:first-child > label {
  /* style for the root element - IE8 supports :first-child
  but not :last-child ..... */
  
}

.treeview li.last {
  background-position: 0 -1766px;
}

.treeview li > input {
  height: 16px;
  width: 16px;
  /* hide the inputs but keep them in the layout with events (use opacity) */
  opacity: 0;
  filter: alpha(opacity=0); /* internet explorer */ 
  -ms-filter:"progid:DXImageTransform.Microsoft.Alpha(opacity=0)"; /*IE8*/
}

.treeview li > label {
  background: url(https://www.thecssninja.com/demo/css_custom-forms/gr_custom-inputs.png) 0 -1px no-repeat;
  /* move left to cover the original checkbox area */
  margin-left: -20px;
  /* pad the text to make room for image */
  padding-left: 20px;
}

/* Unchecked styles */

.treeview .custom-unchecked {
  background-position: 0 -1px;
}
.treeview .custom-unchecked:hover {
  background-position: 0 -21px;
}

/* Checked styles */

.treeview .custom-checked { 
  background-position: 0 -81px;
}
.treeview .custom-checked:hover { 
  background-position: 0 -101px; 
}

/* Indeterminate styles */

.treeview .custom-indeterminate { 
  background-position: 0 -141px; 
}
.treeview .custom-indeterminate:hover { 
  background-position: 0 -121px; 
}
</style>

<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                          <h5 class="card-title">Manage Center Rights</h5>
                            
                            @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                            @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif

                            <div class="form-row">
                                <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="exampleEmail11" class="">Center Name</label>
                                        <select name="selected_user" id="selected_user" class="form-control" required="">
                                            <option value="">Select</option>
                                            @foreach($center_data as $user)
                                            <option value="{{$user['id']}}">{{$user['email']}} - {{$user['center_name']}} - {{$user['center_city']}}</option>
                                            @endforeach
                                          
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                           <div class="box" id="panel_box">
                               <div style="color:#828282;margin-top:20px;visibility:hidden;" id="menu_tree">
                                   
                               </div>    
                           </div> 
                            
                            <div class="form-row">
                                <div class="col-md-6" id="update_btn" style="visibility:hidden;margin-top:15px;">
                                <input type="submit" id="update_btn_main" onclick="check()" class="btn btn-success btn-grad" data-original-title="" title="" value="Save">
                                &nbsp;<a href="{{route('home')}}" class="btn btn-danger btn-grad" data-original-title="" title="">Exit</a>
                            </div>
                            </div> 
                        <div id="status" style="visibility:hidden;"><span id="status_span" style="color:green; display:block;padding-top:5px;padding-bottom:5px;margin-left:200px;">Updated Successfully</span></div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
  
	<script>
    

  

  function checkboxChanged() {
    var $this = $(this),
        checked = $this.prop("checked"),
        container = $this.parent(),
        siblings = container.siblings();

    container.find('input[type="checkbox"]')
    .prop({
        indeterminate: false,
        checked: checked
    })
    .siblings('label')
    .removeClass('custom-checked custom-unchecked custom-indeterminate')
    .addClass(checked ? 'custom-checked' : 'custom-unchecked');

    checkSiblings(container, checked);
  }

  function checkSiblings($el, checked) {
    var parent = $el.parent().parent(),
        all = true,
        indeterminate = false;

    $el.siblings().each(function() {
      return all = ($(this).children('input[type="checkbox"]').prop("checked") === checked);
    });

    if (all && checked) {
      parent.children('input[type="checkbox"]')
      .prop({
          indeterminate: false,
          checked: checked
      })
      .siblings('label')
      .removeClass('custom-checked custom-unchecked custom-indeterminate')
      .addClass(checked ? 'custom-checked' : 'custom-unchecked');

      checkSiblings(parent, checked);
    } 
    else if (all && !checked) {
      indeterminate = parent.find('input[type="checkbox"]:checked').length > 0;

      parent.children('input[type="checkbox"]')
      .prop("checked", checked)
      .prop("indeterminate", indeterminate)
      .siblings('label')
      .removeClass('custom-checked custom-unchecked custom-indeterminate')
      .addClass(indeterminate ? 'custom-indeterminate' : (checked ? 'custom-checked' : 'custom-unchecked'));

      checkSiblings(parent, checked);
    } 
    else {
      $el.parents("li").children('input[type="checkbox"]')
      .prop({
          indeterminate: true,
          checked: false
      })
      .siblings('label')
      .removeClass('custom-checked custom-unchecked custom-indeterminate')
      .addClass('custom-indeterminate');
    }
  }

</script>
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
                $("#menu_tree input:checkbox").prop("checked", false);
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
            $("#menu_tree").css({"visibility": "hidden"});
            $("#update_btn").css({"visibility": "hidden"});;
        }else{
            
                
            $.post('get-pages',{user: $('#selected_user').val()}, function(menu){
            $("#menu_tree").html(menu);
            $("#panel_box input:checkbox").prop("checked", false); 
            $('input[type="checkbox"]').change(checkboxChanged);
            $.getJSON("<?php echo 'get-access'; ?>", {user: $('#selected_user').val()}, function (data) {
            if(Object.keys(data).length === 0)
            {
               // $("#main_container input:checkbox").prop("checked", true);
                
            }
            else
            {
            $.each(data, function (key, val) {
                $.each(val, function (key1, val1) {
                    //console.log(val1);
                    var acces = val1.access;
                    var p_acces = val1.parent_access;
                    var acces_arr = acces.split(',');
                    
                    
                    
                    $.each(acces_arr, function (index, value) {
                        
                        try{
                            $('#lbl' + value).removeClass('custom-unchecked');
                            $('#lbl' + value).addClass('custom-checked');
                            $('#' + value).prop('checked',true);
                        }
                        catch(err)
                        {
                            
                        }
                    });
                    
                    var p_acces_arr = p_acces.split(',');
                    $.each(p_acces_arr, function (index, value) {
                        try
                        {
                          //console.log(value);
                          $('#lbl' + value).removeClass('custom-unchecked');
                          $('#lbl' + value).addClass('custom-checked');
                          $('#' + value).prop('checked',true);
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

  