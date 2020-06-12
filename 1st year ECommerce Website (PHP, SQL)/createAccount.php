<?php
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	require_once('settings.php');
	require_once('mainNavigation.php');
	$email="";
	$fname="";
	$lname="";
	$password="";
	$address="";
	if (isset($_SESSION['gmail'])) {
		$email=$_SESSION['gmail'];
		$fname=$_SESSION['user_given_name'];
		$lname=$_SESSION['user_family_name'];
	}

	if($_SERVER["REQUEST_METHOD"]=="POST"){
		$email=test_input($_POST["email"]);
		$fname=$_POST['fname'];
		$lname=$_POST['lname'];
		$password=$_POST['password'];
		$address=$_POST['address'];
		if(!filter_var($email,FILTER_VALIDATE_EMAIL))
		{
			echo "<script>alert('invalid email!');</script>";
		}else{

			$alreadyExists=0;
			$sql="select email from accounts";
			$result=mysqli_query($conn,$sql);
			if(mysqli_num_rows($result)>0)
			{
				while($row=mysqli_fetch_assoc($result))
				{
					if ($row['email']==$email) {
						echo "<script>alert('email already used');</script>";
						$alreadyExists=1;
					}
				}
			}

			if ($alreadyExists==0) {
				$verifyCode = random_int(10,1000000000000000);
				$sql="insert into accounts(email,fname,lname,password,address,verify_code)
				values('$email', '$fname', '$lname', '$password', '$address', '$verifyCode')";
				if(mysqli_query($conn,$sql))
				{
					$lastid=mysqli_insert_id($conn);
					echo "<script>alert('Account created and email verification sent!');</script>";

					/* Include the Composer generated autoload.php file. */
					require 'C:\myserver\htdocs\ASMR\vendor\autoload.php';

				    $password='qwertykeyboard'; //This is John Doe’s email account password
					/* Create a new PHPMailer object. Passing TRUE to the constructor enables exceptions. */
					$mail = new PHPMailer(TRUE);

					/* Open the try/catch block. */
					try {
					   /* Set the mail sender. */
					   $mail->setFrom('asmrnoreply@gmail.com', 'ASMR');

					   /* Add a recipient. */
					   $mail->addAddress($email);

					   /* Set the subject. */
					   $mail->Subject = 'VERIFY ASMR';

					   /* Set the mail message body. */
					   $mail->Body = 'click this link: http://localhost/ASMR/emailVerification.php?verify='.$verifyCode;

					/* SMTP parameters. */
					   
					   /* Tells PHPMailer to use SMTP. */
					   $mail->isSMTP();
					   
					   /* SMTP server address. */
					   $mail->Host = 'smtp.gmail.com';

					   /* Use SMTP authentication. */
					   $mail->SMTPAuth = TRUE;
					   
					   /* Set the encryption system. */
					   $mail->SMTPSecure = 'tls';
					   
					   /* SMTP authentication username. */
					   $mail->Username = 'asmrnoreply@gmail.com';
					   
					   /* SMTP authentication password. */
					   $mail->Password = $password;
					   
					   /* Set the SMTP port. */
					   $mail->Port = 587;


					   /* Finally send the mail. */
					   $mail->send();
					}
					catch (Exception $e)
					{
					   /* PHPMailer exception. */
					   echo $e->errorMessage();
					}
					catch (\Exception $e)
					{
					   /* PHP exception (note the backslash to select the global namespace Exception class). */
					   echo $e->getMessage();
					}

				}
				else
				{
				echo "<script>alert('error, please try again!');</script>";
				}
			}
		}
	}

	function test_input($data)
	{
		$data=trim($data);
		$data=stripslashes($data);
		$data=htmlspecialchars($data);

		return $data;
	}

?>
<!DOCTYPE html>
<html>
<head>
	<title>CREATE ACCOUNT</title>
</head>
<body style="background-color: white;">
	<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="bg-light" style="text-align: center; max-width: 35em; margin: auto; box-shadow: 10px 10px 10px #888;">
		<br><br>
		<img style="max-width: 5em;" src="images/logo-large.png">
		<br><br>
		<h2 style="font-weight: bolder;">CREATE ACCOUNT</h2>
		<br>
		<input type="text" placeholder="email" name="email" value="<?php echo $email; ?>" required>
        <br><br>
        <input type="text" placeholder="first name" name="fname" value="<?php echo $fname; ?>" required>
        <br><br>
        <input type="text" placeholder="last name" name="lname" value="<?php echo $lname;?>" required> 
        <br><br>
        <input type="password" placeholder="password" name="password" required>
        <br><br>
        <input type="text" placeholder="address" name="address" required>
        <br><br>
        <button class="btn btn-outline-primary" type="submit" name="create" value="create">CREATE</button>
        <br>
        <br>
	</form>
</body>
</html>