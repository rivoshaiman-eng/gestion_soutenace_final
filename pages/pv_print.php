<?php
include("../includes/config.php");

$id = $_GET['id'];

$sql = mysqli_query($conn,"
SELECT s.*, e.nom, e.prenoms, e.niveau, e.parcours,
o.design AS organisme
FROM soutenir s
LEFT JOIN etudiant e ON s.matricule=e.matricule
LEFT JOIN organisme o ON s.idorg=o.idorg
WHERE s.id='$id'
");

$pv = mysqli_fetch_assoc($sql);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
<meta charset="UTF-8">
<title>Procès Verbal</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    margin:40px;
    font-family:Arial;
}

h2{
    text-align:center;
    margin-bottom:30px;
}

table{
    margin-top:20px;
}

@media print{

button{
display:none;
}

}

</style>

</head>

<body>

<div class="container">

<h2>PROCES VERBAL DE SOUTENANCE</h2>

<table class="table table-bordered">

<tr>
<th width="35%">Matricule</th>
<td><?= $pv['matricule']; ?></td>
</tr>

<tr>
<th>Nom et Prénoms</th>
<td><?= $pv['nom']; ?> <?= $pv['prenoms']; ?></td>
</tr>

<tr>
<th>Niveau</th>
<td><?= $pv['niveau']; ?></td>
</tr>

<tr>
<th>Parcours</th>
<td><?= $pv['parcours']; ?></td>
</tr>

<tr>
<th>Organisme</th>
<td><?= $pv['organisme']; ?></td>
</tr>

<tr>
<th>Année Universitaire</th>
<td><?= $pv['annee_univ']; ?></td>
</tr>

<tr>
<th>Date Soutenance</th>
<td><?= $pv['date_soutenance']; ?></td>
</tr>

<tr>
<th>Président</th>
<td><?= $pv['president']; ?></td>
</tr>

<tr>
<th>Examinateur</th>
<td><?= $pv['examinateur']; ?></td>
</tr>

<tr>
<th>Rapporteur Interne</th>
<td><?= $pv['rapporteur_int']; ?></td>
</tr>

<tr>
<th>Rapporteur Externe</th>
<td><?= $pv['rapporteur_ext']; ?></td>
</tr>

<tr>
<th>Note Finale</th>
<td><strong><?= $pv['note']; ?>/20</strong></td>
</tr>

</table>

<div class="text-center mt-4">

<button onclick="window.print()" class="btn btn-success">
🖨️ Imprimer le Procès-Verbal
</button>

</div>

</div>

</body>
</html>