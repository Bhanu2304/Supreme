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
                            <h5 class="card-title"> Spare Part Allocation</h5>
                            
                            
                                 
                                <table id="table_id" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                   <tr>
                                      <th>Sr.No</th>
                                      <th>Status</th>
                                      <th>Brand</th>
                                      <th>Product Detail</th>
                                      <th>Product</th>
                                      <th>Model</th>
                                      <th>Spare Part Name</th>
                                      <th>Part No</th>
                                      <th>HSN Code</th>
                                      <th>Job Id</th>
                                   </tr>
                                </thead>
                                    <tbody>
                                        @php $i = 0; @endphp
                                          @foreach($job_part_pending as $Data)


                                       <tr id="row_{{$Data->spare_id}}">
                                          <td>{{++$i}}</td>
                                          <td><font color="<?php if($Data->stock_status=='stock not available') echo 'red'; else 'green'; ?>">{{$Data->stock_status}}</font></td>
                                          <td>{{$Data->brand_name}}</td>
                                          <td>{{$Data->category_name}}</td>
                                          <td>{{$Data->product_name}}</td>
                                          <td>{{$Data->model_name}}</td>
                                          <td>{{$Data->part_name}}</td>
                                          <td>{{$Data->part_no}}</td>
                                          <td>{{$Data->hsn_code}}</td>
                                          <td>{{$Data->job_id}}</td>
                                       </tr>
                                       @endforeach

                                    </tbody>
                                </table> 
                                <a href="view-part-pending-ho" class="btn btn-success">Back</a> 
                                 
                        </div>    
                           
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 

@endsection
