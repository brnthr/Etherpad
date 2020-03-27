<?php
error_reporting(E_ALL ^ E_DEPRECATED);
ini_set('display_errors', true);

if (session_id() === '') session_start();

require 'vendor/autoload.php';
include_once __DIR__."/config.php";

// instance
function initInstance() {
   $ip = $_SERVER['SERVER_ADDR'];
   $adresse = 'http://'.$ip.':9001/api';
   $instance = new EtherpadLite\Client('e0ef80f2d50032a393ec8f6c75bcd117dfc9dccccf1a59784ec2279616f64934', $adresse);
   return $instance;
}


function getUserID($mail) {
   $mysqli = connexion_mysqli();
   $sql  = "SELECT * FROM authors WHERE aut_email='$mail' ";
   //echo $sql."<br>";

   $result = mysqli_query($mysqli,$sql);
   if ($result) { 
      $nbrow = mysqli_num_rows($result);
      if ($nbrow==1) {
         $row = $result->fetch_assoc();
         return $row['aut_id'];
      }
      //else return $nbrow;
      else return -1;
   }
   else return -1;
}

function listeUsers() {
   $mysqli = connexion_mysqli();
   $sql  = "SELECT * FROM authors";
   //echo $sql."<br>";

   $result = mysqli_query($mysqli,$sql);
   $effectif = array();
   while ($row = $result->fetch_assoc()) {
     $effectif[] = $row;
   }
   return $effectif;
}



function listePadsEtherpad() {
   $instance = initInstance();
   $listAllPads = $instance->listAllPads();
   $listePads = $listAllPads->padIDs;
   return $listePads;
}


function listePadsMySQL() {
   $aut_id = $_SESSION["aut_id"];
   $mysqli = connexion_mysqli();
   $sql  = "SELECT pads.pad_id, aut_pad AS createur ";
   //$sql  = "SELECT pads.pad_id ";
   $sql .= "FROM pads, pad_aut ";
   $sql .= "WHERE pads.pad_id = pad_aut.pad_id AND pad_aut.aut_id='$aut_id' ";
   //echo $sql."<br>";

   $result = mysqli_query($mysqli,$sql);
   $effectif = array();
   while ($row = $result->fetch_assoc()) {
     $effectif[] = $row;
   }
   return $effectif;
}


function creerPad($padID) {
   $instance = initInstance();
   $text = "Bienvenue au pad $padID.";
   $aut_id = $_SESSION["aut_id"];

   $chaine = "";

   try {
      $instance->createPad($padID, $text);
      //print_r($pad);
      //$code = $pad->code;
      $pad_id = $padID;
      $pad_name = $padID;
      $aut_id = $_SESSION["aut_id"];

      $mysqli = connexion_mysqli();
      $sql  = "INSERT INTO pads(pad_id, pad_name) ";
      $sql .= "VALUES('$pad_id', '$pad_name')";
      $result = mysqli_query($mysqli,$sql);

      $sql  = "INSERT INTO pad_aut(pad_id, aut_id, aut_pad) ";
      $sql .= "VALUES('$pad_id', '$aut_id', 1)";
      $result = mysqli_query($mysqli,$sql);
      $chaine .= "ok";
   } 
   catch (Exception $e) {
      $chaine .= "La création du pad a échouée : ". $e->getMessage();
   }
   echo $chaine;
}


function supprimerPad_old($padID) {
   $instance = initInstance();

   try {
      $instance->deletePad($padID);

      $aut_id = $_SESSION["aut_id"];

      $mysqli = connexion_mysqli();

      $sql  = "DELETE FROM pads WHERE pad_id='$padID'";
      $result = mysqli_query($mysqli,$sql);

      $sql  = "DELETE FROM pad_aut WHERE pad_id='$padID' AND aut_id='$aut_id'";
      $result = mysqli_query($mysqli,$sql);


   } 
   catch (Exception $e) {
      echo "La suppression du pad a échouée :". $e->getMessage();
   }
}


function partagerPad_old() {
   $aut_id = $_GET["aut_id"];
   $pad_id = $_GET["pad_id"];

   $chaine = "";

   $mysqli = connexion_mysqli();

   $sql  = "DELETE FROM pad_aut WHERE pad_id='$pad_id'";
   $result = mysqli_query($mysqli,$sql);

   $chaine .= mysqli_error($mysqli);

   $tab = explode(";", $aut_id);
   $sql  = "INSERT INTO pad_aut(pad_id, aut_id, aut_pad) VALUES ";
   for ($i=0; $i<count($tab)-1; $i++) {
      $user = $tab[$i];
      $sql .= "('$pad_id', '$user', 0)";
      if ($i < count($tab)-2) $sql .= ",";
   }
   $result = mysqli_query($mysqli,$sql);
   $chaine .= mysqli_error($mysqli);

   //echo $aut_id." - ".$pad_id;
   //echo "coucou";
   //echo $sql;
   echo $chaine;

}


