<footer class="Footer bg-dark dker">
         <p> </p>
      </footer>
      <!-- /#footer -->
      <!-- #helpModal -->
      <div id="helpModal" class="modal fade">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  <h4 class="modal-title">Modal title</h4>
               </div>
               <div class="modal-body">
                  <p>
                     Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore
                     et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                     aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                     cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in
                     culpa qui officia deserunt mollit anim id est laborum.
                  </p>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
               </div>
            </div>
            <!-- /.modal-content -->
         </div>
         <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->
      <!-- /#helpModal -->
      <!--jQuery -->
      <!--Bootstrap -->
      <script>
         $(function() {
           Metis.formValidation();
         });
      </script>
      <!-- Metis core scripts -->
      <script src="{{ asset('assets/lib/jquery/jquery.js') }}"></script>
      <script src="{{ asset('assets/js/jquery-3.3.1.js') }}"></script>
      <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
      <script src="{{ asset('assets/js/dataTables.bootstrap.min.js') }}"></script>
      <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
      <script src="{{ asset('assets/js/moment.min.js') }}"></script>
      <script src="{{ asset('assets/js/jquery.uniform.min.js') }}"></script>
      <script>
         $(document).ready(function() {
         $('#example').DataTable();
         } );
      </script>
      <script>
         $(document).ready(function() {
         $('#example1').DataTable();
         } );
      </script>
      <!--Bootstrap -->
      <script src="{{ asset('assets/js/jquery.tagsinput.min.js') }}"></script>
      <script src="{{ asset('assets/js/jquery.autosize.min.js') }}"></script>
      <script src="{{ asset('assets/js/jasny-bootstrap.min.js') }}"></script>
      <script src="{{ asset('assets/js/bootstrap-switch.min.js') }}"></script>
      <script src="{{ asset('assets/js/bootstrap-datepicker.min.js') }}"></script>
      <script src="{{ asset('assets/js/bootstrap-colorpicker.min.js') }}"></script>
      <script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>
      <script src="{{ asset('assets/js/jquery.inputlimiter.1.3.1.min.js') }}"></script>
      <script src="{{ asset('assets/js/jquery.validVal.min.js') }}"></script>
      <script src="{{ asset('assets/js/daterangepicker.js') }}"></script>
      <!-- MetisMenu -->
      <script src="{{ asset('assets/js/metisMenu.min.js') }}"></script>
      <!-- onoffcanvas -->
      <script src="{{ asset('assets/lib/onoffcanvas/onoffcanvas.js') }}"></script>
      <!-- Screenfull -->
      <script src="{{ asset('assets/js/screenfull.min.js') }}"></script>
      <!-- Metis core scripts -->
      <script src="{{ asset('assets/js/core.js') }}"></script>
      <!-- Metis demo scripts -->
      <script src="{{ asset('assets/js/app.js') }}"></script>