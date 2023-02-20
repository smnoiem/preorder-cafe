<?php
    include('server.php');
    if(isset($_SESSION['std_id'])=="17301043"){
        //echo($_SESSION['std_id']);
        header('Location: admin.php');
    }
    else if(isset($_SESSION['std_id'])) header('Location: index.php');
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Login @Khaba Naki</title>

    <link rel="stylesheet" type="text/css" href="style.css">
  </head>
<body>





    <div class="loginbox">

        <div class="header">
        <h1>Khaba Naki?</h1>
        <br>
        <br>
        <br>
        <br>
        <h2>Login</h2>
      </div>

      <form method="post" action="login.php">
        <?php include('errors.php'); ?>
        <div class="input-group">
            <label>Student Id:</label>
            <input type="text" name="std_id" >
        </div>
        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password">
        </div>
        <div class="input-group">
            <button type="submit" class="btn" name="login_user">Login</button>
        </div>
        <p>
            Not yet a member? <a href="registration.php">Sign up</a>
        </p>
      </form>
    </div>



</body>
</html>
