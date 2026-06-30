<?php
include("../includes/config.php");
include("../includes/header.php");
include("../includes/sidebar.php");

$nb_etudiants = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM etudiant"))['total'];
$nb_professeurs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM professeur"))['total'];
$nb_soutenances = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM soutenir"))['total'];

$nb_non_soutenus = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM etudiant e
    LEFT JOIN soutenir s ON e.matricule = s.matricule
    WHERE s.matricule IS NULL
"))['total'];

$niveaux = ['L1','L2','L3','M1','M2'];
$parcours = ['GB','SR','IG'];

$recentes = mysqli_query($conn,"
SELECT s.*, e.nom, e.prenoms, e.niveau, e.parcours
FROM soutenir s
LEFT JOIN etudiant e ON s.matricule = e.matricule
ORDER BY s.date_soutenance DESC
LIMIT 5
");
?>

<style>
.content{background:#f8fafc;min-height:100vh;padding:30px;}
.topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;}
.topbar h2{font-weight:700;color:#0f172a;}
.search-box{width:320px;border-radius:12px;height:45px;}
.btn-main{background:#2563eb;color:white;border-radius:12px;padding:10px 18px;text-decoration:none;}
.stat-card{background:white;border-radius:16px;padding:22px;box-shadow:0 4px 15px rgba(0,0,0,.06);}
.stat-card h3{font-weight:800;font-size:32px;margin:0;}
.stat-card p{margin:0;color:#64748b;}
.icon{width:52px;height:52px;border-radius:14px;}
.blue{background:#dbeafe;}
.green{background:#d1fae5;}
.orange{background:#ffedd5;}
.red{background:#fee2e2;}
.panel{background:white;border-radius:16px;box-shadow:0 4px 15px rgba(0,0,0,.06);overflow:hidden;}
.panel-header{padding:18px 22px;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center;}
.panel-header h5{margin:0;font-weight:700;}
.panel-body{padding:22px;}
.table th{text-transform:uppercase;font-size:13px;color:#64748b;}
.badge-level{background:#dbeafe;color:#2563eb;padding:5px 12px;border-radius:20px;font-weight:700;}
.badge-note{background:#d1fae5;color:#059669;padding:5px 12px;border-radius:20px;font-weight:700;}
.simple-btn{border:1px solid #e5e7eb;background:white;border-radius:10px;padding:8px 15px;color:#0f172a;}
.info{background:#dbeafe;color:#1d4ed8;border-radius:12px;padding:14px 18px;margin-top:25px;}
</style>

<div class="content">

    <div class="topbar">
        <h2>Tableau de bord</h2>

        <div class="d-flex gap-3">
            <input type="text" class="form-control search-box" placeholder="Rechercher un étudiant...">
            <a href="etudiants.php" class="btn-main">+ Nouveau</a>
            <button class="simple-btn">Filtres</button>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card d-flex gap-3 align-items-center">
                <div class="icon blue"></div>
                <div>
                    <h3><?= $nb_etudiants ?></h3>
                    <p>Étudiants inscrits</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card d-flex gap-3 align-items-center">
                <div class="icon green"></div>
                <div>
                    <h3><?= $nb_professeurs ?></h3>
                    <p>Professeurs</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card d-flex gap-3 align-items-center">
                <div class="icon orange"></div>
                <div>
                    <h3><?= $nb_soutenances ?></h3>
                    <p>Soutenances effectuées</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-card d-flex gap-3 align-items-center">
                <div class="icon red"></div>
                <div>
                    <h3><?= $nb_non_soutenus ?></h3>
                    <p>Non soutenus</p>
                </div>
            </div>
        </div>
    </div>

    <div class="panel mb-4">
        <div class="panel-header">
            <h5>Effectifs par niveau</h5>
        </div>

        <div class="panel-body">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Niveau</th>
                        <th>Parcours GB</th>
                        <th>Parcours SR</th>
                        <th>Parcours IG</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($niveaux as $niveau){ ?>
                    <tr>
                        <td><span class="badge-level"><?= $niveau ?></span></td>

                        <?php
                        $total_niveau = 0;
                        foreach($parcours as $p){
                            $q = mysqli_query($conn, "SELECT COUNT(*) AS total FROM etudiant WHERE niveau='$niveau' AND parcours='$p'");
                            $count = mysqli_fetch_assoc($q)['total'];
                            $total_niveau += $count;
                            echo "<td>$count</td>";
                        }
                        ?>

                        <td><strong><?= $total_niveau ?></strong></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>

            <small class="text-muted">Mis à jour le <?= date("d/m/Y"); ?> à <?= date("H:i"); ?></small>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h5>Soutenances récentes</h5>
            <a href="soutenances.php" class="simple-btn text-decoration-none">Voir tout</a>
        </div>

        <div class="panel-body">
            <?php while($r = mysqli_fetch_assoc($recentes)){ ?>
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <div>
                        <strong><?= $r['nom']; ?> <?= $r['prenoms']; ?></strong><br>
                        <small><?= $r['niveau']; ?> - <?= $r['parcours']; ?></small>
                    </div>

                    <div>
                        <span class="badge-note"><?= $r['note']; ?>/20</span>
                        <span class="ms-3"><?= $r['date_soutenance']; ?></span>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <div class="info">
        Bienvenue sur votre tableau de bord. Retrouvez ici un aperçu général des soutenances.
    </div>

</div>

</body>
</html>