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
                        <div class="card-body"><h5 class="card-title"> Closure Codes</h5>
                            
                            <h5 class="card-title" style="text-color:blue;">
                                <a href="#" onclick="form_toggle('se_form','table_div');" style="cursor: pointer;">Add</a> / <a href="#" onclick="form_toggle('table_div','se_form');" style="cursor: pointer;">View</a>
                            </h5> 
                            @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                            
                            <form id="se_form" method="post"  style="display:none;">
                                <div class="form-row">
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Closure Code</label>
                                            <!-- <input name="se_name" id="se_name" placeholder="Display Name" type="text" class="form-control"> -->
                                            <textarea name="closure_code" id="closure_code" cols="30" rows="10" class="form-control" placeholder="Enter Closure Code" required></textarea>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit"  class="mt-2 btn btn-primary">Save</button>
                                <a href="#" onclick="form_toggle('table_id','se_form');" class="mt-2 btn btn-danger"  title="view">Exit</a>
                            </form>
                                 <div id="table_div">
                                     <table id="table_id" class="table table-striped table-bordered" style="width:100%">
                              <thead>
                                 <tr>
                                    <th>Sr.No</th>
                                    <th>Closure Code</th> 
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
                                    <td>{{$Data->closure_code}}</td>
                                    <td>{{ date('d-m-Y', strtotime($Data->created_at)) }}</td>

                                    <td class="Status">@if($Data->status=='1') {{'Active'}} @else {{'De-Active'}} @endif</td>
                                    <td class="Officer"><a href="edit-closure?closure_id=<?php echo base64_encode($Data->id); ?>" >Edit</a></td>
                                 </tr>
                                 @endforeach
                                
                              </tbody>
                           </table>     
                                 </div>   
                            
                                 
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
 
<script>
    
 function form_toggle(first,second)
{
    $('#'+first).show();
    $('#'+second).hide();
}
 $('#table_id').DataTable( );
 
</script>

@endsection
