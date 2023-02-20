<?php
    include('server.php');
  //session_start();
  $ordermsg="";
  if (!isset($_SESSION['std_id'])) {
  	$_SESSION['msg'] = "You must log in first";
  	header('location: login.php');
  }
  else if($_SESSION['std_id']=="17301043"){
      header('Location: admin.php');
  }
  else{
      $std_id = $_SESSION['std_id'];
      $user_check_query = "SELECT * FROM student_info WHERE Id='$std_id' LIMIT 1";
      $result = mysqli_query($db, $user_check_query);
      $user = mysqli_fetch_assoc($result);
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
	<title>Profile</title>
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
    	<p>Welcome <strong><?php echo("<a href=\"/profile.php\">$user[Name]</a>"); ?></strong></p>
    	<p>Balance: <?php echo"$user[Balance]" ?></p>
    	<p> <a href="index.php?logout='1'" style="color: red;">logout</a> </p>
    <?php endif ?>
</div>
    <?php
        echo("<div class=\"content\">");
        echo("<p>My Orders</p>");
        $ordersrc = "SELECT * FROM `order` WHERE (Student_Id='$_SESSION[std_id]' and status=1) ORDER BY Date ASC";
        $res = mysqli_query($db, $ordersrc);
        while($orders=mysqli_fetch_array($res, MYSQLI_ASSOC)){
            if(($orders['Date']==date("Y-m-d")&&date("H")<16)||($orders['Date']>date("Y-m-d"))){
                echo("<p>Order No: $orders[Order_No]</p>");
                echo("<p>Date: $orders[Date]</p>");
                $ordermenu = "SELECT * FROM `junk_order_menu` WHERE (Set_Menu_Counter>0 and Order_No='$orders[Order_No]')";
                $res2 = mysqli_query($db, $ordermenu);
                while($menus=mysqli_fetch_array($res2, MYSQLI_ASSOC)){
                    echo("<p>Menu: $menus[Set_Menu_No] Quantity: $menus[Set_Menu_Counter]</p>");
                }
            }
            echo("<br>");
        }
        echo("</div>");
    ?>

</body>
</html>
