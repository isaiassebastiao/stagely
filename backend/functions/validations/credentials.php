<?php
/*
strlen:string length
count:array length
str_contains: like the JS "includes" function
filter_var($var,filter_type): validar/sanitizar variáveis
*/
function check_email($email)
{
  $email = filter_var($email, FILTER_SANITIZE_EMAIL);
  $email_length = strlen($email);
  $min_length = 8;
  $max_length = 30;

  if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    return false;

  if ($email_length < $min_length || $email_length > $max_length)
    return false;

  return true;
}

function check_password($password)
{
  $password = trim($password);
  $password = filter_var($password, FILTER_SANITIZE_EMAIL);
  $min_length = 8;
  $max_length = 20;
  $pass_length = strlen($password);

  if ($pass_length < $min_length || $pass_length > $max_length)
    return false;

  if (str_contains($password, " "))
    return false;

  return true;
}

//função de verificação principal para login:
function check_credentials($email, $password)
{
  return check_password($password) && check_email($email) ?
    ["success" => true, "message" => "Credenciais verificadas com sucesso"] :
    ["success" => false, "message" => "Email ou senha inválidos"];
}

function check_empty_credentials($arr)
{
  $res = false;
  foreach ($arr as $a => $value) {
    if (is_array($arr[$a]))
      $res = check_empty_credentials($arr[$a]);

    if (empty($arr[$a]) || !isset($arr[$a]))
      return true;
  }
  return $res;
}

function validTime($entry, $exit)
{
  try {
    $in = new DateTime($entry);
    $out = new DateTime($exit);
    return ["success" => true];
  } catch (Exception $ex) {
    return ["success" => false, "message" => "Formato de hora inválido"];
  }
}
function validDate($start, $end)
{
  try {
    $current = new DateTime();
    $current->setTime(0, 0, 0, 0);

    $dateStart = new DateTime($start);
    $dateStart->setTime(0, 0, 0, 0);

    $dateEnd = new DateTime($end);
    $dateEnd->setTime(0, 0, 0, 0);

    $yearStart = (int) $dateStart->format("Y");
    $yearEnd = (int) $dateEnd->format("Y");
    $thisYear = (int) $current->format("Y");

    if ($dateStart < $current)
      return ["success" => false, "message" => "Data de início anterior a data actual"];
    else if ($dateStart > $dateEnd)
      return ["success" => false, "message" => "Data de início posterior a data de término"];
    else if ($dateEnd < $current)
      return ["success" => false, "message" => "Data de término anterior a data actual"];
    else if ($dateEnd == $dateStart)
      return ["success" => false, "message" => "Datas de início e término iguais"];
    else if ($current == $dateEnd)
      return ["success" => false, "message" => "Datas de término igual a data actual"];

    if (strlen($dateStart->format("Y")) > 4 || strlen($dateStart->format("Y")) < 4)
      return ["success" => false, "message" => "Comprimento de data inválido"];
    else if (strlen($dateEnd->format("Y")) > 4 || strlen($dateEnd->format("Y")) < 4)
      return ["success" => false, "message" => "Comprimento de data inválido"];

    if (($yearStart > $thisYear + 1) || ($yearEnd > $thisYear + 1))
      return ["success" => false, "message" => "Início e fim não podem ser superiores a 1 ano relativo ao anual"];

    return ["success" => true];
  } catch (Exception $ex) {
    return ["success" => false, "message" => "Formato de data inválido"];
  }
}
function validDatetime($entry, $exit, $start, $end)
{
  $date = validDate($start, $end);
  $time = validTime($entry, $exit);

  if (!$date["success"])
    return $date;
  else if (!$time["success"])
    return $time;

  return ["success" => true];
}
function validDateOnUpdate($start, $end, $status)
{
  try {
    $current = new DateTime();
    $current->setTime(0, 0, 0, 0);

    $dateStart = new DateTime($start);
    $dateStart->setTime(0, 0, 0, 0);

    $dateEnd = new DateTime($end);
    $dateEnd->setTime(0, 0, 0, 0);

    $yearStart = (int) $dateStart->format("Y");
    $yearEnd = (int) $dateEnd->format("Y");
    $thisYear = (int) $current->format("Y");

    if ($dateStart < $current && $status === "Pendente")
      return ["success" => false, "message" => "Data de início anterior a data actual"];
    else if ($dateStart > $dateEnd)
      return ["success" => false, "message" => "Data de início posterior a data de término"];
    else if ($dateEnd < $current)
      return ["success" => false, "message" => "Data de término anterior a data actual"];
    else if ($dateEnd == $dateStart)
      return ["success" => false, "message" => "Datas de início e término iguais"];
    else if ($current == $dateEnd)
      return ["success" => false, "message" => "Data de término igual a data actual"];

    if (strlen($dateStart->format("Y")) > 4 || strlen($dateStart->format("Y")) < 4)
      return ["success" => false, "message" => "Comprimento de data inválido"];
    else if (strlen($dateEnd->format("Y")) > 4 || strlen($dateEnd->format("Y")) < 4)
      return ["success" => false, "message" => "Comprimento de data inválido"];

    if (($yearStart > $thisYear + 1) || ($yearEnd > $thisYear + 1))
      return ["success" => false, "message" => "Início e fim não podem ser superiores a 1 ano relativo ao anual"];

    return ["success" => true];
  } catch (Exception $ex) {
    return ["success=>false", "message" => "Formato de data inválido"];
  }
}