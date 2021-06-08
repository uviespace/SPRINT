<?php 
session_start();
require 'api/db_config.php';

//$pdo = new PDO('mysql:host=localhost;dbname=test', 'root', '');
function password_verify_md5($pwd, $hash) {
    if (md5($pwd) == $hash) {
        return true;
    } else {
        return false;
    }
}

if(isset($_GET['login'])) {
    $email = $_POST['email'];
    $passwort = $_POST['passwort'];
    
    /*$statement = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $result = $statement->execute(array('email' => $email));
    $user = $statement->fetch();*/
    $sql = "SELECT * FROM `user` WHERE `email` = '".$email."'";
    $result = $mysqli->query($sql);
    $num_rows = mysqli_num_rows($result);
    //echo "Found: ".$num_rows."<br/>";
    $row = $result->fetch_assoc();
    //echo "Password verification: ".password_verify_md5($passwort, $row['password'])."<br/>";

    //Überprüfung des Passworts
    if ($num_rows == 1 && password_verify_md5($passwort, $row['password'])) {
        $_SESSION['userid'] = $row['id'];
        // write new date of last signed in into DB
        $date = date('Y-m-d G:i:s');
        $sql = "UPDATE user SET `lastSignedIn` = '".$date."' WHERE `id` = ".$row['id'];
        $result = $mysqli->query($sql);
        //echo "Login successful. Continue to <a href='index.php'>internal area</a>";
        header( "refresh:0;url=index.php" );
        //die('Login successful. Continue to <a href="index.php">internal area</a>');
    } else {
        $errorMessage = "<b><font color=red>The email or password was invalid</font></b><br>";
    }
    
}
?>
<!DOCTYPE html> 
<html> 
<head>
  <title>Login</title>
	<!-- https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css -->
	<link rel="stylesheet" type="text/css" href="ext/bootstrap/3.3.7/css/bootstrap.min.css">
	<!-- //cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css -->
	<link href="ext/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="int/layout.css">
</head> 
<body>

<div class="container">

		<div class="row">
		    <div class="col-lg-12 margin-tb">
		        <div class="pull-left">
		            <h2>CORDET FW Editor</h2>
		        </div>
		    </div>
		</div>



<div>
<h3>Login</h3>

<?php 
if(isset($errorMessage)) {
    echo $errorMessage;
}
?>

<br/>

<form action="?login=1" method="post">
E-Mail:<br>
<input type="email" size="40" maxlength="250" name="email"><br><br>
 
Password:<br>
<input type="password" size="40"  maxlength="250" name="passwort"><br>

<br>

<input type="submit" value="Login">
</form> 

</div>

		<div class="topcorner_left">
			<img src="img/grp__NM__menu_img__NM__logo.png" alt="Logo P&P Software" width="150" style="background-color: darkblue; padding: 5px;"><br/>
			<img src="img/uni_logo_220.jpg" alt="Logo University of Vienna" width="150" style="padding: 5px;"><br/>
			<img src="img/csm_uni_logo_schwarz_0ca81bfdea.jpg" alt="Logo Institute for Astrophysics" width="150" style="padding: 5px;">
			<br/><br/>
			<a href="register_w.php">Register new user</a>
		<div/>


</div>

</body>
</html>