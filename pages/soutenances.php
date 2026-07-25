<?php

/* =========================================================
   AFFICHAGE DES ERREURS PENDANT LE DÉVELOPPEMENT
   ========================================================= */

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


/* =========================================================
   VALEURS PAR DÉFAUT
   ========================================================= */

$erreur = null;
$message_succes = null;
$transaction_active = false;

$soutenances_list = [];
$etudiants_list = [];
$organismes_list = [];
$professeurs_list = [];


/* =========================================================
   FONCTIONS UTILES
   ========================================================= */

/**
 * Protège les textes affichés dans le HTML.
 */
function h($valeur): string
{
    return htmlspecialchars(
        (string) $valeur,
        ENT_QUOTES,
        'UTF-8'
    );
}


/**
 * Récupère proprement une valeur envoyée par formulaire.
 */
function valeurPost(string $cle): string
{
    return trim((string) ($_POST[$cle] ?? ''));
}


/**
 * Vérifie et récupère l’identifiant d’un organisme.
 * Si l’organisme n’existe pas, il est automatiquement créé.
 */
function getOrganismeId(mysqli $conn, string $organisme_input): int
{
    $organisme_input = trim($organisme_input);

    if ($organisme_input === '') {
        throw new RuntimeException(
            'Veuillez renseigner un organisme.'
        );
    }

    /*
     * Cas où l’utilisateur sélectionne un organisme existant :
     * Exemple : 3 - Université de Mahajanga - Mahajanga
     */
    if (preg_match('/^\s*(\d+)\s*-\s*/', $organisme_input, $correspondance)) {
        $idorg = (int) $correspondance[1];

        $requete = $conn->prepare(
            'SELECT idorg FROM organisme WHERE idorg = ? LIMIT 1'
        );

        $requete->bind_param('i', $idorg);
        $requete->execute();

        $resultat = $requete->get_result();

        if ($resultat->num_rows > 0) {
            return $idorg;
        }
    }

    /*
     * Vérification par désignation afin d’éviter
     * la création de doublons.
     */
    $requete = $conn->prepare(
        'SELECT idorg
         FROM organisme
         WHERE design = ?
         LIMIT 1'
    );

    $requete->bind_param('s', $organisme_input);
    $requete->execute();

    $resultat = $requete->get_result();
    $organisme_existant = $resultat->fetch_assoc();

    if ($organisme_existant !== null) {
        return (int) $organisme_existant['idorg'];
    }

    /*
     * Création du nouvel organisme.
     */
    $lieu = '';

    $requete = $conn->prepare(
        'INSERT INTO organisme (design, lieu)
         VALUES (?, ?)'
    );

    $requete->bind_param(
        'ss',
        $organisme_input,
        $lieu
    );

    $requete->execute();

    return (int) $conn->insert_id;
}


/* =========================================================
   CONNEXION ET TRAITEMENTS
   ========================================================= */

