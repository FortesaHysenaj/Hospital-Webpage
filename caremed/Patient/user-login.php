<?php
session_start();
error_reporting(0);
include("include/config.php");
if(isset($_POST['submit']))
{
	$username = mysqli_real_escape_string($con, $_POST['username']);
	$password = mysqli_real_escape_string($con, $_POST['password']);
	$password = md5($password);
	$sql = "SELECT * FROM users WHERE email = '".$username ."'";
	if(!isset($_COOKIE['user_email']))
	{
		$sql .= "AND password = '".$password."'";
	}
	$ret=mysqli_query($con,$sql);
	$num=mysqli_fetch_array($ret);

if($num>0)
{
	$verified = $num['verified'];
	$email = $num['email'];
	$date = $num['regDate'];
	$date = strtotime($date);
	$date = date('M d Y',$date);
	if(!empty($_POST['remember']))
	{
		setcookie("user_email", $email, time() + (365*24*60*60));
		setcookie ("user_password",$_POST["password"],time()+ (365 * 24 * 60 * 60));
	}else{
		if(isset($_COOKIE['user_email']))
		{
			setcookie("user_email","");
			setcookie("user_password", "");
		}
	}
	if($verified == 1)
	{
		$extra="dashboard.php";//
		$_SESSION['login']=$_POST['username'];
		$_SESSION['id']=$num['id'];
		$_SESSION['gender'] = $num['gender'];
		$host=$_SERVER['HTTP_HOST'];
		$uip=$_SERVER['REMOTE_ADDR'];
		$status=1;
		$log=mysqli_query($con,"insert into userlog(uid,username,userip,status) values('".$_SESSION['id']."','".$_SESSION['login']."','$uip','$status')");
		$uri=rtrim(dirname($_SERVER['PHP_SELF']),'/\\');
		header("location:http://$host$uri/$extra");
		exit();
	}else{
		$error = "This account has not yet been verified. An email was sent to $email on $date";
	}
}
else
{
	$_SESSION['login']=$_POST['username'];	
	$uip=$_SERVER['REMOTE_ADDR'];
	$status=0;
	mysqli_query($con,"insert into userlog(username,userip,status) values('".$_SESSION['login']."','$uip','$status')");
	$_SESSION['errmsg']="Incorrect username or password";
	$extra="user-login.php";
	$host  = $_SERVER['HTTP_HOST'];
	$uri  = rtrim(dirname($_SERVER['PHP_SELF']),'/\\');
	header("location:http://$host$uri/$extra");
	exit();
	}
}
?>


<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Patient-Login</title>

		<meta charset="utf-8" />
		<link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
		<link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
		<link rel="stylesheet" href="assets/css/styles.css">
		<style>
		body{
			background:url(caremed.jpg);
			background-repeat:no-repeat;
			background-size:cover;
			margin-left:20px;
			margin-top:-60px;
			background-attachment: fixed;
			background-position:cover;
		}
		.form-login{
			background-color: transparent !important;

		}
		</style>
	</head>
	<body class="login">
		<div class="row" style="margin-top:90px;">
		
			<div class="main-login col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
				<div class="logo margin-top-30">
				<h1 style="color: #000000; margin-left:70px;font-size: 50px;font-weight:bold;"> Patient Login</h1>

				</div>

				<div class="box-login" style="width:450px;">
				<span style="color:red;"><?php echo $error?></span>

					<form class="form-login" method="post">
						<fieldset>
							<legend>
								Sign in to your account
							</legend>
							<p>
								Please enter your name and password to log in.<br />
								<span style="color:red;"><?php echo $_SESSION['errmsg']; ?><?php echo $_SESSION['errmsg']="";?></span>
							</p>
							<div class="form-group">
								<span class="input-icon">
									<input type="text" class="form-control" value="<?php if(isset($_COOKIE["user_email"])) { echo $_COOKIE["user_email"]; } ?>" name="username" placeholder="Username">
									<i class="fa fa-user"></i> </span>
							</div>
							<div class="form-group form-actions">
								<span class="input-icon">
									<input type="password" class="form-control password" value="<?php if(isset($_COOKIE["user_password"])) { echo $_COOKIE["user_password"]; } ?>" name="password" placeholder="Password">
									<i class="fa fa-lock"></i>
									 </span>
									
							</div>
							<div class="field-group">
		<div><input type="checkbox" name="remember" id="remember" <?php if(isset($_COOKIE["user_email"])) { ?> checked <?php } ?> />
		<label for="remember-me">Remember me</label>
	</div>
							<div class="forgot-pass">
							<a href="forgot-password.php">
							Forgot your password?
							</a>
							<div class="form-actions">
								
								<button type="submit" style="width:150px;font-size:20px;padding:7px;" class="btn btn-primary center-block" name="submit">
									Sign in <i class="fa fa-arrow-circle-right"></i>
								</button>
							</div>
							<div class="new-account">
								Don't have an account yet?
								<a href="registration.php">
									Create an account
								</a>
							</div>
						</fieldset>

					</form>

					<div class="copyright">
						&copy; <span class="current-year"></span><span class="text-bold text-uppercase"> CAREMED</span>. <span>All rights reserved</span>
					</div>
			
				</div>

			</div>
		</div>
  
 
 		<div class="button home_button">
			<a href="../index.php" style="font-size: 35px;
			 position: absolute; left: 30px; top: 20px; " 
				class="button button1" role="button">Home</a>					
		</div>


		<script src="vendor/jquery/jquery.min.js"></script>
		<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
		<script src="vendor/modernizr/modernizr.js"></script>
		<script src="vendor/jquery-cookie/jquery.cookie.js"></script>
		<script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
		<script src="vendor/switchery/switchery.min.js"></script>
		<script src="vendor/jquery-validation/jquery.validate.min.js"></script>
	
		<script src="assets/js/main.js"></script>

		<script src="assets/js/login.js"></script>
		
	
	</body>
	<!-- end: BODY -->
</html>