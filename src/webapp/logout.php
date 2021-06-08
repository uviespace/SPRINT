<?php
session_start();
session_destroy();

echo "Logout successfull, have a nice day";

echo "<br/><br/>";

echo "<img src='img/loading.gif' />";

header( "refresh:8;url=login.php" );
?>