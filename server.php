<?php
session_start();
date_default_timezone_set("Asia/Dhaka");

// initializing variables
$std_id = "";
$username = "";
$email    = "";
$dept_name = "";
$phone = "";
$errors = array();

// connect to the database
$db = mysqli_connect('localhost', 'root', 'bd7toRy5%', 'testdb');

// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receive all input values from the form
  $std_id = mysqli_real_escape_string($db, $_POST['std_id']);
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $dept_name = mysqli_real_escape_string($db, $_POST['dept_name']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $phone = mysqli_real_escape_string($db, $_POST['phone']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  if (empty($std_id)) { array_push($errors, "Student ID is required"); }
  if (empty($username)) { array_push($errors, "Username is required"); }
  if (empty($dept_name)) { array_push($errors, "Department is required"); }
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($phone)) { array_push($errors, "Phone number is required"); }
  if (empty($password_1)) { array_push($errors, "Password is required"); }
  if ($password_1 != $password_2) {
	array_push($errors, "The two passwords do not match");
  }

  // first check the database to make sure
  // a user does not already exist with the same id and/or email
  $user_check_query = "SELECT * FROM student_info WHERE Id='$std_id' OR email='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);

  if ($user) { // if user exist
    if ($user['Id'] === $std_id) {
      array_push($errors, "Student already exists");
    }

    if ($user['Email'] === $email) {
      array_push($errors, "email already exists");
    }
  }

  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) {
  	$password = md5($password_1);//encrypt the password before saving in the database

  	$query = "INSERT INTO student_info (Id, Name, Password, Department, Phone, Email)
  			  VALUES('$std_id', '$username', '$password', '$dept_name', '$phone', '$email')";
  	mysqli_query($db, $query);
  	$_SESSION['username'] = $username;
  	$_SESSION['success'] = "You are now logged in";
  	header('location: index.php');
  }
}
if (isset($_POST['login_user'])) {
  $std_id = mysqli_real_escape_string($db, $_POST['std_id']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  if (empty($std_id)) {
  	array_push($errors, "std_id is required");
  }
  if (empty($password)) {
  	array_push($errors, "Password is required");
  }
  if (count($errors) == 0) {
  	$password = md5($password);
  	$query = "SELECT * FROM student_info WHERE Id='$std_id' AND Password='$password'";
  	$results = mysqli_query($db, $query);
  	if (mysqli_num_rows($results) == 1) {
        //echo"$std_id";
  	  $_SESSION['std_id'] = $std_id;
  	  $_SESSION['success'] = "You are now logged in";
  	  //if($_SESSION['std_id']=="17301043") echo($_SESSION['std_id']);
  	  //header('location: index.php');
  	}else {
  		array_push($errors, "Wrong std_id/password combination");
  	}
  }
}
?>
