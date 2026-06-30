<?php
session_start();
include("../includes/config.php");

if(isset($_POST['ajouter'])){
    $matricule = mysqli_real_escape_string($conn, $_POST['matricule']);
    $nom = mysqli_real_escape_string($conn, $_POST['nom']);
    $prenoms = mysqli_real_escape_string($conn, $_POST['prenoms']);
    $niveau = mysqli_real_escape_string($conn, $_POST['niveau']);
    $parcours = mysqli_real_escape_string($conn, $_POST['parcours']);
    $email = mysqli_real_escape_string($conn, $_POST['adr_email']);

    $check = mysqli_query($conn, "SELECT * FROM etudiant WHERE matricule='$matricule'");

    if(mysqli_num_rows($check) > 0){
        $_SESSION['message'] = "Ce matricule existe déjà. Impossible d’ajouter cet étudiant.";
        $_SESSION['message_type'] = "danger";
        header("Location: etudiants.php");
        exit();
    }

    mysqli_query($conn,"INSERT INTO etudiant VALUES('$matricule','$nom','$prenoms','$niveau','$parcours','$email')");

    $_SESSION['message'] = "Étudiant ajouté avec succès.";
    $_SESSION['message_type'] = "success";
    header("Location: etudiants.php");
    exit();
}

if(isset($_GET['supprimer'])){
    $matricule = mysqli_real_escape_string($conn, $_GET['supprimer']);
    mysqli_query($conn,"DELETE FROM etudiant WHERE matricule='$matricule'");

    $_SESSION['message'] = "Étudiant supprimé avec succès.";
    $_SESSION['message_type'] = "success";
    header("Location: etudiants.php");
    exit();
}

if(isset($_POST['modifier'])){
    $matricule = mysqli_real_escape_string($conn, $_POST['matricule']);
    $nom = mysqli_real_escape_string($conn, $_POST['nom']);
    $prenoms = mysqli_real_escape_string($conn, $_POST['prenoms']);
    $niveau = mysqli_real_escape_string($conn, $_POST['niveau']);
    $parcours = mysqli_real_escape_string($conn, $_POST['parcours']);
    $email = mysqli_real_escape_string($conn, $_POST['adr_email']);

    mysqli_query($conn,"UPDATE etudiant SET
        nom='$nom',
        prenoms='$prenoms',
        niveau='$niveau',
        parcours='$parcours',
        adr_email='$email'
        WHERE matricule='$matricule'
    ");

    $_SESSION['message'] = "Étudiant modifié avec succès.";
    $_SESSION['message_type'] = "success";
    header("Location: etudiants.php");
    exit();
}

$niveau = $_GET['niveau'] ?? '';
$parcours = $_GET['parcours'] ?? '';
$search = $_GET['search'] ?? '';

$where = "WHERE 1";

if($niveau != ''){
    $niveau_sql = mysqli_real_escape_string($conn, $niveau);
    $where .= " AND niveau='$niveau_sql'";
}

if($parcours != ''){
    $parcours_sql = mysqli_real_escape_string($conn, $parcours);
    $where .= " AND parcours='$parcours_sql'";
}

if($search != ''){
    $search_sql = mysqli_real_escape_string($conn, $search);
    $where .= " AND (matricule LIKE '%$search_sql%' OR nom LIKE '%$search_sql%' OR prenoms LIKE '%$search_sql%' OR adr_email LIKE '%$search_sql%')";
}

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1){ $page = 1; }
$offset = ($page - 1) * $limit;

$total_result = mysqli_query($conn,"SELECT COUNT(*) AS total FROM etudiant $where");
$total_rows = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total_rows / $limit);

$result = mysqli_query($conn,"SELECT * FROM etudiant $where ORDER BY nom ASC LIMIT $limit OFFSET $offset");

include("../includes/header.php");
include("../includes/sidebar.php");
?>

<style>
.content{
    background:#f8fafc;
    min-height:100vh;
}

.top-zone{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

.top-title{
    font-weight:700;
    color:#0f172a;
}

.search-top{
    width:300px;
    border-radius:12px;
    height:45px;
}

.btn-main{
    background:#2563eb;
    color:white;
    border-radius:12px;
    padding:10px 20px;
    border:none;
}

.btn-main:hover{
    background:#1d4ed8;
    color:white;
}

.filter-card{
    background:white;
    border-radius:15px;
    padding:20px;
    box-shadow:0 4px 15px rgba(0,0,0,0.06);
    margin-bottom:25px;
}

.table-card{
    background:white;
    border-radius:15px;
    box-shadow:0 4px 15px rgba(0,0,0,0.06);
    overflow:hidden;
}

.table-card-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:20px 25px;
    border-bottom:1px solid #e5e7eb;
}

.table-card-header h5{
    font-weight:700;
    margin:0;
}

.table thead th{
    text-transform:uppercase;
    color:#64748b;
    font-size:13px;
    border-bottom:1px solid #e5e7eb;
}

.table td{
    padding:16px;
    vertical-align:middle;
}

.badge-niveau{
    background:#dbeafe;
    color:#2563eb;
    padding:5px 12px;
    border-radius:20px;
    font-weight:600;
}

