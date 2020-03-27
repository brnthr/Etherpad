<?php
error_reporting(E_ALL ^ E_DEPRECATED);
ini_set('display_errors', true);


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


function listeUsers() {
   $mysqli = connexion_mysqli();
   $sql  = "SELECT * FROM authors";
   //echo $sql."<br>";

   $result = mysqli_query($mysqli,$sql);
   $effectif = array();
   while ($row = $result->fetch_assoc()) {
     if ($row["aut_role"]==1) $row["aut_role"]="Administrateur"; else $row["aut_role"]="Auteur";
     $effectif[] = $row;
   }
   return $effectif;
}


function existUser($aut_id) {
   $mysqli = connexion_mysqli();
   $sql  = "SELECT * FROM authors WHERE aut_id='$aut_id' ";
   $result = mysqli_query($mysqli,$sql);
   if ($result) { 
      $nbrow = mysqli_num_rows($result);
      if ($nbrow==1) {
         return 1;
      }
      //else return $nbrow;
      else return 0;
   }
   else return 0;
}

function sauverUsers($aut_id, $aut_name, $aut_email, $aut_psw, $aut_role) {
   $chaine = "";
         $mysqli = connexion_mysqli();

   if ($aut_role=="Administrateur") $aut_role="1"; else $aut_role="0";
   if (existUser($aut_id)==1) {
      $sql  = "UPDATE authors SET ";
      $sql .= "   aut_id = '$aut_id', ";
      $sql .= "   aut_name = '$aut_name', ";
      $sql .= "   aut_email = '$aut_email', ";
      $sql .= "   aut_psw = '$aut_psw', ";
      $sql .= "   aut_role = '$aut_role' ";
      $sql .= "WHERE aut_id = '$aut_id' ";

      $chaine .= "update $aut_id";
   }
   else {
      $sql  = "INSERT INTO authors(aut_id, aut_name, aut_email, aut_psw, aut_role) ";
      $sql .= "VALUES('$aut_id', '$aut_name', '$aut_email', '$aut_psw', '$aut_role')";

      $chaine .= "insert $aut_id";
   }
   $result = mysqli_query($mysqli,$sql);

   return $chaine;
}

function supprimerUser($aut_id) {
   $mysqli = connexion_mysqli();

   $sql  = "DELETE FROM authors WHERE aut_id='$aut_id'"; 
   $result = mysqli_query($mysqli,$sql);

   $chaine = "supprimer";
   return $chaine;
}

if (isset($_GET['action'])) {

   if ($_GET['action']=="listeUsers") {
      $liste = listeUsers();
      echo json_encode($liste, JSON_NUMERIC_CHECK);
      //echo "coucou";
   }
   else if ($_GET['action']=="sauverUsers") {
      $aut_id    = $_GET["aut_id"];
      $aut_name  = $_GET["aut_name"];
      $aut_email = $_GET["aut_email"];
      $aut_psw   = $_GET["aut_psw"];
      $aut_role  = $_GET["aut_role"];
      $ret = sauverUsers($aut_id, $aut_name, $aut_email, $aut_psw, $aut_role);
      echo $ret;
   }
   else if ($_GET['action']=="supprimerUser") {
      $aut_id    = $_GET["aut_id"];
      $ret = supprimerUser($aut_id);
      echo $ret;
   }

}


?>
