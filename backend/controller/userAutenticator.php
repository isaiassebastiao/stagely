<?php
require_once("../model/auth/UserAutenticator.php");
require_once("../functions/validations/credentials.php");
$user = json_decode(file_get_contents("php://input"), true);

function authenticateUser()
{
  global $user;
  $check = check_credentials($user["email"], $user["password"])["success"];
  if (!$check) {
    echo json_encode(["success" => false, "message" => "Verifique as suas credenciais"]);
    die();
  }
  $auth = new UserAutenticator();
  $auth_result = $auth->authenticateUser($user["email"], $user["password"]);
  if (!$auth_result["success"]) {
    echo json_encode($auth_result);
    die();
  }
  echo json_encode($auth_result);
}
