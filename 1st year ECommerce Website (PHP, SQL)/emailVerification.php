<?php
require_once('mainNavigation.php');

if (isset($_GET['verify'])) {
   $sql="select verify_code,email from accounts";
   $result=mysqli_query($conn,$sql);
   $email="";
   $message="";
   if(mysqli_num_rows($result)>0)
   {
      while($row=mysqli_fetch_assoc($result))
      {
         if ($row['verify_code']==$_GET['verify']) {
            $email=$row['email'];
            $message="success";
         }
      }
   }
   if ($message=="success") {
      $sql="update accounts set verify_code='0' where email='$email'";
      if(mysqli_query($conn,$sql)){
         $message="success, ".$email." is now verified!";
      }else{
         $message="error, please try again";
      }
   }
}
?>
<!DOCTYPE html>
<html>
<head>
   <title>Account</title>
</head>
<body style="background-color: white;">
   <form class="bg-light" style="text-align: center; max-width: 35em; margin: auto; box-shadow: 10px 10px 10px #888; text-align: center;">
      <br><br>
      <img style="max-width: 5em;" src="images/logo-large.png">
      <br><br>
      <h2><?php echo $message; ?></h2>
      <br><br>
   </form>
</body>
</html>
