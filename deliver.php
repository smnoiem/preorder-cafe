<?php
    include('server.php');
  //session_start();
  $insertInfo = "";
  echo("
    <div class=\"header\">
        <h2>Khaba Naki?</h2>
    </div>
    ");
  if (!isset($_SESSION['std_id'])) {
  	$_SESSION['msg'] = "You must log in first";
  	header('location: login.php');
  }
  else if($_SESSION['std_id']=="17301043"){
      $std_id = $_SESSION['std_id'];
      $user_check_query = "SELECT * FROM student_info WHERE Id='$std_id' LIMIT 1";
      $result = mysqli_query($db, $user_check_query);
      $user = mysqli_fetch_assoc($result);
      if(isset($_POST['done'])){
          $ord=$_POST['ordno'];
          $menuno=$_POST['setmenu'];
          $quan=$_POST['quantity'];
          $q = "UPDATE junk_order_menu SET Set_Menu_Counter= Set_Menu_Counter-$quan WHERE (Order_No='$ord' and Set_Menu_No='$menuno') ";
          if(mysqli_query($db, $q)){
          }
          $q="SELECT * FROM junk_order_menu WHERE (Order_No='$ord')";
          $r = mysqli_query($db, $q);
          $sum=0;
          while($s=mysqli_fetch_array($r, MYSQLI_ASSOC)){
              $sum += $s['Set_Menu_Counter'];
          }
          if($sum<=0){
              $q="UPDATE `order` SET status=0 WHERE (Order_No='$ord')";
              if(!mysqli_query($db, $q)) echo("not");
          }

      }


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
          $date = date('Y-m-d');
          $srcstd= "SELECT * FROM `order` WHERE (Student_Id='$_POST[id]' and Date='$date')";
          $result = mysqli_query($db, $srcstd);
            echo("<div class=\"content\">");
            while($ord=mysqli_fetch_assoc($result)){
                echo("<p>Order No: $ord[Order_No]</p><br>");
                    $query2="SELECT * FROM `junk_order_menu` WHERE (Order_No='$ord[Order_No]')";
                    $res2 = mysqli_query($db, $query2);
                    echo("<div class=\"content\">");
                    while($menuNo=mysqli_fetch_assoc($res2)){
                          echo("
                                  <p>Menu No: $menuNo[Set_Menu_No] Quantity: $menuNo[Set_Menu_Counter]</p>
                            ");
                  }
                  echo("
                        <form method=\"post\" action=\"\">
                            <p>Enter Order No:</p>
                            <input type='text' name='ordno'>
                                <p>Enter Set Menu No:</p>
                            <input type='text' name='setmenu'>
                                <p>Enter Quantity:</p>
                            <input type='text' name='quantity'>
                            <input type='submit' name='done' value='Deliver'>
                            </form>
                        ");
                  echo("</div>");
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
<?php
echo("$insertInfo");

 ?>



</body>
</html>
