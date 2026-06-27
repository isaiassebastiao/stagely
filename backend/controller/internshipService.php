<?php
require_once __DIR__ . "/../functions/validations/credentials.php";
require_once __DIR__ . "/../model/internship/internshipService.php";
$student = json_decode(file_get_contents("php://input"), true);
$internship = new InternshipServices();
function listStudents()
{
    global $internship;
    global $student;
    if (!$internship->listAvailableStudents()["success"]) {
        echo json_encode(["success" => false, "message" => "Nenhum aluno disponível"]);
        die();
    }
    echo json_encode($internship->listAvailableStudents());
    die();
}
function setStage()
{
    global $internship;
    global $student;

    $validDatetime = validDatetime($student["time_entry"], $student["time_out"], $student["date_start"], $student["date_end"]);

    if (check_empty_credentials($student)) {
        echo json_encode(["success" => false, "message" => "Preencha todos os campos"]);
        die();
    } else if (!$validDatetime["success"]) {
        echo json_encode($validDatetime);
        die();
    }


    echo json_encode($internship->assignStudentToInternship(
        $student["enterprise"],
        $student
    ));
}
function internshipStatus()
{
    global $internship;
    if (!$internship->getCompanyInternshipStatus($_SESSION["id"])["success"]) {
        echo json_encode(["success" => false]);
        die();
    }
    echo json_encode($internship->getCompanyInternshipStatus($_SESSION["id"]));
}
function updateVacancies()
{
    global $internship, $student;
    $update = $internship->updateInternshipVacancy($_SESSION["id"], $student["newVacancies"]);
    if (!$update["success"]) {
        echo json_encode(["success" => false]);
        die();
    }
    echo json_encode($update["success"]);
}
function getAvailableVacancies()
{
    global $internship;
    if (!$internship->getInternshipVacanciesAvailalable($_SESSION["id"])["success"]) {
        echo json_encode(["success" => false]);
        die();
    }
    echo json_encode($internship->getInternshipVacanciesAvailalable($_SESSION["id"]));
}
function getDataPerCourse($course)
{
    echo json_encode([
        "enterprises" => InternshipServices::getEnterprisesPerCourse($course),
        "students" => InternshipServices::getStudentsPerCourse($course)
    ]);
}
function getStageEnterprisesPerCourse($course)
{
    echo json_encode([
        "enterprises" => InternshipServices::getEnterpriseWithStagePerCourse($course)
    ]);
}
function getMyEnterpriseInterners($id = null)
{
    echo json_encode(InternshipServices::myEnterpriseInteners($_SESSION["id"]));
}
function deleteEnterpriseInternship()
{
    global $student;
    echo json_encode(InternshipServices::deleteInternship($student["id"], $student["curso"], $student["area"]));
}
function getEnterpriseWithInternshipInfo($id, $area, $course)
{
    echo json_encode(InternshipServices::getEnterpriseInternshipInfo($id, $area, $course));
}
function updateInternship()
{
    global $student;
    $validDate = validDateOnUpdate($student["date_start"], $student["date_end"], $student["status"]);
    $validTime = validTime($student["time_entry"], $student["time_out"]);

    if (check_empty_credentials($student)) {
        echo json_encode(["success" => false, "message" => "Preencha todos os campos"]);
        die();
    }

    if (!$validTime["success"]) {
        echo json_encode($validTime);
        die();
    } else if (!$validDate["success"]) {
        echo json_encode($validDate);
        die();
    }

    echo json_encode(InternshipServices::updateInternship(
        $student["id"],
        $student["area_internship"],
        $student["students"],
        $student["date_start"],
        $student["date_end"],
        $student["time_entry"],
        $student["time_out"],
        $student["days"],
        $student["receivedStudents"],
        $student["course"],
        $student["oldAreaId"]
    ));
}
function getInternerInfo($internerId)
{
    echo json_encode(InternshipServices::getInternerInfo($internerId));
}
function updateInternersStatus()
{
    InternshipServices::updateInternersStatus($_SESSION["id"]);
}
function getEnterpriseInterners($id, $area, $course)
{
    echo json_encode(InternshipServices::getEnterpriseInterners($id, $area, $course));
}