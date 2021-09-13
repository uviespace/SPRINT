<?php

require 'db_config.php';

$num_rec_per_page = 5;

if (isset($_GET["page"])) { $page  = $_GET["page"]; } else { $page=1; };
if (isset($_GET["classification"])) { $classification  = $_GET["classification"]; } else { $classification=-1; };
if (isset($_GET["showAll"])) { $showAll  = $_GET["showAll"]; } else { $showAll=0; };

if ($showAll == 1) {
    $num_rec_per_page = 1000;
    $page=1;
}

$start_from = ($page-1) * $num_rec_per_page;

if ($classification == -1) { // ALL
    $sqlTotal = 
      "SELECT ".
      "a.*, ac.idClassification AS idClassification ".
      "FROM ".
      "`acronym` AS a ".
      "LEFT JOIN ".
      "`acronymclassification` AS ac ON (a.id = ac.idAcronym) ".
      "UNION ".
      "  SELECT ".
      "  a.*, ac.idClassification AS idClassification ".
      "  FROM ".
      "  `acronym` AS a ".
      "  RIGHT JOIN ".
      "  `acronymclassification` AS ac ON (a.id = ac.idAcronym)";
    $sql = 
      "SELECT ".
      "a.*, ac.idClassification AS idClassification ".
      "FROM ".
      "`acronym` AS a ".
      "LEFT JOIN ".
      "`acronymclassification` AS ac ON (a.id = ac.idAcronym) ".
      "UNION ".
      "  SELECT ".
      "  a.*, ac.idClassification AS idClassification ".
      "  FROM ".
      "  `acronym` AS a ".
      "  RIGHT JOIN ".
      "  `acronymclassification` AS ac ON (a.id = ac.idAcronym) ".
      "ORDER BY name ASC LIMIT $start_from, $num_rec_per_page";
} else if ($classification == 0) { // no classification (SELECT * FROM Table1 WHERE id NOT IN (SELECT id FROM Table2))
    $sqlTotal = "SELECT *, 'null' AS idClassification FROM `acronym` WHERE id NOT IN (SELECT DISTINCT idAcronym FROM `acronymclassification`)";
    $sql = "SELECT *, 'null' AS idClassification FROM `acronym` WHERE id NOT IN (SELECT DISTINCT idAcronym FROM `acronymclassification`) ORDER BY name ASC LIMIT $start_from, $num_rec_per_page";
} else { // distinct classification
    $sqlTotal = "SELECT * FROM `acronym` AS a, `acronymclassification` AS ac WHERE ac.idAcronym = a.id AND ac.idClassification = ".$classification;
    $sql = "SELECT * FROM `acronym` AS a, `acronymclassification` AS ac WHERE ac.idAcronym = a.id AND ac.idClassification = ".$classification." ORDER BY a.name ASC LIMIT $start_from, $num_rec_per_page";
}

$result = $mysqli->query($sql);

$json = array();

while($row = $result->fetch_assoc()){
	$json[] = $row;
}

$data['data'] = $json;

$result =  mysqli_query($mysqli,$sqlTotal);

$data['total'] = mysqli_num_rows($result);

echo json_encode($data);

?>