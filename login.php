<?
	// Cache Control
	$expires = 0; # 0 seconds
	header("Pragma: public");
	header("Cache-Control: maxage=".$expires);
	header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="../../assets/ico/favicon.ico">

    <title>Log in - Your Org Name</title>

    <!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

    <!-- Bootstrap theme -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">

	<link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <style>
		body {
		  padding-top: 80px;
		  padding-bottom: 40px;
		  background-color: #eee;
		}

		.form-signin {
		  max-width: 330px;
		  padding: 15px;
		  margin: 0 auto;
		}
		.form-signin .form-signin-heading,
		.form-signin .checkbox {
		  margin-bottom: 10px;
		}
		.form-signin .checkbox {
		  font-weight: normal;
		}
		.form-signin .form-control {
		  position: relative;
		  height: auto;
		  -webkit-box-sizing: border-box;
			 -moz-box-sizing: border-box;
				  box-sizing: border-box;
		  padding: 10px;
		  font-size: 16px;
		}
		.form-signin .form-control:focus {
		  z-index: 2;
		}
		.form-signin input[type="email"] {
		  margin-bottom: -1px;
		  border-bottom-right-radius: 0;
		  border-bottom-left-radius: 0;
		}
		.form-signin input[type="password"] {
		  margin-bottom: 10px;
		  border-top-left-radius: 0;
		  border-top-right-radius: 0;
		}
		
		.navbar {
			background-color: #205081;
		}
		
		.form-group { 
			margin-bottom: auto;
		}
    </style>

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

	<nav class="navbar navbar-fixed-top" role="navigation">
	  <div class="container">
		<!-- Logo Here -->
	  </div>
	</nav>
    <div class="container">

      <form method="post" action="login_action.php" class="form-signin" role="form" data-toggle="validator">
      <input type="hidden" name="os_destination" value="<?=$_REQUEST["os_destination"]?>">
      
		<img width="230" height="80" style="max-width: 100%;" border="0" src="/path/to/your/logo.jpg" alt="Your Logo here" class="center-block"><br/>

		<?php
			if (isset($_REQUEST["loginfailed"])) { 
		?>
				<div class="panel panel-danger">
				   <div class="panel-heading">Login failed</div>
				   <div class="panel-body">
					Check supplied email/password.
				  </div>
				</div>
		<?	
			}
		?>
		
		
        <h2 class="form-signin-heading">Please sign in</h2>
		<div class="form-group">
			<input type="email" class="form-control" name="email" placeholder="Email address" value="<?=$_REQUEST["email"]?>"
				required autofocus data-error="Enter a valid email address.">
			<div class="help-block with-errors"></div>
		</div>
		<div class="form-group">
	        <input type="password" name="password" class="form-control" placeholder="Password" required data-error="Invalid password">
			<div class="help-block with-errors"></div>
		</div>

        <label class="checkbox">
          <input type="checkbox" name="os_cookie" value="true"> Remember me 
        </label>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button><br/>
        <a href="/forgotuserpassword.action">Forgot your password?</a>
      </form>

    </div> <!-- /container -->




	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	
	<script src="/lib/validator.js"></script>	
		

  </body>
</html>
