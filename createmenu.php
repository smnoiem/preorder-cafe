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
      //find Empty Slot
      if(!isset($_POST['addmenu'])){
          echo"not";
            $srcMenuQuery="SELECT * FROM `menu`";
            $resultMenu = mysqli_query($db, $srcMenuQuery);
            $emptyNo=0;
            $flag=1;
            while($menu=mysqli_fetch_array($resultMenu, MYSQLI_ASSOC)){
                $emptyNo++;
                if($menu['Set_Menu_No']!=$emptyNo){
                    $flag = 0;
                    break;
                }
            }
            if($flag) $emptyNo++;
            //echo"$emptyNo\n";
            //insert new row with Set_Menu_No $emptyNo
            //insertInfo contains the html forms for new Set Menu
            $insertInfo = "
                <div class=\"div1\">
                        <div class=\"SetMenuBox\" style=\"width:100%\">
                        <p>Create Set Menu No: $emptyNo</p>
                        <form method=\"post\" action=\"\">
                        <input type=\"hidden\" name=\"SetMenuNo\" value=\"$emptyNo\"><br>
                        <br>
                        <p>Insert Items:</p>
                        <input type=\"text\" name=\"items[]\"><br>
                        <input type=\"text\" name=\"items[]\"><br>
                        <input type=\"text\" name=\"items[]\"><br>
                        <input type=\"text\" name=\"items[]\"><br>
                        <input type=\"text\" name=\"items[]\"><br>
                        <input type=\"text\" name=\"items[]\"><br>
                        <br>
                        <p>Insert Available Days:</p>
                        <input type=\"hidden\" name=\"days[]\" value=\"dummyday\">
                        <input type=\"checkbox\" name=\"days[]\" value=\"Saturday\"> Saturday<br>
                        <input type=\"checkbox\" name=\"days[]\" value=\"Sunday\"> Sunday<br>
                        <input type=\"checkbox\" name=\"days[]\" value=\"Monday\"> Monday<br>
                        <input type=\"checkbox\" name=\"days[]\" value=\"Tuesday\"> Tuesday<br>
                        <input type=\"checkbox\" name=\"days[]\" value=\"Wednesday\"> Wednesday<br>
                        <input type=\"checkbox\" name=\"days[]\" value=\"Thursday\"> Thursday<br>
                        <input type=\"checkbox\" name=\"days[]\" value=\"Friday\"> Friday<br>
                        <br>
                        <p>Set Price:</p>
                        <input type=\"text\" name=\"price\">
                        <br>
                        <br>

                        <div class=\"upmodif\" style=\"width:37%\">
                            <input type='submit' name='addmenu' value='INSERT'>
                        </div>
                        </form>
                        </div>
                        </div>



            ";

      }
      else if(isset($_POST['addmenu'])){
          //add menu started


            $insertInfo .= "<div class=\"modifnote\" style=\"background-color:white\">";
            //var_dump($_POST);
            //echo($_POST['olditems']['0']);
            $insert_menu_query = "INSERT INTO menu (Set_Menu_No, Price) VALUES ('$_POST[SetMenuNo]', '$_POST[price]')";
            if(mysqli_query($db, $insert_menu_query)){
                        $insertInfo .= "<p>Set Menu No: $_POST[SetMenuNo] CREATED!!!!</p>\n";
                        if($_POST['price']<=0||$_POST['price']>=2147483647){
                             $insertInfo .= "Valid Price Required! Modify Set Menu and Add Valid Price!<br>";
                        }
                        else $insertInfo .= "Price: $_POST[price] ADDED!!!!<br>";
            }
            else{
                $insertInfo .= "<p>Failed inserting Set Menu No: $_POST[SetMenuNo] price: $_POST[price] :(<p>";
            }
            $itemCount=0;
            foreach ($_POST['items'] as $item) {
                $insert_items_query = "INSERT INTO menu_items (Set_Menu_No, Items) VALUES ('$_POST[SetMenuNo]', '$item')";
                //echo"$item";
                if($item!=""){
                    //echo"$item $_POST[SetMenuNo]";
                    if(mysqli_query($db, $insert_items_query)){
                        $insertInfo .= "<p>Item $item ADDED!!!!</p>\n";
                        $itemCount++;
                    }
                }
            }
            if($itemCount<=0) $insertInfo .= "At least One ITEM Required! Modify Set Menu and Add ITEMS!<br>";
            $dayCount=0;
            foreach ($_POST['days'] as $day) {
                //echo"$day";
                $insert_days_query = "INSERT INTO menu_day (Set_Menu_No, Day) VALUES ('$_POST[SetMenuNo]', '$day')";
                if($day!=""&&$day!="dummyday"){
                    //echo"$day $_POST[SetMenuNo]";
                    if(mysqli_query($db, $insert_days_query)){
                        $insertInfo .= "<p>$day ADDED!!!!</p>\n";
                        $dayCount++;
                    }
                }
            }
            if($dayCount<=0) $insertInfo .= "At least One Available Day Required! Modify Set Menu and Add DAYS!<br>";
            $insertInfo .= "
                <div class=\"upmodif\">
                    <form method='post' action='/admin.php'>
                        <input type='hidden' name='SetMenuNo' value='$_POST[SetMenuNo]'>
                        <input type='submit' name='delete' value='DELETE This Set Menu'>
                    </form>
                    <form method='post' action='/admin.php'>
                        <input type='hidden' name='SetMenuNo' value='$_POST[SetMenuNo]'>
                        <input type='hidden' name='price' value='$_POST[price]'>
                        <input type='submit' name='modify' value='MODIFY This Set Menu'>
                    </form>
                </div>
            </div>
            ";



          //add menu ended
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
