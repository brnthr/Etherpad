<?php
   error_reporting(E_ALL ^ E_DEPRECATED);
   ini_set('display_errors', true);

   if (session_id() === '') session_start();

   include_once __DIR__."/config.php";
?>

<!DOCTYPE html>
<html>
<head>

<!--
<script src="outils/jquery/jquery-3.3.1.min.js"></script>
-->


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


<script src="outils2/jquery.js"></script>

<link rel="stylesheet" href="outils2/bootstrap.min.css" >
<script src="outils2/bootstrap.min.js"></script>
<link rel="stylesheet" href="outils2/bootstrap-multiselect.css" >
<script src="outils2/bootstrap-multiselect.js"></script>

<script type="application/javascript" src="japp.js"></script>

<style>
bodyx {
  background-image: url('fond.jpg');
}

.box {
  float: left;
  margin: 0.5em;
}
.after-box {
  clear: left;
}
.hidden{ display: none; }

</style> 

</head>
<body >

<?php
   if (isset($_GET["author"])) {
      $user = $_GET["author"];

      $url = $urlapp.'zapi.php?action=getUserIdAndProfil&mail='.$user;
      $tab = file_get_contents($url);
      $tab = json_decode($tab);

      $id = $tab->aut_id;
      $name = $tab->aut_name;
      $profil = $tab->aut_role;

      if ($id==-1) die("Vous n'êtes pas autorisé!"); 
      $_SESSION["aut_id"]=$id;
      $_SESSION["aut_name"]=$name;
      $_SESSION["aut_role"]=$profil;
   }
   else {
      if (isset($_SESSION["aut_id"])) {
      }
      else {
         die("Vous n\'êtes pas autorisé!");
      }
   }

   $id = $_SESSION["aut_id"];

   $url = $urlapp.'zapi.php?action=listePadsMySQL&aut_id='.$id;
   $listePads = file_get_contents($url); 
   $listePads = json_decode($listePads);


   $url = $urlapp.'zapi.php?action=listeUsers';
   $authors = file_get_contents($url);
   $authors = json_decode($authors);

?>

<div class="container" id="contenu" style="width:100%; height:100%; padding-bottom: 50px; overflow:auto;">


<div class="box">
   <button class="btn"><i class="fa fa-user-circle"></i> <?php echo $_SESSION["aut_name"]; ?></button>
   <input type="hidden" id="aut_id" value="<?php echo $_SESSION["aut_id"]; ?>" readonly >
   <input type="hidden" id="profil" value="<?php echo $_SESSION["aut_role"]; ?>" readonly >
</div>

<div class="box hidden">
   <input type="hidden" id="adresse_ip" value="<?php echo $_SERVER["SERVER_ADDR"]; ?>" readonly >
</div>

<div class="box">
   <button type="button" class="btn btn-primary" onClick="nouveau_pad();">Nouveau pad</button>
</div>


<div class="box">
   <select class="btn btn-info" id="pads" onchange="selectPad();" placeholder="Ouvrir...">
   <option value="">Ouvrir...</option>
   <?php 
   foreach ($listePads as $pad):
      $pad_id = $pad->pad_id;
      $createur = $pad->createur;
      echo '<option value="'.$pad_id.'">'.$pad_id.' - '.$createur.'</option>';
   endforeach;
   ?>
   </select>
</div>

<div id="partager" class="box">
   <select id="selection" multiple="multiple" class="btn btn-info" size="100" name="tab[]" onChange="partager_pad();"> 
   <?php
   foreach ($authors as $author):
      $code = $author->aut_id;
      $nom = $author->aut_name; 
      echo '<option value="'.$code.'" >'.$nom.'</option>';
   endforeach;
   ?>

   </select>
</div>

<div id="supprimer" class="box">
   <button type="button" class="btn btn-primary" onClick="supprimer_pad();">Supprimer</button>
</div>

<div id="administrer" class="box">
   <button type="button" class="btn btn-primary" onClick="administrer_app();">Administrer</button>
</div>



<div class="box hidden">
   <input type="hidden" id="requete" value="" size="150">
</div>

<div id="mypad" style="background-color:rgb(255,255,255);"></div>

</div>

</body>
</html>
