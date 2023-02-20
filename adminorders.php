<?php
    include('server.php');
  //session_start();
  $ordermsg="";
  if (!isset($_SESSION['std_id'])) {
  	$_SESSION['msg'] = "You must log in first";
  	header('location: login.php');
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
	<title>Orders</title>
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
        if(isset($_POST['orderdate'])){
        echo("<div class=\"content\">");
        $ordersrc = "SELECT * FROM `order` WHERE (Date='$_POST[date]')";
        $res = mysqli_query($db, $ordersrc);
        $array = array();
        for ($i = 0; $i < 30; $i++)
        {
            array_push($array, 0);
        }
        while($orders=mysqli_fetch_array($res, MYSQLI_ASSOC)){
            $menusrc="SELECT * FROM `junk_order_menu` WHERE (Order_No='$orders[Order_No]')";
            $res2 = mysqli_query($db, $menusrc);
            while($menus=mysqli_fetch_array($res2, MYSQLI_ASSOC)){
                $array[$menus['Set_Menu_No']] += $menus['Set_Menu_Counter'];
                //echo("order no: $orders[Order_No] SetMenu No: $menus[Set_Menu_No] SetMenu counter: $menus[Set_Menu_Counter]<br>");
            }

        }
        //echo(print_r($array));
        for($i=0; $i<30; $i++){
            if($array[$i]!=0) echo("<p>Set Menu No: $i Total: $array[$i]</p><br>");
        }
        echo("</div>");
        }
    ?>

</body>
</html>
