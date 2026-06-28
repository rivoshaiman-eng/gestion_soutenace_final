<?php
include("../includes/config.php");
include("../includes/header.php");
include("../includes/sidebar.php");
?>

<div class="content">
    <h2>Tableau de bord</h2>

    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card p-4 shadow-sm">
                <h3>0</h3>
                <p>Étudiants inscrits</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-4 shadow-sm">
                <h3>0</h3>
                <p>Professeurs</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-4 shadow-sm">
                <h3>0</h3>
                <p>Soutenances effectuées</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-4 shadow-sm">
                <h3>0</h3>
                <p>Non soutenus</p>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header">
            <h5>Effectifs par niveau</h5>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-hover">
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
                    <tr><td>L1</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                    <tr><td>L2</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                    <tr><td>L3</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                    <tr><td>M1</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                    <tr><td>M2</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>