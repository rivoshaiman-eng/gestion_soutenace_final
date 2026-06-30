<?php
include("../includes/config.php");

if(isset($_POST['ajouter'])){
    $idprof = $_POST['idprof'];
    $nom = $_POST['nom'];
    $prenoms = $_POST['prenoms'];
    $civilite = $_POST['civilite'];
    $grade = $_POST['grade'];

    mysqli_query($conn,"INSERT INTO professeur VALUES('$idprof','$nom','$prenoms','$civilite','$grade')");
    header("Location: professeurs.php");
    exit();
}

if(isset($_GET['supprimer'])){
    $idprof = $_GET['supprimer'];
    mysqli_query($conn,"DELETE FROM professeur WHERE idprof='$idprof'");
    header("Location: professeurs.php");
    exit();
}

if(isset($_POST['modifier'])){
    $idprof = $_POST['idprof'];
    $nom = $_POST['nom'];
    $prenoms = $_POST['prenoms'];
    $civilite = $_POST['civilite'];
    $grade = $_POST['grade'];

    mysqli_query($conn,"UPDATE professeur SET
        nom='$nom',
        prenoms='$prenoms',
        civilite='$civilite',
        grade='$grade'
        WHERE idprof='$idprof'
    ");

    header("Location: professeurs.php");
    exit();
}

$result = mysqli_query($conn,"SELECT * FROM professeur ORDER BY nom ASC");

include("../includes/header.php");
include("../includes/sidebar.php");
?>

<div class="content">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestion des Professeurs</h2>

        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajoutModal">
            + Nouveau
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5>Liste des Professeurs</h5>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénoms</th>
                        <th>Civilité</th>
                        <th>Grade</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                <?php while($p = mysqli_fetch_assoc($result)){ ?>
                    <tr>
                        <td><?= $p['idprof']; ?></td>
                        <td><?= $p['nom']; ?></td>
                        <td><?= $p['prenoms']; ?></td>
                        <td><?= $p['civilite']; ?></td>
                        <td><?= $p['grade']; ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modif<?= $p['idprof']; ?>">
                                Modifier
                            </button>

                            <a href="professeurs.php?supprimer=<?= $p['idprof']; ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Supprimer ce professeur ?')">
                                Supprimer
                            </a>
                        </td>
                    </tr>

                    <div class="modal fade" id="modif<?= $p['idprof']; ?>">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Modifier Professeur</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <input type="hidden" name="idprof" value="<?= $p['idprof']; ?>">

                                        <label>ID Professeur</label>
                                        <input type="text" class="form-control mb-2" value="<?= $p['idprof']; ?>" disabled>

                                        <label>Nom</label>
                                        <input type="text" name="nom" class="form-control mb-2" value="<?= $p['nom']; ?>" required>

                                        <label>Prénoms</label>
                                        <input type="text" name="prenoms" class="form-control mb-2" value="<?= $p['prenoms']; ?>" required>

                                        <label>Civilité</label>
                                        <select name="civilite" class="form-select mb-2" required>
                                            <option <?= $p['civilite']=="Mr"?"selected":"" ?>>Mr</option>
                                            <option <?= $p['civilite']=="Mme"?"selected":"" ?>>Mme</option>
                                            <option <?= $p['civilite']=="Mlle"?"selected":"" ?>>Mlle</option>
                                        </select>

                                        <label>Grade</label>
                                        <select name="grade" class="form-select mb-2" required>
                                            <option <?= $p['grade']=="Professeur Titulaire"?"selected":"" ?>>Professeur Titulaire</option>
                                            <option <?= $p['grade']=="Maître de Conférences"?"selected":"" ?>>Maître de Conférences</option>
                                            <option <?= $p['grade']=="Assistant d'Enseignement Supérieur et de Recherche"?"selected":"" ?>>Assistant d'Enseignement Supérieur et de Recherche</option>
                                            <option <?= $p['grade']=="Docteur HDR"?"selected":"" ?>>Docteur HDR</option>
                                            <option <?= $p['grade']=="Docteur en Informatique"?"selected":"" ?>>Docteur en Informatique</option>
                                            <option <?= $p['grade']=="Doctorant en Informatique"?"selected":"" ?>>Doctorant en Informatique</option>
                                        </select>
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
                    <h5 class="modal-title">Nouveau Professeur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label>ID Professeur</label>
                    <input type="text" name="idprof" class="form-control mb-2" required>

                    <label>Nom</label>
                    <input type="text" name="nom" class="form-control mb-2" required>

                    <label>Prénoms</label>
                    <input type="text" name="prenoms" class="form-control mb-2" required>

                    <label>Civilité</label>
                    <select name="civilite" class="form-select mb-2" required>
                        <option>Mr</option>
                        <option>Mme</option>
                        <option>Mlle</option>
                    </select>

                    <label>Grade</label>
                    <select name="grade" class="form-select mb-2" required>
                        <option>Professeur Titulaire</option>
                        <option>Maître de Conférences</option>
                        <option>Assistant d'Enseignement Supérieur et de Recherche</option>
                        <option>Docteur HDR</option>
                        <option>Docteur en Informatique</option>
                        <option>Doctorant en Informatique</option>
                    </select>
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