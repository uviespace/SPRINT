<html>
<body>
<table>
<?php

  $id  = $_POST["id"];
  $pusdatatype = $_POST['id'];

  $post = $_POST;

  string $msg = "";

    foreach ($_POST as $key => $value) {
        $msg += "<tr>";
        $msg += "<td>";
        $msg += $key;
        $msg += "</td>";
        $msg += "<td>";
        $msg += $value;
        $msg += "</td>";
        $msg += "</tr>";
    }
	
	echo $msg;

  //var_dump($_POST)

?>
</table>
</body>
</html>