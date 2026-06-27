<?php
require_once __DIR__ . "/../../../backend/routes/sessionControl.php";
if($_SESSION["role"]!=="Escola")
  header("location: /stagely/frontend/pages/inicio")
?>
<!doctype html>
<html lang="pt">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="../../assets/favicons/favicon.ico" type="image/x-icon" />
  <script>
    (function () {
      const theme = localStorage.getItem('stagely-theme') ||
        (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
      if (theme === 'light') {
        document.documentElement.setAttribute('data-theme', 'light');
      }
    })();
  </script>
  <link rel="stylesheet" href="../../assets/css/pages/editar-estagio/index.css" />
  <title>Editar Estágio | Stagely</title>
</head>

<body>
  <?php include "../../components/global/icons/icons.php"; ?>
  <?php include "../../components/global/alert/alert.php"; ?>
  <?php include "../../components/global/aside/aside.php"; ?>
  <?php include "../../components/global/header/header.php"; ?>
  <?php include "../../components/pages/editar-estagio/sectionUpdate.php"; ?>
</body>
<script src="../../assets/js/global/aside.js"></script>
<script src="../../assets/js/global/header.js"></script>
<script src="../../assets/js/global/theme.js"></script>
<script src="../../assets/js/global/screen.js"></script>
<script src="../../assets/js/global/alert.js"></script>
<script src="../../assets/js/global/dropdown.js"></script>
<script src="../../assets/js/global/select.js"></script>
<script src="../../assets/js/pages/editar-estagio/fillSelect.js"></script>
<script src="../../assets/js/pages/editar-estagio/updateInternship.js"></script>

</html>
