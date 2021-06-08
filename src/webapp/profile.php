<?php
session_start();
if(!isset($_SESSION['userid'])) {
    //die('Please <a href="login.php">login</a> first!');
    echo "Please <a href='login.php'>login</a> first!";
    echo "<br/><br/>";
    echo "<img src='img/loading.gif' />";
    header( "refresh:8;url=login.php" );
    die('');
}
require 'api/db_config.php';

function password_verify_md5($pwd, $hash) {
    if (md5($pwd) == $hash) {
        return true;
    } else {
        return false;
    }
}

//Abfrage der Nutzer ID vom Login
$userid = $_SESSION['userid'];
 
//echo "Hallo User: ".$userid;

if(isset($_GET['saved'])) {
    $email = $_POST['email'];
    $name = $_POST['name'];
    //echo "Email: ".$email."<br/>";
    //echo "Name: ".$name."<br/>";
    $editMessage = "";

    // get info from DB to current user 
    $sql = "SELECT * FROM `user` WHERE `id` = ".$userid;
    $result = $mysqli->query($sql);
    $num_rows = mysqli_num_rows($result);
    //echo "Found: ".$num_rows."<br/>";
    $row = $result->fetch_assoc();

    if ($email != $row["email"]) {
        $sql = "UPDATE user SET `email` = '".$email."' WHERE `id` = ".$userid;
        $result = $mysqli->query($sql);
        $editMessage .= "<font color=blue>Email changed successfully.</font><br/>";
    }
    if ($name != $row["name"]) {
        $sql = "UPDATE user SET `name` = '".$name."' WHERE `id` = ".$userid;
        $result = $mysqli->query($sql);
        $editMessage .= "<font color=blue>Name changed successfully.</font><br/>";
    }
}

// get user information from database
$sql = "SELECT * FROM `user` WHERE `id` = ".$userid;

$result = $mysqli->query($sql);

$row = $result->fetch_assoc();

$userName = $row["name"];

if(isset($_GET['changed'])) {
    $passwort = $_POST['passwort'];
    $passwortNew = $_POST['passwortNew'];
    $passwortNew2 = $_POST['passwortNew2'];
    //echo "Old password: ".$passwort."<br/>";
    //echo "New password: ".$passwortNew."<br/>";
    //echo "New password again: ".$passwortNew2."<br/>";
    $changeMessage = "";
    $continue = true;

    if (!password_verify_md5($passwort, $row["password"])) {
        $changeMessage .= "<font color=red>Wrong old password entered!</font><br/>";
        $continue = false;
    }

    if ($passwortNew != $passwortNew2) {
        $changeMessage .= "<font color=red>New entered passwords are different!</font><br/>";
        $continue = false;
    } else if (strlen($passwortNew) < 6) {
        $changeMessage .= "<font color=red>New entered password is too short! Please use a password with at least 6 characters.</font><br/>";
        $continue = false;
    }

    if ($continue) {
        // Write new password to DB
        $passwort_hash = md5($passwortNew);
        $sql = "UPDATE user SET `password` = '".$passwort_hash."' WHERE `id` = ".$userid;
        $result = $mysqli->query($sql);
        $changeMessage .= "<font color=blue>Password changed successfully.</font><br/>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>CORDET FW Editor</title>
	<!-- https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css -->
	<link rel="stylesheet" type="text/css" href="ext/bootstrap/3.3.7/css/bootstrap.min.css">
	<!-- https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.js -->
	<script type="text/javascript" src="ext/ajax/libs/jquery/3.1.0/jquery.js"></script>
	<!-- https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/js/bootstrap.min.js -->
	<script type="text/javascript" src="ext/ajax/libs/twitter-bootstrap/4.0.0-alpha/js/bootstrap.min.js"></script>
	<!-- https://cdnjs.cloudflare.com/ajax/libs/twbs-pagination/1.3.1/jquery.twbsPagination.min.js -->
	<script type="text/javascript" src="ext/ajax/libs/twbs-pagination/1.3.1/jquery.twbsPagination.min.js"></script>
	<!-- https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js -->
	<script src="ext/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js"></script>
	<!-- //cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js -->
	<script type="text/javascript" src="ext/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
	<!-- //cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css -->
	<link href="ext/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="int/layout.css">
	<script type="text/javascript">
		var url = "http://localhost/dbeditor/";
	</script>
	<script type="text/javascript" src="js/item-ajax.js"></script>
<!--
<style type="text/css">
 .topcorner{
   position:absolute;
    top:10px;
    right: 10px;
  }
  
   .topcorner_left{
   position:absolute;
    top:10px;
    left: 10px;
  }

	.logo  {
  background-color: black;
  color: white;
  margin: 20px;
  padding: 20px;
	}
	
	.offset {
	background-color: blue;
    width: 100%;
    height: 500px;
}
	</style>
-->
</head>
<body>

	<div class="container">

		<div class="row">
		    <div class="col-lg-12 margin-tb">
		        <div class="pull-left">
		            <h2>CORDET FW Editor</h2>
		        </div>
				
				<!--
		        <div class="pull-right">
				<button type="button" class="btn btn-success" data-toggle="modal" data-target="#create-item">
					  Create Item
				</button>
		        </div>
				-->
		    </div>
		</div>

		<div>
<h3>Edit Profile</h3>

<?php 
if(isset($editMessage)) {
    echo $editMessage;
}
?>

<br/>

<form action="?saved=1" method="post">
E-Mail:<br>
<input type="email" size="40" maxlength="250" name="email" value="<?php echo $row["email"]; ?>"><br><br>
 
Name:<br>
<input type="name" size="40" maxlength="250" name="name" value="<?php echo $row["name"]; ?>"><br><br>

<input type="submit" value="Save">
</form>

<br/>

		</div>

		<div>
<h3>Change Password</h3>

<?php 
if(isset($changeMessage)) {
    echo $changeMessage;
}
?>

<br/>

<form action="?changed=1" method="post">
Old Password:<br>
<input type="password" size="40"  maxlength="250" name="passwort"><br>

New Password:<br>
<input type="password" size="40"  maxlength="250" name="passwortNew"><br>
 
New Password again:<br>
<input type="password" size="40" maxlength="250" name="passwortNew2"><br><br>
 
<input type="submit" value="Change">
</form>

<br/>

		</div>

		<div class="topcorner_left">
			<img src="img/grp__NM__menu_img__NM__logo.png" alt="Logo P&P Software" width="150" style="background-color: darkblue; padding: 5px;"><br/>
			<img src="img/uni_logo_220.jpg" alt="Logo University of Vienna" width="150" style="padding: 5px;"><br/>
			<img src="img/csm_uni_logo_schwarz_0ca81bfdea.jpg" alt="Logo Institute for Astrophysics" width="150" style="padding: 5px;">
			<br/><br/>
			You are logged in as: <br/>
			<?php 
			    echo "<b>".$userName."</b><br/>";
			?>
			<br/><br/>
			<a href="index.php">Back</a>
		<div/>
		
	</div>

</body>

</html>