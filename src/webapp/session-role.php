<?php
session_start();
/*
if(!isset($_SESSION['userid'])) {
    //die('Please <a href="login.php">login</a> first!');
    echo "Please <a href='login.php'>login</a> first!";
    echo "<br/><br/>";
    echo "<img src='img/loading.gif' />";
    header( "refresh:8;url=login.php" );
    die('');
}
*/
require 'api/db_config.php';

if (isset($_GET['var'])) {
    if ($_GET['var'] == "role") {
        if (isset($_GET['idProject']) && $_GET['idProject'] != "") {
            //Abfrage der Nutzer ID vom Login
            $userid = $_SESSION['userid'];
        
            // get user name from database
            $sql = "SELECT * FROM `user` WHERE `id` = ".$userid;
            $result = $mysqli->query($sql);
            $row = $result->fetch_assoc();

            $userName = $row["name"];
            $userEmail = $row["email"];

            //Project from 
            $projectid = $_GET['idProject'];

            // get access level 
            $sql = "SELECT * FROM `userproject` WHERE ".
                   "`idProject` = ".$projectid." AND ".
                   "(".
                   "(`idUser` = ".$userid." AND (`idRole` = 1 OR `idRole` = 2)) OR ".
                   "(`email` = '".$userEmail."' AND (`idRole` = 3 OR `idRole` = 4))".
                   ")";
            $result = $mysqli->query($sql);
            
            $json = array();

            $max_access_level = 5;
            while($row = $result->fetch_assoc()){
                if ($row['idRole'] < $max_access_level) {
                    $max_access_level = $row['idRole'];
                    // empty array
                    unset($json);
                    // add row
                    $json[] = $row;
                }
            }

            echo json_encode($json);
        } else {
            echo json_encode("Input not allowed! (project is missing)");
        }
    } else {
        echo json_encode("Input not allowed!");
    }
};




 






?>