function listeUsersPadPartage($padID) {
   $mysqli = connexion_mysqli();

   //$sql  = "SELECT authors.aut_id, authors.aut_name, IFNULL(T.aut_id, '0') AS partage ";
   $sql  = "SELECT authors.aut_id, authors.aut_name, IF(T.aut_id IS NULL, '0', '1') AS partage, IF(T.aut_pad=1, '1', '0') AS createur ";
   $sql .= "FROM authors ";
   $sql .= "LEFT OUTER JOIN ";
   $sql .= "(";
   $sql .= "   SELECT * ";
   $sql .= "   FROM pad_aut ";
   $sql .= "   WHERE pad_id='$padID' ";
   $sql .= ") T ";
   $sql .= "ON authors.aut_id=T.aut_id";

   //echo $sql."<br>";

   $result = mysqli_query($mysqli,$sql);
   $effectif = array();
   while ($row = $result->fetch_assoc()) {
     $effectif[] = $row;
   }
   return $effectif;
}


/////////////////////////////////////////////////////////////////////////
/*
UPDATE pad_aut 
   SET aut_pad=1
WHERE pad_id='utec' AND aut_id='a.zk'

UPDATE pad_aut 
   SET aut_pad=1
WHERE aut_id='a.zk'
*/

/*
$listAllPads = $instance->listAllPads();
$listePads = $listAllPads->padIDs;
//echo "Calcul de validUntil<br>";
$date = date_create();
$time = date_timestamp_get($date) + 10000;
//print_r($time);
//echo "<br><br>";
$groupID='g.mYMuKVEcSVVN5YLA'; //array(); //1; //""; //[];
$authorID='a.jfm4tpDJNUMCVomm'; //"admin";
$validUntil=$time; //1312201246;
//$session = $instance->createSession($groupID, $authorID, $validUntil);
*/



/////////////////////////////////////////////////////////////////////////
function nettoyerBase() {
   $instance = initInstance();
   $listAllPads = $instance->listAllPads();
   $listePads = $listAllPads->padIDs;
   $listAllGroups = $instance->listAllGroups();

   print_r($listAllPads); echo "<br>";
   $lap = $listAllPads->padIDs;
   for ($i=0; $i<count($lap); $i++) {
      $padID = $lap[$i];
      echo $padID."<br>";
      //$instance->deletePad($padID);
      supprimerPad($padID);
   }

   print_r($listAllGroups); echo "<br>";
   $lag = $listAllGroups->groupIDs;
   for ($i=0; $i<count($lag); $i++) {
      $groupID = $lag[$i];
      echo $groupID."<br>";

      try {
         $instance->deleteGroup($groupID);
      } 
      catch (Exception $e) {
         echo "<br>La suppression du pad a échouée :". $e->getMessage();
      }
   }
}
/////////////////////////////////////////////////////////////////////////




if (isset($_GET['action'])) {

   if ($_GET['action']=="listePads") {
      $liste = listePadsEtherpad();
      //echo json_encode($liste, JSON_NUMERIC_CHECK);
      //print_r($liste);

      $myliste = listePadsMySQL();
      //print_r($myliste);

      $Resultat = array();
      $Resultat[] = ["dataEtherpad" => $liste, "dataMySQL" => $myliste];
      echo json_encode($Resultat, JSON_NUMERIC_CHECK);
   }

   else if ($_GET['action']=="creerPad") {
      $padNom = $_GET["nompad"];
      creerPad($padNom);
   }

   else if ($_GET['action']=="ouvrirPad") {
      $padID = $_GET["nompad"];
      $authors = listeUsersPadPartage($padID);
      echo json_encode($authors, JSON_NUMERIC_CHECK);
      //echo "coucou";
   }


   else if ($_GET['action']=="supprimerPad") {
      $pad_id = $_GET["pad_id"];

      //$url = 'http://localhost/etherpad/app/zapi.php?action=supprimerPad&pad_id=test3';
      $url = 'http://localhost/etherpad/app/zapi.php?action=supprimerPad&pad_id='.$pad_id;
      $resultat = file_get_contents($url); 
      $resultat = json_decode($resultat);

      //supprimerPad($padNom);
      //echo $pad_id." - ".$url;
      echo $resultat;
   }

   else if ($_GET['action']=="partagerPad") {
      $aut_id = $_GET["aut_id"];
      $pad_id = $_GET["pad_id"];
      $createur = $_GET["createur"];
      $url = 'http://localhost/etherpad/app/zapi.php?action=partagerPad&aut_id='.$aut_id."&pad_id=".$pad_id."&createur=".$createur;
      $resultat = file_get_contents($url); 
      $resultat = json_decode($resultat);

      //partagerPad();
      //echo "coucou";
      echo $resultat;
   }

}

//echo "coucou";
//nettoyerBase();
/*
$user = $_GET["author"];
$id = getUserID($user);
//echo $id;
if ($id==-1) die("Vous n'êtes pas autorisé!"); 
//echo $id;
$_SESSION["aut_id"]=$id;
*/

?>
