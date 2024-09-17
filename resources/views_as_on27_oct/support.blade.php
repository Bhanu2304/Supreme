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
                                    
                                    <div class="card-body"><h5 class="card-title">Support</h5>
                                        
                                        <h3>Welcome to Supreme Customer Support. </h3>    
                                        <hr>  
                                        
                                        <table >
                                            <tr>
                                                <td width="250px;"><img src="{{ asset('assets/images/cm_support.jpg') }}" width="200px;" style="width: 200px;"> </td>
                                                <td width="200px;"><span > Contact No. 1</span><br/><span > Krishna Kumar</span><div> 7290093903</div></td>
                                                <td  width="200px;"><span > Contact No. 2</span><br/><span > Anil Kumar</span><div> 8882240641</div></td>
                                            </tr>
                                        </table>
                                        
                                        <hr>  
                                        
                                    </div>
                                    
                            </div>
                         </div>
                    </div>
 </div>
 </div> 
</div>    
@endsection
