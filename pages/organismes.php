<?php
include("../includes/config.php");

if(isset($_POST['ajouter'])){
    $design = $_POST['design'];
    $lieu = $_POST['lieu'];

    mysqli_query($conn,"INSERT INTO organisme(design, lieu) VALUES('$design','$lieu')");
    header("Location: organismes.php");
    exit();
}

if(isset($_GET['supprimer'])){
    $idorg = $_GET['supprimer'];

    mysqli_query($conn,"DELETE FROM organisme WHERE idorg='$idorg'");
    header("Location: organismes.php");
    exit();
}

if(isset($_POST['modifier'])){
    $idorg = $_POST['idorg'];
    $design = $_POST['design'];
    $lieu = $_POST['lieu'];

    mysqli_query($conn,"UPDATE organisme SET
        design='$design',
        lieu='$lieu'
        WHERE idorg='$idorg'
    ");

    header("Location: organismes.php");
    exit();
}

$result = mysqli_query($conn,"SELECT * FROM organisme ORDER BY design ASC");

include("../includes/header.php");
include("../includes/sidebar.php");
?>

<div class="content">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestion des Organismes</h2>

        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajoutModal">
            + Nouveau
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5>Liste des Organismes</h5>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Désignation</th>
                        <th>Lieu</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                <?php while($o = mysqli_fetch_assoc($result)){ ?>
                    <tr>
                        <td><?= $o['idorg']; ?></td>
                        <td><?= $o['design']; ?></td>
                        <td><?= $o['lieu']; ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modif<?= $o['idorg']; ?>">
                                Modifier
                            </button>

                            <a href="organismes.php?supprimer=<?= $o['idorg']; ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Supprimer cet organisme ?')">
                                Supprimer
                            </a>
                        </td>
                    </tr>

                    <div class="modal fade" id="modif<?= $o['idorg']; ?>">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Modifier Organisme</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <input type="hidden" name="idorg" value="<?= $o['idorg']; ?>">

                                        <label>Désignation</label>
                                        <input type="text" name="design" class="form-control mb-2" value="<?= $o['design']; ?>" required>

                                        <label>Lieu</label>
                                        <input type="text" name="lieu" class="form-control mb-2" value="<?= $o['lieu']; ?>" required>
                                    </div>

                                    <div class="modal-footer">
                                        <button class="btn btn-warning" name="modifier">Modifier</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<div class="modal fade" id="ajoutModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Nouvel Organisme</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label>Désignation</label>
                    <input type="text" name="design" class="form-control mb-2" required>

                    <label>Lieu</label>
                    <input type="text" name="lieu" class="form-control mb-2" required>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-success" name="ajouter">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>