<?php
session_start();
require_once "../controller/userServices.php";
require_once "../controller/userAutenticator.php";
$action = $_GET["action"] ?? null;
if ($action === "getProfile")
    profilePic();
else if ($action === "login")
    authenticateUser();
else if ($action === "changePassword")
    changePassword();