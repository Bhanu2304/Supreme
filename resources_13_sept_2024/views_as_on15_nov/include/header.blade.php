<div id="top">
            <!-- .navbar -->
            <nav class="navbar navbar-inverse navbar-static-top">
               <div class="container-fluid">
                  <!-- Brand and toggle get grouped for better mobile display -->
                  <header class="navbar-header">
                     <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                     <span class="sr-only">Toggle navigation</span>
                     <span class="icon-bar"></span>
                     <span class="icon-bar"></span>
                     <span class="icon-bar"></span>
                     </button>
                     <a href="index.html" class="navbar-brand">
                     <img src="assets/img/dialdesk.png" alt=""></a>
                  </header>
                  <div class="topnav">
                     <div class="btn-group">
                        <a data-placement="bottom" data-original-title="Fullscreen" data-toggle="tooltip"
                           class="btn btn-default btn-sm" id="toggleFullScreen">
                        <i class="glyphicon glyphicon-fullscreen"></i>
                        </a>
                     </div>
                     <div class="btn-group">
                        <a data-placement="bottom" data-original-title="E-mail" data-toggle="tooltip"
                           class="btn btn-default btn-sm">
                        <i class="glyphicon glyphicon-envelope"></i>
                        <span class="label label-warning">5</span>
                        </a>
                        <a data-placement="bottom" data-original-title="Messages" href="#" data-toggle="tooltip"
                           class="btn btn-default btn-sm">
                        <i class="glyphicon glyphicon-comment"></i>
                        <span class="label label-danger">4</span>
                        </a>
                        <a data-toggle="modal" data-original-title="Help" data-placement="bottom"
                           class="btn btn-default btn-sm"
                           href="#helpModal">
                        <i class="glyphicon glyphicon-question-sign"></i>
                        </a>
                     </div>
                     <div class="btn-group">
                        <a href="{{ route('logout') }}" data-toggle="tooltip" data-original-title="Logout" data-placement="bottom"
                           class="btn btn-metis-1 btn-sm" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                        <i class="glyphicon glyphicon-off"></i>
                        </a>
                         <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                     </div>
                     <div class="btn-group">
                        <a data-placement="bottom" data-original-title="Show / Hide Left" data-toggle="tooltip"
                           class="btn btn-primary btn-sm toggle-left" id="menu-toggle">
                        <i class="glyphicon glyphicon-list"></i>
                        </a>
                        <a href="#right" data-toggle="onoffcanvas" class="btn btn-default btn-sm" aria-expanded="false">
                        <i class="glyphicon glyphicon-comment"></i>
                        </a>
                     </div>
                  </div>
                  <div class="collapse navbar-collapse navbar-ex1-collapse">
                     <!-- .nav -->
                     <ul class="nav navbar-nav visible-xs visible-sm hidden-lg hidden-md">
                        <li><a href="dashboard.html">Dashboard</a></li>
                        <li class='dropdown active'>
                           <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                           Master module <b class="caret"></b>
                           </a>
                           <ul class="dropdown-menu">
                              <li>
                                 <a href="add-client-wise-project">
                                 <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Project creation </a>
                              </li>
                              <li>
                                 <a href="Department_designation.html">
                                 <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Department/designation</a>
                              </li>
                              <li>
                                 <a href="User_creation.html">
                                 <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; User creation</a>
                              </li>
                              <li>
                                 <a href="User%20View.html">
                                 <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; User view </a>
                              </li>
                              <li>
                                 <a href="Create_password_Secret_question.html">
                                 <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Create password/secret question </a>
                              </li>
                              <li>
                                 <a href="executive_creation.html.html">
                                 <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Executive creation</a>
                              </li>
                              <li>
                                 <a href="executive_view.html">
                                 <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Executive view</a>
                              </li>
                              <li>
                                 <a href="Vendor_Creation.html">
                                 <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Vendor creation</a>
                              </li>
                              <li>
                                 <a href="Vendor%20view.html">
                                 <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Vendor view </a>
                              </li>
                              <li>
                                 <a href="Ticket%20Status%20Creation.html">
                                 <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Status creation </a>
                              </li>
                       <li>
                        <a href="Required_fields.html">
                        <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Required fields</a>
                     </li>
                     <li>
                        <a href="Scenario_creation.html">
                        <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Scenario creation</a>
                     </li>
                     <li>
                        <a href="Action_fields.html">
                        <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Action fields</a>
                     </li>>
                              <li>
                                 <a href="#">
                                 <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; TAT creation</a>
                              </li>
                              <li>
                                 <a href="#">
                                 <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; TAT alerts</a>
                              </li>
                              <li>
                                 <a href="#">
                                 <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Define escalations</a>
                              </li>
                              <li>
                                 <a href="#">
                                 <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Define alerts</a>
                              </li>
                              <li>
                                 <a href="#">
                                 <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Upload customer database</a>
                              </li>
                              <li>
                                 <a href="#">
                                 <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Upload documents</a>
                              </li>
                              <li>
                                 <a href="#">
                                 <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; My account</a>
                              </li>
                           </ul>
                        </li>
                          
                     </ul>
                     <!-- /.nav -->
                  </div>
               </div>
               <!-- /.container-fluid -->
            </nav>
            <!-- /.navbar -->
            <header class="head">
               <div class="search-bar">
                  <form class="main-search" action="">
                     <div class="input-group">
                        <input type="text" class="form-control" placeholder="Live Search ...">
                        <span class="input-group-btn">
                        <button class="btn btn-primary btn-sm text-muted" type="button">
                        <i class="glyphicon glyphicon-search"></i>
                        </button>
                        </span>
                     </div>
                  </form>
                  <!-- /.main-search -->                                
               </div>
               <!-- /.search-bar -->
               <div class="main-bar">
                  <h3>
                     <i class="glyphicon glyphicon-edit"></i>&nbsp;
                     Project Creation
                  </h3>
               </div>
               <!-- /.main-bar -->
            </header>
            <!-- /.head -->
         </div>
         <!-- /#top -->
         <div id="left">
            <div class="media user-media bg-dark dker">
               <div class="user-media-toggleHover">
                  <span class="fa fa-user"></span>
               </div>
               <div class="user-wrapper bg-dark">
                  <a class="user-link" href="">
                  <img class="media-object img-thumbnail user-img" alt="User Picture" src="assets/img/user1.png">
                  <span class="label label-danger user-label">16</span>
                  </a>
                  <div class="media-body">
                     <h5 class="media-heading">Krishna Kumar</h5>
                     <ul class="list-unstyled user-info">
                        <li><a href="#" class="Administrator">Administrator</a></li>
                        <li>Last Access : <br>
                           <small><i class="glyphicon glyphicon-calendar"></i>&nbsp;16 Mar 16:32</small>
                        </li>
                     </ul>
                  </div>
               </div>
            </div>
            <!-- #menu -->
            <ul id="menu" class="">
               <li class="nav-header">Menu</li>
               <li class="nav-divider"></li>
               <li class="">
                  <a href="dashboard.html">
                  <i class="glyphicon glyphicon-dashboard"></i><span class="link-title">&nbsp;Dashboard</span>
                  </a>
               </li>
               <li class="">
                  <a href="javascript:;">
                  <i class="glyphicon glyphicon-list"></i>
                  <span class="link-title">Master module</span>
                  <i class="glyphicon glyphicon-menu-right arrow"></i>
                  </a>
                  <ul class="collapse">
                     <li>
                        <a href="Project_creation.html">
                        <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Project creation </a>
                     </li>
                     <li>
                        <a href="Department_designation.html">
                        <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Department/designation</a>
                     </li>
                     <li>
                        <a href="User_creation.html">
                        <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; User creation</a>
                     </li>
                     <li>
                        <a href="User%20View.html">
                        <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; User view </a>
                     </li>
                     <li>
                        <a href="Create_password_Secret_question.html">
                        <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Create password/secret question </a>
                     </li>
                     <li>
                        <a href="executive_creation.html.html">
                        <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Executive creation</a>
                     </li>
                     <li>
                        <a href="executive_view.html">
                        <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Executive view</a>
                     </li>
                     <li>
                        <a href="Vendor_Creation.html">
                        <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Vendor creation</a>
                     </li>
                     <li>
                        <a href="Vendor%20view.html">
                        <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Vendor view </a>
                     </li>
                     <li>
                        <a href="Ticket%20Status%20Creation.html">
                        <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Status creation </a>
                     </li>
                      <li>
                        <a href="Required_fields.html">
                        <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Required fields</a>
                     </li>
                     <li>
                        <a href="Scenario_creation.html">
                        <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Scenario creation</a>
                     </li>
                     <li>
                        <a href="Action_fields.html">
                        <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Action fields</a>
                     </li>
                     <li>
                        <a href="#">
                        <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; TAT creation</a>
                     </li>
                     <li>
                        <a href="#">
                        <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; TAT alerts</a>
                     </li>
                     <li>
                        <a href="#">
                        <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Define escalations</a>
                     </li>
                     <li>
                        <a href="#">
                        <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Define alerts</a>
                     </li>
                     <li>
                        <a href="#">
                        <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Upload customer database</a>
                     </li>
                     <li>
                        <a href="#">
                        <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; Upload documents</a>
                     </li>
                     <li>
                        <a href="#">
                        <i class="glyphicon glyphicon-chevron-right"></i>&nbsp; My account</a>
                     </li>
                  </ul>
               </li>
                 
            </ul>
            <!-- /#menu -->
         </div>