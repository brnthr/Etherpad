window.addEventListener("resize", function(e) {
   resizeTheFrame('iFrame1');
   //alert("coucou 2");
});

window.addEventListener('DOMContentLoaded', function(e) {
    resizeTheFrame('iFrame1');
    //alert("coucou 1");
} );



function resizeTheFrame(le_iframe) {
   var w = window.innerWidth;
   var h = window.innerHeight;
   document.getElementById(le_iframe).height = h-100;
   document.getElementById(le_iframe).width = w-50;
}

function selectPad() {
   var pad_id = document.getElementById("pads").value;

   var ip = document.getElementById("adresse_ip").value;

   var src = "http://"+ip+":9001/p/"+pad_id;
   src += "?showChat=false&showLineNumbers=true";
   src = " src='"+src+"' ";

   var id = " id='iFrame1' ";

   var iframe = "<iframe "+ id + src+"></iframe>";
   document.getElementById('mypad').innerHTML = iframe;

   resizeTheFrame('iFrame1');


   var ip = document.getElementById("adresse_ip").value;
   var createur = document.getElementById("aut_id").value;
   var url = 'http://'+ip+'/etherpad/zapi.php?action=ouvrirPad&pad_id='+pad_id+'&aut_id='+createur;
   //alert(url);

   $.ajax({
      cache: false,
      url: url, /*"mapp.php?action=ouvrirPad",*/
      dataType: "json",
      type: "GET",
      data: "", /*"action=ouvrirPad&nompad="+pad_id,*/
      success: function(json) {
         console.log(json);

         for (var i in json) {
            //alert("coucou");
            
            //alert(json[i].aut_id+' - '+json[i].partage);
            //if (json[i].partage=='1') partage.push(json[i].aut_id);
            var createur = json[i].aut_id;

            var ocreateur = '#selection option[value="'+createur+'"]';
            //alert(ocreateur);
            //$(ocreateur).prop("disabled", true);
            $(ocreateur).attr('disabled', 'disabled');

            if (json[i].partage=='1') 
               $('#selection').multiselect('select', [json[i].aut_id], true);
            else
               $('#selection').multiselect('deselect', [json[i].aut_id], false);

            if (json[i].createur=='1') {
               ////// alert([json[i].aut_id]+' est createur');
               //$('#selection').attr("disabled", true); 
               //$('#selection option:selected').attr('disabled', 'disabled');
               //$('#selection option[value="'+createur+'"]').attr("disabled", true);
               //$('#selection option[value="'+createur+'"]').attr('disabled', 'disabled');

                
             
            }

            else {
               ///////alert([json[i].aut_id]+' est non createur');
            }

            combinaison_menu();
            


         }
         //$('#selection').multiselect('select', ['a.xx', 'a.zk'], true);
         //$('#selection').multiselect('select', partage, true);



      },
      error: function(reponse, statut) {
         alert('Erreur: '+reponse.status);
      }

   });
   
}

function nouveau_pad() {
   var apikey = 'e0ef80f2d50032a393ec8f6c75bcd117dfc9dccccf1a59784ec2279616f64934';
   var groupe = 'g.ui9UMSCoZ0LsukLc';
   //alert('hi');

   var pad_id = prompt("Entrez le nom du nouveau pad", "MyPads"); 
   if (pad_id != null) { 
      //alert(nompad);
   } 
   else {
      alert("Vous n\'avez rien saisie");
      return;
   }

/*
   var o = new Option(nompad, "value");
   /// jquerify the DOM object 'o' so we can use the html method
   $(o).html(nompad);
   $("#pads").append(o);
*/
   var o = '<option value="'+pad_id+'" selected>'+pad_id+' - 1</option>'
   $('#pads').append(o);
   // $("#pads").val(o);

   $("#supprimer").show();

   var ip = document.getElementById("adresse_ip").value;
   var createur = document.getElementById("aut_id").value;
   var url = 'http://'+ip+'/etherpad/zapi.php?action=creerPad&pad_id='+pad_id+'&aut_id='+createur;
   //alert(url);

   $.ajax({
      cache: false,
      url: url, /*"mapp.php?action=creerPad",*/
      dataType: "html",
      type: "GET",
      data: "",
      success: function(retour) {
         console.log(retour);
         var ret = JSON.parse(retour);
         //alert(ret);

         var result = ret.localeCompare("ok");  // 0: =; -1: !=
         //alert("result: "+result);

         if (result==0) {
            
            var src = "";
            //src += "http://"+ip+":9001/1/createGroupPad?apikey="+apikey+"&groupID="+groupe+"&padName="+nompad+"&text=Bienvenue au nouveau pad";
            //src += "http://"+ip+":9001/1/createPad?apikey="+apikey+"&padName="+nompad+"&text=Bienvenue au nouveau pad";

            var src = "http://"+ip+":9001/p/"+pad_id;
            src += "?showChat=false&showLineNumbers=true";

            document.getElementById("requete").value = src;
            src = " src='"+src+"' ";
            //alert(src);

            var id = " id='iFrame1' ";
            //alert(id);

            var iframe = "<iframe "+ id + src+"></iframe>";
            //alert(iframe);

            //document.getElementById('iFrame1').src = src;
            document.getElementById('mypad').innerHTML = iframe;

            resizeTheFrame('iFrame1');
         }
         else {
            alert("Pad existe déjà! \n"+retour);
         }

      },
      error: function(reponse, statut) {
         alert('Erreur: '+reponse.status);
      }

   });
}


