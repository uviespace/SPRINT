<?php
chdir(__DIR__);
if (array_key_exists('QUERY_STRING', $_SERVER))  {
	$queryString = $_SERVER['QUERY_STRING'];
} else {
	$queryString = '';
}

$filePath = realpath(ltrim(($queryString ? $_SERVER["SCRIPT_NAME"] : $_SERVER["REQUEST_URI"]), '/'));
if ($filePath && is_dir($filePath)){
    // attempt to find an index file
    foreach (['index.php', 'index.html'] as $indexFile){
        if ($filePath = realpath($filePath . DIRECTORY_SEPARATOR . $indexFile)){
            break;
        }
    }
}
if ($filePath && is_file($filePath)) {
    // 1. check that file is not outside (behind) of this directory for security
    // 2. check for circular reference to server.php
    // 3. don't serve dotfiles
    if (strpos($filePath, __DIR__ . DIRECTORY_SEPARATOR) === 0
        && $filePath != __DIR__ . DIRECTORY_SEPARATOR . 'server.php'
        && substr(basename($filePath), 0, 1) != '.'
    ) {
        if (strtolower(substr($filePath, -4)) == '.php') {
            // change directory for php includes
            chdir(dirname($filePath));

            // php file; serve through interpreter
            include $filePath;
        } else {
            // asset file; serve from filesystem
            return false;
        }
    } else {
        // disallowed file
        header("HTTP/1.1 404 Not Found");
        echo "404 Not Found";
    }
} else {
    // rewrite to our router file
    // this portion should be customized to your needs
   $_SERVER['REQUEST_URI'] = "/somethingrandom".$_SERVER['REQUEST_URI'];

    include __DIR__ . DIRECTORY_SEPARATOR . 'api/v2/api_routing.php';
}
?>
