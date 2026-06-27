<?php
require_once __DIR__ . "/../../../backend/routes/sessionControl.php";
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
  <link rel="stylesheet" href="../../assets/css/pages/entrar/index.css" />
  <title>Entrar na Aplicação | Stagely</title>
</head>

<body>
  <?php include "../../components/global/icons/icons.php"; ?>
  <?php include "../../components/global/alert/alert.php"; ?>
  <?php include "../../components/pages/entrar/sectionLogin.php"; ?>
</body>
<script src="../../assets/js/global/alert.js"></script>
<script src="../../assets/js/pages/entrar/login.js"></script>
<script src="../../assets/js/global/theme.js"></script>
<script src="../../assets/js/global/tooglePassword.js"></script>

</html>
