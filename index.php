<?php

  $path = $_SERVER["DOCUMENT_ROOT"];
  include('server.php');
  //session_start();
  $ordermsg="";
  if (!isset($_SESSION['std_id'])) {
  	$_SESSION['msg'] = "You must log in first";
  	header("location: login.php");
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
	<title>Khaba Naki??</title>
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
    	<p> <?php echo("$ordermsg"); ?></p>
    	<p> <a href="index.php?logout='1'" style="color: red;">logout</a> </p>
    <?php endif ?>
</div>
    <?php
        if(isset($_POST['orderbtn'])){
            //echo("$_POST[date]");
            $lastid;
            $cost=0;
            foreach (array_combine($_POST['OrderMenuNo'], $_POST['quantity']) as $menuno => $quantity) {
                $price_q = "SELECT Price FROM menu WHERE Set_Menu_No='$menuno'";
                $result = mysqli_query($db, $price_q);
                $pr = mysqli_fetch_assoc($result);
                $cost += $pr['Price']*$quantity;
                //echo("$menuno $quantity ");
            }
            $balance_q="SELECT Balance FROM student_info WHERE Id='$_SESSION[std_id]'";
            if($res=mysqli_query($db, $balance_q)){
                $balance=mysqli_fetch_assoc($res);
                if($cost<=$balance['Balance']){
                    $addorder_query = "INSERT INTO `order` (Date, Student_Id, status) VALUES ('$_POST[date]', '$_SESSION[std_id]', 1)";
                    if(mysqli_query($db, $addorder_query)){
                        $lastid=mysqli_insert_id($db);
                        echo("
                            <div class=\"confirmdel\">
                            <p>Order ADDED!!!</p>
                            <p>Total Cost: $cost<p>
                            <h4>Order ID: $lastid</h4>
                            <p>Preserve Order ID for later Query</p>
                            <a href=\"/profile.php\">View Orders</a>
                            </div>
                            ");
                        foreach (array_combine($_POST['OrderMenuNo'], $_POST['quantity']) as $menuno => $quantity) {
                        $addorderdetail_query = "INSERT INTO `junk_order_menu` (Order_No, Set_Menu_No, Set_Menu_Counter) VALUES ('$lastid', '$menuno', '$quantity')";
                            //echo("$menuno $quantity ");
                            if($quantity>0) if(!mysqli_query($db, $addorderdetail_query)){
                                echo(mysqli_error($db));
                            }
                        }
                    }

                }
                else{
                    echo("<div  class=\"confirmdel\"><p>Insufficient balance. You have $balance[Balance] taka.<br>This order needs $cost taka.</p></div>");
                }
            }
            else echo(mysqli_error($db));


        }
        else if(isset($_POST['date'])){
            $currentDate = date("Y-m-d");
            $maxDate = date('Y-m-d', strtotime($currentDate. ' + 10 days'));
            echo("
            <div class=\"div1\">
            <form method=\"post\" action=\"\">
                <p>Select a Date to ORDER: </p>
                <input type=\"date\" name=\"deliveryDate\" min=\"$currentDate\" max=\"$maxDate\">
                <input type=\"submit\" name=\"date\" value=\"Show Available Set Menus on this day\">
                <br>
                <br>
                <hr>
                <br>
            </form>
            ");
            $dd = preg_split ("/\-/", $_POST['deliveryDate']);
            $d=mktime(1, 0, 0, $dd[1], $dd[2], $dd[0]);
            $deliveryDate = date("Y-m-d", $d);
            $deliveryDay = date("l", $d);
            //echo($deliveryDate);
            //echo($deliveryDay);
            //INNER JOIN menu_items ON Menu.set_menu_no=menu_items.set_menu_no
            $day_query = "SELECT * FROM menu_day WHERE Day='$deliveryDay'";
            $result_day = mysqli_query($db, $day_query);
            echo("<form method='post' action=''>
                <input type='hidden' name='date' value='$deliveryDate'>
                <input type='hidden' name='day' value='$deliveryDay'>
                    ");
            echo("<p>Showing Menus Available on $deliveryDay, $deliveryDate");
            while($menuno=mysqli_fetch_array($result_day, MYSQLI_ASSOC)){
                $user_check_query = "SELECT * FROM menu WHERE Set_Menu_No='$menuno[Set_Menu_No]'";
                $result = mysqli_query($db, $user_check_query);
                //var_dump($result);
                //$menus = mysqli_fetch_assoc($result);
                while($menus=mysqli_fetch_array($result, MYSQLI_ASSOC)){
                    $check2 = "SELECT * FROM menu_items WHERE Set_Menu_No=".$menus['Set_Menu_No'];
                    $result2 = mysqli_query($db, $check2);

                    echo("<div class=\"SetMenuBox\">");
                    echo"<br> $menus[Set_Menu_No]";
                    echo"<br>Items: ";
                    while($items=mysqli_fetch_array($result2, MYSQLI_ASSOC)){
                        echo("<br>".$items['Items']);
                    }
                    echo("<br>Price: ".$menus['Price']."<br>");

                    echo("</div>");
                    echo(
                        "
                        <div class=\"upmodif\">
                        <input type='hidden' name='OrderMenuNo[]' value='$menus[Set_Menu_No]'>
                        <input type=\"number\" min=\"0\" id=\"quantity\" name='quantity[]' value=\"0\">

                        </div>
                        "
                    );
                }
            }
            echo("<input type='hidden' name='std_id' value='$_SESSION[std_id]'>
            <input class='orderbtn' type='submit' name='orderbtn' value='ORDER'>
            </form>

            </div>");
        }
        else{
            //showing all Set Menus
            $currentDate = date("Y-m-d");
            $maxDate = date('Y-m-d', strtotime($currentDate. ' + 10 days'));
            echo("<div class=\"div1\">\n");
            echo("
            <form method=\"post\" action=\"\">
                <p>Select a Date to ORDER: </p>
                <input type=\"date\" name=\"deliveryDate\" min=\"$currentDate\" max=\"$maxDate\">
                <input type=\"submit\" name=\"date\" value=\"Show Available Set Menus on this day\">
                <br>
                <br>
                <hr>
                <br>
            </form>
            ");
            echo("Showing All Available Set Menu: <br>\n");
            $user_check_query = "SELECT * FROM menu ";
            $result = mysqli_query($db, $user_check_query);
            while($menus=mysqli_fetch_array($result, MYSQLI_ASSOC)){
                  $check2 = "SELECT * FROM menu_items WHERE Set_Menu_No=".$menus['Set_Menu_No'];
                  $result2 = mysqli_query($db, $check2);
                  $check3 = "SELECT * FROM menu_day WHERE Set_Menu_No=".$menus['Set_Menu_No'];
                  $result3 = mysqli_query($db, $check3);
                  echo("<div class=\"SetMenuBox\" style=\"width:100%\">");
                  echo"$menus[Set_Menu_No]";
                  echo"<br>Items: ";
                  while($items=mysqli_fetch_array($result2, MYSQLI_ASSOC)){
                      echo("<br>".$items['Items']);
                  }
                  echo"<br>Available Days: ";
                  while($days=mysqli_fetch_array($result3, MYSQLI_ASSOC)){
                      echo("<br>".$days['Day']);
                  }
                  echo("<br>Price: ".$menus['Price']."<br></div>\n");

            }
            echo("</div>");
        }
    ?>

</body>
</html>
