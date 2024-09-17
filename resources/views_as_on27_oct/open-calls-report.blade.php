@extends('layouts.app')

@section('content')

<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title">OPEN CALLS REPORT</h5>
                            <form method="post" action="export-report-job">
                               
                                <div class="form-row">
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">From Date</label>
                                            <input name="from_date" id="from_date" placeholder="From Date" type="text" class="form-control datepicker" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">To Date</label>
                                            <input name="to_date" id="to_date" placeholder="To Date" type="text" class="form-control datepicker" required="">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Region</label>
                                            <Select name="region" id="region" class="form-control">
                                                <option value="">Select</option>
                                                <?php   foreach($region_master as $region_arr)
                                                        {
                                                            $region =  $region_arr->region_name;
                                                            echo '<option value="'.$region.'">'.$region.'</option>';
                                                        }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Brand</label>
                                            <select id="brand"  name="brand" onchange="get_product(this.value)" class="form-control">
                                                <option value="">Select</option>
                                                <?php   foreach($brand_master as $brand_arr)
                                                        {
                                                            $brand =  $brand_arr->brand_name;
                                                            echo '<option value="'.$brand.'">'.$brand.'</option>';
                                                        }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Product</label>
                                            <select id="product"  name="product" onchange="get_model(this.value)" class="form-control">
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Model No.</label>
                                            <Select name="model" id="model" class="form-control">
                                                <option value="">Select</option>
                                                
                                            </select>
                                        </div>
                                    </div>
                                    
                                </div>

                                <div class="form-row">
                                    
                                    <div class="col-md-2">
                                        <div class="position-relative form-group"><label for="exampleAddress" class="">Warranty</label>
                                            <Select name="state" id="state" class="form-control">
                                                
                                                <option value="ALL">ALL</option>
                                                <option value="UNDER WARRANTY">UNDER WARRANTY</option>
                                                <option value="OUT OF WARRANTY SELECTION">OUT OF WARRANTY SELECTION</option>
                                            </select>
                                        </div>
                                    </div> 
                                    
                                    <div class="col-md-2">
                                        <div class="position-relative form-group"><label for="exampleAddress" class="">Call Status</label>
                                            <Select name="call_status" id="call_status" class="form-control">
                                                <option value="">Select</option>
                                                <option value="Open">Open</option>
                                                <option value="Close">Close</option>
                                                <option value="Cancel">Cancel</option>
                                                <option value="Part Pending">Part Pending</option>
                                            </select>
                                        </div>
                                    </div> 
                                    
                                    <div class="col-md-2">
                                        <div class="position-relative form-group"><label for="exampleAddress" class="">ASC Code</label>
                                            <input name="asc_code" id="asc_code" placeholder="ASC Code" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="position-relative form-group"><label for="exampleAddress" class="">Number</label>
                                            <input name="number" id="number" placeholder="Numbers" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="position-relative form-group"><label for="exampleAddress" class="">Mobile No./Contact No.</label>
                                            <input name="mobno" id="mobno" placeholder="Mobile No./Contact No." type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="position-relative form-group"><label for="exampleAddress" class="">Call Id</label>
                                            <input name="call_id" id="call_id" placeholder="CALL ID" type="text" class="form-control">
                                        </div>
                                    </div>
                                    
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
		xmlhttp=new XMLHttpRequest();
	}
	else
	{
        xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
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
       var Region = document.getElementById("Region").value;
       var brand = document.getElementById("brand").value;
       var Model = document.getElementById("Model").value;
       var state = document.getElementById("state").value;
       var call_status = document.getElementById("call_status").value;
       var dealer = document.getElementById("dealer").value;
       var number = document.getElementById("number").value;
       var mobno = document.getElementById("mobno").value;


       var uqry = 'from_date='+from_date+'&to_date='+to_date+'&Region='+Region+'&brand='+brand+'&Model='+Model+'&state='+state+'&call_status='+call_status+'&dealer='+dealer+'&number='+number+'&mobno='+mobno+'&report_type=view';
       ajax('export-report-job',uqry,'disp_records');
       
        


       return false;    
    }
</script>

@endsection
