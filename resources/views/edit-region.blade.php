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
                        <div class="card-body"><h5 class="card-title">Edit Region</h5>
                            <form method="post" action="update-region">
                                @if(Session::has('message'))<h5><font color="green"> {{ Session::get('message') }}</font></h5> @endif
                                 @if(Session::has('error'))<h5><font color="red"> {{ Session::get('error') }}</font></h5> @endif
                                

                                

                                <div class="form-row">
                                    
                                    
                                    
                                    <div class="col-md-6">
                                        <div class="position-relative form-group">
                                            <label for="exampleAddress" class="">Region</label>
                                            <input name="region_name" id="region_name" placeholder="Region" type="text" value="<?php echo $data['region_name']; ?>" class="form-control" required="">
                                        </div>
                                    </div>
                                </div>

                                


                                
                                 <div class="form-row">
                                     <div class="col-md-6">
                                    <div class="position-relative form-group">
                                        <label for="exampleEmail11" class="">Status</label>
                                        <select id="region_status" name="region_status" class="form-control" required="">
                                            <option value="1" <?php if($data['region_status']=='1') { echo 'selected';} ?>>Active</option>
                                            <option value="0" <?php if($data['region_status']=='0') { echo 'selected';} ?>>De-Active</option>
                                        </select>
                                    </div>
                                     </div>
                                 </div>
                                 <input type="hidden" id="brand_id" name="region_id" value="<?php echo $data['region_id']; ?>" />
                                 <a href="view-region" class="btn btn-danger btn-grad btnr1" data-original-title="" title="">Back</a>
                                <button type="submit"  class="mt-2 btn btn-primary">Update</button>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
 


@endsection
