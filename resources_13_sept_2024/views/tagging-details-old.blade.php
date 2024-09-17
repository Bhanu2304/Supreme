@extends('layouts.app')
@section('content')

<script>

menu_select('{{$url}}');   
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
                        <div class="card-body"><h5 class="card-title">Customer Registration/Call Booking</h5>
                            
                                @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                  
                        <form method="get" action="{{route('tagging-master')}}" class="form-horizontal"  >
                           
                            <div class="form-row">
                                                                    
                                <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">Mobile Number</label>
                                
                                            <input class="form-control" type="text" id="Contact_No" name="Contact_No" value="<?php echo $contact_no;?>" data-original-title="Mobile Number" data-placement="top" placeholder="Mobile Number" required="">
                                        </div>
                                </div>
 				<div class="col-md-8">
                                        <div class="position-relative form-group">
                                                                           
                                            <input type="submit"  class="btn btn-success btn-grad" data-original-title="" title="" value="Search" >
                                &nbsp;<a href="{{route('home')}}" class="btn btn-danger btn-grad" data-original-title="" title="">Exit</a>
                                        </div>
                                </div>



                                 </div>


                        </form>
                    </div>

<div class="card-body">
                            <h5 class="card-title">Customer Details</h5>
                            <table id="table1" class="table table-striped table-bordered" style="width:100%">
                              <thead>
                                 <tr>
                                    <th>Sr.No</th>
                                    <th>Job No.</th>
                                    <th>Status</th>
                                    <th>PDF</th>
                                    <th>Invoice</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>State</th>
                                    <th>Contact No.</th>
                                    <th>Edit</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 
                                 <?php $srno = 1;
                                        foreach($DataArr as $record)
                                        {
                                            echo '<tr>';
                                            echo '<td>'.$srno++.'</td>';
                                                    echo '<td>'.$record['job_id'].'</td>';
                                                    echo '<td>';
                                                    
                                                    if($record['warranty_card']=='No' && $record['warranty_card']=='No' && $record['observation']=='Part Required')
                                                    {
                                                        if($record['case_close']=='1' &&  $record['inv_status']=='0' && $record['payment_entry']=='0' && $record['final_symptom_status']=='0')
                                                        {
                                                            echo 'Job No. Closed';
                                                        }
                                                        else if($record['case_close']=='1' &&  $record['inv_status']=='0' && $record['payment_entry']=='0' && $record['final_symptom_status']!='0')
                                                        {
                                                            echo $record['payment_status'];
                                                        }
                                                        else if($record['case_close']=='1' &&  $record['inv_status']=='0' && $record['payment_entry']=='1' )
                                                        {
                                                            echo 'Payment Not Made';
                                                        }
                                                        else if($record['case_close']=='1' &&  $record['inv_status']=='1'  )
                                                        {
                                                            echo 'Invoice Not Made';
                                                        }
                                                        else if($record['case_close']=='1' && $record['part_status']=='1')
                                                        {
                                                            echo 'Part Allocation Pending';
                                                        }
                                                        else
                                                        {
                                                            echo 'Observation Pending';
                                                        }
                                                    }
                                                    else
                                                    {
                                                        echo 'Service Not Required';
                                                    }
                                                    
                                                    
                                                    echo '</td>';
                                                    
                                                    echo '<td>';
                                                    if($record['case_close']=='1' )
                                                        {
                                                            echo '<a href="generate-pdf?TagId='.$record['TagId'].'">PDF </a></td>';
                                                        }
                                                    echo '</td>';
                                                    
                                                    echo '<td>';
                                                    if($record['case_close']=='1' &&  $record['inv_status']=='0' )
                                                        {
                                                            echo '<a href="generate-TXInvoice?TagId='.$record['TagId'].'">Invoice</a>';
                                                        }
                                                    echo '</td>';
                                                    
                                                    echo '<td>'.$record['Customer_Name'].'</td>';
                                                    echo '<td>'.$record['Customer_Address'].'</td>';
                                                    echo '<td>'.$record['State'].'</td>';
                                                    echo '<td>'.$record['Contact_No'].'</td>';
                                                    echo '<td><a href="edit-tagging-data?TagId='.$record['TagId'].'">Edit</a></td>';
                                            echo '</tr>';
                                        }
                                 
                                 
                                 ?>
                                  
                                 
                                
                              </tbody>
                           </table>
                       <h5 class="card-title">Registered Product Details</h5>
                            <table id="table1" class="table table-striped table-bordered" style="width:100%">
                              <thead>
                                 <tr>
                                    <th>Sr.No</th>
                                    <th>Serial No</th>
                                    <th>Product</th>
                                    <th>Model</th>
                                    <th>Purchase Date</th>
                                    <th>Warranty</th>
                                    <th>Invoice</th>
                                    
                                </tr>
                              </thead>
                              <tbody>
                                 
                                 <?php $srno = 1;
                                        foreach($DataArr as $record)
                                        {
                                            echo '<tr>';
                                            echo '<td>'.$srno++.'</td>';
                                                    echo '<td>'.$record['Serial_No'].'</td>';
                                                    echo '<td>'.$record['Product'].'</td>';
                                                    echo '<td>'.$record['Model'].'</td>';
                                                    echo '<td>'.$record['Bill_Purchase_Date'].'</td>';
                                                    echo '<td>'.$record['warranty_card'].'</td>';
                                                    echo '<td>'.$record['invoice'].'</td>';
                                                    
                                                    
                                                    
                                            echo '</tr>';
                                        }
                                 
                                 
                                 ?>
                              </tbody>
                           </table>
                      
                            <div class="form-group text-right">
                             <a href="tagging-data?tag_type=complaint&entry_type=calling&TagId=<?php echo $TagId;?>" class="btn btn-success btn-grad btnr1" data-original-title="" title="">Make Complaint/Installation</a>
			 <a href="tagging-data?tag_type=general_query" class="btn btn-success btn-grad btnr1" data-original-title="" title="">Genaral Query</a>
	
                              <a href="home" class="btn btn-danger btn-grad btnr1" data-original-title="" title="">Exit</a>
                           </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection
