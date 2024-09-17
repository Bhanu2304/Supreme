<html class="no-js" lang="">
<head>
<?php 
$actual_link = "$_SERVER[REQUEST_URI]";
$exp=explode("/", $actual_link);
$page_link=$exp[1];

if($page_link =="about-us"){
    $page_title="About Us";
    $page_descr="";
}
else if($page_link =="contact-us"){
    $page_title="Contact Us";
    $page_descr="";
}
else if($page_link =="terms-of-use"){
    $page_title="Terms Of Use";
    $page_descr="";
}
else if($page_link =="privacy-policy"){
    $page_title="Privacy Policy";
    $page_descr="";
}
else if($page_link =="signup"){
    $page_title="Sign Up";
    $page_descr="";
}
else if($page_link =="login"){
    $page_title="Login";
    $page_descr="";
}
else if($page_link =="view-details"){
    $page_title="View Details"; 
    $page_descr="";
}
else{
  $page_title="Paypik";
  $page_descr="";  
}
?>

<title><?php echo $page_title;?> </title>
<meta name="description" content="<?php echo $page_descr;?>"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="shortcut icon" href="../public/views/img/logos/faviconlogo.png">
<link rel="stylesheet" href="../public/views/style.css">
<link href="../public/views/slide/sliderResponsive.css" rel="stylesheet" type="text/css">
<script src="../public/views/js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>
</head>
<body>
    
<div class="preloader">
    <div class="spinner-wrap">
        <div class="spinner spinner-wave">
            <div class="rect1"></div>
            <div class="rect2"></div>
            <div class="rect3"></div>
            <div class="rect4"></div>
            <div class="rect5"></div>
        </div>
    </div>
</div>
   
<section class="section gray-bg" style="background-color: black;padding:5px;height: auto;">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div style="float:left;margin-top:-5px;" class="hidden-xs"  >

                    <a class="cst-btn-ten" href="javascript:void(0);"><i class="fa fa-facebook"></i><span>f</span></a>
                    <a class="cst-btn-ten" href="javascript:void(0);"><i class="fa fa-google"></i><span>g</span></a>
                </div>
                

                
                <div style="float:right;" >
                    
                   
                    <?php if($this->Session->read("Id") !=""){?>
                    <div class="navb">
                        <div class="dropd">
                            <button class="dropdbtn" onclick="myFunction()" >My Account 
                              <i class="fa fa-caret-down"></i>
                            </button>
                            <div class="dropd-content" id="myDropdown">
                              <a href="#">Hi <?php echo $this->Session->read("UserName");?></a>
                              <a href="view-details">View Details</a>
                              <a href="logout">Logout</a>
                            </div>
                        </div> 
                    </div>
                    <?php }else{?>
                    <a href="signup" id="reg-btn1" class="btn btn-primary btn-sm" title="Sign Up" id="reg-btn">Sign Up <i class="fa fa-angle-right"></i></a>
                    <a href="login" id="login-btn1" class="btn btn-primary btn-sm" title="LOGIN">LOGIN <i class="fa fa-angle-right"></i></a>
                    <?php }?>
                    
                </div>
            </div>
        </div>
    </div>
</section>
    
<nav class="navbar navbar-custom transparent-nav navbar-fixed-top mega-menu "  style="padding: 20px;background-color:#ffffff;margin-buttom:-50px;" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle  collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
       
            <a style="position:relative;top:8px;" class="navbar-brand"  href=""><img style="max-height: 40px" src="../public/views/img/logos/maslogo.png"></a>
            
        </div>		

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right"   >
                <li class="dropdown"><a href="">Home</a></li>
                <li class="dropdown"><a href="about-us">About Us</a></li>
                <li class="dropdown"><a href="contact-us">Contact Us</a></li>
                <li class="dropdown"><a href="developer">Developer</a></li>
            </ul>
        </div>
    </div>
</nav>
    
    
<div class="content" id="content">
    <?php echo $this->fetch('content'); ?> 
</div>
     
<section class="section" style="color:#FFF;background-image: url('../public/views/images/footer-bg.jpg')">
    <div class="container">
        
        <!--
        <div class="row">
            <div class="col-sm-12"  >
                <div style="float:left;"  >                    
                    <a class="cst-btn-ten" href="javascript:void(0);"><i class="fa fa-facebook"></i><span>f</span></a>
                    <a class="cst-btn-ten" href="javascript:void(0);"><i class="fa fa-linkedin"></i><span>in</span></a>
                </div>
            </div>
        </div>
        <hr/>
        -->
        
        <div class="row">
            <div class="col-lg-12 col-sm-12">
                <h4 class="strong white">About</h4>
                <p>Paypik is a revolutionary, unified payment solution that empowers merchants to accept payment methods of their customer,s choice.</p>  
                <p>
                    With the introduction of a wide range of payment options to customers, it is getting difficult for merchants to integrate with each one of them and manage them separately. Paypik has simplified this by providing a single integration to accept payments from a wide range of payment methods to include Debit Cards, Credit Cards, Netbanking, eWallets, UPI and EMIs.
                </p>
            </div>
            <div class="col-lg-12 col-sm-12">
                <a href="contact-us" title="Contact Us" class="btn btn-icon contact-btn contact-button"><i class="livicon" data-name="mail" data-color="#fff" data-hovercolor="false" data-size="18"></i> Contact Us</a>
            </div>
            
            
        </div>
    </div>
