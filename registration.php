<?php

  include "includes/db_connect.inc.php";

  $fName = $lName = $uName = $uPass = $uEmail = $err = $uNameInDB = "" ;
	
	
	/* mysqli_real_escape_string() helps prevent sql injection */
  if($_SERVER["REQUEST_METHOD"]=="POST"){
    if(!empty($_POST['first_name'])){
      $fName = mysqli_real_escape_string($conn, $_POST['first_name']);
    }
    if(!empty($_POST['last_name'])){
      $lName = mysqli_real_escape_string($conn, $_POST['last_name']);
    }
    if(!empty($_POST['user_name'])){
      $uName = mysqli_real_escape_string($conn, $_POST['user_name']);
    }
    if(!empty($_POST['user_pass'])){
      $uPass = mysqli_real_escape_string($conn, $_POST['user_pass']);
      $uPassToDB = password_hash($uPass, PASSWORD_DEFAULT);
    }
    if(!empty($_POST['user_email'])){
      $uEmail = mysqli_real_escape_string($conn, $_POST['user_email']);
    }

    $sqlUserCheck = "SELECT user_name FROM users WHERE user_name = '$uName'";
    $result = mysqli_query($conn, $sqlUserCheck);

    while($row = mysqli_fetch_assoc($result)){
      $uNameInDB = $row['user_name'];
    }

    if($uNameInDB == $uName){
      $err = "UserName already exists!";
    }
    else{
      $sql = "INSERT INTO users (first_name, last_name, user_name, email, password)
              VALUES ('$fName','$lName','$uName','$uEmail', '$uPassToDB');";

      mysqli_query($conn, $sql);
    }
  }

?>

<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Registration: </title>
  </head>
  <body>
    <form action="registration.php" method="post">
      <fieldset>
        <legend>User Registration: </legend>
        <label for="first_name">First name: </label>
        <input type="text" name="first_name" value="" required><br>
        <label for="last_name">Last name: </label>
        <input type="text" name="last_name" value="" required><br>
        <label for="user_name">User name: </label>
        <input type="text" name="user_name" value="" required><br>
        <label for="user_pass">Password: </label>
        <input type="password" name="user_pass" value="" required><br>
        <label for="user_email">E-mail: </label>
        <input type="email" name="user_email" value="" required><br>
        <button type="submit" name="button">Register</button><br>
        <span style="color:red;"><?php echo $err; ?></span>
        <span><b>Or Log In <a href="login.php">here</a></b></span>
      </fieldset>
    </form>
  </body>
</html>
