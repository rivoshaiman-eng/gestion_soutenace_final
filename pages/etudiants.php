<?php
include("../includes/config.php");

/* AJOUTER */
if(isset($_POST['ajouter'])){
    $matricule = $_POST['matricule'];
    $nom = $_POST['nom'];
    $prenoms = $_POST['prenoms'];
    $niveau = $_POST['niveau'];
    $parcours = $_POST['parcours'];
    $email = $_POST['adr_email'];

    mysqli_query($conn, "INSERT INTO etudiant 
    VALUES('$matricule','$nom','$prenoms','$niveau','$parcours','$email')");

    header("Location: etudiants.php");
    exit();
}

/* SUPPRIMER */
if(isset($_GET['supprimer'])){
    $matricule = $_GET['supprimer'];
    mysqli_query($conn, "DELETE FROM etudiant WHERE matricule='$matricule'");
    header("Location: etudiants.php");
    exit();
}

/* MODIFIER */
if(isset($_POST['modifier'])){
    $matricule = $_POST['matricule'];
    $nom = $_POST['nom'];
    $prenoms = $_POST['prenoms'];
    $niveau = $_POST['niveau'];
    $parcours = $_POST['parcours'];
    $email = $_POST['adr_email'];

    mysqli_query($conn, "UPDATE etudiant SET
        nom='$nom',
        prenoms='$prenoms',
        niveau='$niveau',
        parcours='$parcours',
        adr_email='$email'
        WHERE matricule='$matricule'
    ");

    header("Location: etudiants.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM etudiant ORDER BY nom ASC");

include("../includes/header.php");
include("../includes/sidebar.php");
?>

<div class="content">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestion des Étudiants</h2>

        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajoutModal">
            + Nouveau
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5>Liste des Étudiants</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Matricule</th>
                            <th>Nom</th>
                            <th>Prénoms</th>
                            <th>Niveau</th>
                            <th>Parcours</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)){ ?>
                        <tr>
                            <td><?= $row['matricule']; ?></td>
                            <td><?= $row['nom']; ?></td>
                            <td><?= $row['prenoms']; ?></td>
                            <td><?= $row['niveau']; ?></td>
                            <td><?= $row['parcours']; ?></td>
                            <td><?= $row['adr_email']; ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modif<?= $row['matricule']; ?>">
                                    Modifier
                                </button>

                                <a href="etudiants.php?supprimer=<?= $row['matricule']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Supprimer cet étudiant ?')">
                                    Supprimer
                                </a>
                            </td>
                        </tr>

                        <!-- MODAL MODIFICATION -->
                        <div class="modal fade" id="modif<?= $row['matricule']; ?>">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Modifier étudiant</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <input type="hidden" name="matricule" value="<?= $row['matricule']; ?>">

                                            <label>Matricule</label>
                                            <input type="text" class="form-control mb-2" value="<?= $row['matricule']; ?>" disabled>

                                            <label>Nom</label>
                                            <input type="text" name="nom" class="form-control mb-2" value="<?= $row['nom']; ?>" required>

                                            <label>Prénoms</label>
                                            <input type="text" name="prenoms" class="form-control mb-2" value="<?= $row['prenoms']; ?>" required>

                                            <label>Niveau</label>
                                            <select name="niveau" class="form-select mb-2">
                                                <option <?= $row['niveau']=="L1"?"selected":"" ?>>L1</option>
                                                <option <?= $row['niveau']=="L2"?"selected":"" ?>>L2</option>
                                                <option <?= $row['niveau']=="L3"?"selected":"" ?>>L3</option>
                                                <option <?= $row['niveau']=="M1"?"selected":"" ?>>M1</option>
                                                <option <?= $row['niveau']=="M2"?"selected":"" ?>>M2</option>
                                            </select>

                                            <label>Parcours</label>
                                            <select name="parcours" class="form-select mb-2">
                                                <option <?= $row['parcours']=="GB"?"selected":"" ?>>GB</option>
                                                <option <?= $row['parcours']=="IG"?"selected":"" ?>>IG</option>
                                                <option <?= $row['parcours']=="SR"?"selected":"" ?>>SR</option>
                                            </select>

                                            <label>Email</label>
                                            <input type="email" name="adr_email" class="form-control mb-2" value="<?= $row['adr_email']; ?>">
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
</div>

<!-- MODAL AJOUT -->
<div class="modal fade" id="ajoutModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Nouvel étudiant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label>Matricule</label>
                    <input type="text" name="matricule" class="form-control mb-2" required>

                    <label>Nom</label>
                    <input type="text" name="nom" class="form-control mb-2" required>

                    <label>Prénoms</label>
                    <input type="text" name="prenoms" class="form-control mb-2" required>

                    <label>Niveau</label>
                    <select name="niveau" class="form-select mb-2">
                        <option>L1</option>
                        <option>L2</option>
                        <option>L3</option>
                        <option>M1</option>
                        <option>M2</option>
                    </select>

                    <label>Parcours</label>
                    <select name="parcours" class="form-select mb-2">
                        <option>GB</option>
                        <option>IG</option>
                        <option>SR</option>
                    </select>

                    <label>Email</label>
                    <input type="email" name="adr_email" class="form-control mb-2">
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