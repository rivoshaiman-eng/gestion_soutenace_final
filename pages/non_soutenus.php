<?php
include("../includes/config.php");
include("../includes/header.php");
include("../includes/sidebar.php");

$result = mysqli_query($conn,"
SELECT e.*
FROM etudiant e
LEFT JOIN soutenir s ON e.matricule = s.matricule
WHERE s.matricule IS NULL
ORDER BY e.nom ASC
");
?>

<div class="content">

    <h2 class="mb-4">Étudiants Non Soutenus</h2>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5>Liste des étudiants qui n'ont pas encore soutenu</h5>
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
                        <th>Email</th>
                    </tr>
                </thead>

                <tbody>
                <?php while($e = mysqli_fetch_assoc($result)){ ?>
                    <tr>
                        <td><?= $e['matricule']; ?></td>
                        <td><?= $e['nom']; ?></td>
                        <td><?= $e['prenoms']; ?></td>
                        <td><?= $e['niveau']; ?></td>
                        <td><?= $e['parcours']; ?></td>
                        <td><?= $e['adr_email']; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>