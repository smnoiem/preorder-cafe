<?php
    include('server.php');
  //session_start();
  $askconfirmation = "";
  $menudeleted = "";
  if (!isset($_SESSION['std_id'])) {
  	$_SESSION['msg'] = "You must log in first";
  	header('location: login.php');
  }
  else if($_SESSION['std_id']=="17301043"){
      $std_id = $_SESSION['std_id'];
      $user_check_query = "SELECT * FROM student_info WHERE Id='$std_id' LIMIT 1";
      $result = mysqli_query($db, $user_check_query);
      $user = mysqli_fetch_assoc($result);
      //delete/modify
      if(isset($_POST['delete'])||isset($_POST['canceldel'])||isset($_POST['confirmdel'])||isset($_POST['deletedok'])){
          //echo"$_POST[SetMenuNo]";
          if(isset($_POST['canceldel'])) {
              //echo"Cancelled!";
              $_POST = array();
              header('location: admin.php');
          }
          else if(isset($_POST['deletedok'])){
              $_POST = array();
              header('location: admin.php');
          }
          else if(isset($_POST['confirmdel'])){
                //echo"CONFIRMBALLLLL$_POST[SetMenuNo]";
                $dayDelQuery="DELETE FROM `menu_day` WHERE Set_Menu_No=$_POST[SetMenuNo]";
                if(mysqli_query($db, $dayDelQuery)){
                    $menuItemsQuery="DELETE FROM `menu_items` WHERE Set_Menu_No=$_POST[SetMenuNo]";
                    if(mysqli_query($db, $menuItemsQuery)){
                        //echo("deleted items of menu no: $_POST[SetMenuNo]");
                        $menuDelQuery="DELETE FROM `menu` WHERE Set_Menu_No=$_POST[SetMenuNo]";
                        if(mysqli_query($db, $menuDelQuery)){
                            $menudeleted =
                                 "<div class='confirmdel'>
                                    <p>Set Menu No: $_POST[SetMenuNo] Deleted!</p>
                                    <form class='cancel' method='post' action=''>
                                        <input type='submit' name='deletedok' value='OK'>
                                    </form>
                                  </div>";
                        }
                    }
                }
          }
          else {
              $ordersrc_q = "SELECT Set_Menu_No FROM junk_order_menu WHERE Set_Menu_No=$_POST[SetMenuNo] LIMIT 1";
                if(!mysqli_query($db, $ordersrc_q)){
                  $askconfirmation =
                  "
                    <div class='confirmdel'>
                        <p>Are You Sure to Delete Items No: $_POST[SetMenuNo] ?<p>
                        <form class='confirm' method='post' action=''>
                            <input type='hidden' name='SetMenuNo' value='$_POST[SetMenuNo]'>
                            <input type='submit' name='confirmdel' value='CONFIRM'>
                        </form>
                        <form class='cancel' method='post' action=''>
                            <input type='submit' name='canceldel' value='CANCEL'>
                        </form>
                    </div>
                  ";
              }
              else{
                  //exists in order
                  $menudeleted =
                     "<div class='confirmdel'>
                        <p>Set Menu No: $_POST[SetMenuNo] exists in ORDER. Can't be Deleted!</p>
                        <form class='cancel' method='post' action=''>
                            <input type='submit' name='canceldel' value='OK'>
                        </form>
                      </div>";
              }
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
<?php echo("$askconfirmation\n$menudeleted"); ?>



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
    <a href="/createmenu.php">Create New Set Menu</a>
    <a href="/addbalance.php">Add Balance</a>
    <a href="/deliver.php">Deliver</a>
    <form method="post" action="adminorders.php">
        <input type="date" name="date">
        <input type="submit" name="orderdate" value="View Orders">
    </form>

</div>
    <?php
        if(!isset($_POST['modify'])&&!isset($_POST['confirmmodify'])){
            //showing all Set Menus
            $user_check_query = "SELECT * FROM menu ";
            $result = mysqli_query($db, $user_check_query);
            while($menus=mysqli_fetch_array($result, MYSQLI_ASSOC)){
                  $check2 = "SELECT * FROM menu_items WHERE Set_Menu_No=".$menus['Set_Menu_No'];
                  $result2 = mysqli_query($db, $check2);
                  $check3 = "SELECT * FROM menu_day WHERE Set_Menu_No=".$menus['Set_Menu_No'];
                  $result3 = mysqli_query($db, $check3);

                echo("<div class=\"div1\">\n<div class=\"SetMenuBox\">");
                  echo"$menus[Set_Menu_No]";
                  echo"<br>Items: ";
                  while($items=mysqli_fetch_array($result2, MYSQLI_ASSOC)){
                      echo("<br>".$items['Items']);
                  }
                  echo"<br>Available Days: ";
                  while($days=mysqli_fetch_array($result3, MYSQLI_ASSOC)){
                      echo("<br>".$days['Day']);
                  }
                  echo("<br>Price: ".$menus['Price']."<br>");

                echo("</div>");
                echo(
                    "
                        <div class=\"upmodif\">
                            <form method='post' action=''>
                                <input type='hidden' name='SetMenuNo' value='$menus[Set_Menu_No]'>
                                <input type='submit' name='delete' value='DELETE'>
                            </form>
                            <form method='post' action=''>
                                <input type='hidden' name='SetMenuNo' value='$menus[Set_Menu_No]'>
                                <input type='hidden' name='price' value='$menus[Price]'>
                                <input type='submit' name='modify' value='MODIFY'>
                            </form>
                        </div>
                        </div>
                    "
                );
            }
        }
        else if(!isset($_POST['confirmmodify'])) {
            //modify
            //echo"modify set menu no $_POST[SetMenuNo]";
            //show it in a form for editing

            $check2 = "SELECT * FROM menu_items WHERE Set_Menu_No=".$_POST['SetMenuNo'];
            $result2 = mysqli_query($db, $check2);
            $check3 = "SELECT * FROM menu_day WHERE Set_Menu_No=".$_POST['SetMenuNo'];
            $result3 = mysqli_query($db, $check3);

            echo("<div class=\"div1\">\n<div class=\"SetMenuBox\" style=\"width:100%\">\n");
            echo"<p> Set Menu No: $_POST[SetMenuNo]</p>\n";
            echo("<form method=\"post\" action=\"\">\n");
            echo("<input type=\"hidden\" name=\"SetMenuNo\" value=\"$_POST[SetMenuNo]\">\n");
            echo"<p>Items:</p>\n";
            while($items=mysqli_fetch_array($result2, MYSQLI_ASSOC)){
                echo("<input type=\"hidden\" name=\"olditems[]\" value=\"$items[Items]\">\n");
                echo("<input type=\"text\" name=\"cngitems[]\" value=\"$items[Items]\"><br>\n");
            }
            echo("<p>Insert a New Item:</p>\n");
            echo("<input type=\"hidden\" name=\"olditems[]\" value=\"\">\n");
            echo("<input type=\"text\" name=\"cngitems[]\"><br>\n");
            echo"<p>Available Days:</p>\n";
            while($days=mysqli_fetch_array($result3, MYSQLI_ASSOC)){
                echo("<input type=\"hidden\" name=\"oldday[]\" value=\"$days[Day]\">\n");
                echo("<input type=\"text\" name=\"cngday[]\" value=\"$days[Day]\"><br>\n");
            }
            echo("<p>Insert New Day:</p>\n");
            echo("<input type=\"hidden\" name=\"oldday[]\" value=\"\">\n");
            echo("<input type=\"text\" name=\"cngday[]\"><br>\n");

            echo("<input type=\"hidden\" name=\"oldprice\" value=\"$_POST[price]\">\n");
            echo("<p>Edit Price:</p>\n<input type=\"text\" name=\"cngprice\" value=\"$_POST[price]\">\n");

            echo("<br>\n");
            echo(
                    "
                        <div class=\"upmodif\" style=\"width:37%\">
                            <input type='submit' name='confirmmodify' value='CONFIRM MODIFICATION'>
                        </div>
                        </form>
                        </div>
                        </div>
                    "
                );
        }
        else if(isset($_POST['confirmmodify'])){
            //confirm modify (update query)
            echo"<div class=\"modifnote\" style=\"background-color:white\">";
            //var_dump($_POST);
            //echo($_POST['olditems']['0']);
            foreach (array_combine($_POST['olditems'], $_POST['cngitems']) as $old => $new) {
                //echo"$old $new";
                if($old!=$new){
                    //echo"$old $new $_POST[SetMenuNo]";
                    $update_query = "UPDATE menu_items SET Items='$new' WHERE (Items='$old' and Set_Menu_No='$_POST[SetMenuNo]')";
                    $delete_query = "DELETE FROM menu_items WHERE (Items='$old' and Set_Menu_No='$_POST[SetMenuNo]')";
                    $insert_query = "INSERT INTO menu_items (Set_Menu_No, Items) VALUES ('$_POST[SetMenuNo]', '$new')";
                    if($new==""&&$old!=""){
                        if(mysqli_query($db, $delete_query)){
                            echo"<p>Item $old DELETED!!!!</p>\n";
                        }
                    }
                    else if($new!=""&&$old==""){
                        if(mysqli_query($db, $insert_query)){
                            echo"<p>Item $new INSERTED!!!!</p>\n";
                        }
                    }
                    else if(mysqli_query($db, $update_query)){
                        echo"<p>Item $old UPDATED to $new!!!!</p>\n";
                    }
                }
            }
            foreach (array_combine($_POST['oldday'], $_POST['cngday']) as $old => $new) {
                //echo"$old $new";
                if($old!=$new){
                    //echo"$old $new $_POST[SetMenuNo]";
                    $update_query = "UPDATE menu_day SET Day='$new' WHERE (Day='$old' and Set_Menu_No='$_POST[SetMenuNo]')";
                    $delete_query = "DELETE FROM menu_day WHERE (Day='$old' and Set_Menu_No='$_POST[SetMenuNo]')";
                    $insert_query = "INSERT INTO menu_day (Set_Menu_No, Day) VALUES ('$_POST[SetMenuNo]', '$new')";
                    if($new==""&&$old!=""){
                        if(mysqli_query($db, $delete_query)){
                            echo"<p>Day $old DELETED!!!!</p>\n";
                        }
                    }
                    if($new!=""&&$old==""){
                        if(mysqli_query($db, $insert_query)){
                            echo"<p>Day $new INSERTED!!!!</p>\n";
                        }
                    }
                    else if(mysqli_query($db, $update_query)){
                        echo"<p>Day $old UPDATED to $new!!!!</p>\n";
                    }
                }
            }
            if($_POST['oldprice']!=$_POST['cngprice']&&$_POST['cngprice']<=2147483647){
                $price_update_query = "UPDATE menu SET Price='$_POST[cngprice]' WHERE (Price='$_POST[oldprice]' and Set_Menu_No='$_POST[SetMenuNo]')";
                if(mysqli_query($db, $price_update_query)){
                    echo"<p>Price Updated to $_POST[cngprice] from $_POST[oldprice]!!!</p>\n";
                }
            }
            else if($_POST['cngprice']>2147483647){
                    echo"<p>Price didn't change. Change into valid price</p>\n";
            }
            echo"</div>";
        }
    ?>

</body>
</html>
