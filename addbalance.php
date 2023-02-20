<?php
    include('server.php');
  //session_start();
  $insertInfo = "";
  if (!isset($_SESSION['std_id'])) {
  	$_SESSION['msg'] = "You must log in first";
  	header('location: login.php');
  }
  else if($_SESSION['std_id']=="17301043"){
      $std_id = $_SESSION['std_id'];
      $user_check_query = "SELECT * FROM student_info WHERE Id='$std_id' LIMIT 1";
      $result = mysqli_query($db, $user_check_query);
      $user = mysqli_fetch_assoc($result);
      if(!isset($_POST['studentsrc'])&&!isset($_POST['submitamt'])){
          $insertInfo="
          <div class=\"content\">
        <form method=\"post\" action=\"\">
        <p>Enter Student ID:</p>
        <input type=\"text\" name=\"id\">
        <input type=\"submit\" name=\"studentsrc\" value=\"Search Student\">
        </form>
        </div>

          ";
      }
      else if(isset($_POST['studentsrc'])&&!isset($_POST['submitamt'])){
          $srcstd= "SELECT * FROM student_info WHERE (Id='$_POST[id]') LIMIT 1";
          $result = mysqli_query($db, $srcstd);
          if(mysqli_num_rows($result) >0){
            $std=mysqli_fetch_assoc($result);
              $insertInfo="
                  <div class=\"content\">
                <p>Name: $std[Name]</p>
                <form method=\"post\" action=\"\">
                <p>Enter Recharge Amount:</p>
                <input type=\"hidden\" name=\"id2\" value=\"$_POST[id]\">
                <input type=\"text\" name=\"amount\">
                <input type=\"submit\" name=\"submitamt\" value=\"Recharge Amount\">
                </form>
                </div>
              ";
          }
          else{
              $insertInfo="
                  <div class=\"content\">
                <p>ID doesn't exist. Retry again. </p>
                <form method=\"post\" action=\"\">
                <p>Enter Student ID:</p>
                <input type=\"text\" name=\"id\">
                <input type=\"submit\" name=\"studentsrc\" value=\"Search Student\">
                </form>
                </div>
              ";
          }
      }
      else if(isset($_POST['submitamt'])){
          $insert_q = "UPDATE student_info SET Balance= Balance+$_POST[amount] WHERE Id='$_POST[id2]'";

          if(mysqli_query($db, $insert_q)){
              $insertInfo="
                  <div class=\"content\">
                <p>$_POST[amount] taka has been added to $_POST[id2] </p>
                </div>
              ";
          }
          else{
              echo"faldfja";
              mysqli_error($db);
          }
      }
  }
  else{
      header('Location: index.php');
  }
  if (isset($_GET['logout'])) {
  	session_destroy();
  	unset($_SESSION['std_id']);
  	header("location: login.php");
  }
?>
<!DOCTYPE html>
<html>
<head>
	<title>Khaba Naki Vaiya??</title>
	<link rel="stylesheet" type="text/css" href="style3.css">
</head>
<body background="backimg.jpg">



<div class="header">
	<h2>Khaba Naki?</h2>
</div>
<div class="content">
  	<!-- notification message -->
  	<?php if (isset($_SESSION['success'])) : ?>
      <div class="error success" >
      	<h3>
          <?php
          	echo $_SESSION['success'];
          	unset($_SESSION['success']);
          ?>
      	</h3>
      </div>
  	<?php endif ?>

    <!-- logged in user information -->
    <?php  if (isset($_SESSION['std_id'])) : ?>
        <img src="<?php
        if($user['photo']!=""){ echo $user['photo'];}
            else { echo"profilepic/unavailable.jpg";}
         ?>" height="120">
    	<p>Welcome <strong><?php echo $user['Name']; ?></strong></p>
    	<p> <a href="index.php?logout='1'" style="color: red;">logout</a> </p>
    <?php endif ?>
    <!--create order and Order Management-->
    <form action="/adminorders.php">
        <input type="text" value="yyyy/mm/dd">
        <input type="submit" value="View Orders">
    </form>

</div>
    <?php
         echo("$insertInfo");
    ?>

</body>
</html>
