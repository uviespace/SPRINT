<?php
require 'api/db_config.php';

if (isset($_GET["idProject"])) { $idProject = $_GET["idProject"]; } else { $idProject=0; };

$post = $_POST;

echo "<b><font color=red>Start Processing.</font></b><br/>";

$sql = "SELECT * FROM `project` WHERE id = ".$idProject;
$result = $mysqli->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $projectName = $row['name'];
} else {
    $projectName = "GENERIC";
}
$projectNameLC = strtolower($projectName);

echo "<b>Project ID:</b> ".$idProject."<br/>";
echo "<b>Project Name:</b> ".$projectNameLC."<br/>";
echo "<b>docType:</b> ".$post['docType']."<br/>";
echo "<b>number:</b> ".$post['number']."<br/>";

echo "<br/>";

echo "<b><font color=red>Initialize ...</font></b><br/>";

switch ($post['docType']) {
    case 'TN':
        echo "Technical Note<br/>";
        $docName = "tn".$post['number'];
        break;
    case 1:
        echo "i ist gleich 1";
        break;
    case 2:
        echo "i ist gleich 2";
        break;
    default:
       echo "i ist nicht gleich 0, 1 oder 2";
}

$path_in = "documentation/templates/";
$path_out = "documentation/project/".$projectNameLC."/".$docName."/";
$filename_read = "document_template.tex";
$filename_write = $docName.".tex";

// Get content from file
$file = $path_in . $filename_read;
$newfile = $path_out . $filename_write;

$content = file_get_contents($file);

// Generate content to replace

$vhEntry_template = 
  "  \\vhEntry{{{version}}}{{{date}}}{{{editor}}}{{{changes}}}";

echo "".$vhEntry_template."<br/>";

$insert_vhEntry = "";
$insert_newacronym = "";
$insert_newglossaryentry = "";

$newvhEntry = $vhEntry_template;
$newvhEntry = str_replace("{{version}}", $post['version1'], $newvhEntry);
$newvhEntry = str_replace("{{date}}", $post['date'], $newvhEntry);
$newvhEntry = str_replace("{{editor}}", "CR,RO", $newvhEntry);
$newvhEntry = str_replace("{{changes}}", "initial version", $newvhEntry);

echo "".$newvhEntry."<br/>";

$insert_vhEntry .= $newvhEntry;

$newcontent = $content;
$newcontent = str_replace("{{insert_vhEntry}}", $insert_vhEntry, $newcontent);
$newcontent = str_replace("{{insert-newacronym}}", $insert_newacronym, $newcontent);
$newcontent = str_replace("{{insert-newglossaryentry}}", $insert_newglossaryentry, $newcontent);

/*
  \vhEntry{0.2}{03.05.2021}{RO}{changes according to iSRR}
  \vhEntry{0.1}{31.01.2020}{RO}{initial version}  
*/

// get document prefix
$sql = "SELECT * FROM `docprefix` WHERE idProject = ".$idProject;
$result = $mysqli->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $docPrefix = $row['prefix'];
} else {
    $docPrefix = "GENERIC-UVIE-INST";
}

$doc_title = $post['title'];
$doc_identifier = $docPrefix."-".$post['docType']."-".$post['number'];  // TODO: add prefix
$doc_version = $post['version1'];
$doc_date = $post['date'];

if (substr($doc_version, 0, 2) == "0.") {
    $doc_version = "Draft ".substr($doc_version, 2);
} else if (substr($doc_version, 0, 2) == "0d") {
    $doc_version = "Draft ".substr($doc_version, 2);
} else {
    $doc_version = "Issue ".$doc_version;
}

//$source = '2012-07-31';
$source = $doc_date;
$date = new DateTime($source);
/*echo $date->format('d.m.Y')."<br/>"; // 31.07.2012
echo $date->format('d-m-Y')."<br/>"; // 31-07-2012
echo $date->format('M j, Y')."<br/>"; // 20120731*/
$doc_date = $date->format('M j, Y');

echo "<b>title:</b> ".$doc_title."<br/>";
echo "<b>identifier:</b> ".$doc_identifier."<br/>";
echo "<b>version:</b> ".$doc_version."<br/>";
echo "<b>date:</b> ".$doc_date."<br/>";

$newcontent = str_replace("{{doc_title}}", $doc_title, $newcontent);
$newcontent = str_replace("{{doc_identifier}}", $doc_identifier, $newcontent);
$newcontent = str_replace("{{doc_version}}", $doc_version, $newcontent);
$newcontent = str_replace("{{doc_date}}", $doc_date, $newcontent);

/*
\title{Software Management Plan}
\def \documentid {ARIEL-UVIE-PL-PL-002}
\date{Draft 2, May 3, 2021}
*/
/*
\title{{{doc_title}}}
\def \documentid {{{doc_identifier}}}
\date{{{doc_version}}, {{doc_date}}}
*/

$preparedby = "Roland Ottensamer";
$checkedby = "Christian Reimers";
$approvedby = "Franz Kerschbaum";
$affiliation = "Department of Astrophysics, University of Vienna";

echo "<b>prepared by:</b> ".$preparedby."<br/>";
echo "<b>checked by:</b> ".$checkedby."<br/>";
echo "<b>approved by:</b> ".$approvedby."<br/>";
echo "<b>affiliation:</b> ".$affiliation."<br/>";

$newcontent = str_replace("{{preparedby}}", $preparedby, $newcontent);
$newcontent = str_replace("{{checkedby}}", $checkedby, $newcontent);
$newcontent = str_replace("{{approvedby}}", $approvedby, $newcontent);
$newcontent = str_replace("{{affiliation}}", $affiliation, $newcontent);

