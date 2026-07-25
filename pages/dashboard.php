<?php

declare(strict_types=1);

/* Affichage des erreurs pendant le développement */
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

/* Transformer les erreurs MySQL en exceptions */
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* Valeurs par défaut pour éviter une page blanche */
$nb_etudiants = 0;
$nb_professeurs = 0;
$nb_soutenances = 0;
$nb_non_soutenus = 0;

$niveaux = ['L1', 'L2', 'L3', 'M1', 'M2'];
$parcours = ['GB', 'SR', 'IG'];

$effectifs = [];
$soutenances_recentes = [];
$erreur_dashboard = null;

/**
 * Sécurise les textes affichés dans le HTML.
 */
function afficherTexte(mixed $valeur): string
{
    return htmlspecialchars(
        (string) $valeur,
        ENT_QUOTES,
        'UTF-8'
    );
}

/**
 * Retourne le total obtenu par une requête COUNT().
 */
function obtenirTotal(mysqli $connexion, string $requete): int
{
    $resultat = $connexion->query($requete);
    $ligne = $resultat->fetch_assoc();

    return (int) ($ligne['total'] ?? 0);
}

try {
    /*
     * dashboard.php se trouve dans pages/
     * config.php se trouve dans includes/
     */
    require_once __DIR__ . '/../includes/config.php';

    if (!isset($conn)) {
        throw new RuntimeException(
            'La variable $conn n’est pas définie dans includes/config.php.'
        );
    }

    if (!($conn instanceof mysqli)) {
        throw new RuntimeException(
            'La variable $conn doit être une connexion MySQLi valide.'
        );
    }

    $conn->set_charset('utf8mb4');

    /* Nombre total d’étudiants */
    $nb_etudiants = obtenirTotal(
        $conn,
        "SELECT COUNT(*) AS total FROM etudiant"
    );

    /* Nombre total de professeurs */
    $nb_professeurs = obtenirTotal(
        $conn,
        "SELECT COUNT(*) AS total FROM professeur"
    );

    /* Nombre total de soutenances */
    $nb_soutenances = obtenirTotal(
        $conn,
        "SELECT COUNT(*) AS total FROM soutenir"
    );

    /* Étudiants qui n’ont pas encore soutenu */
    $nb_non_soutenus = obtenirTotal(
        $conn,
        "
        SELECT COUNT(*) AS total
        FROM etudiant AS e
        LEFT JOIN soutenir AS s
            ON e.matricule = s.matricule
        WHERE s.matricule IS NULL
        "
    );

    /*
     * Initialisation du tableau des effectifs
     */
    foreach ($niveaux as $niveau) {
        foreach ($parcours as $p) {
            $effectifs[$niveau][$p] = 0;
        }
    }

    /*
     * Effectifs groupés par niveau et parcours
     */
    $resultat_effectifs = $conn->query(
        "
        SELECT niveau, parcours, COUNT(*) AS total
        FROM etudiant
        WHERE niveau IN ('L1', 'L2', 'L3', 'M1', 'M2')
          AND parcours IN ('GB', 'SR', 'IG')
        GROUP BY niveau, parcours
        "
    );

    while ($ligne = $resultat_effectifs->fetch_assoc()) {
        $niveau = $ligne['niveau'];
        $p = $ligne['parcours'];

        if (
            isset($effectifs[$niveau]) &&
            array_key_exists($p, $effectifs[$niveau])
        ) {
            $effectifs[$niveau][$p] = (int) $ligne['total'];
        }
    }

    /*
     * Cinq dernières soutenances
     */
    $resultat_recentes = $conn->query(
        "
        SELECT
            s.*,
            e.nom,
            e.prenoms,
            e.niveau,
            e.parcours
        FROM soutenir AS s
        LEFT JOIN etudiant AS e
            ON s.matricule = e.matricule
        ORDER BY s.date_soutenance DESC
        LIMIT 5
        "
    );

    while ($ligne = $resultat_recentes->fetch_assoc()) {
        $soutenances_recentes[] = $ligne;
    }
} catch (Throwable $erreur) {
    /*
     * Au lieu d’afficher une page blanche,
     * le message d’erreur sera visible dans le tableau de bord.
     */
    $erreur_dashboard = $erreur->getMessage();
}

