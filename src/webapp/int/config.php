<?php
/**
 * CONFIGURATION
 */

//var_dump(PHP_OS);  /* string(5) "WINNT" OS: */
//echo "<br/>";
//echo php_uname()."<br/>";  /* Windows NT LAPTOP-I3Q1KQNV 10.0 build 19044 (Windows 10) AMD64 */
$os = PHP_OS;
//echo $os."<br/>";  /* WINNT */

if ($os == "WINNT") { // IF WINDOWS
/*
    $path = "C:\\xampp-7.3.11\\htdocs\\dbeditor\\uploads\\import\\";
    
    $path_to_python = "C:\\Users\\chris\\Anaconda2\\";
    $python_cmd = "python.exe";
    $path_to_pyscripts = "..\\cordetfw\\editor-1.1\\_lib\\libraries\\sys\\python\\src\\";
    
    $path_to_imports = "uploads\\import\\";
	*/
    $path = "C:\\dev\\xampp-7.4.27\\htdocs\\dbeditor\\uploads\\import\\";
    
    $path_to_python = "";
    $python_cmd = "python";
    $path_to_pyscripts = "python\\";
    
    $path_to_imports = "uploads\\import\\";
	
} else { // IF LINUX

    $path = "/var/www/html/SPRINT/uploads/import/";
    
    $path_to_python = "";
    $python_cmd = "python3";
    $path_to_pyscripts = "./python/";
    
    $path_to_imports = "./uploads/import/";

}

?>