function supprimer_pad() {
   //alert("supprimer pad");
   var pad_id = document.getElementById("pads").value;
   //alert(pad_id);

   var donnees = "pad_id="+pad_id;
   //alert(donnees);

   var ip = document.getElementById("adresse_ip").value;
   var url = 'http://'+ip+'/etherpad/zapi.php?action=supprimerPad&pad_id='+pad_id;

   if (confirm("Etes-vous sûr de vouloir supprimer le pad : "+pad_id+ " ?")) {

      $.ajax({
         url: url, /*"mapp.php?action=supprimerPad&pad_id="+pad_id,*/
         type: "GET",
         data: donnees,
         success: function(retour) {
            alert("success: "+retour);
            //$("#"+dom_id).remove();

            document.getElementById('mypad').innerHTML = "";
            $('#selection').multiselect('refresh');

            $('#selection').multiselect('deselectAll', true);

            var opt = "#pads option[value='"+pad_id+"']";
            $(opt).remove();

            $("#partager").hide();
            $("#supprimer").hide();

         },
         error: function(reponse, statut) {
            alert('Erreur: '+reponse.status);
         }
      });

   }

}

function administrer_app() {
   var ip = document.getElementById("adresse_ip").value;

   var src = "http://"+ip+"/etherpad/admin/v_adminUsers.php";
   src = " src='"+src+"' ";
   //alert(src);

   var id = " id='iFrame1' ";
   //alert(id);

   var iframe = "<iframe "+ id + src+"></iframe>";
   //alert(iframe);

   document.getElementById('mypad').innerHTML = iframe;

   resizeTheFrame('iFrame1');
}


function partager_pad() {
   //alert("coucou");

   var createur = document.getElementById("aut_id").value;

   var pad_id = document.getElementById("pads").value;
   //alert(pad_id);


   var result = pad_id.localeCompare("");  // 0: =; -1: !=
   if (result==0) {
      alert("Il n'y a pas de pad à partager!");
      return;
   }

   var selected = $("#selection option:selected");
   var aut_id="";
   selected.each(function() {
      aut_id += $(this).val()+";";
   });

   var donnees = "&pad_id="+pad_id+"&aut_id="+aut_id+"&createur="+createur;
   //alert(donnees);

   var ip = document.getElementById("adresse_ip").value;	
   var url = 'http://'+ip+'/etherpad/zapi.php?action=partagerPad';
   $.ajax({
      cache: false,
      url: url,
      dataType: "json",
      type: "GET",
      data: donnees,
      success: function(retour) {
         console.log(retour);
         if (retour!="") alert(retour);
         //document.location.reload();
         combinaison_menu();


      },
      error: function(reponse, statut) {
         alert('Erreur: '+reponse.status);
      }

   });


   //alert("fin");
}


function combinaison_menu() {
   //var pad_id = document.getElementById("pads").value;
   var val = $('#pads').val();
   //alert(val);

   var txt = $("#pads option:selected").html();
   //alert(txt);
   var res1 = txt.split("-"); //.trim();
   var res = res1[1].trim();
   //alert(">"+res+"<");



   if (val=="") {
      //$("#administrer").hide();
      $("#partager").hide();
      $("#supprimer").hide();
   }
   else {
      //$("#administrer").show();
      
      if (res=="1") {
         //alert("--> 1");
         $("#partager").show();
         $("#supprimer").show(); 
      }
      else {
         //alert("--> 0");
         $("#partager").hide();
         $("#supprimer").hide();
      }
   }

/*
   var profil = $('#profil').val();
   alert("profil: "+profil);

   if (profil=="1") {
      $("#administrer").show();
   }
   else {
      $("#administrer").hide();
   }
*/

}

$(document).ready(function() {

   //combinaison_menu();
   $("#partager").hide();
   $("#supprimer").hide();

   var profil = $('#profil').val();
   //alert("profil: "+profil);

   if (profil=="1") {
      $("#administrer").show();
   }
   else {
      $("#administrer").hide();
   }

   //$("#selection").append( '<option value="aaa">aaa</option>' );

   $("#selection").multiselect({
      autoOpen: true,
      includeSelectAllOpen: true,
      includeSelectAllOption: true,
      maxHeight: 300,
      minHeight: 200,
      minWidth:'400px',
      dropRight: true,
      numberDisplayed: 5,
      enableFiltering: true,
      enableCaseInsensitiveFiltering: true, 
      autoOpen: "true",

      nonSelectedText:'Parteger avec',
      
      onChange: function(option, checked, select) {
	     //if (checked===true) alert("checked");      
      },
      beforeclose: function(event, ui) {
         alert("beforeClose");
         return false;
      },
      onInitialized: function(select, container) {
         alert("Initialized");
      },
      onDropDownShow: function(event) {
         alert("onDropDownShow");
      },
      close: function(event, ui) {
         alert("close");
         $("#selection").multiselect("open");
      },
   });

   $( "#selection option:first-child" ).attr("disabled","disabled");


});

