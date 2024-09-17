@extends('layouts.app')
@section('content')

<script>

menu_select('{{$url}}');
function reloadPage()
{
    location.reload(true);
}



function checkNumber(val,evt)
{
    var charCode = (evt.which) ? evt.which : event.keyCode;
    
    if (charCode > 31 && (charCode < 48 || charCode > 57))
    {            
        return false;
    }
    if(val.length>10)
    {
        return false;
    }
    return true;
}

@if($error == "model_open")
    $(document).ready(function() {
        $('#openModalBtn').trigger('click');
    });
@endif


</script>

<div class="app-main">
    <div class="app-main__outer">
        <div class="app-main__inner">
            <div class="tab-content">
                <div class="tab-pane tabs-animation fade show active" id="tab-content-0" role="tabpanel">
                    <div class="main-card mb-3 card">
                        <div class="card-body"><h5 class="card-title">Customer Registration/Call Booking</h5>
                            
                            @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                            @if(Session::has('error') && Session::get('error') != "model_open" )<h5><font color="red"> {!! Session::get('error') !!}</font></h5> @endif
                            <button type="button" style="display:none;" class="btn btn-primary" id="openModalBtn" data-toggle="modal" data-target="#exampleModal"></button>
                            <form method="post" action="{{route('call-registration-form')}}" name="form" class="form-horizontal" enctype="multipart/form-data">
                            
                                <div class="form-row">
                                                                        
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="examplePassword11" class="">Upload File</label>
                                            <input type="file" name="call_file" id="call_file" class="form-control">
                                            <a href="{{url('sample/registration-form.xlsx')}}">User Sample File</a>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="position-relative form-group">                              
                                            <input type="submit" class="btn btn-success btn-grad" data-original-title="" title="" value="Upload">
                                            &nbsp;
                                            <a href="{{route('home')}}" class="btn btn-danger btn-grad" data-original-title="" title="">Exit</a>
                                        </div>
                                    </div>

                                </div>

                            </form>
                            
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

@endsection

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <!-- <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button> -->
      </div>
      <div class="modal-body">
        <h5>Complaint already exists still want to register a new complaint!</h5>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <a class="btn btn-primary" href="ho-tag-view-cl?TagId={{ $last_insert_id }}">Proceed</a>
      </div>
    </div>
  </div>
</div>
