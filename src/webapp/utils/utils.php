<?php

function lib_dwnFile($clean, $file) {
	//$_SESSION['scriptcase']['form_Application_mob']['contr_erro'] = 'on';
	if (file_exists($file)) {
		$size = filesize($file);
		header('Content-Description: File Transfer');
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Content-Length: ' . $size);
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: no-cache, no-store, must-revalidate');
		header('Pragma: public');
		header('Connection: Keep-Alive');
		if ($clean) {
			@ob_end_clean();
			@ob_end_clean();
		}
		flush();
		readfile($file);
	}
}

function rmdirr($dirname)
{
    // Sanity check
    if (!file_exists($dirname)) {
        return false;
    }

    // Simple delete for a file
    if (is_file($dirname)) {
        return unlink($dirname);
    }

    // Loop through the folder
    $dir = dir($dirname);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Recurse
        rmdirr("$dirname/$entry");
    }

    // Clean up
    $dir->close();
    return rmdir($dirname);
}

?>
