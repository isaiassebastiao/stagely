<?php
//inclua esse arquivo em suas páginas protegidas...
session_start();

$userId = $_SESSION['id'] ?? null;
$userRole = $_SESSION['role'] ?? null;

//$currentPage = basename($_SERVER['SCRIPT_NAME'], '.php');
$currentPage = $_SERVER["SCRIPT_NAME"];

//redirecionar usuário não logado para a página de login...
if ($currentPage!="/stagely/frontend/pages/entrar/index.php" && !$userId) {
    header('Location: /stagely/frontend/pages/entrar/index.php');
    exit;
}

//se tiver na página de login e tiver um usuário logado, lhe redirecionar para a sua página com base no tipo de usuário...
if ($currentPage === '/stagely/frontend/pages/entrar/index.php' && $userId) {

    if ($userRole == 'Escola') {
        header('Location: /stagely/frontend/pages/inicio/index.php');
    } else {
        header('Location: ./companyPage.php');
    }
    exit;
}