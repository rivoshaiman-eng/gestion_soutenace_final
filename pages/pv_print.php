<?php
include("../includes/config.php");

$id = $_GET['id'];

$sql = mysqli_query($conn,"
SELECT s.*, e.nom, e.prenoms, e.niveau, e.parcours
FROM soutenir s
LEFT JOIN etudiant e ON s.matricule = e.matricule
WHERE s.id='$id'
");

$pv = mysqli_fetch_assoc($sql);

function noteLettre($note){
    if($note == 18) return "dix-huit sur vingt";
    if($note == 17) return "dix-sept sur vingt";
    if($note == 16) return "seize sur vingt";
    if($note == 15) return "quinze sur vingt";
    if($note == 14) return "quatorze sur vingt";
    if($note == 13) return "treize sur vingt";
    if($note == 12) return "douze sur vingt";
    if($note == 11) return "onze sur vingt";
    if($note == 10) return "dix sur vingt";
    return $note." sur vingt";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Procès-verbal de soutenance</title>

<style>
body{
    font-family: "Times New Roman", serif;
    background:#eee;
    margin:0;
}

.page{
    width:21cm;
    min-height:29.7cm;
    margin:20px auto;
    background:white;
    padding:3cm 2.5cm;
    box-sizing:border-box;
    font-size:18px;
    line-height:1.55;
}

.center{
    text-align:center;
}

.title{
    font-weight:bold;
    font-size:22px;
    margin-top:35px;
}

.big{
    font-size:22px;
    font-weight:bold;
}

.underline{
    text-decoration:underline;
}

.jury p{
    margin:14px 0;
}

.signature{
    margin-top:70px;
    display:flex;
    justify-content:space-between;
}

.print-btn{
    text-align:center;
    margin:20px;
}

button{
    padding:10px 20px;
    font-size:16px;
    cursor:pointer;
}

@media print{
    body{
        background:white;
    }

    .page{
        margin:0;
        width:100%;
        min-height:100%;
        box-shadow:none;
    }

    .print-btn{
        display:none;
    }
}
</style>
</head>

<body>

<div class="print-btn">
    <button onclick="window.print()">🖨️ Imprimer le procès-verbal</button>
</div>

<div class="page">

    <div class="center">
        <div class="title">PROCES VERBAL</div>

        <p class="big">
            SOUTENANCE DE FIN D’ETUDES POUR L’OBTENTION DU DIPLOME DE LICENCE PROFESSIONNELLE
        </p>

        <p><strong>Mention :</strong> Informatique</p>
        <p><strong>Parcours :</strong> <?= $pv['parcours']; ?></p>
    </div>

    <p style="margin-top:45px;">
        Mr/Mlle <strong><?= strtoupper($pv['nom']); ?></strong> <?= $pv['prenoms']; ?>
    </p>

    <p>
        a soutenu publiquement son mémoire de fin d’études pour l’obtention du diplôme de Licence professionnelle
    </p>

    <p>
        Après la délibération, la commission des membres du Jury a attribué la note de
        <strong><?= $pv['note']; ?>/20</strong>
        (<?= noteLettre($pv['note']); ?>)
    </p>

    <p class="underline" style="margin-top:35px;">
        Membres du Jury
    </p>

    <div class="jury">
        <p><strong>Président :</strong> <?= $pv['president']; ?></p>
        <p><strong>Examinateur :</strong> <?= $pv['examinateur']; ?></p>
        <p><strong>Rapporteurs :</strong> <?= $pv['rapporteur_int']; ?></p>
        <p style="margin-left:135px;"><?= $pv['rapporteur_ext']; ?></p>
    </div>

</div>

</body>
</html>