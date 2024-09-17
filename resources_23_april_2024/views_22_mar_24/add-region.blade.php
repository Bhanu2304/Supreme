@extends('layouts.app')

@section('content')

<script>
    
    menu_select('{{$url}}');
    function form_toggle(first,second)
{
    $('#'+first).show();
    $('#'+second).hide();
}
</script>


<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body"><h5 class="card-title">Add Region</h5>
                            
                            <h5 class="card-title" style="text-color:blue;">
                                <a href="#" onclick="form_toggle('state_form','table_id');" style="cursor: pointer;">Add</a> / <a href="#" onclick="form_toggle('table_id','state_form');" style="cursor: pointer;">View</a>
                            </h5> 
                            @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                
                            
                            <form id="state_form" method="post" action="save-region" style="display:none;">
                                

                                

                                <div class="form-row">
                                    
                                    
                                    
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Region</label>
                                            <input name="region_name" id="region_name" placeholder="Region" type="text" class="form-control" required="">
                                        </div>
                                    </div>
                                </div>

                                


                                
                                 
                                <button type="submit"  class="mt-2 btn btn-primary">Save</button>
                                <a href="{{route('home')}}" class="mt-2 btn btn-danger"  title="home">Exit</a>
                            </form>
                            <table id="table_id" class="table table-striped table-bordered" style="width:100%">
                              <thead>
                                 <tr>
                                    <th>Sr.No</th>
                                    
                                    <th>Region Name</th>
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
                                    
                                    <td>{{$Data->region_name}}</td>
                                    <td class="Officer">{{$Data->created_at}}</td>
                                    <td class="Status">@if($Data->region_status=='1') {{'Active'}} @else {{'De-Active'}} @endif</td>
                                    <td class="Officer"><a href="edit-region?region_id=<?php echo base64_encode($Data->region_id); ?>" >Edit</a></td>
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
 


@endsection
