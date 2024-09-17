@extends('layouts.app')

@section('content')
<script>
                                    
  
menu_select('{{$url}}');                                                             
</script>

<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title">NPC fresh stock REPORT</h5>
                            <form method="post" action="export-defective-scrap-report">
                               
                                <div class="form-row">
                                    
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">From Date</label>
                                            <input name="from_date" id="from_date" autocomplete="off" placeholder="From Date" type="text" class="form-control datepicker" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">To Date</label>
                                            <input name="to_date" id="to_date" autocomplete="off" placeholder="To Date" type="text" class="form-control datepicker" required="">
                                        </div>
                                    </div>
                                  
                                </div>

                                <div class="form-row">
                                    

                                    
                                </div>
                                <a href="#"  onclick="return get_view();" class="mt-2 btn btn-primary">View</a> 
                                <button type="submit" name="report_type" value="export"    class="mt-2 btn btn-primary">Export</button>
                                <a href="{{route('home')}}" class="mt-2 btn btn-danger"  title="home">Exit</a>
                            </form> 
                        </div>
                        <div class="card-body" id="disp_records">
                            
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
 
<script>
    
    function ajax(url,uqry,div)
    { 
        var xmlhttp = false;
        
        if (window.XMLHttpRequest)
        {
            xmlhttp = new XMLHttpRequest();
        }
        else
        {
            xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function()
        {
            if(xmlhttp.readyState==4 && xmlhttp.status==200)
            {  //alert(xmlhttp.responseText);
            // var str = xmlhttp.responseText;
                
                document.getElementById(div).innerHTML = xmlhttp.responseText;
            }
        }
        
        xmlhttp.open("POST",url,true);
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send(uqry);
    }
    
    function get_view()
    {

       var from_date = document.getElementById("from_date").value;
       var to_date = document.getElementById("to_date").value;

       var uqry = 'from_date='+from_date+'&to_date='+to_date+'&report_type=view';
       ajax('export-defective-scrap-report',uqry,'disp_records');
       

       return false;    
    }
</script>

@endsection