try {

    require_once __DIR__ . '/../includes/config.php';

    /*
     * Compatibilité si config.php utilise un autre nom
     * pour la variable de connexion.
     */
    if (
        !isset($conn) &&
        isset($connexion) &&
        $connexion instanceof mysqli
    ) {
        $conn = $connexion;
    }

    if (
        !isset($conn) &&
        isset($mysqli) &&
        $mysqli instanceof mysqli
    ) {
        $conn = $mysqli;
    }

    if (!isset($conn)) {
        throw new RuntimeException(
            'La variable $conn n’est pas définie dans includes/config.php.'
        );
    }

    if (!($conn instanceof mysqli)) {
        throw new RuntimeException(
            'La connexion définie dans config.php n’est pas une connexion MySQLi valide.'
        );
    }

    $conn->set_charset('utf8mb4');


    /* =====================================================
       AJOUT D’UNE SOUTENANCE
       ===================================================== */

    if (isset($_POST['ajouter'])) {

        $matricule = valeurPost('matricule');
        $organisme_input = valeurPost('organisme_input');
        $annee_univ = valeurPost('annee_univ');

        $note_texte = str_replace(
            ',',
            '.',
            valeurPost('note')
        );

        $president = valeurPost('president');
        $examinateur = valeurPost('examinateur');
        $rapporteur_int = valeurPost('rapporteur_int');
        $rapporteur_ext = valeurPost('rapporteur_ext');
        $date_soutenance = valeurPost('date_soutenance');

        if ($matricule === '') {
            throw new RuntimeException(
                'Veuillez sélectionner un étudiant.'
            );
        }

        if ($annee_univ === '') {
            throw new RuntimeException(
                'Veuillez renseigner l’année universitaire.'
            );
        }

        if (!is_numeric($note_texte)) {
            throw new RuntimeException(
                'La note saisie n’est pas valide.'
            );
        }

        $note = (float) $note_texte;

        if ($note < 0 || $note > 20) {
            throw new RuntimeException(
                'La note doit être comprise entre 0 et 20.'
            );
        }

        if ($president === '') {
            throw new RuntimeException(
                'Veuillez sélectionner le président du jury.'
            );
        }

        if ($examinateur === '') {
            throw new RuntimeException(
                'Veuillez sélectionner l’examinateur.'
            );
        }

        if ($rapporteur_int === '') {
            throw new RuntimeException(
                'Veuillez sélectionner le rapporteur interne.'
            );
        }

        if ($rapporteur_ext === '') {
            throw new RuntimeException(
                'Veuillez sélectionner le rapporteur externe.'
            );
        }

        if (
            !preg_match(
                '/^\d{4}-\d{2}-\d{2}$/',
                $date_soutenance
            )
        ) {
            throw new RuntimeException(
                'La date de soutenance n’est pas valide.'
            );
        }

        $conn->begin_transaction();
        $transaction_active = true;

        $idorg = getOrganismeId(
            $conn,
            $organisme_input
        );

        $requete = $conn->prepare(
            'INSERT INTO soutenir (
                matricule,
                idorg,
                annee_univ,
                note,
                president,
                examinateur,
                rapporteur_int,
                rapporteur_ext,
                date_soutenance
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $requete->bind_param(
            'sisdsssss',
            $matricule,
            $idorg,
            $annee_univ,
            $note,
            $president,
            $examinateur,
            $rapporteur_int,
            $rapporteur_ext,
            $date_soutenance
        );

        $requete->execute();

        $conn->commit();
        $transaction_active = false;

        header(
            'Location: soutenances.php?succes=ajout'
        );

        exit;
    }


    /* =====================================================
       MODIFICATION D’UNE SOUTENANCE
       ===================================================== */

    if (isset($_POST['modifier'])) {

        $id = (int) valeurPost('id');
        $matricule = valeurPost('matricule');
        $organisme_input = valeurPost('organisme_input');
        $annee_univ = valeurPost('annee_univ');

        $note_texte = str_replace(
            ',',
            '.',
            valeurPost('note')
        );

        $president = valeurPost('president');
        $examinateur = valeurPost('examinateur');
        $rapporteur_int = valeurPost('rapporteur_int');
        $rapporteur_ext = valeurPost('rapporteur_ext');
        $date_soutenance = valeurPost('date_soutenance');

        if ($id <= 0) {
            throw new RuntimeException(
                'Identifiant de soutenance invalide.'
            );
        }

        if ($matricule === '') {
            throw new RuntimeException(
                'Veuillez sélectionner un étudiant.'
            );
        }

        if (!is_numeric($note_texte)) {
            throw new RuntimeException(
                'La note saisie n’est pas valide.'
            );
        }

        $note = (float) $note_texte;

        if ($note < 0 || $note > 20) {
            throw new RuntimeException(
                'La note doit être comprise entre 0 et 20.'
            );
        }

        if (
            !preg_match(
                '/^\d{4}-\d{2}-\d{2}$/',
                $date_soutenance
            )
        ) {
            throw new RuntimeException(
                'La date de soutenance n’est pas valide.'
            );
        }

        $conn->begin_transaction();
        $transaction_active = true;

        $idorg = getOrganismeId(
            $conn,
            $organisme_input
        );

        $requete = $conn->prepare(
            'UPDATE soutenir SET
                matricule = ?,
                idorg = ?,
                annee_univ = ?,
                note = ?,
                president = ?,
                examinateur = ?,
                rapporteur_int = ?,
                rapporteur_ext = ?,
                date_soutenance = ?
             WHERE id = ?'
        );

        $requete->bind_param(
            'sisdsssssi',
            $matricule,
            $idorg,
            $annee_univ,
            $note,
            $president,
            $examinateur,
            $rapporteur_int,
            $rapporteur_ext,
            $date_soutenance,
            $id
        );

        $requete->execute();

        $conn->commit();
        $transaction_active = false;

        header(
            'Location: soutenances.php?succes=modification'
        );

        exit;
    }


    /* =====================================================
       SUPPRESSION D’UNE SOUTENANCE
       ===================================================== */

    if (isset($_POST['supprimer'])) {

        $id = (int) valeurPost('id');

        if ($id <= 0) {
            throw new RuntimeException(
                'Identifiant de soutenance invalide.'
            );
        }

        $requete = $conn->prepare(
            'DELETE FROM soutenir WHERE id = ?'
        );

        $requete->bind_param('i', $id);
        $requete->execute();

        header(
            'Location: soutenances.php?succes=suppression'
        );

        exit;
    }


    /* =====================================================
       LISTE DES SOUTENANCES
       ===================================================== */

    $resultat_soutenances = $conn->query(
        'SELECT
            s.*,
            e.nom AS nom_etudiant,
            e.prenoms AS prenoms_etudiant,
            o.design AS organisme,
            o.lieu AS lieu_organisme
         FROM soutenir AS s
         LEFT JOIN etudiant AS e
            ON s.matricule = e.matricule
         LEFT JOIN organisme AS o
            ON s.idorg = o.idorg
         ORDER BY s.id DESC'
    );

    while (
        $soutenance = $resultat_soutenances->fetch_assoc()
    ) {
        $soutenances_list[] = $soutenance;
    }


    /* =====================================================
       LISTE DES ÉTUDIANTS
       ===================================================== */

    $resultat_etudiants = $conn->query(
        'SELECT *
         FROM etudiant
         ORDER BY nom ASC, prenoms ASC'
    );

    while (
        $etudiant = $resultat_etudiants->fetch_assoc()
    ) {
        $etudiants_list[] = $etudiant;
    }


    /* =====================================================
       LISTE DES ORGANISMES
       ===================================================== */

    $resultat_organismes = $conn->query(
        'SELECT *
         FROM organisme
         ORDER BY design ASC'
    );

    while (
        $organisme = $resultat_organismes->fetch_assoc()
    ) {
        $organismes_list[] = $organisme;
    }


    /* =====================================================
       LISTE DES PROFESSEURS
       ===================================================== */

    $resultat_professeurs = $conn->query(
        'SELECT *
         FROM professeur
         ORDER BY nom ASC, prenoms ASC'
    );

    while (
        $professeur = $resultat_professeurs->fetch_assoc()
    ) {
        $professeurs_list[] = $professeur;
    }


    /* =====================================================
       MESSAGE DE SUCCÈS
       ===================================================== */

    $succes = $_GET['succes'] ?? '';

    if ($succes === 'ajout') {
        $message_succes =
            'La soutenance a été enregistrée avec succès.';
    }

    if ($succes === 'modification') {
        $message_succes =
            'La soutenance a été modifiée avec succès.';
    }

    if ($succes === 'suppression') {
        $message_succes =
            'La soutenance a été supprimée avec succès.';
    }

} catch (Throwable $exception) {

    if (
        $transaction_active &&
        isset($conn) &&
        $conn instanceof mysqli
    ) {
        $conn->rollback();
    }

    $erreur = $exception->getMessage();
}


/* =========================================================
   CHARGEMENT DE L’INTERFACE
   ========================================================= */

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

?>


<style>
    .content {
        background: #f8fafc;
        min-height: 100vh;
        padding: 30px;
    }

    .page-title {
        color: #0f172a;
        font-weight: 700;
    }

    .card {
        border: none;
        border-radius: 16px;
        overflow: hidden;
    }

    .card-header {
        background: white;
        padding: 18px 22px;
        border-bottom: 1px solid #e5e7eb;
    }

    .card-header h5 {
        margin: 0;
        font-weight: 700;
    }

    .btn {
        border-radius: 10px;
    }

    .table th {
        color: #64748b;
        font-size: 13px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .jury-details {
        min-width: 220px;
        font-size: 14px;
        line-height: 1.7;
    }

    .action-buttons {
        min-width: 180px;
    }

    .note-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        background: #d1fae5;
        color: #047857;
        font-weight: 700;
        white-space: nowrap;
    }

    .message-erreur {
        padding: 18px;
        margin-bottom: 20px;
        border: 1px solid #ef4444;
        border-radius: 12px;
        background: #fee2e2;
        color: #991b1b;
    }

    .message-succes {
        padding: 18px;
        margin-bottom: 20px;
        border: 1px solid #10b981;
        border-radius: 12px;
        background: #d1fae5;
        color: #065f46;
    }

    .table-empty {
        padding: 30px;
        color: #64748b;
        text-align: center;
    }

    .modal-content {
        border: none;
        border-radius: 16px;
    }

    .modal-header {
        border-bottom: 1px solid #e5e7eb;
    }

    .modal-footer {
        border-top: 1px solid #e5e7eb;
    }

    .form-control,
    .form-select {
        min-height: 44px;
        border-radius: 10px;
    }

    .form-label {
        margin-top: 10px;
        margin-bottom: 6px;
        font-weight: 600;
    }
</style>


<main class="content">

    <?php if ($erreur !== null): ?>

        <div class="message-erreur">
            <strong>
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Erreur détectée
            </strong>

            <div class="mt-2">
                <?= h($erreur); ?>
            </div>
        </div>

    <?php endif; ?>


    <?php if ($message_succes !== null): ?>

        <div class="message-succes">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= h($message_succes); ?>
        </div>

    <?php endif; ?>


    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <h2 class="page-title mb-0">
            Gestion des soutenances
        </h2>

        <button
            type="button"
            class="btn btn-primary px-4"
            data-bs-toggle="modal"
            data-bs-target="#ajoutModal"
            <?= $erreur !== null ? 'disabled' : ''; ?>
        >
            <i class="bi bi-plus-lg me-2"></i>
            Nouveau
        </button>

    </div>


    <div class="card shadow-sm">

        <div class="card-header">
            <h5>Liste des soutenances</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

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

                        <?php if (count($soutenances_list) === 0): ?>

                            <tr>
                                <td colspan="8" class="table-empty">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                    Aucune soutenance enregistrée.
                                </td>
                            </tr>

                        <?php else: ?>

                            <?php foreach ($soutenances_list as $s): ?>

                                <?php
                                $id_soutenance = (int) ($s['id'] ?? 0);

                                $nom_etudiant = trim(
                                    ($s['nom_etudiant'] ?? '') .
                                    ' ' .
                                    ($s['prenoms_etudiant'] ?? '')
                                );

                                if ($nom_etudiant === '') {
                                    $nom_etudiant =
                                        'Étudiant introuvable';
                                }

                                $note_affichee = is_numeric(
                                    $s['note'] ?? null
                                )
                                    ? number_format(
                                        (float) $s['note'],
                                        2,
                                        ',',
                                        ''
                                    )
                                    : '0,00';
                                ?>

                                <tr>

                                    <td>
                                        <?= $id_soutenance; ?>
                                    </td>

                                    <td>
                                        <strong>
                                            <?= h($s['matricule'] ?? ''); ?>
                                        </strong>

                                        <br>

                                        <small class="text-muted">
                                            <?= h($nom_etudiant); ?>
                                        </small>
                                    </td>

                                    <td>
                                        <?= h(
                                            $s['organisme'] ??
                                            'Non défini'
                                        ); ?>

                                        <?php if (!empty($s['lieu_organisme'])): ?>
                                            <br>
                                            <small class="text-muted">
                                                <?= h($s['lieu_organisme']); ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?= h($s['annee_univ'] ?? ''); ?>
                                    </td>

                                    <td>
                                        <span class="note-badge">
                                            <?= h($note_affichee); ?>/20
                                        </span>
                                    </td>

                                    <td>
                                        <?= h($s['date_soutenance'] ?? ''); ?>
                                    </td>

                                    <td class="jury-details">

                                        <strong>Président :</strong>
                                        <?= h($s['president'] ?? ''); ?>

                                        <br>

                                        <strong>Examinateur :</strong>
                                        <?= h($s['examinateur'] ?? ''); ?>

                                        <br>

                                        <strong>Rapporteur int. :</strong>
                                        <?= h($s['rapporteur_int'] ?? ''); ?>

                                        <br>

                                        <strong>Rapporteur ext. :</strong>
                                        <?= h($s['rapporteur_ext'] ?? ''); ?>

                                    </td>

                                    <td class="action-buttons">

                                        <button
                                            type="button"
                                            class="btn btn-warning btn-sm mb-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modif<?= $id_soutenance; ?>"
                                        >
                                            <i class="bi bi-pencil-square"></i>
                                            Modifier
                                        </button>

                                        <form
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Supprimer cette soutenance ?');"
                                        >
                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= $id_soutenance; ?>"
                                            >

                                            <button
                                                type="submit"
                                                name="supprimer"
                                                class="btn btn-danger btn-sm mb-1"
                                            >
                                                <i class="bi bi-trash"></i>
                                                Supprimer
                                            </button>
                                        </form>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</main>


<!-- ======================================================
     MODAL D’AJOUT
     ====================================================== -->

<div
    class="modal fade"
    id="ajoutModal"
    tabindex="-1"
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg modal-dialog-scrollable">

        <div class="modal-content">

            <form method="POST">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Nouvelle soutenance
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>

                </div>

                <div class="modal-body">

                    <label class="form-label">
                        Étudiant
                    </label>

                    <select
                        name="matricule"
                        class="form-select"
                        required
                    >
                        <option value="">
                            Sélectionner un étudiant
                        </option>

                        <?php foreach ($etudiants_list as $e): ?>

                            <option value="<?= h($e['matricule'] ?? ''); ?>">
                                <?= h($e['matricule'] ?? ''); ?>
                                -
                                <?= h($e['nom'] ?? ''); ?>
                                <?= h($e['prenoms'] ?? ''); ?>
                            </option>

                        <?php endforeach; ?>
                    </select>


                    <label class="form-label">
                        Organisme
                    </label>

                    <input
                        type="text"
                        list="listeOrganismesAjout"
                        name="organisme_input"
                        class="form-control"
                        placeholder="Choisissez ou saisissez un organisme"
                        required
                    >

                    <datalist id="listeOrganismesAjout">

                        <?php foreach ($organismes_list as $o): ?>

                            <option
                                value="<?= h(
                                    ($o['idorg'] ?? '') .
                                    ' - ' .
                                    ($o['design'] ?? '') .
                                    ' - ' .
                                    ($o['lieu'] ?? '')
                                ); ?>"
                            ></option>

                        <?php endforeach; ?>

                    </datalist>

                    <small class="text-muted">
                        Vous pouvez sélectionner un organisme existant
                        ou saisir un nouvel organisme.
                    </small>


                    <br>


                    <label class="form-label">
                        Année universitaire
                    </label>

                    <input
                        type="text"
                        name="annee_univ"
                        class="form-control"
                        value="2025-2026"
                        required
                    >


                    <label class="form-label">
                        Note
                    </label>

                    <input
                        type="number"
                        name="note"
                        class="form-control"
                        min="0"
                        max="20"
                        step="0.01"
                        required
                    >


                    <label class="form-label">
                        Président
                    </label>

                    <select
                        name="president"
                        class="form-select"
                        required
                    >
                        <option value="">
                            Sélectionner le président
                        </option>

                        <?php foreach ($professeurs_list as $p): ?>

                            <?php
                            $nom_professeur = trim(
                                ($p['nom'] ?? '') .
                                ' ' .
                                ($p['prenoms'] ?? '')
                            );
                            ?>

                            <option value="<?= h($nom_professeur); ?>">
                                <?= h($nom_professeur); ?>

                                <?php if (!empty($p['grade'])): ?>
                                    - <?= h($p['grade']); ?>
                                <?php endif; ?>
                            </option>

                        <?php endforeach; ?>
                    </select>


                    <label class="form-label">
                        Examinateur
                    </label>

                    <select
                        name="examinateur"
                        class="form-select"
                        required
                    >
                        <option value="">
                            Sélectionner l’examinateur
                        </option>

                        <?php foreach ($professeurs_list as $p): ?>

                            <?php
                            $nom_professeur = trim(
                                ($p['nom'] ?? '') .
                                ' ' .
                                ($p['prenoms'] ?? '')
                            );
                            ?>

                            <option value="<?= h($nom_professeur); ?>">
                                <?= h($nom_professeur); ?>

                                <?php if (!empty($p['grade'])): ?>
                                    - <?= h($p['grade']); ?>
                                <?php endif; ?>
                            </option>

                        <?php endforeach; ?>
                    </select>


                    <label class="form-label">
                        Rapporteur interne
                    </label>

                    <select
                        name="rapporteur_int"
                        class="form-select"
                        required
                    >
                        <option value="">
                            Sélectionner le rapporteur interne
                        </option>

                        <?php foreach ($professeurs_list as $p): ?>

                            <?php
                            $nom_professeur = trim(
                                ($p['nom'] ?? '') .
                                ' ' .
                                ($p['prenoms'] ?? '')
                            );
                            ?>

                            <option value="<?= h($nom_professeur); ?>">
                                <?= h($nom_professeur); ?>

                                <?php if (!empty($p['grade'])): ?>
                                    - <?= h($p['grade']); ?>
                                <?php endif; ?>
                            </option>

                        <?php endforeach; ?>
                    </select>


                    <label class="form-label">
                        Rapporteur externe
                    </label>

                    <select
                        name="rapporteur_ext"
                        class="form-select"
                        required
                    >
                        <option value="">
                            Sélectionner le rapporteur externe
                        </option>

                        <?php foreach ($professeurs_list as $p): ?>

                            <?php
                            $nom_professeur = trim(
                                ($p['nom'] ?? '') .
                                ' ' .
                                ($p['prenoms'] ?? '')
                            );
                            ?>

                            <option value="<?= h($nom_professeur); ?>">
                                <?= h($nom_professeur); ?>

                                <?php if (!empty($p['grade'])): ?>
                                    - <?= h($p['grade']); ?>
                                <?php endif; ?>
                            </option>

                        <?php endforeach; ?>
                    </select>


                    <label class="form-label">
                        Date de soutenance
                    </label>

                    <input
                        type="date"
                        name="date_soutenance"
                        class="form-control"
                        required
                    >

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Annuler
                    </button>

                    <button
                        type="submit"
                        name="ajouter"
                        class="btn btn-success"
                    >
                        <i class="bi bi-check-lg me-2"></i>
                        Enregistrer
                    </button>

                </div>

            </form>

        </div>

    </div>
