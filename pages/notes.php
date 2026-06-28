<?php
include("../includes/config.php");
include("../includes/header.php");
include("../includes/sidebar.php");

$result = mysqli_query($conn,"
SELECT s.*, e.nom, e.prenoms, e.niveau, e.parcours
FROM soutenir s
LEFT JOIN etudiant e ON s.matricule = e.matricule
ORDER BY s.note DESC
");

function mention($note){
    if($note < 10) return "Ajourné";
    if($note <= 11) return "Passable";
    if($note <= 13) return "Assez Bien";
    if($note <= 15) return "Bien";
    return "Très Bien";
}
?>

<div class="content">

    <h2 class="mb-4">Liste des Notes</h2>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5>Notes des étudiants soutenus</h5>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Matricule</th>
                        <th>Nom</th>
                        <th>Prénoms</th>
                        <th>Niveau</th>
                        <th>Parcours</th>
                        <th>Année Univ.</th>
                        <th>Note</th>
                        <th>Mention</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while($n = mysqli_fetch_assoc($result)){ ?>
                    <tr>
                        <td><?= $n['matricule']; ?></td>
                        <td><?= $n['nom']; ?></td>
                        <td><?= $n['prenoms']; ?></td>
                        <td><?= $n['niveau']; ?></td>
                        <td><?= $n['parcours']; ?></td>
                        <td><?= $n['annee_univ']; ?></td>
                        <td><strong><?= $n['note']; ?>/20</strong></td>
                        <td><?= mention($n['note']); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>