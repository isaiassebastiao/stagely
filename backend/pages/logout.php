<?php

//não inclua o arquivo de sessões aqui.
session_start();

if (isset($_SESSION['id'])) {
  session_destroy();
  header('Location: /stagely/frontend/pages/entrar');
  }
  header('Location: /stagely/frontend/pages/entrar');