</div>


<!-- ======================================================
     MODALS DE MODIFICATION
     Placés en dehors du tableau
     ====================================================== -->

<?php foreach ($soutenances_list as $s): ?>

    <?php
    $id_soutenance = (int) ($s['id'] ?? 0);

    $valeur_organisme = trim(
        ($s['idorg'] ?? '') .
        ' - ' .
        ($s['organisme'] ?? '') .
        ' - ' .
        ($s['lieu_organisme'] ?? '')
    );
    ?>

    <div
        class="modal fade"
        id="modif<?= $id_soutenance; ?>"
        tabindex="-1"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg modal-dialog-scrollable">

            <div class="modal-content">

                <form method="POST">

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Modifier la soutenance
                        </h5>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                        ></button>

                    </div>

                    <div class="modal-body">

                        <input
                            type="hidden"
                            name="id"
                            value="<?= $id_soutenance; ?>"
                        >


                        <label class="form-label">
                            Étudiant
                        </label>

                        <select
                            name="matricule"
                            class="form-select"
                            required
                        >

                            <?php foreach ($etudiants_list as $e): ?>

                                <?php
                                $matricule_etudiant =
                                    $e['matricule'] ?? '';
                                ?>

                                <option
                                    value="<?= h($matricule_etudiant); ?>"
                                    <?= (
                                        $matricule_etudiant ===
                                        ($s['matricule'] ?? '')
                                    ) ? 'selected' : ''; ?>
                                >
                                    <?= h($matricule_etudiant); ?>
                                    -
                                    <?= h($e['nom'] ?? ''); ?>
                                    <?= h($e['prenoms'] ?? ''); ?>
                                </option>

                            <?php endforeach; ?>

                        </select>


                        <label class="form-label">
                            Organisme
                        </label>

                        <input
                            type="text"
                            list="listeOrganismesModif<?= $id_soutenance; ?>"
                            name="organisme_input"
                            class="form-control"
                            value="<?= h($valeur_organisme); ?>"
                            required
                        >

                        <datalist
                            id="listeOrganismesModif<?= $id_soutenance; ?>"
                        >

                            <?php foreach ($organismes_list as $o): ?>

                                <option
                                    value="<?= h(
                                        ($o['idorg'] ?? '') .
                                        ' - ' .
                                        ($o['design'] ?? '') .
                                        ' - ' .
                                        ($o['lieu'] ?? '')
                                    ); ?>"
                                ></option>

                            <?php endforeach; ?>

                        </datalist>


                        <label class="form-label">
                            Année universitaire
                        </label>

                        <input
                            type="text"
                            name="annee_univ"
                            class="form-control"
                            value="<?= h($s['annee_univ'] ?? ''); ?>"
                            required
                        >


                        <label class="form-label">
                            Note
                        </label>

                        <input
                            type="number"
                            name="note"
                            class="form-control"
                            min="0"
                            max="20"
                            step="0.01"
                            value="<?= h($s['note'] ?? ''); ?>"
                            required
                        >


                        <label class="form-label">
                            Président
                        </label>

                        <input
                            type="text"
                            name="president"
                            class="form-control"
                            value="<?= h($s['president'] ?? ''); ?>"
                            required
                        >


                        <label class="form-label">
                            Examinateur
                        </label>

                        <input
                            type="text"
                            name="examinateur"
                            class="form-control"
                            value="<?= h($s['examinateur'] ?? ''); ?>"
                            required
                        >


                        <label class="form-label">
                            Rapporteur interne
                        </label>

                        <input
                            type="text"
                            name="rapporteur_int"
                            class="form-control"
                            value="<?= h($s['rapporteur_int'] ?? ''); ?>"
                            required
                        >


                        <label class="form-label">
                            Rapporteur externe
                        </label>

                        <input
                            type="text"
                            name="rapporteur_ext"
                            class="form-control"
                            value="<?= h($s['rapporteur_ext'] ?? ''); ?>"
                            required
                        >


                        <label class="form-label">
                            Date de soutenance
                        </label>

                        <input
                            type="date"
                            name="date_soutenance"
                            class="form-control"
                            value="<?= h($s['date_soutenance'] ?? ''); ?>"
                            required
                        >

                    </div>

                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            Annuler
                        </button>

                        <button
                            type="submit"
                            name="modifier"
                            class="btn btn-warning"
                        >
                            <i class="bi bi-save me-2"></i>
                            Enregistrer les modifications
                        </button>

                    </div>

                </form>

            </div>

        </div>
    </div>

<?php endforeach; ?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>