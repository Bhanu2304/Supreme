@extends('layouts.app')
@section('content')


<script>
                                    

menu_select('{{$url}}');    

function get_pincode(state_name)
 {
     $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
              }
          });
    jQuery.ajax({
              url: 'get-pincode-by-state-name',
              method: 'post',
              data: {
                 state_name: state_name 
              },
              success: function(result){
                  $('#pincode').html(result)
              }});
 }





</script>


<div class="app-main__outer">
                    <div class="app-main__inner">
                        <div class="app-page-title">
                            <div class="page-title-wrapper">
                                <div class="page-title-heading">
                                    
                                    <div> Dashboard
<!--                                        <div class="page-title-subheading">This is an example dashboard created using build-in elements and components.
                                        </div>-->
                                    </div>
                                </div>
                                    </div>
                        </div>
                        
                        <div class="row">
                            <form method="get" action="dashboard" class="form-horizontal"  >
                           
                            <div class="form-row">
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">State</label>
                                            <select id="state_id" name="state_id" class="form-control" onchange="get_pincode(this.value)">
                                                <option value="">State</option>
                                                <option value="All">All</option>
                                                @foreach($state_master as $state_id=>$state_name)
                                                    <option value="{{$state_name}}" <?php if( $state_name==$state) 
                                                        { echo 'selected';} ?>>{{$state_name}}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                </div>
                                
                                
                                
                                <div class="col-md-3">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Warranty Category </label>
                                                        <select id="warranty_category" name="warranty_category" class="form-control" >
                                                            <option value="">Select</option>
                                                            <option value="Standard warranty">Standard Warranty</option>
                                                            <option value="Out Warranty">Out Warranty</option>
                                                            <option value="Extended">Extended</option>
                                                            <option value="International">International</option>
                                                        </select>
                                                    </div>
                                                </div>
                                <div class="col-md-3">
                                                    <div class="position-relative form-group"><label for="examplePassword11" class="">Service Type </label>
                                                        <select id="service_type" name="service_type" class="form-control"  >
                                                            <option value="">Select</option>
                                                            <option value="Home Service">Home Service</option>
                                                            <option value="Walk in Service">Walk in Service</option>
                                                            <option value="Refurbrish">Refurbrish</option>
                                                            <option value="D&I">D&I</option>
                                                        </select>
                                                    </div>
                                                </div>
                                
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">Pin Code</label>
                                        <input type="number" maxlength="6" id="pincode" name="pincode" placeholder="Pincode" class="form-control">
                                    </div>
                                </div>


                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">ASC </label>
                                            <select id="asc_code" name="asc_code" class="form-control" >
                                                <option value="">ASC Code</option>
                                                <option value="All">All</option>
                                                @foreach($asc_master as $asc)
                                                    <option value="{{$asc->center_id}}" <?php if( $center_id==$asc->center_id) 
                                                        { echo 'selected';} ?>>{{$asc->center_name}}</option>
                                                @endforeach
                                            </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">Ticket No.</label>
                                            <input class="form-control" type="text" id="job_id" name="job_id" value="<?php echo $job_id;?>" placeholder="Job Id">
                                        </div>
                                    </div>
                                <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">Contact No. </label>
                                            <input class="form-control" type="text" id="contact_no" name="contact_no" value="<?php echo $contact_no;?>" data-original-title="Mobile No." data-placement="top" placeholder="Contact No.">
                                        </div>
                                    </div>
                                <div class="col-md-2">
                                        <div class="position-relative form-group">
                                            <label for="exampleEmail11" class="">From Date</label>
                                            <input name="from_date" id="from_date" placeholder="From" type="text" value="<?php echo $from_date; ?>" autocomplete="off" class="form-control datepicker" >
                                        </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="position-relative form-group">
                                        <label for="examplePassword11" class="">To Date</label>
                                        <input name="to_date" id="to_date" placeholder="To" type="text" value="<?php echo $to_date; ?>" autocomplete="off" class="form-control datepicker" >
                                    </div>
                                </div>
                                
                                    
                                
                                    
                                
                                    <div class="col-md-3">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">&nbsp;</label>
                                            <br>                   
                                            <input type="submit" name="submit" class="btn btn-primary btn-grad" data-original-title="" title="" value="Search" >
                                            <input type="submit" name="submit" class="btn btn-primary btn-grad" data-original-title="" title="" value="Download Report" >
                                        </div>
                                    </div>

 

                                 </div>


                        </form>
                            
                            <table id="table1" class="table table-striped table-bordered" style="width:100%">
                              <thead>
                                 <tr>
                                    <th>Sr.No</th>
                                    <th>ASC Name</th>
                                    <th>State</th>
                                    <th>Ticket No.</th>
                                    <th>Job No.</th>
                                    <th>Customer Name</th>
                                    <th>Phone No.</th>
                                    <th>Pin Code</th>
                                    
                                    <th>Brand</th>
                                    <th>Model No.</th>
                                    <th>Serial No.</th>
                                    <th>Warranty Category</th>
                                    <th>Service Type</th>
                                    
                                    <th>Job Status</th>
                                    <th>Job PDF</th>
                                    <th>Invoice</th>
                                    
                                    
                                    
                                    
                                    
                                   
                                 </tr>
                              </thead>
                              <tbody>
                                 
                                 <?php $srno = 1;
                                        foreach($DataArr as $record)
                                        {
                                            //$record = json_decode($record1,true);
                                            echo '<tr>';
                                            echo '<td>'.$srno++.'</td>';
                                            echo '<td>'.$record->center_name.'</td>';
                                            echo '<td>'.$record->State.'</td>';
                                                    echo '<td>'.$record->ticket_no.'</td>';
                                                    echo '<td>'.$record->job_no.'</td>';
                                                    echo '<td>'.$record->Customer_Name.'</td>';
                                                    echo '<td>'.$record->Contact_No.'</td>';
                                                    echo '<td>'.$record->Pincode.'</td>';
                                                    echo '<td>'.$record->Brand.'</td>';
                                                    echo '<td>'.$record->Model.'</td>';
                                                    echo '<td>'.$record->Serial_No.'</td>';
                                                    echo '<td>'.$record->warranty_category.'</td>';
                                                    echo '<td>'.$record->service_type.'</td>';
                                                    
                                                    echo '<td>';
                                                    
                                                    if($record->warranty_card=='No' && $record->warranty_card=='No' && $record->observation=='Part Required')
                                                    {
                                                        if($record->case_close=='1' &&  $record->inv_status=='0' && $record->payment_entry=='0' && $record->final_symptom_status=='0')
                                                        {
                                                            echo 'Job No. Closed';
                                                        }
                                                        else if($record->case_close=='1' &&  $record->inv_status=='0' && $record->payment_entry=='0' && $record->final_symptom_status!='0')
                                                        {
                                                            echo $record->payment_status;
                                                        }
                                                        else if($record->case_close=='1' &&  $record->inv_status=='0' && $record->payment_entry=='1' )
                                                        {
                                                            echo 'Payment Not Made';
                                                        }
                                                        else if($record->case_close=='1' &&  $record->inv_status=='1'  )
                                                        {
                                                            echo 'Invoice Not Made';
                                                        }
                                                        else if($record->case_close=='1' && $record->part_status=='1')
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
                                                    
                                                        echo '<a href="view-generate-pdf?TagId='.$record->TagId.'">View </a>';
                                                       echo '<a href="generate-pdf?TagId='.$record->TagId.'">PDF </a>';
                                                    
                                                    echo '</td>';
                                                    
                                                    echo '<td>';
                                                    if($record->case_close=='1' &&  $record->inv_status=='0' )
                                                        {
                                                            echo '<a href="generate-TXInvoice?TagId='.$record->TagId.'">Invoice</a>';
                                                        }
                                                    echo '</td>';
                                                    
                                                    
                                                    //echo '<td>'.$record->Customer_Address.'</td>';
                                                    
                                                    
                                                    
                                            echo '</tr>';
                                        }
                                 
                                 
                                 ?>
                                  
                                 
                                
                              </tbody>
                           </table>
                            
                            
                        </div>
                        
                        
                        
                        <div class="row">
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content bg-midnight-bloom">
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading">Total Open Calls</div>
<!--                                            <div class="widget-subheading">Last year expenses</div>-->
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white"><span><?php echo $dialer_open_total; ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content bg-arielle-smile">
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading">Per day Incoming Calls</div>
<!--                                            <div class="widget-subheading">Total Clients Profit</div>-->
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white"><span><?php echo $tagging_perday_call; ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content bg-grow-early">
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading">Per day Completed Calls</div>
<!--                                            <div class="widget-subheading">People Interested</div>-->
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white"><span><?php echo $dialer_completed_call; ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content bg-grow-early">
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading">Total Part Pending Calls</div>
<!--                                            <div class="widget-subheading">People Interested</div>-->
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white"><span><?php echo $total_pending; ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content bg-grow-early">
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading">Total Defective Pending Cases</div>
<!--                                            <div class="widget-subheading">People Interested</div>-->
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white"><span><?php echo 0; ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content bg-grow-early">
                                    <div class="widget-content-wrapper text-white">
                                        <div class="widget-content-left">
                                            <div class="widget-heading">Total Tickets pending for Allocation</div>
<!--                                            <div class="widget-subheading">People Interested</div>-->
                                        </div>
                                        <div class="widget-content-right">
                                            <div class="widget-numbers text-white"><span><?php echo $total_allocation_pending; ?></span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        
                        
                        
                        
<!--                        <div class="row">
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content">
                                    <div class="widget-content-outer">
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left">
                                                <div class="widget-heading">Total Orders</div>
                                                <div class="widget-subheading">Last year expenses</div>
                                            </div>
                                            <div class="widget-content-right">
                                                <div class="widget-numbers text-success">1896</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content">
                                    <div class="widget-content-outer">
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left">
                                                <div class="widget-heading">Products Sold</div>
                                                <div class="widget-subheading">Revenue streams</div>
                                            </div>
                                            <div class="widget-content-right">
                                                <div class="widget-numbers text-warning">$3M</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content">
                                    <div class="widget-content-outer">
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left">
                                                <div class="widget-heading">Followers</div>
                                                <div class="widget-subheading">People Interested</div>
                                            </div>
                                            <div class="widget-content-right">
                                                <div class="widget-numbers text-danger">45,9%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-xl-none d-lg-block col-md-6 col-xl-4">
                                <div class="card mb-3 widget-content">
                                    <div class="widget-content-outer">
                                        <div class="widget-content-wrapper">
                                            <div class="widget-content-left">
                                                <div class="widget-heading">Income</div>
                                                <div class="widget-subheading">Expected totals</div>
                                            </div>
                                            <div class="widget-content-right">
                                                <div class="widget-numbers text-focus">$147</div>
                                            </div>
                                        </div>
                                        <div class="widget-progress-wrapper">
                                            <div class="progress-bar-sm progress-bar-animated-alt progress">
                                                <div class="progress-bar bg-info" role="progressbar" aria-valuenow="54" aria-valuemin="0" aria-valuemax="100" style="width: 54%;"></div>
                                            </div>
                                            <div class="progress-sub-label">
                                                <div class="sub-label-left">Expenses</div>
                                                <div class="sub-label-right">100%</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>-->
                        
<!--                        <div class="row">
                            <div class="col-md-6 col-lg-3">
                                <div class="card-shadow-danger mb-3 widget-chart widget-chart2 text-left card">
                                    <div class="widget-content">
                                        <div class="widget-content-outer">
                                            <div class="widget-content-wrapper">
                                                <div class="widget-content-left pr-2 fsize-1">
                                                    <div class="widget-numbers mt-0 fsize-3 text-danger">71%</div>
                                                </div>
                                                <div class="widget-content-right w-100">
                                                    <div class="progress-bar-xs progress">
                                                        <div class="progress-bar bg-danger" role="progressbar" aria-valuenow="71" aria-valuemin="0" aria-valuemax="100" style="width: 71%;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="widget-content-left fsize-1">
                                                <div class="text-muted opacity-6">Income Target</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card-shadow-success mb-3 widget-chart widget-chart2 text-left card">
                                    <div class="widget-content">
                                        <div class="widget-content-outer">
                                            <div class="widget-content-wrapper">
                                                <div class="widget-content-left pr-2 fsize-1">
                                                    <div class="widget-numbers mt-0 fsize-3 text-success">54%</div>
                                                </div>
                                                <div class="widget-content-right w-100">
                                                    <div class="progress-bar-xs progress">
                                                        <div class="progress-bar bg-success" role="progressbar" aria-valuenow="54" aria-valuemin="0" aria-valuemax="100" style="width: 54%;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="widget-content-left fsize-1">
                                                <div class="text-muted opacity-6">Expenses Target</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card-shadow-warning mb-3 widget-chart widget-chart2 text-left card">
                                    <div class="widget-content">
                                        <div class="widget-content-outer">
                                            <div class="widget-content-wrapper">
                                                <div class="widget-content-left pr-2 fsize-1">
                                                    <div class="widget-numbers mt-0 fsize-3 text-warning">32%</div>
                                                </div>
                                                <div class="widget-content-right w-100">
                                                    <div class="progress-bar-xs progress">
                                                        <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="32" aria-valuemin="0" aria-valuemax="100" style="width: 32%;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="widget-content-left fsize-1">
                                                <div class="text-muted opacity-6">Spendings Target</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="card-shadow-info mb-3 widget-chart widget-chart2 text-left card">
                                    <div class="widget-content">
                                        <div class="widget-content-outer">
                                            <div class="widget-content-wrapper">
                                                <div class="widget-content-left pr-2 fsize-1">
                                                    <div class="widget-numbers mt-0 fsize-3 text-info">89%</div>
                                                </div>
                                                <div class="widget-content-right w-100">
                                                    <div class="progress-bar-xs progress">
                                                        <div class="progress-bar bg-info" role="progressbar" aria-valuenow="89" aria-valuemin="0" aria-valuemax="100" style="width: 89%;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="widget-content-left fsize-1">
                                                <div class="text-muted opacity-6">Totals Target</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>-->
                    </div>
<!--                    <div class="app-wrapper-footer">
                        <div class="app-footer">
                            <div class="app-footer__inner">
                                <div class="app-footer-left">
                                    <ul class="nav">
                                        <li class="nav-item">
                                            <a href="javascript:void(0);" class="nav-link">
                                                Footer Link 1
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0);" class="nav-link">
                                                Footer Link 2
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="app-footer-right">
                                    <ul class="nav">
                                        <li class="nav-item">
                                            <a href="javascript:void(0);" class="nav-link">
                                                Footer Link 3
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0);" class="nav-link">
                                                <div class="badge badge-success mr-1 ml-0">
                                                    <small>NEW</small>
                                                </div>
                                                Footer Link 4
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>    -->
            </div>

@endsection
