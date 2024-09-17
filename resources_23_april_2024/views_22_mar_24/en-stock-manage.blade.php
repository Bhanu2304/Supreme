@extends('layouts.app')
@section('content')


<script>
                                    

menu_select('{{$url}}');                                                             
</script>
<script>


function reloadPage(){
    location.reload(true);
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

function get_product_detail(div_id,brand_id)
{
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });
    jQuery.ajax({
        url: 'get-product-category-by-brand-id',
        method: 'post',
        data: {
            brand_id: brand_id,
            all:'1'
        },
        success: function(result){
            $('#product_category_id'+div_id).html(result)
        }
    });
}

function get_product(div_id,product_category_id)
{
    var brand_id = $('#brand_id'+div_id).val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });
    jQuery.ajax({
        url: 'get-product-by-brand-id',
        method: 'post',
        data: {
            brand_id: brand_id,
            product_category_id:product_category_id,
            all:'1'
        },
        success: function(result){
            $('#product_id'+div_id).html(result);
        }
    });
}
 
function get_model(div_id,product_id)
{
    var brand_id = $('#brand_id'+div_id).val();
    var product_category_id = $('#product_category_id'+div_id).val();
     
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });
    jQuery.ajax({
        url: 'get-model-by-product-id',
        method: 'post',
        data: {
            brand_id: brand_id,
            product_category_id:product_category_id,
            product_id: product_id,
            all:'1'
        },
        success: function(result){
            $('#model_id'+div_id).html(result);
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
                        <div class="card-body"><h5 class="card-title">Engineer Stock Management</h5>
                            
                                @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                  
                        <form method="get" action="{{route('center-realloc-view')}}" class="form-horizontal">
                           
                            <div class="form-row">
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="exampleAddress" class="">Brand</label>
                                        <select id="brand_id1" name="brand" onchange="get_product_detail('1',this.value)" class="form-control" required="">
                                            <option value="">Brand</option>
                                            <?php foreach($brand_arr as $brand) {?>       
                                                <option value="<?php echo $brand['brand_id']; ?>"><?php echo $brand['brand_name']; ?></option>     
                                            <?php  }?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="exampleAddress" class="">Product Category</label>
                                        <select id="product_category_id1" name="product_category" onchange="get_product('1',this.value)" class="form-control" required="">
                                            <option value="">Product Category</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="exampleAddress" class="">Model</label>
                                        <select id="product_id1" name="product" onchange="get_model('1',this.value)" class="form-control" required="">
                                            <option value="">Product</option>
                                            
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">From Date</label>
                                            <input name="from_date" autocomplete="off" id="from_date" placeholder="From" type="text" value="<?php echo $from_date; ?>" class="form-control datepicker" >
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">To Date</label>
                                            <input name="to_date" autocomplete="off" id="to_date" placeholder="To" type="text" value="<?php echo $to_date; ?>" class="form-control datepicker" >
                                        </div>
                                    </div>
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">Contact No. / PinCode</label>
                                        <input class="form-control" type="text" id="contact_no" name="contact_no" value="<?php echo $contact_no;?>" data-original-title="Mobile No." data-placement="top" placeholder="Mobile No. / Pincode">
                                    </div>
                                </div>
                                
 				<div class="col-md-2">
                                    <label for="examplePassword11" class="">&nbsp;</label>
                                        <div class="position-relative form-group">
                                                       
                                                   
                                            <input type="submit"  class="btn btn-primary"  value="Search" >
                                &nbsp;<a href="{{route('home')}}" class="btn btn-danger" >Exit</a>
                                        </div>
                                </div>



                                 </div>


                        </form>
                    </div>
                        
                            
                            <div class="card-body">
                                
                                <form method="POST" action="ce-reallocate"  >
                                
                                <div class="form-row">
                                    <div class="col-md-3">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">Service Center</label>
                                        <select id="se_id" name="se_id" class="form-control" required=""> 
                                            <option value="">Select</option>
                                            @foreach($se_arr as $se)
                                                <option value="{{$se->center_id}}" >{{$se->center_name}} - {{$se->state_name}} - {{$se->city}} - {{$se->pincode}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <label for="examplePassword11" class="">&nbsp;</label>
                                        <div class="position-relative form-group">
                                            <input type="submit"  class="btn btn-primary"  value="Re-Allocate" >
                                
                                        </div> 
                                    </div>
                                    
                                </div>    
                           
                            <h5 class="card-title">Customer Details</h5>
                            <table id="table1" class="table table-striped table-bordered" style="width:100%">
                              <thead>
                                 <tr>
                                    <th>Sr.</th>
                                    <th>Ticket No.</th>
                                    <th>Center</th>
                                    <th>Job No.</th>
                                    <th>Status</th>
                                    <th>Cust. Gr.</th>
                                    <th>Cust Name</th>
                                    <th>State</th>
                                    <th>District</th>
                                    <th>Location</th>
                                    <th>Mobile No.</th>
                                    <th>Pincode</th>
                                    <th>Brand</th>
                                    <th>Product</th>
                                    <th>Model No.</th>
                                    <th>Serial No.</th>
                                    <th>Reason of Job Returning</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 
                                 <?php $srno = 1;
                                        foreach($DataArr as $record)
                                        {
                                            $created_at = $record->created_at;
                                            $curTime = date('Y-m-d H:i:s');
                                           
                                            $str_diff_time_hour = $str_diff_time/1000;
                                            $color = "";
                                           if($str_diff_time_hour>144000)
                                           {
                                            $color = "red";
                                           }
                                           else if($str_diff_time_hour>72000)
                                           {
                                            $color = "blue";
                                           }

                                            echo '<tr color="'.$color.'">';
                                            echo '<td>';
                                            echo '<input type="checkbox" name="case[]" value="'.$record->TagId.'" />';
                                            echo $srno++.'</td>';
                                                echo '<td>'.$record->ticket_no.'</td>';
                                                echo '<td>'.$record->center_name.'</td>';
                                                echo '<td>'.$record->job_no.'</td>';
                                                echo '<td>';
                                                if($record->job_reject=='1')
                                                {
                                                    echo 'Rejected';
                                                }
                                                else if($record->job_accept=='1')
                                                {
                                                    echo 'Accepted';
                                                }
                                                else
                                                {
                                                    echo 'Pending';
                                                }
                                                echo '</td>';
                                                echo '<td>'.$record->Customer_Group.'</td>';
                                                echo '<td>'.$record->Customer_Name.'</td>';
                                                echo '<td>'.$record->State.'</td>';
                                                echo '<td>'.$record->dist_name.'</td>';
                                                echo '<td>'.$record->Landmark.'</td>';
                                                echo '<td>'.$record->Contact_No.'</td>';
                                                echo '<td>'.$record->Pincode.'</td>';
                                                echo '<td>'.$record->Brand.'</td>';
                                                echo '<td>'.$record->Product.'</td>';
                                                echo '<td>'.$record->Model.'</td>';
                                                echo '<td>'.$record->Serial_No.'</td>';
                                                echo '<td>'.$record->job_reject_reason.'</td>';
                                                
                                            echo '</tr>';
                                        }
                                 ?>
                                  
                              </tbody>
                           </table>
                            
                            
                            <input type="hidden" name="whereTag" value="<?php echo $whereTag; ?>" >
                                </form>
                        </div>
                        
                        

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

@endsection
