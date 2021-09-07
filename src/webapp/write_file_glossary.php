<?php
require 'api/db_config.php';

if (isset($_GET["idProject"])) { $idProject = $_GET["idProject"]; } else { $idProject=0; };

echo "<b><font color=red>Start Processing.</font></b><br/>";

$sql = "SELECT * FROM `project` WHERE id = ".$idProject;
$result = $mysqli->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $projectName = strtolower($row['name']);
} else {
    $projectName = strtolower("generic");
}

echo "<br/>";

echo "<b><font color=red>Initialize ...</font></b><br/>";

$path_in = "documentation/templates/";
$path_out = "documentation/shared/";
$filename_read = "glossary_template.tex";
$filename_write = "glossary_".$projectName.".tex";

// Get content from file
$file = $path_in . $filename_read;
$newfile = $path_out . $filename_write;

$content = file_get_contents($file);

// Generate content to replace

$newdualentry_template = 
  "\n\\newdualentry{{{name}}}%\n".
  "  {{{name}}}%\n".
  "  {{{shortDesc}}}%\n".
  "  {{{desc}}}%\n";
  
echo "<br/>".$newdualentry_template."<br/>";

$insert_newdualentry = "";
$insert_newacronym = "";
$insert_newglossaryentry = "";

$sql =
  "SELECT * FROM ".
  "`projectacronym` AS pa, `acronym` AS a ".
  "WHERE ".
  "pa.idAcronym = a.id AND ".
  "pa.idProject = ".$idProject;
$result = $mysqli->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()){
        $acr_name = $row['name'];
        $acr_shortdesc = $row['shortDesc'];
        $acr_desc = $row['desc'];
        
        $newdualentry = $newdualentry_template;
        $newdualentry = str_replace("{{name}}", $acr_name, $newdualentry);
        $newdualentry = str_replace("{{shortDesc}}", $acr_shortdesc, $newdualentry);
        $newdualentry = str_replace("{{desc}}", $acr_desc, $newdualentry);
        
        echo "<br/>".$newdualentry."<br/>";
        
        $insert_newdualentry .= $newdualentry;
    }
} else {
    echo "No acronym found!<br/>";
}

$newcontent = $content;
$newcontent = str_replace("{{insert-newdualentry}}", $insert_newdualentry, $newcontent);
$newcontent = str_replace("{{insert-newacronym}}", $insert_newacronym, $newcontent);
$newcontent = str_replace("{{insert-newglossaryentry}}", $insert_newglossaryentry, $newcontent);

echo "<br/>";

echo "<b><font color=red>Write File ...</font></b><br/>";

echo "Filename: ".$filename_write."<br/>";

$myfile = fopen($newfile, "w");
fwrite($myfile, $newcontent);
fclose($myfile);

echo "<br/>";

echo "<b><font color=red>End Processing.</font></b><br/>";

header( "refresh:2;url=sel_project-documentation.php?idProject=".$idProject );
die('');

?>