</section>

   
<footer class="footer"   >
    <div class="container" style="margin-top:15px;"   >
        <div class="row"  >
            <div class="col-lg-6 col-sm-6"  >			
                <p class="copyright">&copy; <span style="font-size:13px;" >2017 All Right Reserved.Developed by Paypik</span></p>				
            </div>
            <div class="col-sm-6 hidden-xs">
                <ul class="list-inline">
                    <li><a href="privacy-policy" title="Privacy Policy">Privacy Policy</a></li>
                    <li><a href="terms-of-use" title="Terms Of Use">Terms Of Use</a></li>
                    <li><a href="about-us" title="About Us">About Us</a></li>
                    <li><a href="contact-us" title="Contact Us">Contact Us</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div id="overlay" style="position:fixed; height:100%; width:100%; background:rgba(0,0,0,.5); left:0; top:0; right:0; bottom:0; display:none;"></div>
</footer>
   
<div id="topcontrol" style="position: fixed; bottom: 7px; right:45px; opacity: 1; cursor: pointer;" title="">
    <a href="become-a-partner" id="reg-btn1" class="btn btn-primary btn-sm" title="Become a Partner" id="reg-btn">Become a Partner <i class="fa fa-angle-right"></i></a>
</div>
    
<div id="topcontrol" style="position: fixed; bottom: 100px; right: 10px; opacity: 1; cursor: pointer;border:2px;border-radius: 5px;background-color:#ed323e;color: #FFF; text-align: center;" title="">
    
    <a class="cst-btn-ten" href="javascript:void(0);" onclick="return window.open('<?php $this->webroot;?>app/webroot/maschat', 'Live Chat', 'width=500, height=500, top=450, left=950');" ><i class="fa fa-comment pull-right"></i><span></span></a><br/>
    <span onclick="return window.open('<?php $this->webroot;?>app/webroot/maschat', 'Live Chat', 'width=500, height=500, top=450, left=950');" style="font-weight: bold;text-align:center;padding: 4px;cursor: pointer;" >Live Chat</span>
</div>

<?php echo $this->Html->script('function'); ?>
<script src="../public/views/js/vendor/jquery.js"></script>
<script src="../public/views/js/vendor/bootstrap.js"></script>
<script src="../public/views/js/easing.js"></script>
<script src="../public/views/js/scrollbar.js"></script>
<script src="../public/views/js/retina.js"></script>
<script src="../public/views/js/raphael.js"></script>
<script src="../public/views/js/tabs.js"></script>
<script src="../public/views/js/livicons.js"></script>
<script src="../public/views/js/icheck.js"></script>
<script src="../public/views/js/mousewheel.js"></script>
<script src="../public/views/js/selectik.js"></script>
<script src="../public/views/js/spinedit.js"></script>
<script src="../public/views/js/wow.js"></script>
<script src="../public/views/js/hover-dropdown.js"></script>
<script src="../public/views/js/classie.js"></script>
<script src="../public/views/cloudslider/js/cloudslider.jquery.min.js"></script>
<script src="../public/views/cubeportfolio/js/jquery.cubeportfolio.js"></script>
<script src="../public/views/nivo-lightbox/nivo-lightbox.min.js"></script>
<script src="../public/views/js/appear.js"></script>
<script src="../public/views/js/pie-chart.js"></script>
<script src="../public/views/js/vide.js"></script>
<script src="../public/views/js/fitvids.js"></script>
<script src="../public/views/owl-carousel/owl.carousel.min.js"></script>
<script src="../public/views/js/jflickrfeed.js"></script>
<script src="../public/views/js/tweecool.js"></script>
<script src="../public/views/js/chart.js"></script>
<script src="../public/views/js/totop.js"></script>
<script src="../public/views/js/sm-scroll.js"></script>
<script src="../public/views/js/smooth-scroll.js"></script>
<script src="../public/views/js/ajaxchimp.js"></script>
<script src="../public/views/js/contact.js"></script>
<script src="../public/views/js/form.js"></script>
<script src="../public/views/js/validate.js"></script>
<script src="../public/views/js/countdown.js"></script>
<script src="../public/views/js/tempo.js"></script>	
<script src="../public/views/js/main.js"></script>	
<script src="../public/views/slide/sliderResponsive.js"></script>
<script>
    $(document).ready(function() {

      $("#slider1").sliderResponsive({
      // Using default everything
        // slidePause: 5000,
        // fadeSpeed: 800,
        // autoPlay: "on",
        // showArrows: "off", 
        // hideDots: "off", 
        // hoverZoom: "on", 
        // titleBarTop: "off"
      });  
    }); 
    
      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-36251023-1']);
      _gaq.push(['_setDomainName', 'jqueryscript.net']);
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();

    $("#reg-btn").click(function() {
      $("#register-fix").show();
      $("#overlay").show();
    });

    $("#overlay").click(function() {
      $("#register-fix").hide();
      $("#overlay").hide();
    });
   
    $("#login-btn").click(function() {
      $("#login-fix").show();
      $("#overlay").show();
    });

    $("#overlay").click(function() {
      $("#login-fix").hide();
      $("#overlay").hide();
    });
    
    function closeform(id){
        $("#"+id).hide();
        $("#overlay").hide();
    }
    
    
function myFunction() {
    document.getElementById("myDropdown").classList.toggle("show");
}

window.onclick = function(e) {
  if (!e.target.matches('.dropdbtn')) {
    var myDropdown = document.getElementById("myDropdown");
      if (myDropdown.classList.contains('show')) {
        myDropdown.classList.remove('show');
      }
  }
}
    
</script>


</body>
</html>
