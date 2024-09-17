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
                            <h5 class="card-title">View Brand</h5>
                            <table id="table1" class="table table-striped table-bordered" style="width:100%">
                              <thead>
                                 <tr>
                                    <th>Sr.No</th>
                                    
                                    <th>Brand Name</th>
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
                                    
                                    <td>{{$Data->brand_name}}</td>
                                    <td class="Officer">{{$Data->created_at}}</td>
                                    <td class="Status">@if($Data->brand_status=='1') {{'Active'}} @else {{'De-Active'}} @endif</td>
                                    <td class="Officer"><a href="edit-brand?brand_id=<?php echo base64_encode($Data->brand_id); ?>" >Edit</a></td>
                                 </tr>
                                 @endforeach;
                                
                              </tbody>
                           </table>
                       
                            <div class="form-group text-right"> 
<!--                              <a href="vendor-export" class="btn btn-success btn-grad btnr1" data-original-title="" title="">Export Records</a>-->
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
