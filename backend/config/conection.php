<?php
$pdo = new PDO("mysql:host=localhost;dbname=db_stagely", "root", "");
if (!$pdo) {
  die();
}