<?php
	error_reporting(E_ALL);
	ini_set('display_errors','On');

 // Variable de connexion

$test = "host=localhost port=5432 dbname=bdd_eval_poireau user=admin password=admin";

//On se connecte a la bdd

$connect = pg_connect($test);

    if($connect) {
       echo 'connected';
    } else {
        echo 'there has been an error connecting';
    } 


// Afficher le nom et la quantité des légumes



$rqSt = pg_query($connect, 'SELECT pro_nom, sum(st) 
FROM (SELECT pro_leg,pro_nom, -sto_qte as st FROM stock INNER JOIN produit ON pro_id=spro_id WHERE sto_pert = True UNION SELECT pro_leg, pro_nom, sto_qte as st FROM stock INNER JOIN produit ON pro_id=spro_id WHERE sto_pert = False UNION SELECT pro_leg, pro_nom, -con_qte as st FROM contenu INNER JOIN produit ON cpro_id = pro_id) as s GROUP BY pro_leg,pro_nom ORDER BY  pro_leg,pro_nom;');

$liste="";
$stock="";

while ($liste = pg_fetch_assoc($rqSt)){
$stock.= "<tr><td class='name-row'>".$liste['pro_nom']."</td><td>".$liste['sum']."</td></tr>";
}



// Afficher les produits dont le stock est inférieur à 5

$rqAlerte = pg_query($connect,'SELECT pro_nom, sto_qte FROM produit INNER JOIN stock
ON pro_id = spro_id WHERE sto_qte <5;');

$liste="";
$Alerte="";

while ($liste = pg_fetch_assoc($rqAlerte)){
$Alerte.= "<tr><td class='name-row'>".$liste['pro_nom']."</td><td>".$liste['sto_qte']."</td></tr>";
}

	


//imprimer une fiche récapitulative contenant pour chaque fruit et chaque légume, la quantité restante, la quantité jetée et la quantité vendue sur les 7 derniers jours. La liste doit être triée par catégorie (fruit ou légume) et par ordre alphabétique

//A FAIRE


// Choix des produits (ventes et renouvellement du stock)

$rqAjStock = pg_query($connect,'SELECT pro_nom FROM produit ORDER BY  pro_nom ASC;');

$liste="";
$option="";

while ($liste = pg_fetch_assoc($rqAjStock)){
$option.= "<option value='".$liste['pro_nom']."'>".$liste['pro_nom']."</option>";
}


// Choix des villes

$rqAjVille = pg_query($connect,'SELECT com_nom FROM commune ORDER BY  com_nom ASC;');

$liste="";
$optionVille="";

while ($liste = pg_fetch_assoc($rqAjVille)){
$optionVille.= "<option value='".$liste['com_nom']."'>".$liste['com_nom']."</option>";
}



// Recapitulatif des ventes par commune

$rqRecap = pg_query($connect,'SELECT com_nom, pro_leg, SUM(con_qte) FROM commune INNER JOIN vente ON com_code = vcom_code INNER JOIN contenu ON cven_id = ven_id INNER JOIN produit ON cpro_id = pro_id GROUP BY com_nom, pro_leg, con_qte ORDER BY con_qte DESC LIMIT 5;');

$liste="";
$recap="";

while ($liste = pg_fetch_assoc($rqRecap)){
$recap.= "<tr><td class='name-row'>".$liste['com_nom']."</td><td>".$liste['pro_leg']."</td><td>".$liste['sum']."</td></tr>";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="ie=edge">
   <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
      crossorigin="anonymous">
   <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
   <link rel="stylesheet" href="style.css">
   <title>Document</title>
</head>

<body>
	<p id="successConnection"></p>
   <!-- Menu -->
   <section class="navbar-top container-fluid">
      <div class="row">
         <div class="col-md-12 menu-logo">
            <img class="logo" src="/img/logo.png" alt="Logo de FaisPasLPoireau">
         </div>
      </div>
   </section>
   <!-- Fin menu  -->


   <!-- ///////// Listes à gauche /////////////////-->
   
   <section class="div-left">
      <div class="table-items col-md-3 stock">
      
      <h5><i class="fa fa-print fa-1x"></i>&nbsp;Stock</h5>         
      
         <!-- Boutons pour sélectionner quel tableau afficher -->
         <ul class="nav nav-tabs">
            <li class="nav-item">
               <a class="nav-link active" data-toggle="tab" href="#home">Tous</a>
            </li>
            <li class="nav-item">
               <a class="nav-link" data-toggle="tab" href="#menu1">Fruits</a>
            </li>
            <li class="nav-item">
               <a class="nav-link" data-toggle="tab" href="#menu2">Légumes</a>
            </li>
         </ul>
         <!-- Tableaux -->
         <div class="tab-content">
            <!-- Tableau Tous -->
            <div class="tab-pane active table-responsive" id="home">
               <table class="table">
                  <thead>
                     <tr>
                        <th id="name-column">Nom</th>
                        <th>Qté</th>
                     </tr>
                  </thead>
                  <tbody>

					<?php echo $stock; ?>

                  </tbody>
               </table>
            </div>
            <!-- Tableau Fruits -->
            <div class="tab-pane table-responsive" id="menu1">...</div>
            <!-- Tableau Légumes -->
            <div class="tab-pane table-responsive" id="menu2">...</div>
         </div>
      </div>
      <!-- ///////// FIN Listes à gauche /////////////////-->


      <!-- ///////// DIVs colonne nouvelle vente et colonne ajouter/supprimer/géomarketing  /////////////////-->
      <div class="col-md-9">
         <div class="row">

            <!-- Nouvelle vente -->
            <div class="col-md-7">
               <div class="container new-sale">
                  <h5>Nouvelle vente</h5>
                  <!-- formulaire -->
                  <form action="">

                     <div class="row">
                        <div class="col-md-3">
                           <div class="form-group">
                              <label for="time">Heure</label>
                              <input type="number" class="form-control" id="" placeholder="00:00" disabled>
                           </div>
                        </div>
                        <div class="col-md-9">
                           <div class="form-group">
                              <label for="villes">Ville</label>
                              <!-- Menu déroulant -->
                              <select class="form-control" name="" id="">
								<?php echo $optionVille; ?>
                              </select>
                           </div>
                        </div>
                     </div>

                     <div class="row">
                        <div class="col-md-3">
                           <div class="form-group">
                              <label for="quantityToAdd">Quantité</label>
                              <input type="number" class="form-control" id="" placeholder="">
                           </div>
                        </div>
                        <div class="col-md-9">
                           <div class="form-group">
                              <label for="itemToAdd">Fruit/Légume</label>
                              <!-- Menu déroulant -->
                              <select class="form-control" name="" id="">
								<?php echo $option; ?>
                              </select>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-3">
                           <div class="form-group">
                              <input type="number" class="form-control" id="" placeholder="">
                           </div>
                        </div>
                        <div class="col-md-9">
                           <div class="form-group">
                              <!-- Menu déroulant -->
                              <select class="form-control" name="" id="">
                               <?php echo $option; ?>
                              </select>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-3">
                           <div class="form-group">
                              <input type="number" class="form-control" id="" placeholder="">
                           </div>
                        </div>
                        <div class="col-md-9">
                           <div class="form-group">
                              <!-- Menu déroulant -->
                              <select class="form-control" name="" id="">
								<?php echo $option; ?>
                              </select>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-3">
                           <div class="form-group">
                              <input type="number" class="form-control" id="" placeholder="">
                           </div>
                        </div>
                        <div class="col-md-9">
                           <div class="form-group">
                              <!-- Menu déroulant -->
                              <select class="form-control" name="" id="">
								<?php echo $option; ?>
                              </select>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-3">
                           <div class="form-group">
                              <input type="number" class="form-control" id="" placeholder="">
                           </div>
                        </div>
                        <div class="col-md-9">
                           <div class="form-group">
                              <!-- Menu déroulant -->
                              <select class="form-control" name="" id="">
								<?php echo $option; ?>
                              </select>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-3">
                           <div class="form-group">
                              <input type="number" class="form-control" id="" placeholder="">
                           </div>
                        </div>
                        <div class="col-md-9">
                           <div class="form-group">
                              <!-- Menu déroulant -->
                              <select class="form-control" name="" id="">
								<?php echo $option; ?>
                              </select>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-3">
                           <div class="form-group">
                              <input type="number" class="form-control" id="" placeholder="">
                           </div>
                        </div>
                        <div class="col-md-9">
                           <div class="form-group">
                              <!-- Menu déroulant -->
                              <select class="form-control" name="" id="">
								<?php echo $option; ?>
                              </select>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-3">
                           <div class="form-group">
                              <input type="number" class="form-control" id="" placeholder="">
                           </div>
                        </div>
                        <div class="col-md-9">
                           <div class="form-group">
                              <!-- Menu déroulant -->
                              <select class="form-control" name="" id="">
								<?php echo $option; ?>
                              </select>
                           </div>
                        </div>
                     </div>
                     <br>
                     <div class="action-buttons">
                        <button class="btn btn-danger" type="submit">Annuler</button>
                        <button class="btn btn-success" type="submit">Valider</button>
                     </div>

                  </form>
               </div>
            </div>

            <!-- Ajouter/Supprimer/Géomarketing -->
            <div class="col-md-5 right-panel">

               <!-- Ajouter - Nouvelle entrée dans le stock -->
               <h5>Nouvelle entrée dans le stock</h5>
               <!-- formulaire -->
               <form action="">
                  <div class="container">
                     <div class="row">

                        <div class="col-md-4">
                           <div class="form-group">
                              <label for="quantityToAdd">Quantité</label>
                              <input type="number" class="form-control" id="" placeholder="">
                           </div>
                        </div>
                        <div class="col-md-8">
                           <div class="form-group">
                              <label for="itemToAdd">Fruit/Légume</label>
                              <!-- Menu déroulant -->
                              <select class="form-control" name="" id="">
								<?php echo $option; ?>
                              </select>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="action-buttons">
                     <button type="submit" class="btn btn-success">Ajouter</button>
                  </div>
               </form>

               <!-- Supprimer - Quantité perdue/jetée -->
               <h5>Quantité perdue/jetée</h5>
               <!-- formulaire -->
               <form action="">
                  <div class="container">
                     <div class="row">

                        <div class="col-md-4">
                           <div class="form-group">
                              <label for="quantityToRemove">Quantité</label>
                              <input type="number" class="form-control" id="" placeholder="">
                           </div>
                        </div>
                        <div class="col-md-8">
                           <div class="form-group">
                              <label for="itemToRemove">Fruit/Légume</label>
                              <!-- Menu déroulant -->
                              <select class="form-control" name="" id="">
								<?php echo $option; ?>
                              </select>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="action-buttons">
                     <button type="submit" class="btn btn-danger">Supprimer</button>
                  </div>
               </form>

               <!-- Geomarketing -->
               <div class="row">
                  <div class="col-md-9 geo-title">
                     <h5>
                        <i class="fa fa-print fa-1x icon-menu">&nbsp;</i>Géomarketing</h5>

                  </div>
                  <div class="col-md-3 geo">


                  </div>
               </div>
               <table class="table">
                  <thead>
                     <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nom</th>
                     </tr>
                  </thead>
                  <tbody>
					<?php echo $recap; ?>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
      <!-- ///////// FIN DIVs colonne nouvelle vente et colonne ajouter/supprimer/géomarketing  /////////////////-->

   </section>

<!-- Good luck -->

   <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
      crossorigin="anonymous"></script>
</body>

</html>