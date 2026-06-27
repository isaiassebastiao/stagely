<?php
session_start();
require_once "../controller/userServices.php";
require_once "../controller/internshipService.php";
require_once "../controller/evaluationService.php";

$action = $_GET["action"] ?? null;
$course = $_GET["curso"] ?? null;
$method = $_SERVER["REQUEST_METHOD"] ?? null;
$enterpriseId = $_GET["id"] ?? null;
$enterpriseArea = $_GET["area"] ?? null;

changeEnterpriseStatus();
updateInternersStatus();

if ($action === "getEnterprise")
    getEnterprise($_GET["id"]);
else if ($action === "getProfile")
    profilePic();
else if ($action === "fillInternship")
    getDataPerCourse($course);
else if ($action === "getInterners")
    getMyEnterpriseInterners();
else if ($action === "listInternship")
    getStageEnterprisesPerCourse($course);
else if ($action === "setInternship" && $method === "POST")
    setStage();
else if ($action === "addEnterprise" && $method === "POST")
    registerEnterprise();
else if ($action === "deleteEnterprise" && $method === "DELETE")
    deleteEnterprise();
else if ($action === "listEnterprises")
    listEnterprises();
else if ($action === "registerInfo")
    staticData();
else if ($action === "editEnterprise")
    editEnterprise();
else if ($action === "changePassword")
    changePassword();
else if ($action === "editUser")
    editUser();
else if ($action === "generalStats")
    getGeneralStats();
else if ($action === "getEvaluations")
    getEvaluations($course);
else if ($action === "deleteInternship" && $method === "DELETE")
    deleteEnterpriseInternship();
else if ($action === "getEnterpriseInternshipInfo")
    getEnterpriseWithInternshipInfo($enterpriseId, $enterpriseArea, $course);
else if ($action === "updateInternship")
    updateInternship();
else if ($action === "enterpriseInterners")
    getEnterpriseInterners($enterpriseId, $enterpriseArea, $course);