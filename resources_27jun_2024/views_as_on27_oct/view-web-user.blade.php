@extends('layouts.app')

@section('content')



<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <h5 class="card-title">View User</h5>
                            <table id="table1" class="table table-striped table-bordered" style="width:100%">
                              <thead>
                                 <tr>
                                    <th>Sr.No</th>
                                    <th>User Name</th>
                                    <th>User Type</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Create Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                 </tr>
                              </thead>
                              <tbody>
                                  @php $i = 0; @endphp
                                    @foreach($DataArr as $Data)
                                  
                                  
                                 <tr>
                                    <td>{{++$i}}</td>
                                    <td>{{$Data->UserName}}</td>
                                    <td>{{$Data->UserType}}</td>
                                    <td class="emailid">{{$Data->email}}</td>
                                    <td class="emailid">{{$Data->mobile}}</td>
                                    <td class="Officer">{{$Data->created_at}}</td>
                                    
                                    <td class="Status">@if($Data->user_status=='1') {{'Active'}} @else {{'De-Active'}} @endif</td>
                                    <td class="Officer"><a href="edit-user?UserId=<?php echo base64_encode($Data->UserId); ?>" >Edit</a></td>
                                 </tr>
                                 @endforeach;
                                
                              </tbody>
                           </table>
                       
                            <div class="form-group text-right">
                              <a href="vendor-export" class="btn btn-success btn-grad btnr1" data-original-title="" title="">Export Records</a>
                              <a href="/home" class="btn btn-danger btn-grad btnr1" data-original-title="" title="">Exit</a>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- /.inner -->
            </div>
            <!-- /.outer -->
         </div>
    </div>  

@endsection
