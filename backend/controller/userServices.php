<?php
require_once __DIR__ . "/../model/user/userServices.php";
require_once __DIR__ . "/../functions/validations/credentials.php";
require_once __DIR__ . "/../functions/validations/files.php";
$user = $_POST ?: json_decode(file_get_contents("php://input"), true);
$services = new UserService();
$photo = $_FILES["photo"] ?? null;
function registerEnterprise()
{
    global $user;
    global $services;
    global $photo;
    $imagePath = null;
    //validações ficarão aqui

    if (check_empty_credentials($user)) {
        echo json_encode(["success" => false, "message" => "Preencha todos os campos"]);
        die();
    }

    //LOCALSAVE
    if ($photo) {
        $imageInfo = doAllImageThings($photo, $user["name"]);

        $imagePath = $imageInfo["success"] ? ["path" => $imageInfo["path"], "getPath" => $imageInfo["getPath"]] : $imageInfo;

        if (!$imageInfo["success"]) {
            echo json_encode($imageInfo);
            die();
        }
    }

    //DBSAVE
    $ans = $services->registerUser(
        $user["email"],
        $user["name"],
        $user["vacancies"],
        $user["hood"],
        $user["street"],
        $user["area_activity"],
        $user["password"],
        $imagePath["getPath"] ?? null
    );

    echo json_encode($ans);
    die();
}

function listEnterprises()
{
    global $services;
    echo json_encode($services->listEnterprises());
}

function staticData()
{
    global $services;
    echo json_encode($services->getAllStaticData());
}
function changePassword()
{
    global $user;
    echo json_encode(UserService::changePassword($user["current_password"], $user["new_password"]));
}
function getEnterprise($id)
{
    echo json_encode(UserService::getEnterpriseInfo($id));
}
function editEnterprise()
{
    global $user, $photo;

    $imagePath = null;
    //validações ficarão aqui

    if (check_empty_credentials($user)) {
        echo json_encode(["success" => false, "message" => "Fill all the fields"]);
        die();
    }

    if ($photo) {
        $imageInfo = doAllImageThings($photo, $user["name"]);

        $imagePath = $imageInfo["success"] ? ["path" => $imageInfo["path"], "getPath" => $imageInfo["getPath"]] : $imageInfo;

        if (!$imageInfo["success"]) {
            echo json_encode($imageInfo);
            die();
        }
    }

    $ans = UserService::editEnterpriseInfo(
        $user["id"],
        $user["email"],
        $user["name"],
        $user["vacancies"],
        $user["hood"],
        $user["street"],
        $user["area_activity"],
        $imagePath["getPath"] ?? null
    );

    echo json_encode($ans);
    die();
}
function deleteEnterprise()
{
    global $user;

    $userInfo = UserService::getEnterpriseInfo($user["id"]);

    $localDelete = deleteEnterpriseFiles($userInfo["data"]["name"]);

    if (!$localDelete["success"]) {
        echo json_encode(["success" => false, "message" => "Error"]);
        die();
    }

    echo json_encode(UserService::removeEnterprise($user["id"]));
}
function profilePic()
{
    echo json_encode(UserService::getProfile($_SESSION["id"]));
}
function editUser()
{
    global $user;
    global $photo;
    $imagePath = null;

    if (check_empty_credentials($user)) {
        echo json_encode(["success" => false, "message" => "Preencha todos os campos"]);
        die();
    }

    if ($photo) {
        $imageInfo = doAllImageThings($photo, $user["name"]);

        $imagePath = $imageInfo["success"] ? ["path" => $imageInfo["path"], "getPath" => $imageInfo["getPath"]] : $imageInfo;

        if (!$imageInfo["success"]) {
            echo json_encode($imageInfo);
            die();
        }
    }

    $ans = UserService::editUserInfo(
        $_SESSION["id"],
        $user["name"],
        $user["email"],
        $imagePath["getPath"] ?? null
    );

    echo json_encode($ans);
    die();
}
function getGeneralStats()
{
    global $services;
    echo json_encode($services->getGeneralInfo());
}
function myEnterpriseGeneralStats()
{
    echo json_encode(UserService::myEnterpriseGeneralInformation($_SESSION["id"]));
}
function changeEnterpriseStatus()
{
    UserService::changeEnterpriseStatus();
}