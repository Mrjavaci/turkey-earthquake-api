<?php
include "Helpers/include.php";

$db = new Database();
$connection = new Connection();
$page = $connection->getBodyOfPage();
$parse = new Parse($page);
$parse->db = $db;
$parse->normalize();
$db->insertArray($parse->modelsArray);