<?php
require_once __DIR__ . "/../model/evaluation/evaluationService.php";
require_once __DIR__ . "/../functions/validations/files.php";

$evaluation = $_FILES["avaliacao"] ?? null;
$evaluationService = new EvaluationService();
function saveEvaluation($internerId, $replace = false)
{
  global $evaluation, $evaluationService;
  $path = null;

  $info = EvaluationService::internerInfo($internerId);

  $check = validateFile($evaluation);
  if (!$check["success"]) {
    echo json_encode($check);
    die();
  }

  $evaluationInfo = doAllEvaluationThings($evaluation, $info["nome"], $info["curso"]);

  if (!$evaluationInfo["success"]) {
    echo json_encode($evaluationInfo);
    die();
  }

  $path = ["path" => $evaluationInfo["path"], "getPath" => $evaluationInfo["getPath"]];

  $backup = $evaluationService->importEvaluationForms(
    $evaluation["tmp_name"],
    $internerId,
    $path["getPath"],
    $replace
  );

  if (!$backup["success"]) {
    echo json_encode($backup);
    die();
  }
  echo json_encode($backup);
}
function getEvaluations($course)
{
  global $evaluationService;
  $evaluations = $evaluationService->exportEvaluationForms($course);
  $existentEvaluations = getExistentEvaluationsFromDir($evaluations);
  echo json_encode($existentEvaluations);
}
function getEnterpriseEvaluations()
{
  global $evaluationService;
  $evaluations=$evaluationService->exportEnterpriseEvaluations($_SESSION["id"]);
  $existentEvaluations=getExistentEvaluationsFromDir($evaluations);
  echo json_encode($existentEvaluations);
}
