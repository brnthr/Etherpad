<?php

function connexion_mysqli() {
   // database
   $dbHost     = "localhost";
   $dbDatabase = "mypads";
   $dbUser     = "hceres";
   $dbPasswrod = "hceres";
   $mysqli = mysqli_connect($dbHost, $dbUser, $dbPasswrod, $dbDatabase);
   $mysqli->set_charset('utf8');
   $mysqli->query("SET collation_connection = utf8_unicode_ci");
   if(!$mysqli){
      die("Connection failed: " . $mysqli->error);
   }
   return $mysqli;
}


$ip = $_SERVER['SERVER_ADDR'];
$ip = "http://localhost:8888/";

$adresse = "http://localhost:8888/api";

$urlapp = "http://localhost:8888/etherpad/";


$apiKey = 'ca5ca919b6bf1e3cc441ff261a766cc1975f64dd0d77a934f72e224ef89ef2f9';

?>
