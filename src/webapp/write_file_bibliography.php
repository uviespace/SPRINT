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

$path_in = "documentation/shared/";
$path_out = "documentation/shared/";
$filename_read = "bibliography_".$projectName."_general.bib";
$filename_write = "bibliography_".$projectName.".bib";

// Copy files
$file = $path_in . $filename_read;
$newfile = $path_out . $filename_write;

if (!copy($file, $newfile)) {
    echo "copy $file schlug fehl...\n";
}

// Generate content to append

/*$ref_name = "leanosSRS";
$ref_title = "LeanOS Software Requirements Specification";
$ref_organization = "University of Vienna";
$ref_year = "2017";
$ref_version = "1.0";*/

/*$append_template = 
  "\n@book{{{name}},\n".
  "\ttitle\t\t\t= \"{{title}}\",\n".
  "\torganization\t= \"{{organization}}\",\n".
  "\tyear\t\t\t= \"{{year}}\",\n".
  "\tversion\t\t\t= \"{{version}}\",\n".
  "}";
  
echo "<br/>".$append."<br/>";*/
  
$append_template = 
  "\n@book{{{name}},\n".
  "    title        = \"{{title}}\",\n".
  "    organization = \"{{organization}}\",\n".
  "    year         = \"{{year}}\",\n".
  "    version      = \"{{version}}\",\n".
  "}\n";

echo "<br/>".$append_template."<br/>";

/*$append = $append_template;
$append = str_replace("{{name}}", $ref_name, $append);
$append = str_replace("{{title}}", $ref_title, $append);
$append = str_replace("{{organization}}", $ref_organization, $append);
$append = str_replace("{{year}}", $ref_year, $append);
$append = str_replace("{{version}}", $ref_version, $append);*/

$sql = 
  "SELECT o.name AS oname, d.name AS dname, d.*, dv.*, o.* FROM ".
  "`projectdocument` AS pd, `document` as d, `docVersion` AS dv, `organisation` AS o ".
  "WHERE ".
  "pd.idDocument = d.id AND ".
  "dv.idDocument = d.id AND ".
  "d.idOrg = o.id AND ".
  "d.idDocRelation = 0 AND ".
  "pd.idProject = ".$idProject;
$result = $mysqli->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()){
        $orgShortDesc = $row['shortDesc'];
        $orgName = $row['oname'];
        $docName = $row['dname'];
        $docShortName = $row['shortName'];
        $docIdentifier = $row['identifier'];
        $docNumber = substr($docIdentifier, -3);
        $docVersion = $row['version'];
        $docDate = $row['date'];
        $docYear = substr($docDate, -4);
        $ref_name = $orgName . $docShortName . $docNumber . $docVersion;
        echo "Document: <b>".$ref_name."</b> - ".$docIdentifier.", ".$docName.", ".$docVersion.", ".$docDate." (".$docYear."), ".$orgName." (".$orgShortDesc.")<br/>";
        
        $ref_name = $ref_name;
        $ref_title = $docIdentifier." ".$docName;
        $ref_organization = $orgShortDesc;
        $ref_year = $docYear;
        $ref_version = $docVersion;
        
        $append = $append_template;
        $append = str_replace("{{name}}", $ref_name, $append);
        $append = str_replace("{{title}}", $ref_title, $append);
        $append = str_replace("{{organization}}", $ref_organization, $append);
        $append = str_replace("{{year}}", $ref_year, $append);
        $append = str_replace("{{version}}", $ref_version, $append);
        
        echo "<br/>".$append."<br/>";
        
        // Schreibt den Inhalt in die Datei
        // unter Verwendung des Flags FILE_APPEND, um den Inhalt an das Ende der Datei anzufügen
        // und das Flag LOCK_EX, um ein Schreiben in die selbe Datei zur gleichen Zeit zu verhindern
        file_put_contents($newfile, $append, FILE_APPEND | LOCK_EX);
    }
} else {
    echo "No document found!<br/>";
}

echo "<br/>";

echo "<b><font color=red>Write File ...</font></b><br/>";

echo "Filename: ".$filename_write."<br/>";
// Open new file and append content

/* METHODE 1
// Öffnet die Datei, um den vorhandenen Inhalt zu laden
$current = file_get_contents($newfile);
// Fügt eine neue Person zur Datei hinzu
$current .= "\n\nJohn Smith\n";
// Schreibt den Inhalt in die Datei zurück
file_put_contents($newfile, $current);
*/
 /* METHODE 2
// Schreibt den Inhalt in die Datei
// unter Verwendung des Flags FILE_APPEND, um den Inhalt an das Ende der Datei anzufügen
// und das Flag LOCK_EX, um ein Schreiben in die selbe Datei zur gleichen Zeit zu verhindern
file_put_contents($newfile, $append, FILE_APPEND | LOCK_EX);*/

echo "<br/>";

echo "<b><font color=red>End Processing.</font></b><br/>";

header( "refresh:2;url=sel_project-documentation.php?idProject=".$idProject );
die('');

?>