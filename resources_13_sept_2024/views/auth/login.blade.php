<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <!--IE Compatibility modes-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--Mobile first-->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Login Page - Supreme</title>
   <meta name="description" content="QA Template">
    <meta name="author" content="">
    
    <meta name="msapplication-TileColor" content="#5bc0de" />
    <meta name="msapplication-TileImage" content="{{ asset('assets/css/metis-tile.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/lib/bootstrap/css/bootstrap.css')}}" />
    <link rel="stylesheet" href="{{ asset('assets/lib/font-awesome/css/font-awesome.css')}}" />
    
    
    <!-- Metis core stylesheet -->
    <link rel="stylesheet" href="{{ asset('assets/css/main.css')}}">
    
    <!-- metisMenu stylesheet -->
    <link rel="stylesheet" href="{{ asset('assets/lib/metismenu/metisMenu.css')}}">
    
    <!-- onoffcanvas stylesheet -->
    <link rel="stylesheet" href="{{ asset('assets/lib/onoffcanvas/onoffcanvas.css')}}">
    
    <!-- animate.css stylesheet -->
    <link rel="stylesheet" href="{{ asset('assets/lib/animate.css/animate.css')}}">
    
</head>
<body class="login">


<div class="form-signin">
    @csrf
    <div class="text-center">
        <img src="{{ asset('assets/images/logo-in.png')}}">

    </div>    
    <hr>
    <div class="tab-content">
        @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (session('warning'))
                        <div class="alert alert-warning">
                            {{ session('warning') }}
                        </div>
                    @endif
        <div id="login" class="tab-pane active" aria-label="{{ __('Login') }}">
            <form action="{{ route('login') }}" method="post" onsubmit="return validate_login_credential_first()" >
                <p class="text-muted text-center">
                    Enter your username and password
                </p>
                <input type="text" id="UserName" name="email" placeholder="Email Id" value="{{ old('email') }}" class="form-control top" required>
                
                
                
                <input type="password" id="passwordLogin" name="password" placeholder="Password" class="form-control bottom" required>
                
                                    
                
                <span id="strength1"></span>
                
                @if ($errors->has('email') && !in_array($errors->first('email'),array("We can't find a user with that e-mail address.","The email has already been taken.","The email must be a valid email address."))  )
                    <span class="invalid-feedback" role="alert" style="color:red;">
                        {{ $errors->first('email') }}
                    </span>
                @endif
                
                <div class="checkbox">
          <label class"rememberme">
                      <input type="checkbox"  id="remember_me"  value="1"> Remember Me
          </label>
        </div>
                <button class="btn btn-lg btn-primary btn-block"  type="submit">Sign in</button>
            </form>
        </div>
        <div id="forgot" class="tab-pane">
            <form method="post" action="{{ route('password.email') }}">
                {!! csrf_field() !!}
                @if ($errors->has('email') && !in_array($errors->first('email'),array('These credentials do not match our records.',"The email has already been taken.","The email must be a valid email address."))  )
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }} </strong>
                                    </span>
                                @endif
                <p class="text-muted text-center">Enter your valid e-mail</p>
                <input name="email" id="email_reset" type="email" placeholder="mail@domain.com" class="form-control" required>
                <br>
                <button class="btn btn-lg btn-danger btn-block" type="submit">Recover Password</button>
            </form>
        </div>
        <div id="signup" class="tab-pane">
            <form method="POST" action="{{ route('register') }}" aria-label="{{ __('Register') }}" onsubmit="return validate_register_credential_first()" >
                @csrf
                <input type="text" id="name" name="name" placeholder="Display Name" class="form-control top {{ $errors->has('email') ? ' is-invalid' : '' }}" onKeyPress="return alphanum(this.value,event)"  required autofocus>
                @if ($errors->has('name'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                <input type="email" id="email" name="email" required placeholder="mail@domain.com" class="form-control middle">
                @if ($errors->has('email') && !in_array($errors->first('email'),array('These credentials do not match our records.',"We can't find a user with that e-mail address."))   )
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                <span id="strengthMail" style="display:none">Type Password</span>
                <input id="password" type="password" class="form-control middle" name="password" placeholder="password" onkeyup="return passwordChanged();" required>
                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                <span id="strength"></span>
                <input type="password" id="password_confirm" name="password_confirmation" type="password" placeholder="re-password" class="form-control bottom">
                <button type="submit" class="btn btn-lg btn-success btn-block" type="submit">{{ __('Register') }}</button>
            </form>
        </div>
    </div>
    <hr>
    <div class="text-center">
        <ul class="list-inline">
            <li><a class="text-muted" href="#login" data-toggle="tab">Login</a></li>
            <li><a class="text-muted" href="#forgot" data-toggle="tab">Forgot Password</a></li>
            
        </ul>
    </div>
</div>
<script src="{{ asset('assets/lib/jquery/jquery.js')}}"></script>
<script src="{{ asset('assets/lib/bootstrap/js/bootstrap.js')}}"></script>            
<script type="text/javascript">
        (function($) {
            $(document).ready(function() {
                $('.list-inline li > a').click(function() {
                    var activeForm = $(this).attr('href') + ' > form';
                    //console.log(activeForm);
                    $(activeForm).addClass('animated fadeIn');
                    //set timer to 1 seconds, after that, unload the animate animation
                    setTimeout(function() {
                        $(activeForm).removeClass('animated fadeIn');
                    }, 1000);
                });
            });
        })(jQuery);
    </script>
 <script language="javascript">
function passwordChanged() {
var strength = document.getElementById('strength');
var strongRegex = new RegExp("^(?=.{8,})(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*\\W).*$", "g");
var mediumRegex = new RegExp("^(?=.{7,})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$", "g");
var enoughRegex = new RegExp("(?=.{6,}).*", "g");
var pwd = document.getElementById("password");
if (pwd.value.length==0) {
strength.innerHTML = 'Type Password';
} else if (false == enoughRegex.test(pwd.value)) {
strength.innerHTML = 'More Characters';
} else if (strongRegex.test(pwd.value)) {
strength.innerHTML = '<span style="color:green">Strong!</span>';
} else if (mediumRegex.test(pwd.value)) {
strength.innerHTML = '<span style="color:orange">Medium!</span>';
} else {
strength.innerHTML = '<span style="color:red">Weak!</span>';
}
}
</script>

<script>
    function validate_login_credential_first()
    {
        var pass = $('#passwordLogin').val();
        if(pass=='')
        {
            $('#strength1').html('<font color="red">Please Fill Password</font>');
            return false;
        }
        else if(pass.length<6)
        {
            $('#strength1').html('<font color="red">Password Should Be 8 Charecters Long</font>');
            return false;
        }
        
        return true;
    }
    
    function validate_register_credential_first()
    {
        var re = /\S+@\S+\.\S+/;
        var strength = document.getElementById('strength');
        var mailstrength = document.getElementById('strengthMail');
        var strongRegex = new RegExp("^(?=.{7,})(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*\\W).*$", "g");
        var mediumRegex = new RegExp("^(?=.{7,})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$", "g");
        var enoughRegex = new RegExp("(?=.{6,}).*", "g");
        var email = document.getElementById("email");
        var pwd = document.getElementById("password");
        var conPwd = document.getElementById("password_confirm");
        
        
        if(false==re.test(email.value))
        {
            mailstrength.innerHTML = '<span style="color:red">Please Fill Valid Email Id</span>';
            mailstrength.style.display="block";
            return false;
        }
        else
        {
            mailstrength.innerHTML = '';
            mailstrength.style.display="none";
        }
        
        if (pwd.value.length==0) 
        {
            //alert("type");
            strength.innerHTML = 'Type Password';
            return false;
        }
        if (pwd.value.length<8) 
        {
            //alert("type");
            strength.innerHTML = '<span style="color:red">Password Should Be 8 Charecter Long</span>';
            return false;
        }
        
        if( pwd.value.search(/[A-Z]/) == -1 ) {
            strength.innerHTML = '<span style="color:red">Password Should Contain 1 Upper Case Letter</span>';
       return false;
    }
    if( pwd.value.search(/[0-9]/) == -1 ) {
            strength.innerHTML = '<span style="color:red">Password Should Contain 1 Number</span>';
       return false;
    }
       if( pwd.value.search(/[@!#$%&*]/) == -1 ) {
            strength.innerHTML = '<span style="color:red">Password Should Contain 1 Special Char @!#$%&*</span>';
       return false;
    } 
        
        
        if(pwd.value!==conPwd.value)
        {
           strength.innerHTML = '<span style="color:red">Password Not Matched</span>';
           return false;
        }
        if(strongRegex.test(pwd.value)) 
        {
            strength.innerHTML = '<span style="color:green">Strong!</span>';
        }
        
        
        return true;
    }
  function alphanum(val,evt){
      var charCode = (evt.which) ? evt.which : event.keyCode;
    
    if( /[^a-zA-Z. ]/.test( val ) ) {
       return false;
    }
    else if( /[^a-zA-Z. ]/.test( String.fromCharCode(charCode) ) )
    {
     return false;   
    }
    return true;     
   }
  if('{{session('status')}}'=='We have e-mailed your password reset link!' || '{{$errors->first('email')}}'=="We can&#039;t find a user with that e-mail address.")
  {
    $('#login').removeClass("active");
    $('#signup').removeClass("active");
    $('#forgot').addClass("tab-pane active");
  }
  
  if('{{session('status')}}'=='We sent you an activation code. Check your email and click on the link to verify.'|| '{{$errors->first('email')}}' == 'The email has already been taken.' || '{{$errors->first('email')}}' =="The email must be a valid email address." ||'{{$errors->first('password')}}'== "The password format is invalid.")
  {
    $('#login').removeClass("active");
    $('#forgot').removeClass("active");
    $('#signup').addClass("tab-pane active");
  }
  
</script>
<script>
            $(function() {
 
                if (localStorage.chkbx && localStorage.chkbx != '') {
                    $('#remember_me').attr('checked', 'checked');
                    $('#UserName').val(localStorage.usrname);
                    $('#passwordLogin').val(localStorage.pass);
                } else {
                    $('#remember_me').removeAttr('checked');
                    $('#UserName').val('');
                    $('#passwordLogin').val('');
                }
 
                $('#remember_me').click(function() {
 
                    if ($('#remember_me').is(':checked')) {
                        // save username and password
                        localStorage.usrname = $('#UserName').val();
                        localStorage.pass = $('#passwordLogin').val();
                        localStorage.chkbx = $('#remember_me').val();
                    } else {
                        localStorage.usrname = '';
                        localStorage.pass = '';
                        localStorage.chkbx = '';
                    }
                });
            });
 
        </script>

    </body>
</html>