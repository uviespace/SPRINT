<?php 
session_start();
require 'api/db_config.php';
?>
<!DOCTYPE html> 
<html> 
<head>
  <title>Register</title>
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
<h3>Register new user</h3>

<?php
if (false) {
$sql = "SELECT * FROM `user`";

$result = $mysqli->query($sql);

$num_rows = mysqli_num_rows($result);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "id: " . $row["id"]. " - Name: " . $row["name"]. "  - Email: " . $row["email"]. "  - pwd: " . $row["password"]. "  - signed up: " . $row["signedUp"]. "  - last signed in: " . $row["lastSignedIn"]. "<br/>";
    }
} else {
    echo "0 results";
}

echo "<br/><br/>";
echo date('Y-m-d G:i:s');
echo "<br/><br/>";


$passwort = "12345";

//$passwort_hash = password_hash($passwort, PASSWORD_DEFAULT);
$passwort_hash = md5($passwort);

echo "md5()-Hashwert: ".$passwort_hash."<br/>";
echo "<br/><br/>";

/*
md5()-Hashwert von :
d41d8cd98f00b204e9800998ecf8427e

sha1()-Hashwert von :
da39a3ee5e6b4b0d3255bfef95601890afd80709

crypt()-Hashwert von :
$1$d21u7Htf$wWE/QwCj42c0e/LaXpkim1

password_hash()-Hashwert von :
$2y$10$vMB/YK9eZh1uUJX6qxUKdOj5h996.z3zMyn2iOSD9idugxn/3I7Sm
*/


// Update last signed in
$idUser = 1006;
$date = date('Y-m-d G:i:s');

$sql = "UPDATE user SET `lastSignedIn` = '".$date."' WHERE `id` = ".$idUser;

$result = $mysqli->query($sql);

// Update signed up
$idUser = 1006;
$date = "2020-11-03 12:34:16";

$sql = "UPDATE user SET `signedUp` = '".$date."' WHERE `id` = ".$idUser;

$result = $mysqli->query($sql);


// Update/Reset password
$idUser = 1006;
$passwort = "12345";
$passwort_hash = md5($passwort);

$sql = "UPDATE user SET `password` = '".$passwort_hash."' WHERE `id` = ".$idUser;

$result = $mysqli->query($sql);
}

?>

<?php
$showFormular = true; //Variable ob das Registrierungsformular anezeigt werden soll

if(isset($_GET['register'])) {
    $error = false;
    $email = $_POST['email'];
    $name = $_POST['name'];
    $passwort = $_POST['passwort'];
    $passwort2 = $_POST['passwort2'];
    echo $name." (".$email.") ...<br/>";

    // check email
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '<p style="color:red">Please enter a valid email address</font><br/>';
        $error = true;
    }

    // check password
    if(strlen($passwort) == 0) {
        echo '<p style="color:red">Please enter a password</font><br/>';
        $error = true;
    }
    if($passwort != $passwort2) {
        echo '<p style="color:red">The passwords must match</font><br/>';
        $error = true;
    }

    // Check that the email address has not yet been registered
    if(!$error) { 
        /*$statement = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $result = $statement->execute(array('email' => $email));
        $user = $statement->fetch();*/
        $sql = "SELECT * FROM `user` WHERE `email` = '".$email."'";
        $result = $mysqli->query($sql);

        if($result->num_rows > 0) {
            echo '<p style="color:red">This email address is already taken</font><br/>';
            $error = true;
        }    
    }

    // No errors, we can register the user
    if(!$error) {    
        $passwort_hash = md5($passwort);
        $dateSignedUp = date('Y-m-d G:i:s');

        /*
        $passwort_hash = password_hash($passwort, PASSWORD_DEFAULT);

        $statement = $pdo->prepare("INSERT INTO users (email, passwort) VALUES (:email, :passwort)");
        $result = $statement->execute(array('email' => $email, 'passwort' => $passwort_hash));*/

        $sql = "INSERT INTO `user` (`email`, `name`, `password`, `signedUp`) VALUES ('".$email."', '".$name."', '".$passwort_hash."', '".$dateSignedUp."')";
        echo "SQL-Query: ".$sql."<br/>";
        $result = $mysqli->query($sql);
        $numAffectedRows = $mysqli->affected_rows;
        echo "Affected Rows: ".$numAffectedRows."<br/>";
        $result = true;
        
        if($result) {        
            echo 'You have been successfully registered. <a href="login.php">To the login</a>';
            $showFormular = false;
        } else {
            echo 'Unfortunately, an error occurred while saving<br>';
        }
    } 
}

if($showFormular) {
    echo "<br/>";
?>
 
<form action="?register=1" method="post">
E-Mail:<br>
<input type="email" size="40" maxlength="250" name="email"><br><br>
 
Name:<br>
<input type="name" size="40" maxlength="250" name="name"><br><br>
 
Password:<br>
<input type="password" size="40"  maxlength="250" name="passwort"><br>
 
Password again:<br>
<input type="password" size="40" maxlength="250" name="passwort2"><br><br>
 
<input type="submit" value="Register">
</form>

<?php
} //Ende von if($showFormular)
?>

</div>

		<div class="topcorner_left">
			<img src="img/grp__NM__menu_img__NM__logo.png" alt="Logo P&P Software" width="150" style="background-color: darkblue; padding: 5px;"><br/>
			<img src="img/uni_logo_220.jpg" alt="Logo University of Vienna" width="150" style="padding: 5px;"><br/>
			<img src="img/csm_uni_logo_schwarz_0ca81bfdea.jpg" alt="Logo Institute for Astrophysics" width="150" style="padding: 5px;">
			<br/><br/>
			<a href="login.php">Login</a>
		<div/>


</div>

</body>
</html>