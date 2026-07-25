<!DOCTYPE html>
<html lang="fr">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Gestion des Soutenances</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

html,
body{
    width:100%;
    min-height:100%;
    overflow-x:hidden;
    overflow-y:auto;
    background:#f5f7fb;
    font-family:Segoe UI,Tahoma,Geneva,Verdana,sans-serif;
}

/* ==============================
   SIDEBAR
================================ */

.sidebar{

    position:fixed;

    top:0;
    left:0;

    width:260px;
    height:100vh;

    background:#ffffff;

    border-right:1px solid #e5e7eb;

    overflow-y:auto;

    z-index:1000;

    padding:20px;
}

/* ==============================
   CONTENU
================================ */

.content{

    margin-left:260px;

    min-height:100vh;

    padding:30px;

}

/* ==============================
   LOGO
================================ */

.logo{

    font-size:30px;

    color:#2563eb;

}

/* ==============================
   MENU
================================ */

.menu a{

    display:block;

    padding:12px 15px;

    margin-top:8px;

    border-radius:12px;

    color:#334155;

    text-decoration:none;

    transition:.25s;

    font-weight:500;

}

.menu a:hover{

    background:#2563eb;

    color:white;

}

.menu a.active{

    background:#2563eb;

    color:white;

}

/* ==============================
   CARDS
================================ */

.card{

    border:none;

    border-radius:16px;

    box-shadow:0 6px 18px rgba(0,0,0,.08);

}

/* ==============================
   TABLE
================================ */

.table th{

    background:#f8fafc;

    color:#475569;

    text-transform:uppercase;

    font-size:13px;

}

/* ==============================
   BOUTONS
================================ */

.btn{

    border-radius:10px;

}

/* ==============================
   MODAL
================================ */

.modal{

    overflow-y:auto !important;

}

.modal-dialog{

    margin:30px auto;

}

.modal-dialog-scrollable{

    max-height:calc(100% - 60px);

}

.modal-content{

    border:none;

    border-radius:16px;

}

.modal-body{

    max-height:70vh;

    overflow-y:auto;

}

.modal-header{

    border-bottom:1px solid #e5e7eb;

}

.modal-footer{

    border-top:1px solid #e5e7eb;

}

/* ==============================
   FORM
================================ */

.form-control,
.form-select{

    min-height:45px;

    border-radius:10px;

}

/* ==============================
   RESPONSIVE
================================ */

@media(max-width:992px){

.sidebar{

    width:100%;

    height:auto;

    position:relative;

}

.content{

    margin-left:0;

    padding:20px;

}

}

</style>

</head>

<body></body>