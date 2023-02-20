<?php
    include('server.php');
    if(isset($_SESSION['std_id'])){
        header('Location: index.php');
    }
?>
<!DOCTYPE html>
<html>
<head>
  <title>Register @Khaba Naki</title>
  <link rel="stylesheet" type="text/css" href="style3.css">
</head>
<body>
  <div class="header">
  	<h2>Register</h2>
  </div>

  <form method="post" action="registration.php">
  	<?php include('errors.php'); ?>

  	<div class="input-group">
  	  <label>Student ID:</label>
  	  <input type="text" name="std_id" value="<?php echo $std_id; ?>">
  	</div>

  	<div class="input-group">
  	  <label>Name:</label>
  	  <input type="text" name="username" value="<?php echo $username; ?>">
  	</div>
  	<div class="input-group">
  	  <label>Department:</label>
  	  <input type="text" name="dept_name" value="<?php echo $dept_name; ?>">
  	</div>
  	<div class="input-group">
  	  <label>Email:</label>
  	  <input type="email" name="email" value="<?php echo $email; ?>">
  	</div>
  	<div class="input-group">
  	  <label>Phone:</label>
  	  <input type="text" name="phone" value="<?php echo $phone; ?>">
  	</div>
  	<div class="input-group">
  	  <label>Password:</label>
  	  <input type="password" name="password_1">
  	</div>
  	<div class="input-group">
  	  <label>Confirm Password:</label>
  	  <input type="password" name="password_2">
  	</div>
  	<div class="input-group">
  	  <button type="submit" class="btn" name="reg_user">Register</button>
  	</div>
  	<p>
  		Already a member? <a href="login.php">Sign in</a>
  	</p>
  </form>
</body>
</html>
