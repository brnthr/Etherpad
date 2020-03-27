   
document.addEventListener('DOMContentLoaded', function () {
   window.myAppendGrid = new AppendGrid({
      element: document.getElementById("tblAppendGrid"),
         uiFramework: "bulma",
      iconFramework: "fontawesome5",
      caption: 'titre',
      initRows: 1,
      columns: [
         {
            name: "aut_id",
            display: "Id",
            displayCss: { "background-color": "#333333", "color": "#ffffff" }
         },

         {
            name: "aut_name",
            display: "Nom",
            displayCss: { "background-color": "#333333", "color": "#ffffff" }
         },

         {
            name: "aut_email",
            display: "Mail",
            displayCss: { "background-color": "#333333", "color": "#ffffff" },
            ctrlAttr: { placeholder: "Mouseover me!" }, 
         },

         {
            name: "aut_psw",
            display: "Password",
            displayCss: { "background-color": "#333333", "color": "#ffffff" } 
         },

         {
            name: "aut_role",
            display: "Rôle",
            displayCss: { "background-color": "#333333", "color": "#ffffff" },
            type: "select",
            ctrlOptions: [
               "",
               "Administrateur",
               "Auteur"] 
         }
      ],

      sectionClasses: {
         table: 'is-narrow is-fullwidth' 
      },

      beforeRowRemove: function(caller, rowIndex) {
         // Add confirmation before removing a row
         var rowValues = myAppendGrid.getRowValue(rowIndex+1);
         var nom = rowValues["aut_name"];
         var id = rowValues["aut_id"];
         alert("aut_name : " + rowValues["aut_name"]);
         if (confirm("Etes-vous sur de supprimer? " + nom)) {
            supprimerTab(id);
            return true;
         }
         else return false;
      }

   });


   document.getElementById("load").addEventListener("click", function () {
      myAppendGrid.load([
         {
            aut_id: "a.zk",
            aut_name: "Ziad",
            aut_email: "ziad.kachouh@gmail.com",
            aut_psw: "utec",
            aut_role: "Administrateur"
         },
         {
            aut_id: "a.toto",
            aut_name: "Toto",
            aut_email: "totogmail.com",
            aut_psw: "utec",
            aut_role: "Auteur"
         }
      ]);

   });


   document.getElementById("savedata").addEventListener("click", function () {
      //alert("sauvegarde...");
      // Get the values of second row (rowIndex = 1)
      //var rowValues = myAppendGrid.getRowValue(1);
      //alert("Value of Foo in second row is " + rowValues.foo);
      //alert("valeur de aut_name du row 1: " + rowValues["aut_name"]); // Alternate style

      var rowCount = myAppendGrid.getRowCount();
      //alert("There are " + rowCount + " row(s) inside the grid!");

      var data = myAppendGrid.getAllValue();
      //alert(data[0]["aut_name"]);
      //alert("fin de sauvegarde.");


      // Get row order, which is the array of unique index
      var rowOrder = myAppendGrid.getRowOrder();
      //alert("There are " + rowOrder.length + " row(s) in grid.");
      //alert("Unique index of the first row is " + rowOrder[0]);
      //alert("Unique index of the last row is " + rowOrder[rowOrder.length - 1]);

      for (var i=0; i<rowCount; i++) {
         if (data[i]["aut_id"]=="") continue;
         var row = 'action=sauverUsers';
         row += '&aut_id='+data[i]["aut_id"];
         row += '&aut_name='+data[i]["aut_name"];
         row += '&aut_email='+data[i]["aut_email"];
         row += '&aut_psw='+data[i]["aut_psw"];
         row += '&aut_role='+data[i]["aut_role"];
         sauverTab(row);
      }
   });

});


function chargerTab() {
   //alert("chargement...");
   $.ajax({
      url:'m_adminUsers.php',
      datatype:"application/json",
      type:'get',
      data: 'action=listeUsers', 
      success:function(data){
         //alert(data);
         console.log(data);
         //Mes donnees de php (array)
         var objJSON = JSON.parse( data);
         //$('#tblAppendGrid').appendGrid(objJSON);
         myAppendGrid.load(objJSON);
      },
      error: function(reponse, statut) {
         alert('Erreur: '+reponse.status);
      }
   });
}


function sauverTab(row) {
   //alert("sauvegarde: "+row);

   $.ajax({
      url:'m_adminUsers.php',
      datatype:"html",
      type:'get',
      data: row, 
      success:function(data){
         alert(data);
      },
      error: function(reponse, statut) {
         alert('Erreur: '+reponse.status);
      }
   });

}

function supprimerTab(rowId) {
   //alert("sauvegarde: "+row);

   var row = 'action=supprimerUser&aut_id='+rowId;

   $.ajax({
      url:'m_adminUsers.php',
      datatype:"html",
      type:'get',
      data: row, 
      success:function(data){
         alert(data);
      },
      error: function(reponse, statut) {
         alert('Erreur: '+reponse.status);
      }
   });

}


///////////////////////////////////////////////////////////
$(document).ready(function() {

   chargerTab();

});