.action-btn{
    border:none;
    background:none;
    font-size:18px;
    margin:0 5px;
}

.edit-btn{
    color:#475569;
}

.delete-btn{
    color:#e11d48;
}

.simple-btn{
    border:1px solid #e5e7eb;
    background:white;
    border-radius:10px;
    padding:9px 18px;
    color:#0f172a;
}

.pagination .page-link{
    border-radius:10px;
    margin:0 3px;
    color:#2563eb;
}

.pagination .active .page-link{
    background:#2563eb;
    border-color:#2563eb;
    color:white;
}
</style>

<div class="content">

    <?php if(isset($_SESSION['message'])){ ?>
        <div class="alert alert-<?= $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php 
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    } 
    ?>

    <div class="top-zone">
        <h2 class="top-title">Gestion des Étudiants</h2>

        <div class="d-flex gap-3 align-items-center">
            <form method="GET" class="d-flex">
                <input type="hidden" name="niveau" value="<?= htmlspecialchars($niveau); ?>">
                <input type="hidden" name="parcours" value="<?= htmlspecialchars($parcours); ?>">
                <input type="text" name="search" class="form-control search-top" placeholder="Rechercher un étudiant..." value="<?= htmlspecialchars($search); ?>">
            </form>

            <button class="btn-main" data-bs-toggle="modal" data-bs-target="#ajoutModal">
                + Nouveau
            </button>
        </div>
    </div>

    <form method="GET" class="filter-card">
        <div class="row align-items-end">
            <div class="col-md-2">
                <label>Niveau :</label>
                <select name="niveau" class="form-select">
                    <option value="">Tous</option>
                    <option value="L1" <?= $niveau=="L1"?"selected":"" ?>>L1</option>
                    <option value="L2" <?= $niveau=="L2"?"selected":"" ?>>L2</option>
                    <option value="L3" <?= $niveau=="L3"?"selected":"" ?>>L3</option>
                    <option value="M1" <?= $niveau=="M1"?"selected":"" ?>>M1</option>
                    <option value="M2" <?= $niveau=="M2"?"selected":"" ?>>M2</option>
                </select>
            </div>

            <div class="col-md-2">
                <label>Parcours :</label>
                <select name="parcours" class="form-select">
                    <option value="">Tous</option>
                    <option value="GB" <?= $parcours=="GB"?"selected":"" ?>>GB</option>
                    <option value="SR" <?= $parcours=="SR"?"selected":"" ?>>SR</option>
                    <option value="IG" <?= $parcours=="IG"?"selected":"" ?>>IG</option>
                </select>
            </div>

            <div class="col-md-3">
                <label>Recherche</label>
                <input type="text" name="search" class="form-control" placeholder="Recherche par nom..." value="<?= htmlspecialchars($search); ?>">
            </div>

            <div class="col-md-2">
                <button class="simple-btn w-100">Filtrer</button>
            </div>

            <div class="col-md-2">
                <a href="etudiants.php" class="simple-btn w-100 d-block text-center text-decoration-none">Réinitialiser</a>
            </div>
        </div>
    </form>

    <div class="table-card">
        <div class="table-card-header">
            <h5>Liste des Étudiants</h5>
        </div>

        <div class="p-4">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Matricule</th>
                            <th>Nom</th>
                            <th>Prénoms</th>
                            <th>Niveau</th>
                            <th>Parcours</th>
                            <th>Email</th>
                            <th class="action-col">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)){ ?>
                        <tr>
                            <td><?= $row['matricule']; ?></td>
                            <td><?= $row['nom']; ?></td>
                            <td><?= $row['prenoms']; ?></td>
                            <td><span class="badge-niveau"><?= $row['niveau']; ?></span></td>
                            <td><?= $row['parcours']; ?></td>
                            <td><?= $row['adr_email']; ?></td>
                            <td class="action-col">
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

            <div class="d-flex justify-content-between align-items-center mt-3">
                <p class="mb-0">
                    Affichage de <?= $total_rows > 0 ? $offset + 1 : 0; ?> à <?= min($offset + $limit, $total_rows); ?> sur <?= $total_rows; ?> étudiants
                </p>

                <nav>
                    <ul class="pagination mb-0">
                        <?php for($i=1; $i<=$total_pages; $i++){ ?>
                            <li class="page-item <?= $i==$page?'active':'' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&niveau=<?= $niveau ?>&parcours=<?= $parcours ?>&search=<?= $search ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ajoutModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Nouvel Étudiant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label>Matricule</label>
                            <input type="text" name="matricule" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Nom</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Prénoms</label>
                            <input type="text" name="prenoms" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Niveau</label>
                            <select name="niveau" class="form-select">
                                <option>L1</option>
                                <option>L2</option>
                                <option>L3</option>
                                <option>M1</option>
                                <option>M2</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Parcours</label>
                            <select name="parcours" class="form-select">
                                <option>GB</option>
                                <option>IG</option>
                                <option>SR</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Email</label>
                            <input type="email" name="adr_email" class="form-control">
                        </div>
                    </div>
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