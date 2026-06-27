<?php
session_start();
require_once "../controller/evaluationService.php";
require_once "../controller/internshipService.php";
require_once "../controller/userServices.php";

updateInternersStatus();

$internerId = $_GET["id"] ?? null;
$replace = isset($_GET["replace"]) && $_GET["replace"] === '1';
$action = $_GET["action"] ?? null;

if ($action === "saveEvaluation")
  saveEvaluation($internerId, $replace);
else if ($action === "getEvaluations")
  getEnterpriseEvaluations();
else if ($action === "generalStats")
  myEnterpriseGeneralStats();
else if ($action === "getInternerInfo")
  getInternerInfo($internerId);
