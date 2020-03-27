<?php
error_reporting(E_ALL ^ E_DEPRECATED);
ini_set('display_errors', true);

if (session_id() === '') session_start();

require 'vendor/autoload.php';
include_once __DIR__."/config.php";




function test(){
   $result = array();
   $result["allow_url_fopen"] = ini_get('allow_url_fopen');
   $result["message"] = "Cette API nécessite que le paramétre allow_url_fopen=1 ou On dans php.ini.";
   return $result;
}


// instance
function initInstance() {
   global $ip; // = $_SERVER['SERVER_ADDR'];
   global $adresse;
   //$adresse = 'http://'.$ip.':9001/api';
   //$adresse = $ip.':9001/api';
   $instance = new EtherpadLite\Client($apiKey, $adresse);
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

function getUserIdAndProfil($mail) {
   $mysqli = connexion_mysqli();
   $sql  = "SELECT * FROM authors WHERE aut_email='$mail' ";
   //echo $sql."<br>";

   $result = mysqli_query($mysqli,$sql);
   //$effectif = array();
   if ($result) { 
      $nbrow = mysqli_num_rows($result);
      if ($nbrow==1) {
         $row = $result->fetch_assoc();
         //return $row['aut_id'];
         return $row;
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


function listePadsMySQL($aut_id) {
   //$aut_id = $_SESSION["aut_id"];
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


function creerPad($padID, $aut_id) {
   $instance = initInstance();
   $text = "Bienvenue au pad $padID.";
   //$aut_id = $_SESSION["aut_id"];

   $chaine = '';

   try {
      $instance->createPad($padID, $text);
      //print_r($pad);
      //$code = $pad->code;
      $pad_id = $padID;
      $pad_name = $padID;
      //$aut_id = $_SESSION["aut_id"];

      $mysqli = connexion_mysqli();
      $sql  = "INSERT INTO pads(pad_id, pad_name) ";
      $sql .= "VALUES('$pad_id', '$pad_name')";
      $result = mysqli_query($mysqli,$sql);

      $sql  = "INSERT INTO pad_aut(pad_id, aut_id, aut_pad) ";
      $sql .= "VALUES('$pad_id', '$aut_id', 1)";
      $result = mysqli_query($mysqli,$sql);
      $chaine .= 'ok';
   } 
   catch (Exception $e) {
      $chaine .= "La création du pad a échouée : ". $e->getMessage();
   }
   return $chaine;
}


function supprimerPad($padID) {
   global $ip;

   $chaine = "";

   try {
      $instance = initInstance();
      $instance->deletePad($padID);

      $mysqli = connexion_mysqli();

      $sql  = "DELETE FROM pads WHERE pad_id='$padID'"; //echo $sql."<br>";
      $result = mysqli_query($mysqli,$sql);

      //$sql  = "DELETE FROM pad_aut WHERE pad_id='$padID' AND aut_id='$aut_id'";
      $sql  = "DELETE FROM pad_aut WHERE pad_id='$padID' "; //echo $sql."<br>";
      $result = mysqli_query($mysqli,$sql);
      $chaine = "Suppression ok!";
   } 
   catch (Exception $e) {
      $chaine .= "La suppression du pad a échouée :". $e->getMessage();
   }

   return $chaine;
}



function partagerPad($aut_id, $pad_id, $createur) {
   //$aut_id = $_GET["aut_id"];
   //$pad_id = $_GET["pad_id"];

   $chaine = "";

   $mysqli = connexion_mysqli();

   $sql1  = "DELETE FROM pad_aut WHERE pad_id='$pad_id'";
   $result = mysqli_query($mysqli,$sql1);

   $chaine .= mysqli_error($mysqli);

   $tab = explode(";", $aut_id);
   $sql2  = "INSERT INTO pad_aut(pad_id, aut_id, aut_pad) VALUES ";
   for ($i=0; $i<count($tab)-1; $i++) {
      $user = $tab[$i];
      if ($user==$createur)
         $sql2 .= "('$pad_id', '$user', 1)";
      else
         $sql2 .= "('$pad_id', '$user', 0)";
      if ($i < count($tab)-2) $sql2 .= ",";
   }
   $result = mysqli_query($mysqli,$sql2);

   $chaine .= mysqli_error($mysqli);

   //echo $aut_id." - ".$pad_id;
   //echo "coucou";
   //echo $sql1;
   //echo $chaine;
   $chaine .= "ok";
   return $chaine;
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

//==========================================================================




//==========================================================================


/////////////////////////////////////////////////////////////////////////
// on définit les URLs valables
$possible_url = array("test", "getUserID", "getUserIdAndProfil", "listeUsers", "listePadsMySQL", "creerPad", "ouvrirPad", "partagerPad", "supprimerPad"); 

// on met le message d'erreur par défaut dans une variable
$value = "Une erreur est survenue"; 
/////////////////////////////////////////////////////////////////////////


if (isset($_GET["action"]) && in_array($_GET["action"], $possible_url)) { //si l'URL est OK
   switch ($_GET["action"]) {

      case "test": {
         $value = test(); //Je fais un test
         break;
      }
      case "getUserID": {
         if (isset($_GET["mail"])) {
            $mail = $_GET["mail"];
            $value = getUserID($mail);
         }
         else {
            $value = "Argument manquant"; 
         }
         break;
      }

      case "getUserIdAndProfil": {
         if (isset($_GET["mail"])) {
            $mail = $_GET["mail"];
            $value = getUserIdAndProfil($mail);
         }
         else {
            $value = "Argument manquant"; 
         }
         break;
      }


      case "listeUsers": {
         $value = listeUsers(); 
         break;
      }

      case "listePadsMySQL": {
         if (isset($_GET["aut_id"])) {
            $aut_id = $_GET["aut_id"];
            $value = listePadsMySQL($aut_id);
         }
         else {
            $value = "Argument manquant"; 
         }
         break;
      }

      case "partagerPad": {
         if (isset($_GET["aut_id"]) && isset($_GET["pad_id"]) && isset($_GET["createur"])) {
            $aut_id = $_GET["aut_id"];
            $pad_id = $_GET["pad_id"];
            $createur = $_GET["createur"];
            $value = partagerPad($aut_id, $pad_id, $createur);
         }
         else {
            $value = "Argument manquant"; 
         }
         break;
      }

      case "supprimerPad": {
         
         if (isset($_GET["pad_id"])) {
            $pad_id = $_GET["pad_id"];
            $value = supprimerPad($pad_id);
            //$value = $pad_id;
         }
         else {
            $value = "Argument manquant"; 
         }
         
         //echo $value."coucou";
         //echo "coucou";
         break;
      }


      case "creerPad": {
         
         if (isset($_GET["pad_id"]) && isset($_GET["aut_id"])) {
            $pad_id = $_GET["pad_id"];
            $aut_id = $_GET["aut_id"];
            $value = creerPad($pad_id, $aut_id);
         }
         else {
            $value = "Argument manquant"; 
         }
         break;
      }

      case "ouvrirPad": {
         
         if (isset($_GET["pad_id"])) {
            $pad_id = $_GET["pad_id"];
            $value = listeUsersPadPartage($pad_id);
         }
         else {
            $value = "Argument manquant"; 
         }
         break;
      }

      default: {
         $value = "Action inconnue";
      }



   }
}
else {
   $value = "Action inconnue"; 
}

//exit(json_encode($value, JSON_NUMERIC_CHECK)); // on retourne la réponse en JSON

exit(json_encode($value)); 

?>
