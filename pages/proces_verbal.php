<?php
include("../includes/config.php");
include("../includes/header.php");
include("../includes/sidebar.php");

$result = mysqli_query($conn,"
SELECT s.*, e.nom, e.prenoms, e.niveau, e.parcours, o.design AS organisme
FROM soutenir s
LEFT JOIN etudiant e ON s.matricule = e.matricule
LEFT JOIN organisme o ON s.idorg = o.idorg
ORDER BY s.id DESC
");
?>

<div class="content">
    <h2 class="mb-4">Procès-verbal</h2>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5>Choisir une soutenance</h5>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Étudiant</th>
                        <th>Organisme</th>
                        <th>Note</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                <?php while($s = mysqli_fetch_assoc($result)){ ?>
                    <tr>
                        <td><?= $s['nom']; ?> <?= $s['prenoms']; ?></td>
                        <td><?= $s['organisme']; ?></td>
                        <td><?= $s['note']; ?>/20</td>
                        <td><?= $s['date_soutenance']; ?></td>
                        <td>
                            <a class="btn btn-primary btn-sm" href="pv_print.php?id=<?= $s['id']; ?>">
                                Voir / Imprimer PV
                            </a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>