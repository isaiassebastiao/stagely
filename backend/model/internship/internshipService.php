<?php
require_once __DIR__ . "/../../config/conection.php";
header("Content-type:application/json");

class InternshipServices
{

  private $company_id;

  private $array_of_students_without_internship;

  public function getCompanyInternshipStatus($company_id)
  {

    global $pdo;

    $this->company_id = $company_id;

    $stmt = $pdo->prepare("SELECT status_estagio.status FROM status_estagio_empresa
        JOIN status_estagio on status_estagio_empresa.id_status_estagio = status_estagio.id
        WHERE status_estagio_empresa.id_empresa = ?");

    $stmt->execute([$this->company_id]);

    $companyInternshipStatus = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($companyInternshipStatus) {
      return ['success' => true, 'data' => $companyInternshipStatus];
    }
    return ['success' => false, 'message' => 'Algo deu errado ao solicitar o estado do estágio da empresa'];
  }

  #acredito que o histórico fica na ficha de avaliação então de momento essa função estará inativa
  #public function insternshipHistory(){}

  public function listAvailableStudents()
  {

    global $pdo;

    $stmt = $pdo->prepare("SELECT alunos.id, alunos.nome, alunos.data_nascimento, alunos.bilhete_identidade, alunos.genero, cursos.nome AS 'curso', encarregados.nome AS 'encarregado', status_estagio.status FROM alunos JOIN cursos ON alunos.id_curso = cursos.id JOIN encarregados ON alunos.id_encarregado = encarregados.id JOIN status_estagio ON alunos.id_status_estagio = status_estagio.id WHERE status_estagio.status = 'Pendente'");

    $stmt->execute();

    $availableStudents = $stmt->fetchALL(PDO::FETCH_ASSOC);

    if ($availableStudents) {
      return ['success' => true, 'data' => $availableStudents];
    }
    return ['success' => false, 'message' => 'Erro ao listar alunos disponíveis para as vagas de estágio, tente novamente!'];
  }

  public function createIntershipVacancy($company_id, $qtd)
  {

    global $pdo;

    $this->company_id = $company_id;
    try {
      $stmt = $pdo->prepare("INSERT INTO vagas_estagio(id_empresa, quantidade) VALUES(?,?)");
      $stmt->execute([$this->company_id, $qtd]);

      return ['success' => true, 'message' => 'Vagas de estágio cadastrada com sucesso!'];
    } catch (Exception $ex) {
      return ['success' => false, 'message' => 'Ocorreu algum erro. Tente novamente!'];
    }
  }



  public function assignStudentToInternship($company_id, $array_of_students_without_internship)
  {

    global $pdo;

    $this->company_id = $company_id;
    $this->array_of_students_without_internship = $array_of_students_without_internship;

    $course = $this->array_of_students_without_internship["course"];

    //Processo de cadastro
    try {
      $getCourse = $pdo->prepare("SELECT id from cursos where nome like '%($course)%'");
      $getCourse->execute();
      $courseId = $getCourse->fetchColumn();

      $total_vacancies = $this->getInternshipVacanciesAvailalable($this->company_id)["data"]["quantidade"];
      $total_students = $pdo->prepare("SELECT count(*) from estagiarios
            join empresas on empresas.id=estagiarios.id_empresa
            where empresas.id=?");

      $total_students->execute([$company_id]);
      $total_students = $total_students->fetchColumn();

      //validações
      if ($total_vacancies - $total_students === 0)
        return ["success" => false, "message" => "No more available vacancies"];

      $area = $pdo->prepare("SELECT id from areas_estagio where nome=? and id_curso=?");
      $area->execute([
        $this->array_of_students_without_internship["area_internship"],
        $courseId
      ]);
      $area = $area->fetchColumn();

      if (!$area) {
        $query = $pdo->prepare("INSERT into areas_estagio (nome,id_curso) values (?,?)");
        $query->execute([$this->array_of_students_without_internship["area_internship"], $courseId]);
      }

      $id_area_internship = $pdo->prepare("SELECT id from areas_estagio where nome=? and id_curso=?");
      $id_area_internship->execute([
        $this->array_of_students_without_internship["area_internship"],
        $courseId
      ]);
      $id_area_internship = $area ? $area : $id_area_internship->fetchColumn();

      //verificar se já existe estágio para a área
      $query = $pdo->prepare("SELECT 1 from areas_empresa where id_empresa=? and id_area_estagio=?");
      $query->execute([$company_id, $id_area_internship]);
      $areaHasStage = $query->fetchColumn();

      if ($areaHasStage)
        return ["success" => false, "message" => "Cada área deve conter um estágio apenas"];

      //verificar se aquele horário já existe
      $shiftExists = $pdo->prepare("SELECT id from horarios_estagio
            where id_empresa=? and entrada=? and saida=? and id_curso=? and id_area_estagio=?");

      $shiftExists->execute([
        $company_id,
        $this->array_of_students_without_internship["time_entry"],
        $this->array_of_students_without_internship["time_out"],
        $courseId,
        $id_area_internship
      ]);

      $shiftExists = $shiftExists->fetchColumn();

      if (!$shiftExists) {
        $shift = $pdo->prepare("INSERT into horarios_estagio (id_empresa,entrada,saida,id_curso,id_area_estagio) values (?,?,?,?,?)");
        $shift->execute([
          $company_id,
          $this->array_of_students_without_internship["time_entry"],
          $this->array_of_students_without_internship["time_out"],
          $courseId,
          $id_area_internship
        ]);
      }
      //pegar o id caso tenha sido inserido recently
      $id_shift = $pdo->prepare("SELECT id from horarios_estagio
            where id_empresa=?
            and entrada=?
            and saida=?
            and id_curso=? and id_area_estagio=? order by id desc");
      $id_shift->execute([
        $company_id,
        $this->array_of_students_without_internship["time_entry"],
        $this->array_of_students_without_internship["time_out"],
        $courseId,
        $id_area_internship
      ]);

      $id_shift = $shiftExists ?: $id_shift->fetchColumn();

      //verificar a data e tals
      $dateExists = $pdo->prepare("SELECT id from datas_estagio where inicio=? and fim=? and id_empresa=? and id_curso=? and id_area_estagio=?");
      $dateExists->execute([
        $this->array_of_students_without_internship["date_start"],
        $this->array_of_students_without_internship["date_end"],
        $company_id,
        $courseId,
        $id_area_internship
      ]);
      $dateExists = $dateExists->fetchColumn();

      if (!$dateExists) {
        $query = $pdo->prepare("INSERT into datas_estagio (inicio,fim,id_empresa,id_curso,id_area_estagio) values (?,?,?,?,?)");
        $query->execute([
          $this->array_of_students_without_internship["date_start"],
          $this->array_of_students_without_internship["date_end"],
          $company_id,
          $courseId,
          $id_area_internship
        ]);
      }

      $recentDate = $pdo->prepare("SELECT id from datas_estagio where inicio=? and fim=? and id_empresa=? and id_curso=? and id_area_estagio=?");
      $recentDate->execute([
        $this->array_of_students_without_internship["date_start"],
        $this->array_of_students_without_internship["date_end"],
        $company_id,
        $courseId,
        $id_area_internship
      ]);


      $checkInternshipArea = $pdo->prepare("SELECT 1 from areas_empresa where id_area_estagio=? and id_empresa=?");
      $checkInternshipArea->execute([$id_area_internship, $company_id]);
      $checkInternshipArea = $checkInternshipArea->fetchColumn();

      if (!$checkInternshipArea) {
        $internship_areas = $pdo->prepare("INSERT into areas_empresa (id_area_estagio,id_empresa) values (?,?)");
        $internship_areas->execute([$id_area_internship, $company_id]);
      }

      $internship_status = $pdo->prepare("INSERT INTO status_estagio_empresa (id_empresa,id_status_estagio,id_area_estagio) values (?,?,?)");
      $internship_status->execute([$company_id, 1, $id_area_internship]);

      foreach ($this->array_of_students_without_internship["days"] as $day) {
        $checkAssignedDay = $pdo->prepare("SELECT 1 from dias_horario where id_horario=? and id_dia_estagio=?");
        $checkAssignedDay->execute([$id_shift, $day]);
        $checkAssignedDay = $checkAssignedDay->fetchColumn();

        if (!$checkAssignedDay) {
          $query = $pdo->prepare("INSERT into dias_horario (id_horario,id_dia_estagio) values (?,?)");
          $query->execute([
            $id_shift,
            $day
          ]);
        }
      }

      foreach ($this->array_of_students_without_internship["students"] as $student) {
        $query = $pdo->prepare("INSERT into estagiarios (id_aluno,id_area_estagio,id_empresa) values (?,?,?)");
        $query->execute([
          $student,
          $id_area_internship,
          $this->array_of_students_without_internship["enterprise"]
        ]);

        $query = $pdo->prepare("UPDATE alunos set id_status_estagio=? where id=?");
        $query->execute([1, $student]);
      }
      return ["success" => true, "message" => "Estágio cadastrado com sucesso"];

    } catch (Exception $ex) {
      return ["success" => false, "message" => "Ocorreu algum erro. Tente novamente!"];
    }
  }


  public function updateInternshipVacancy($company_id, $amount)
  {
    global $pdo;

    $stmt = $pdo->prepare("UPDATE vagas_estagio SET quantidade = ? WHERE id_empresa = ?");
    $internship_vacancy_updated = $stmt->execute([$amount, $company_id]);

    if ($internship_vacancy_updated) {
      return ['success' => true, 'message' => 'Vagas de estágio atualizadas com sucesso!'];
    }
    return ['success' => false, 'message' => 'Erro ao atualizar a vagas de estágio, tente novamente!'];
  }

  public function getInternshipVacanciesAvailalable($company_id)
  {

    global $pdo;

    $this->company_id = $company_id;

    $stmt = $pdo->prepare("SELECT quantidade, id_empresa FROM vagas_estagio WHERE id_empresa = ?");
    $stmt->execute([$this->company_id]);

    $number_of_vacancies_available = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($number_of_vacancies_available) {
      return ['success' => true, 'data' => $number_of_vacancies_available];
    }
    return ['success' => false, 'message' => 'Erro ao listar a quantidade de vagas disponíveis desta empresa, tente novamente!'];
  }

  public static function getStudentsPerCourse($course)
  {
    //tenho de conseguir pegar só os alunos sem estágio
    global $pdo;
    $query = $pdo->prepare("SELECT alunos.id,alunos.nome from alunos
        join cursos on cursos.id=alunos.id_curso
        join status_estagio on status_estagio.id=alunos.id_status_estagio
        where cursos.nome like '%($course)%' and status_estagio.status='Indisponível'
        ");
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
  }
  public static function getEnterprisesPerCourse($course)
  {
    global $pdo;
    $query = $pdo->prepare("SELECT empresas.id,empresas.nome,
         usuarios.caminho_imagem_perfil as imagem_perfil from ramos_empresa
         join empresas on empresas.id=ramos_empresa.id_empresa
         join ramos_atuacao on ramos_atuacao.id=ramos_empresa.id_ramo_atuacao
         join cursos on cursos.id=ramos_atuacao.id_curso
         join usuarios on usuarios.id_empresa=empresas.id
         where cursos.nome like '%($course)%'");
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
  }
  public static function getEnterpriseWithStagePerCourse($course)
  {
    global $pdo;
    try {
      $getCourse = $pdo->prepare("SELECT id from cursos where nome like '%($course)%'");
      $getCourse->execute();
      $courseId = $getCourse->fetchColumn();

      //this query ain't mine...
      $query = $pdo->prepare("SELECT DISTINCT empresas.id,empresas.nome,usuarios.caminho_imagem_perfil as imagem_perfil,
            areas_estagio.nome as area_estagio,areas_estagio.id as area_id
            from empresas
            join usuarios on empresas.id=usuarios.id_empresa
            join areas_empresa on areas_empresa.id_empresa=empresas.id
            join areas_estagio on areas_estagio.id=areas_empresa.id_area_estagio
            join estagiarios on empresas.id=estagiarios.id_empresa
            join alunos on alunos.id=estagiarios.id_aluno
            join cursos on cursos.id=alunos.id_curso
            where areas_estagio.id_curso=?");
      $query->execute([$courseId]);
      $enterprises = $query->fetchAll(PDO::FETCH_ASSOC);

      foreach ($enterprises as $i => $e) {
        $query = $pdo->prepare("SELECT horarios_estagio.entrada,horarios_estagio.saida,datas_estagio.inicio,
                datas_estagio.fim from horarios_estagio
                join datas_estagio on datas_estagio.id_area_estagio=horarios_estagio.id_area_estagio
                where horarios_estagio.id_curso=? and datas_estagio.id_curso=? and horarios_estagio.id_empresa=? and
                datas_estagio.id_empresa=? and horarios_estagio.id_area_estagio=? and horarios_estagio.id_area_estagio=?");
        $query->execute([$courseId, $courseId, $e["id"], $e["id"], $e["area_id"], $e["area_id"]]);

        $enterprises[$i]["datetime"] = $query->fetch(PDO::FETCH_ASSOC);

        $query = $pdo->prepare("SELECT status_estagio.status from status_estagio_empresa
                join status_estagio on status_estagio.id=status_estagio_empresa.id_status_estagio
                where status_estagio_empresa.id_empresa=? and status_estagio_empresa.id_area_estagio=?");
        $query->execute([$e["id"], $e["area_id"]]);

        $enterprises[$i]["status"] = $query->fetchColumn();

      }

      //gambiarra para ordenar de acordo ao status
      $statuses = ["Em execução" => 1, "Pendente" => 2, "Concluído" => 3];

      usort($enterprises, function ($a, $b) use ($statuses) {
        $cmp = $statuses[$a["status"]] <=> $statuses[$b["status"]];

        if ($cmp === 0)
          return $a["nome"] <=> $b["nome"];

        return $cmp;
      });
      //I didn't write it, so don't ask me how it works

      return $enterprises;
    } catch (Exception $ex) {
      return ["success" => false, "message" => "Ocorreu algum erro. Tente novamente!"];
    }
  }
  public static function myEnterpriseInteners($id)
  {
    global $pdo;
    try {
      $query = $pdo->prepare("SELECT id_empresa from usuarios where id=?");
      $query->execute([$id]);
      $enterpriseId = $query->fetchColumn();

      $query = $pdo->prepare("SELECT estagiarios.id,alunos.id as id_aluno,alunos.nome,cursos.nome as curso,areas_estagio.nome as area_estagio,status_estagio.status from alunos
            join estagiarios on estagiarios.id_aluno=alunos.id
            join cursos on cursos.id=alunos.id_curso
            join status_estagio on status_estagio.id=alunos.id_status_estagio
            join empresas on empresas.id=estagiarios.id_empresa
            join areas_estagio on areas_estagio.id=estagiarios.id_area_estagio
            where empresas.id=? order by FIELD(status_estagio.status,'Em execução','Pendente','Concluído'),alunos.nome asc");
      $query->execute([$enterpriseId]);

      $interners = $query->fetchAll(PDO::FETCH_ASSOC);
      return $interners;
    } catch (Exception $ex) {
      return ["success" => false, "message" => "Ocorreu algum erro. Tente novamente!"];
    }
  }

  public static function deleteInternship($enterpriseId, $internersCourse, $internersArea)
  {
    global $pdo;
    try {
      $getCourse = $pdo->prepare("SELECT id from cursos where nome like '%($internersCourse)%'");
      $getCourse->execute();
      $internersCourse = $getCourse->fetchColumn();

      $getArea = $pdo->prepare("SELECT id from areas_estagio where nome=? and id_curso=?");
      $getArea->execute([$internersArea, $internersCourse]);
      $internersArea = $getArea->fetchColumn();
      //os gets acabam aqui

      $query = $pdo->prepare("DELETE horarios_estagio from horarios_estagio where id_empresa=?
            and id_area_estagio=? and id_curso=?");
      $query->execute([$enterpriseId, $internersArea, $internersCourse]);

      $query = $pdo->prepare("DELETE datas_estagio from datas_estagio
            where id_empresa=? and id_area_estagio=? and id_curso=?");
      $query->execute([$enterpriseId, $internersArea, $internersCourse]);

      $query = $pdo->prepare("UPDATE alunos
            join estagiarios on estagiarios.id_aluno=alunos.id
            set alunos.id_status_estagio=?
            where alunos.id_curso=? and estagiarios.id_empresa=? and estagiarios.id_area_estagio=?");
      $query->execute([6, $internersCourse, $enterpriseId, $internersArea]);

      $query = $pdo->prepare("DELETE estagiarios from estagiarios
            join alunos on alunos.id=estagiarios.id_aluno
            where alunos.id_curso=? and estagiarios.id_empresa=? and estagiarios.id_area_estagio=?");
      $query->execute([$internersCourse, $enterpriseId, $internersArea]);

      $query = $pdo->prepare("DELETE from areas_empresa
            where id_empresa=? and id_area_estagio=?");
      $query->execute([$enterpriseId, $internersArea]);

      $query = $pdo->prepare("DELETE from status_estagio_empresa where id_empresa=? and id_area_estagio=?");
      $query->execute([$enterpriseId, $internersArea]);

      $query = $pdo->prepare("UPDATE empresas set id_status_empresa=? where id=?");
      $query->execute([5, $enterpriseId]);

      return ["success" => true, "message" => "Estágio deletado com sucesso"];
    } catch (Exception $ex) {
      return ["success" => false, "message" => "Ocorreu algum erro. Tente novamente!"];
    }
  }
  public static function getEnterpriseInternshipInfo($id, $area, $course)
  {
    global $pdo;
    try {

      $getCourse = $pdo->prepare("SELECT id from cursos where nome like '%($course)%'");
      $getCourse->execute();
      $courseId = $getCourse->fetchColumn();

      $getArea = $pdo->prepare("SELECT id from areas_estagio where nome=? and id_curso=?");
      $getArea->execute([$area, $courseId]);
      $area = $getArea->fetchColumn();

      $query = $pdo->prepare("SELECT usuarios.caminho_imagem_perfil as imagem_perfil,empresas.id,empresas.nome,
            status_estagio.status,status_estagio.id as id_status from empresas
            join usuarios on empresas.id=usuarios.id_empresa
            join estagiarios on estagiarios.id_empresa=empresas.id
            join status_estagio_empresa on status_estagio_empresa.id_empresa=empresas.id
            join status_estagio on status_estagio_empresa.id_status_estagio=status_estagio.id
            join datas_estagio on datas_estagio.id_empresa=empresas.id where empresas.id=?
            and status_estagio_empresa.id_area_estagio=? and datas_estagio.id_curso=?");
      $query->execute([$id, $area, $courseId]);

      $enterprise = $query->fetch(PDO::FETCH_ASSOC);

      $query = $pdo->prepare("SELECT entrada,saida from horarios_estagio where id_empresa=? and id_area_estagio=? and id_curso=?");
      $query->execute([$id, $area, $courseId]);

      $enterprise["shifts"] = $query->fetchAll(PDO::FETCH_ASSOC);

      $query = $pdo->prepare("SELECT inicio from datas_estagio where id_empresa=? and id_area_estagio=? and id_curso=? order by inicio asc");
      $query->execute([$id, $area, $courseId]);
      $enterprise["inicio"] = $query->fetchColumn();

      $query = $pdo->prepare("SELECT fim from datas_estagio where id_empresa=? and id_area_estagio=? and id_curso=? order by fim desc");
      $query->execute([$id, $area, $courseId]);
      $enterprise["fim"] = $query->fetchColumn();

      $query = $pdo->prepare("SELECT DISTINCT alunos.id, alunos.nome from estagiarios
            join alunos on alunos.id=estagiarios.id_aluno where estagiarios.id_empresa=?
            and estagiarios.id_area_estagio=? and alunos.id_curso=?");
      $query->execute([$id, $area, $courseId]);
      $enterprise["interners"] = $query->fetchAll(PDO::FETCH_ASSOC);

      $query = $pdo->prepare("SELECT areas_estagio.nome,areas_estagio.id as area_id from areas_empresa
            join empresas on empresas.id=areas_empresa.id_empresa
            join areas_estagio on areas_estagio.id=areas_empresa.id_area_estagio where empresas.id=?
            and areas_estagio.id=? and areas_estagio.id_curso=?");
      $query->execute([$id, $area, $courseId]);
      $enterprise["areas"] = $query->fetchAll(PDO::FETCH_ASSOC);

      $query = $pdo->prepare("SELECT DISTINCT dias_estagio.id from dias_horario
            join horarios_estagio on horarios_estagio.id=dias_horario.id_horario
            join dias_estagio on dias_estagio.id=dias_horario.id_dia_estagio
            join empresas on empresas.id=horarios_estagio.id_empresa where empresas.id=?
            and horarios_estagio.id_area_estagio=? and horarios_estagio.id_curso=?");
      $query->execute([$id, $area, $courseId]);
      $enterprise["days"] = $query->fetchAll(PDO::FETCH_ASSOC);

      return ["success" => true, "data" => $enterprise];
    } catch (Exception $ex) {
      return ["success" => false, "message" => "Ocorreu algum erro. Tente novamente!"];
    }
  }
  public static function getEnterpriseInternshipValues($id)
  {
    global $pdo;
    try {
      $query = $pdo->prepare("SELECT empresas.id,empresas.nome");
    } catch (Exception $ex) {
      return ["success" => false, "message" => "Ocorreu algum erro. Tente novamente!"];
    }
  }
  public static function updateInternship($enterpriseId, $area, $studentsArr, $dateStart, $dateEnd, $timeStart, $timeEnd, $daysArr, $resArr, $course, $oldAreaId)
  {
    global $pdo;
    try {
      $query = $pdo->prepare("SELECT id from cursos where nome like '%($course)%'");
      $query->execute();
      $course = $query->fetchColumn();
      //start
      $areaExists = $pdo->prepare("SELECT 1 from areas_estagio where nome=? and id_curso=?");
      $areaExists->execute([$area, $course]);
      $areaExists = $areaExists->fetchColumn();

      if (!$areaExists)
        $query = $pdo->prepare("INSERT into areas_estagio (nome,id_curso) values (?,?)")->execute([$area, $course]);
      //end
      $query = $pdo->prepare("SELECT id from areas_estagio where nome=? and id_curso=?");
      $query->execute([$area, $course]);
      $newAreaId = $query->fetchColumn();

      //usar o id "antigo" se o que vier do database for igual a ele
      $areaId = $newAreaId === $oldAreaId ? $oldAreaId : $newAreaId;

      //ojanhbgvy
      $query = $pdo->prepare("SELECT id_area_estagio from areas_empresa where id_empresa=? and id_area_estagio=?");
      $query->execute([$enterpriseId, $areaId]);
      $areaHasStage = $query->fetchColumn();

      if ($areaHasStage && $areaHasStage !== $oldAreaId)
        return ["success" => false, "message" => "Cada área deve conter um estágio apenas"];
      //ends here

      $update = $pdo->prepare("UPDATE status_estagio_empresa set id_area_estagio=?
            where id_empresa=? and id_area_estagio=?")->execute([$areaId, $enterpriseId, $oldAreaId]);

      $update = $pdo->prepare("UPDATE areas_empresa set id_area_estagio=? where id_empresa=? and id_area_estagio=?")->execute([$areaId, $enterpriseId, $oldAreaId]);

      $query = $pdo->prepare("SELECT status_estagio.status from status_estagio_empresa
            join status_estagio on status_estagio.id=status_estagio_empresa.id_status_estagio
            where status_estagio_empresa.id_empresa=? and status_estagio_empresa.id_area_estagio=?");
      $query->execute([$enterpriseId, $areaId]);

      $curStatus = $query->fetchColumn();

      if ($curStatus === "Concluído")
        return ["success" => false, "message" => "Estágios já concluídos não são editáveis"];

      $query = $pdo->prepare("UPDATE horarios_estagio
            set entrada=?,saida=?,id_area_estagio=?
            where id_empresa=? and id_area_estagio=?");
      $query->execute([$timeStart, $timeEnd, $areaId, $enterpriseId, $oldAreaId]);

      $query = $pdo->prepare("UPDATE datas_estagio
            set inicio=?,fim=?,id_area_estagio=?
            where id_empresa=? and id_area_estagio=?");
      $query->execute([$dateStart, $dateEnd, $areaId, $enterpriseId, $oldAreaId]);


      $query = $pdo->prepare("SELECT id from horarios_estagio
            where id_empresa=? and id_area_estagio=?");
      $query->execute([$enterpriseId, $areaId]);
      $timeId = $query->fetchColumn();

      $query = $pdo->prepare("DELETE from dias_horario where id_horario=?");
      $query->execute([$timeId]);

      foreach ($daysArr as $day) {
        $query = $pdo->prepare("INSERT into dias_horario (id_horario,id_dia_estagio) values (?,?)");
        $query->execute([$timeId, $day]);
      }

      $update = $pdo->prepare("UPDATE estagiarios set id_area_estagio=? where id_empresa=? and id_area_estagio=?")
        ->execute([$areaId, $enterpriseId, $oldAreaId]);

      $query = $pdo->prepare("SELECT id_aluno from estagiarios where id_empresa=? and id_area_estagio=?");
      $query->execute([$enterpriseId, $areaId]);
      $internersIds = $query->fetchAll(PDO::FETCH_COLUMN);

      foreach ($studentsArr as $student) {
        if (!in_array($student, $internersIds)) {
          $query = $pdo->prepare("INSERT into estagiarios (id_aluno,id_empresa,id_area_estagio) values (?,?,?)");
          $query->execute([$student, $enterpriseId, $areaId]);
        }
      }

      foreach ($resArr as $remove) {
        if (!in_array($remove, $studentsArr)) {
          $query = $pdo->prepare("UPDATE alunos set id_status_estagio=? where id=?");
          $query->execute([6, $remove]);

          $query = $pdo->prepare("DELETE from estagiarios where id_aluno=?");
          $query->execute([$remove]);
        }
      }

      return ["success" => true, "message" => "Estágio actualizado com sucesso"];
    } catch (Exception $ex) {
      return ["success" => false, "message" => "Ocorreu algum erro. Tente novamente!"];
    }
  }
  public static function updateInternersStatus($id)
  {
    global $pdo;
    try {
      $currentDate = new DateTime();

      $query = $pdo->prepare("SELECT id_empresa from usuarios where id=?");
      $query->execute([$id]);
      $enterpriseId = $query->fetchColumn();

      $query = $pdo->prepare("SELECT id_empresa as id,id_area_estagio as area_id,
            id_status_estagio as status_id
            from status_estagio_empresa where id_empresa=?");
      $query->execute([$enterpriseId]);

      $enterprise = $query->fetchAll(PDO::FETCH_ASSOC);

      foreach ($enterprise as $e) {
        $start = $pdo->prepare("SELECT inicio from datas_estagio where id_empresa=? and id_area_estagio=? order by inicio asc");
        $start->execute([$e["id"], $e["area_id"]]);
        $start = $start->fetchColumn();

        $end = $pdo->prepare("SELECT fim from datas_estagio where id_empresa=? and id_area_estagio=? order by fim desc");
        $end->execute([$e["id"], $e["area_id"]]);
        $end = $end->fetchColumn();

        $startDate = new DateTime($start);
        $endDate = new DateTime($end);

        if ($currentDate >= $startDate && $currentDate < $endDate) {
          $query = $pdo->prepare("UPDATE alunos
                    join estagiarios on estagiarios.id_aluno=alunos.id
                    set alunos.id_status_estagio=? where estagiarios.id_empresa=? and estagiarios.id_area_estagio=?");
          $query->execute([$e["status_id"], $e["id"], $e["area_id"]]);
        } else if ($currentDate >= $endDate) {
          $query = $pdo->prepare("UPDATE alunos
                    join estagiarios on estagiarios.id_aluno=alunos.id
                    set alunos.id_status_estagio=? where estagiarios.id_empresa=? and estagiarios.id_area_estagio=?");
          $query->execute([$e["status_id"], $e["id"], $e["area_id"]]);
        } else if ($currentDate < $startDate) {
          $query = $pdo->prepare("UPDATE alunos
                    join estagiarios on estagiarios.id_aluno=alunos.id
                    set alunos.id_status_estagio=? where estagiarios.id_empresa=? and estagiarios.id_area_estagio=?");
          $query->execute([$e["status_id"], $e["id"], $e["area_id"]]);
        }
      }

      $query = $pdo->prepare("SELECT id from alunos");
      $query->execute();
      $studentsId = $query->fetchAll(PDO::FETCH_COLUMN);

      foreach ($studentsId as $id) {
        $query = $pdo->prepare("SELECT 1 from avaliacoes where id_aluno=?");
        $query->execute([$id]);
        $result = $query->fetchColumn();
        if ($result) {
          $query = $pdo->prepare("UPDATE alunos set id_status_estagio=? where id=?");
          $query->execute([3, $id]);
        }
      }
    } catch (Exception $ex) {
      return ["success" => false, "message" => "Ocorreu algum erro. Tente novamente!"];
    }
  }
  public static function getInternerInfo($id)
  {
    global $pdo;
    try {
      $interner = $pdo->prepare("SELECT id_area_estagio as area,id_empresa as empresa from estagiarios where id=?");
      $interner->execute([$id]);
      $interner = $interner->fetch(PDO::FETCH_ASSOC);

      $query = $pdo->prepare("SELECT alunos.nome from estagiarios
            join alunos on alunos.id=estagiarios.id_aluno
            where estagiarios.id=?");
      $query->execute([$id]);
      $name = $query->fetchColumn();

      $query = $pdo->prepare("SELECT cursos.nome from estagiarios
            join alunos on alunos.id=estagiarios.id_aluno
            join cursos on cursos.id=alunos.id_curso
            where estagiarios.id=?");
      $query->execute([$id]);
      $course = $query->fetchColumn();

      $query = $pdo->prepare("SELECT areas_estagio.nome from estagiarios
            join areas_estagio on areas_estagio.id=estagiarios.id_area_estagio
            where estagiarios.id=?");
      $query->execute([$id]);
      $area = $query->fetchColumn();

      $query = $pdo->prepare("SELECT horarios_estagio.entrada,horarios_estagio.saida from estagiarios
            join empresas on empresas.id=estagiarios.id_empresa
            join horarios_estagio on horarios_estagio.id_empresa=empresas.id
            join areas_estagio on areas_estagio.id=horarios_estagio.id_area_estagio
            where estagiarios.id=? and areas_estagio.id=?");
      $query->execute([$id, $interner["area"]]);
      $shifts = $query->fetch(PDO::FETCH_ASSOC);

      $query = $pdo->prepare("SELECT datas_estagio.inicio,datas_estagio.fim from estagiarios
            join empresas on empresas.id=estagiarios.id_empresa
            join datas_estagio on datas_estagio.id_empresa=empresas.id
            join areas_estagio on areas_estagio.id=datas_estagio.id_area_estagio
            where estagiarios.id=? and areas_estagio.id=?");
      $query->execute([$id, $interner["area"]]);
      $dates = $query->fetch(PDO::FETCH_ASSOC);

      $query = $pdo->prepare("SELECT dias_estagio.dia from dias_horario join horarios_estagio on horarios_estagio.id=dias_horario.id_horario join empresas on horarios_estagio.id_empresa=empresas.id
            join dias_estagio on dias_estagio.id=dias_horario.id_dia_estagio
            join estagiarios on empresas.id=estagiarios.id_empresa
            join areas_estagio on horarios_estagio.id_area_estagio=areas_estagio.id
            where estagiarios.id=? and areas_estagio.id=?");
      $query->execute([$id, $interner["area"]]);
      $days = $query->fetchAll(PDO::FETCH_ASSOC);

      $query = $pdo->prepare("SELECT status_estagio.status from estagiarios
            join alunos on alunos.id=estagiarios.id_aluno
            join status_estagio on status_estagio.id=alunos.id_status_estagio
            where estagiarios.id=?");
      $query->execute([$id]);
      $status = $query->fetchColumn();

      return [
        "success" => true,
        "data" => [
          "nome" => $name,
          "curso" => $course,
          "areas" => $area,
          "inicio" => $dates["inicio"],
          "fim" => $dates["fim"],
          "days" => $days,
          "shift" => $shifts,
          "status" => $status
        ]
      ];
    } catch (Exception $ex) {
      return ["success" => false, "message" => "Ocorreu algum erro. Tente novamente!"];
    }
  }
  public static function getEnterpriseInterners($id, $area, $course)
  {
    global $pdo;
    try {
      $query = $pdo->prepare("SELECT id from cursos where nome like '%($course)%'");
      $query->execute();
      $courseId = $query->fetchColumn();

      $query = $pdo->prepare("SELECT id from areas_estagio where nome=? and id_curso=?");
      $query->execute([$area, $courseId]);
      $areaId = $query->fetchColumn();

      $query = $pdo->prepare("SELECT alunos.id,alunos.nome from estagiarios
            join alunos on alunos.id=estagiarios.id_aluno
            where estagiarios.id_empresa=? and estagiarios.id_area_estagio=?");
      $query->execute([$id, $areaId]);
      $students = $query->fetchAll(PDO::FETCH_ASSOC);

      foreach ($students as $i => $s) {
        $students[$i]["interner"] = true;
      }

      $query = $pdo->prepare("SELECT alunos.id,alunos.nome from alunos
            join status_estagio on alunos.id_status_estagio=status_estagio.id
            join cursos on alunos.id_curso=cursos.id
            where cursos.id=? and status_estagio.id=?");
      $query->execute([$courseId, 6]);
      $stagelessStudents = $query->fetchAll(PDO::FETCH_ASSOC);

      if (count($stagelessStudents) > 0)
        foreach ($stagelessStudents as $stageless) {
          array_push($students, $stageless);
        }

      return ["success" => true, "data" => $students];
    } catch (Exception $ex) {
      return ["success" => false, "message" => "Ocorreu algum erro. Tente novamente!"];
    }
  }
}