/*
\def\preparedby {Roland Ottensamer\affil{1}}
\def\checkedby {Christian Reimers\affil{1}}
\def\approvedby {Franz Kerschbaum\affil{1}}

\def\affiliations{
	\affil{1} Department of Astrophysics, University of Vienna
}
*/
/*
\def\preparedby {{{preparedby}}\affil{1}}
\def\checkedby {{{checkedby}}\affil{1}}
\def\approvedby {{{}approvedby}\affil{1}}

\def\affiliations{
	\affil{1} {{affiliation}}
}
*/

$project_instrument_software = $projectName." XXX";

echo "<b>Project Instrument Software:</b> ".$project_instrument_software."<br/>";

$newcontent = str_replace("{{project_instrument_software}}", $project_instrument_software, $newcontent);

/*
ARIEL FGS ...
SMILE SXI IASW
ATHENA WFI ...
CHEOPS IASW
*/

$logo_uvie_astrophysik = "uni_logo_astrophysik_cmyk.eps"; // in titlepage.tex, univieA4.tex, and univie.tex 
$logo_uvie = "uni_logo_farbe_02.eps"; // in titlepage.tex

//$logo_project_instrument = "ariel-logo-med.png";
$logo_project_instrument = "WFI_Logo_RGB-med.png";

echo "<b>Logo Project Instrument:</b> ".$logo_project_instrument."<br/>";

$newcontent = str_replace("{{logo_project_instrument}}", $logo_project_instrument, $newcontent);

/*
{../shared/images/ariel-logo-med.png}
{../shared/images/uni_logo_astrophysik_cmyk.eps}
{../shared/images/uni_logo_farbe_02.eps}
*/

$newcontent = str_replace("{{project}}", $projectNameLC, $newcontent);

$newcontent = str_replace("{{defcolours}}", "", $newcontent);

$newcontent = str_replace("{{miscellaneous}}", "", $newcontent);

$newcontent = str_replace("{{tables}}", "", $newcontent);

$newcontent = str_replace("{{packages}}", "", $newcontent);

$newcontent = str_replace("{{files}}", "", $newcontent);


echo "<br/>";

echo "<b><font color=red>Write File ...</font></b><br/>";

echo "Filename: ".$filename_write."<br/>";

if (!file_exists($path_out)) {
    mkdir($path_out, 0777, true);
}

$myfile = fopen($newfile, "w");
fwrite($myfile, $newcontent);
fclose($myfile);

echo "<br/>";

echo "<b><font color=red>Insert Document into Database.</font></b><br/>";

// TODO: INSERT INTO ...

        echo ">>> shortName = ".$post['docType']."<br/>";
        echo ">>> number = ".$post['number']."<br/>";
        echo ">>> version = ".$post['version1']."<br/>";
        echo ">>> date = ".$post['date']."<br/><br/>";

// check, if document with this number is already in the database 
$sql = "SELECT * FROM `projectdocument` AS pd, `document` AS d WHERE pd.idDocument = d.id AND pd.idProject = ".$idProject;
$result = $mysqli->query($sql);
if ($result->num_rows > 0) {
    $found = false;
    while($row = $result->fetch_assoc()) {
        echo ">>> shortName = ".$row['shortName']."<br/>";
        echo ">>> idDocType = ".$row['idDocType']."<br/>";
        echo ">>> number = ".$row['number']."<br/><br/>";
        if ($row['shortName']==$post['docType']) {
            if ($row['number']==$post['number']) {
                $found = true;
            }
        }
        //$projectName = $row['name'];
    }
    if ($found) {
        echo ">>> Document with this number already exists.<br/>";
    } else {
        echo ">>> Document does not exist => INSERT into database ...<br/>";
        
        $sql = "SELECT * FROM `doctype` WHERE `name` = '".$post['docType']."'";
        $result = $mysqli->query($sql);
        $row = $result->fetch_assoc();
        $idDocType = $row['id'];
        echo "idDocType = ".$idDocType."<br/>";
        
        $sql = "INSERT INTO ".
          "`document` ".
          "(`idDocType`, `idDocRelation`, `idOrg`, `shortName`, `number`, `name`) ".
          "VALUES ".
          "(".$idDocType.", 1, 1, '".$post['docType']."', '".$post['number']."', '".$doc_title."')";
        $result = $mysqli->query($sql);
        $idDocument = $mysqli->insert_id;
        //$idDocument = 5;
        echo $sql."<br/>";
        
        $sql = "INSERT INTO ".
          "`projectDocument` ".
          "(`idProject`, `idDocument`) ".
          "VALUES ".
          "(".$idProject.", ".$idDocument.")";
        $result = $mysqli->query($sql);
        echo $sql."<br/>";
        
        $sql = "INSERT INTO ".
          "`docVersion` ".
          "(`idDocument`, `version`, `date`) ".
          "VALUES ".
          "(".$idDocument.", '".$post['version1']."', '".$post['date']."')";
        $result = $mysqli->query($sql);
        echo $sql."<br/>";
        
    }
} else {
    //$projectName = "GENERIC";
    echo ">>> NO documents found.<br/>";
}

echo "<br/>";

echo "DONE<br/>";

echo "<br/>";

echo "<b><font color=red>End Processing.</font></b><br/>";

//header( "refresh:2;url=sel_project-documentation.php?idProject=".$idProject );
//die('');

?>