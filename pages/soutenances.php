<?php
include("../includes/config.php");

function getOrganismeId($conn, $organisme_input){
    $organisme_input = trim($organisme_input);

    if(strpos($organisme_input, " - ") !== false){
        $parts = explode(" - ", $organisme_input);
        if(is_numeric($parts[0])){
            return $parts[0];
        }
    }

    $design = mysqli_real_escape_string($conn, $organisme_input);
    mysqli_query($conn,"INSERT INTO organisme(design, lieu) VALUES('$design','')");
    return mysqli_insert_id($conn);
}

if(isset($_POST['ajouter'])){
    $matricule = mysqli_real_escape_string($conn, $_POST['matricule']);
    $idorg = getOrganismeId($conn, $_POST['organisme_input']);
    $annee_univ = mysqli_real_escape_string($conn, $_POST['annee_univ']);

    $note = str_replace(',', '.', $_POST['note']);
    $note = mysqli_real_escape_string($conn, $note);

    $president = mysqli_real_escape_string($conn, $_POST['president']);
    $examinateur = mysqli_real_escape_string($conn, $_POST['examinateur']);
    $rapporteur_int = mysqli_real_escape_string($conn, $_POST['rapporteur_int']);
    $rapporteur_ext = mysqli_real_escape_string($conn, $_POST['rapporteur_ext']);
    $date_soutenance = mysqli_real_escape_string($conn, $_POST['date_soutenance']);

    mysqli_query($conn,"INSERT INTO soutenir
    (matricule,idorg,annee_univ,note,president,examinateur,rapporteur_int,rapporteur_ext,date_soutenance)
    VALUES
    ('$matricule','$idorg','$annee_univ','$note','$president','$examinateur','$rapporteur_int','$rapporteur_ext','$date_soutenance')");

    header("Location: soutenances.php");
    exit();
}

if(isset($_GET['supprimer'])){
    $id = mysqli_real_escape_string($conn, $_GET['supprimer']);
    mysqli_query($conn,"DELETE FROM soutenir WHERE id='$id'");
    header("Location: soutenances.php");
    exit();
}

if(isset($_POST['modifier'])){
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $matricule = mysqli_real_escape_string($conn, $_POST['matricule']);
    $idorg = getOrganismeId($conn, $_POST['organisme_input']);
    $annee_univ = mysqli_real_escape_string($conn, $_POST['annee_univ']);

    $note = str_replace(',', '.', $_POST['note']);
    $note = mysqli_real_escape_string($conn, $note);

    $president = mysqli_real_escape_string($conn, $_POST['president']);
    $examinateur = mysqli_real_escape_string($conn, $_POST['examinateur']);
    $rapporteur_int = mysqli_real_escape_string($conn, $_POST['rapporteur_int']);
    $rapporteur_ext = mysqli_real_escape_string($conn, $_POST['rapporteur_ext']);
    $date_soutenance = mysqli_real_escape_string($conn, $_POST['date_soutenance']);

    mysqli_query($conn,"UPDATE soutenir SET
        matricule='$matricule',
        idorg='$idorg',
        annee_univ='$annee_univ',
        note='$note',
        president='$president',
        examinateur='$examinateur',
        rapporteur_int='$rapporteur_int',
        rapporteur_ext='$rapporteur_ext',
        date_soutenance='$date_soutenance'
        WHERE id='$id'
    ");

    header("Location: soutenances.php");
    exit();
}

$soutenances = mysqli_query($conn,"
SELECT s.*, e.nom AS nom_etudiant, e.prenoms AS prenoms_etudiant,
o.design AS organisme, o.lieu AS lieu_organisme
FROM soutenir s
LEFT JOIN etudiant e ON s.matricule = e.matricule
LEFT JOIN organisme o ON s.idorg = o.idorg
ORDER BY s.id DESC
");

$etudiants = mysqli_query($conn,"SELECT * FROM etudiant ORDER BY nom ASC");
$organismes = mysqli_query($conn,"SELECT * FROM organisme ORDER BY design ASC");
$professeurs = mysqli_query($conn,"SELECT * FROM professeur ORDER BY nom ASC");

$organismes_list = [];
while($o = mysqli_fetch_assoc($organismes)){
    $organismes_list[] = $o;
}

$professeurs_list = [];
while($p = mysqli_fetch_assoc($professeurs)){
    $professeurs_list[] = $p;
}

include("../includes/header.php");
include("../includes/sidebar.php");
?>

<div class="content">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestion des Soutenances</h2>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajoutModal">
        + Nouveau
    </button>
</div>

<div class="card shadow-sm">
<div class="card-header">
<h5>Liste des Soutenances</h5>
</div>

<div class="card-body">
<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">
<thead class="table-light">
<tr>
    <th>ID</th>
    <th>Étudiant</th>
    <th>Organisme</th>
    <th>Année</th>
    <th>Note</th>
    <th>Date</th>
    <th>Jury</th>
    <th>Actions</th>
</tr>
</thead>

<tbody>
<?php while($s = mysqli_fetch_assoc($soutenances)){ ?>
<tr>
    <td><?= $s['id']; ?></td>
    <td><?= $s['matricule']; ?> - <?= $s['nom_etudiant']; ?> <?= $s['prenoms_etudiant']; ?></td>
    <td><?= $s['organisme']; ?></td>
    <td><?= $s['annee_univ']; ?></td>
    <td><?= number_format($s['note'], 2, ',', ''); ?>/20</td>
    <td><?= $s['date_soutenance']; ?></td>
    <td>
        Président : <?= $s['president']; ?><br>
        Examinateur : <?= $s['examinateur']; ?><br>
        Rapporteur int. : <?= $s['rapporteur_int']; ?><br>
        Rapporteur ext. : <?= $s['rapporteur_ext']; ?>
    </td>
    <td>
        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modif<?= $s['id']; ?>">
            Modifier
        </button>

        <a href="soutenances.php?supprimer=<?= $s['id']; ?>" 
           class="btn btn-danger btn-sm"
           onclick="return confirm('Supprimer cette soutenance ?')">
           Supprimer
        </a>
    </td>
</tr>

<div class="modal fade" id="modif<?= $s['id']; ?>">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5>Modifier Soutenance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" value="<?= $s['id']; ?>">

                    <label>Matricule</label>
                    <input type="text" name="matricule" class="form-control mb-2" value="<?= $s['matricule']; ?>" required>

                    <label>Organisme</label>
                    <input list="listeOrganismesModif<?= $s['id']; ?>" 
                           name="organisme_input" 
                           class="form-control mb-2"
                           value="<?= $s['idorg']; ?> - <?= $s['organisme']; ?> - <?= $s['lieu_organisme']; ?>"
                           required>

                    <datalist id="listeOrganismesModif<?= $s['id']; ?>">
                        <?php foreach($organismes_list as $o){ ?>
                            <option value="<?= $o['idorg']; ?> - <?= $o['design']; ?> - <?= $o['lieu']; ?>"></option>
                        <?php } ?>
                    </datalist>

                    <label>Année universitaire</label>
                    <input type="text" name="annee_univ" class="form-control mb-2" value="<?= $s['annee_univ']; ?>" required>

                    <label>Note</label>
                    <input type="number" name="note" class="form-control mb-2" min="0" max="20" step="0.01" value="<?= $s['note']; ?>" required>

                    <label>Président</label>
                    <input type="text" name="president" class="form-control mb-2" value="<?= $s['president']; ?>" required>

                    <label>Examinateur</label>
                    <input type="text" name="examinateur" class="form-control mb-2" value="<?= $s['examinateur']; ?>" required>

                    <label>Rapporteur interne</label>
                    <input type="text" name="rapporteur_int" class="form-control mb-2" value="<?= $s['rapporteur_int']; ?>" required>

                    <label>Rapporteur externe</label>
                    <input type="text" name="rapporteur_ext" class="form-control mb-2" value="<?= $s['rapporteur_ext']; ?>" required>

                    <label>Date soutenance</label>
                    <input type="date" name="date_soutenance" class="form-control mb-2" value="<?= $s['date_soutenance']; ?>" required>
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

<div class="modal fade" id="ajoutModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5>Nouvelle Soutenance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <label>Étudiant</label>
                    <select name="matricule" class="form-select mb-2" required>
                        <?php while($e = mysqli_fetch_assoc($etudiants)){ ?>
                            <option value="<?= $e['matricule']; ?>">
                                <?= $e['matricule']; ?> - <?= $e['nom']; ?> <?= $e['prenoms']; ?>
                            </option>
                        <?php } ?>
                    </select>

                    <label>Organisme</label>
                    <input list="listeOrganismesAjout" 
                           name="organisme_input" 
                           class="form-control mb-2"
                           placeholder="Tapez un nouvel organisme ou choisissez dans la liste..." required>

                    <datalist id="listeOrganismesAjout">
                        <?php foreach($organismes_list as $o){ ?>
                            <option value="<?= $o['idorg']; ?> - <?= $o['design']; ?> - <?= $o['lieu']; ?>"></option>
                        <?php } ?>
                    </datalist>

                    <small class="text-muted">
                        Vous pouvez écrire un nouvel organisme ou sélectionner un organisme déjà enregistré.
                    </small>

                    <br><br>

                    <label>Année universitaire</label>
                    <input type="text" name="annee_univ" class="form-control mb-2" value="2025-2026" required>

                    <label>Note</label>
                    <input type="number" name="note" class="form-control mb-2" min="0" max="20" step="0.01" required>

                    <label>Président</label>
                    <select name="president" class="form-select mb-2" required>
                        <?php foreach($professeurs_list as $p){ ?>
                            <option value="<?= $p['nom'].' '.$p['prenoms']; ?>">
                                <?= $p['nom']; ?> <?= $p['prenoms']; ?> - <?= $p['grade']; ?>
                            </option>
                        <?php } ?>
                    </select>

                    <label>Examinateur</label>
                    <select name="examinateur" class="form-select mb-2" required>
                        <?php foreach($professeurs_list as $p){ ?>
                            <option value="<?= $p['nom'].' '.$p['prenoms']; ?>">
                                <?= $p['nom']; ?> <?= $p['prenoms']; ?> - <?= $p['grade']; ?>
                            </option>
                        <?php } ?>
                    </select>

                    <label>Rapporteur interne</label>
                    <select name="rapporteur_int" class="form-select mb-2" required>
                        <?php foreach($professeurs_list as $p){ ?>
                            <option value="<?= $p['nom'].' '.$p['prenoms']; ?>">
                                <?= $p['nom']; ?> <?= $p['prenoms']; ?> - <?= $p['grade']; ?>
                            </option>
                        <?php } ?>
                    </select>

                    <label>Rapporteur externe</label>
                    <select name="rapporteur_ext" class="form-select mb-2" required>
                        <?php foreach($professeurs_list as $p){ ?>
                            <option value="<?= $p['nom'].' '.$p['prenoms']; ?>">
                                <?= $p['nom']; ?> <?= $p['prenoms']; ?> - <?= $p['grade']; ?>
                            </option>
                        <?php } ?>
                    </select>

                    <label>Date soutenance</label>
                    <input type="date" name="date_soutenance" class="form-control mb-2" required>

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