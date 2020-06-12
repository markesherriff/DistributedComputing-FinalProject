<?php
	require_once('settings.php');
	require_once('mainNavigation.php');

	$email="";
	$password="";
	$errorMessage="";

	$sql="select email,fname,lname,address,password,is_admin from accounts";
	$result=mysqli_query($conn,$sql);
	
	if($_SERVER["REQUEST_METHOD"]=="POST"){
		if (isset($_POST['login'])) {
			$email=$_POST['email'];
			$password=$_POST['password'];
			$errorMessage="*incorrect password or email";
			if(mysqli_num_rows($result)>0)
			{
				while($row=mysqli_fetch_assoc($result))
				{
					if($row['email']==$email){
						if ($row['password']==$password) {
							$_SESSION['user_given_name']=$row['fname'];
							$_SESSION['user_family_name']=$row['lname'];
							$_SESSION['user_picture']="images/icon/user.png";
							$_SESSION['user_email']=$row['email'];
							$_SESSION['user_address']=$row['address'];
							$_SESSION['user_is_admin']=$row['is_admin'];
							header("Location:myAccount.php");
						}
					}
				}
			}
		}else if(isset($_POST['createAccount'])){
			header("Location:createAccount.php");
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>LOGIN</title>
</head>
<body style="background-color: white;">
	<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="bg-light" style="text-align: center; max-width: 35em; margin: auto; box-shadow: 10px 10px 10px #888;">
		<br><br>
		<img style="max-width: 5em;" src="images/logo-large.png">
		<h2 style="font-weight: bolder;">LOG IN</h2>
		<p style="font-size: 0.9em; color: red;"><?php echo $errorMessage; ?></p>
		<input style="margin-bottom: 0.3em;" type="text" placeholder="email" name="email" value="<?php echo $email; ?>">
		<br>
		<input type="password" placeholder="password" name="password" value="<?php echo $password; ?>">
		<br>
		<br><br>
		<button style="margin-bottom: 0.8em;" class="btn btn-outline-primary" type="submit" name="login" value="login">log in</button>
		<br>
		<button style="margin-bottom: 0.8em;" class="btn btn-outline-primary" type="submit" name="createAccount" value="createAccount">create account</button>
		<br>
		<a class="btn btn-outline-danger" href="<?= 'https://accounts.google.com/o/oauth2/auth?scope=' . urlencode('https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email') . '&redirect_uri=' . urlencode(CLIENT_REDIRECT_URL) . '&response_type=code&client_id=' . CLIENT_ID . '&access_type=online' ?>"><img style="max-width: 2em; padding-right: 0.3em;" src="images/icon/google_plus.png"/>use google</a>
		<br><br>
	</form>
</body>
</html>