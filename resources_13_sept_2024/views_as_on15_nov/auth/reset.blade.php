<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <!--IE Compatibility modes-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--Mobile first-->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Login Page - Dialdesk</title>
   <meta name="description" content="Dialdesk Template">
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
        <img src="{{ asset('img/dialdesk.png')}}" alt="Dialdesk Logo">
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
        
        
                    <div id="signup" class="tab-pane" style="display: block">
            <form method="POST" action="{{ route('password.request') }}" aria-label="{{ __('Register') }}" onsubmit="return validate_register_credential_first()" >
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="email" id="email" name="email" required placeholder="mail@domain.com" value="" class="form-control middle" readonly="">
                @if ($errors->has('email'))
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
                <button class="btn btn-lg btn-success btn-block" type="submit">{{ __('Register') }}</button>
            </form>
        </div>
    </div>
    <hr>
    
</div>
<script src="{{ asset('assets/lib/jquery/jquery.js')}}"></script>
<script src="{{ asset('assets/lib/bootstrap/js/bootstrap.js')}}"></script>            

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
        else if(pass.length<8)
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
        
        var pwd = document.getElementById("password");
        var conPwd = document.getElementById("password_confirm");
        
        
        
        
        if (pwd.value.length==0) 
        {
            //alert("type");
            strength.innerHTML = 'Type Password';
            return false;
        } 
        else if (false == enoughRegex.test(pwd.value)) 
        {
                strength.innerHTML = '<span style="color:red">Password Should Be 8 Charecter Long</span>';
                return false;
        }
        else if (strongRegex.test(pwd.value)) 
        {
            //alert("strong");
            strength.innerHTML = '<span style="color:green">Strong!</span>';
        }
        else if (mediumRegex.test(pwd.value)) 
        {
            //alert("Medium");
            strength.innerHTML = '<span style="color:orange">Medium!</span>';
            return false;
        }
        
        if(pwd.value!==conPwd.value)
        {
           strength.innerHTML = '<span style="color:red">Password Not Matched</span>';
           return false;
        }
        
        
        
        return true;
    }
    
</script>


    </body>
</html>