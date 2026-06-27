<?php
session_start();
require_once "../controller/internshipService.php";
require_once "../controller/userServices.php";
$course = $_GET["curso"] ?? null;
$action = $_GET["action"];
switch ($action) {
    case "interners":
        getMyEnterpriseInterners();
        break;
    case "staticData":
        staticData();
        break;
}