/*
 * Chargement de l’interface après les traitements PHP
 */
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

?>

<style>
    .content {
        background: #f8fafc;
        min-height: 100vh;
        padding: 30px;
    }

    .topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 25px;
    }

    .topbar h2 {
        margin: 0;
        font-weight: 700;
        color: #0f172a;
    }

    .topbar-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .search-box {
        width: 320px;
        height: 45px;
        border-radius: 12px;
    }

    .btn-main {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 45px;
        padding: 10px 18px;
        border-radius: 12px;
        background: #2563eb;
        color: white;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-main:hover {
        background: #1d4ed8;
        color: white;
    }

    .simple-btn {
        min-height: 45px;
        padding: 8px 15px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: white;
        color: #0f172a;
    }

    .stat-card {
        height: 100%;
        padding: 22px;
        border-radius: 16px;
        background: white;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
    }

    .stat-card h3 {
        margin: 0;
        font-size: 32px;
        font-weight: 800;
        color: #0f172a;
    }

    .stat-card p {
        margin: 0;
        color: #64748b;
    }

    .stat-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 52px;
        width: 52px;
        height: 52px;
        border-radius: 14px;
        font-size: 24px;
    }

    .blue {
        background: #dbeafe;
        color: #2563eb;
    }

    .green {
        background: #d1fae5;
        color: #059669;
    }

    .orange {
        background: #ffedd5;
        color: #ea580c;
    }

    .red {
        background: #fee2e2;
        color: #dc2626;
    }

    .panel {
        overflow: hidden;
        border-radius: 16px;
        background: white;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
    }

    .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 22px;
        border-bottom: 1px solid #e5e7eb;
    }

    .panel-header h5 {
        margin: 0;
        font-weight: 700;
        color: #0f172a;
    }

    .panel-body {
        padding: 22px;
    }

    .table th {
        color: #64748b;
        font-size: 13px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .badge-level {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        background: #dbeafe;
        color: #2563eb;
        font-weight: 700;
    }

    .badge-note {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        background: #d1fae5;
        color: #059669;
        font-weight: 700;
    }

    .recent-item:last-child {
        border-bottom: none !important;
    }

    .info {
        margin-top: 25px;
        padding: 14px 18px;
        border-radius: 12px;
        background: #dbeafe;
        color: #1d4ed8;
    }

    .dashboard-error {
        margin-bottom: 25px;
        padding: 18px 20px;
        border: 1px solid #ef4444;
        border-radius: 12px;
        background: #fee2e2;
        color: #991b1b;
    }

    .empty-message {
        padding: 20px;
        color: #64748b;
        text-align: center;
    }

    @media (max-width: 992px) {
        .topbar {
            align-items: stretch;
            flex-direction: column;
        }

        .topbar-actions {
            flex-wrap: wrap;
        }

        .search-box {
            width: 100%;
        }
    }
</style>

<main class="content">

    <?php if ($erreur_dashboard !== null): ?>
        <div class="dashboard-error">
            <strong>
                <i class="bi bi-exclamation-triangle-fill"></i>
                Erreur détectée dans le tableau de bord
            </strong>

            <div class="mt-2">
                <?= afficherTexte($erreur_dashboard); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="topbar">
        <h2>Tableau de bord</h2>

        <div class="topbar-actions">
            <input
                type="text"
                class="form-control search-box"
                id="rechercheEtudiant"
                placeholder="Rechercher un étudiant..."
            >

            <a href="etudiants.php" class="btn-main">
                <i class="bi bi-plus-lg me-2"></i>
                Nouveau
            </a>

            <button type="button" class="simple-btn">
                <i class="bi bi-funnel me-1"></i>
                Filtres
            </button>
        </div>
    </div>

    <div class="row g-4 mb-4">

        <div class="col-xl-3 col-md-6">
            <div class="stat-card d-flex gap-3 align-items-center">
                <div class="stat-icon blue">
                    <i class="bi bi-people-fill"></i>
                </div>

                <div>
                    <h3><?= $nb_etudiants; ?></h3>
                    <p>Étudiants inscrits</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card d-flex gap-3 align-items-center">
                <div class="stat-icon green">
                    <i class="bi bi-person-video3"></i>
                </div>

                <div>
                    <h3><?= $nb_professeurs; ?></h3>
                    <p>Professeurs</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card d-flex gap-3 align-items-center">
                <div class="stat-icon orange">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>

                <div>
                    <h3><?= $nb_soutenances; ?></h3>
                    <p>Soutenances effectuées</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card d-flex gap-3 align-items-center">
                <div class="stat-icon red">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>

                <div>
                    <h3><?= $nb_non_soutenus; ?></h3>
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

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

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

                        <?php foreach ($niveaux as $niveau): ?>

                            <?php
                            $total_niveau = 0;

                            foreach ($parcours as $p) {
                                $total_niveau += $effectifs[$niveau][$p] ?? 0;
                            }
                            ?>

                            <tr>
                                <td>
                                    <span class="badge-level">
                                        <?= afficherTexte($niveau); ?>
                                    </span>
                                </td>

                                <?php foreach ($parcours as $p): ?>
                                    <td>
                                        <?= (int) ($effectifs[$niveau][$p] ?? 0); ?>
                                    </td>
                                <?php endforeach; ?>

                                <td>
                                    <strong><?= $total_niveau; ?></strong>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>
            </div>

            <small class="text-muted d-block mt-3">
                Mis à jour le
                <?= date('d/m/Y'); ?>
                à
                <?= date('H:i'); ?>
            </small>

        </div>
    </div>

    <div class="panel">

        <div class="panel-header">
            <h5>Soutenances récentes</h5>

            <a
                href="soutenances.php"
                class="simple-btn text-decoration-none"
            >
                Voir tout
            </a>
        </div>

        <div class="panel-body">

            <?php if (count($soutenances_recentes) === 0): ?>

                <div class="empty-message">
                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                    Aucune soutenance récente disponible.
                </div>

            <?php else: ?>

                <?php foreach ($soutenances_recentes as $soutenance): ?>

                    <?php
                    $nom_complet = trim(
                        ($soutenance['nom'] ?? '') . ' ' .
                        ($soutenance['prenoms'] ?? '')
                    );

                    if ($nom_complet === '') {
                        $nom_complet = 'Étudiant inconnu';
                    }

                    $niveau_etudiant =
                        $soutenance['niveau'] ?? 'Niveau non défini';

                    $parcours_etudiant =
                        $soutenance['parcours'] ?? 'Parcours non défini';

                    $note = $soutenance['note'] ?? null;

                    $date_soutenance =
                        $soutenance['date_soutenance'] ?? null;

                    if (!empty($date_soutenance)) {
                        $timestamp = strtotime($date_soutenance);

                        $date_affichee = $timestamp !== false
                            ? date('d/m/Y', $timestamp)
                            : $date_soutenance;
                    } else {
                        $date_affichee = 'Date non définie';
                    }
                    ?>

                    <div class="recent-item d-flex flex-wrap justify-content-between align-items-center gap-3 border-bottom py-3">

                        <div>
                            <strong>
                                <?= afficherTexte($nom_complet); ?>
                            </strong>

                            <br>

                            <small class="text-muted">
                                <?= afficherTexte($niveau_etudiant); ?>
                                -
                                <?= afficherTexte($parcours_etudiant); ?>
                            </small>
                        </div>

                        <div class="d-flex align-items-center gap-3">

                            <span class="badge-note">
                                <?php if ($note !== null && $note !== ''): ?>
                                    <?= afficherTexte($note); ?>/20
                                <?php else: ?>
                                    Note non définie
                                <?php endif; ?>
                            </span>

                            <span>
                                <?= afficherTexte($date_affichee); ?>
                            </span>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>
    </div>

    <div class="info">
        <i class="bi bi-info-circle-fill me-2"></i>
        Bienvenue sur votre tableau de bord. Retrouvez ici un aperçu
        général des soutenances.
    </div>

</main>

<script>
    const champRecherche = document.getElementById('rechercheEtudiant');

    if (champRecherche) {
        champRecherche.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                const recherche = champRecherche.value.trim();

                if (recherche !== '') {
                    window.location.href =
                        'etudiants.php?recherche=' +
                        encodeURIComponent(recherche);
                }
            }
        });
    }
</script>

</body>
</html>