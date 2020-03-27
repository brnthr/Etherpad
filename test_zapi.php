<?php
error_reporting(E_ALL ^ E_DEPRECATED);
ini_set('display_errors', true);

/*
$ip = $_SERVER['HTTP_HOST'];
$url = 'http://'.$ip.'/etherpad/app/zapi.php?action=getUserID&mail=ziad.kachouh@gmail.com';
//echo $ip."<br>";
$resultat = file_get_contents($url);
print_r($resultat);
*/

/*
$aut_id = "a.toto;a.titi;a.zk";
$pad_id = "toto_test1";
$createur = "a.toto";

$url = 'http://localhost/etherpad/app/zapi.php?action=partagerPad&aut_id='.$aut_id."&pad_id=".$pad_id."&createur=".$createur;
$resultat = file_get_contents($url); 
$resultat = json_decode($resultat);

print_r($resultat);
*/

/*
      //$pad_id = $_GET["pad_id"];
      $pad_id='test3';
      $url = 'http://localhost/etherpad/app/zapi.php?action=supprimerPad&pad_id='.$pad_id;
      echo $url."<br>";
      $resultat = file_get_contents($url); print_r($resultat);
      $resultat = json_decode($resultat); print_r($resultat);
*/



$url = 'http://192.168.1.38/etherpad/app/zapi.php?action=ouvrirPad&pad_id=test3&aut_id=a.zk';
echo $url."<br>";

      $tab = file_get_contents($url);
      $tab = json_encode($tab);
      print_r($tab);


?>

