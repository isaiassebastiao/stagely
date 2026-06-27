<?php
require_once __DIR__ . "/../../config/conection.php";
header("content-type:application/json");

#criei um exemplo da tablea relatórios(pode alterar depois), mas não tá no database;
#relatorios(id, id_estagiario, evaluation_form_path, import_date)
class EvaluationService
{

  private $intern_id, $file_bin;
  private $evaluation_form_path;

  public static function internerInfo($id)
  {
    global $pdo;
    $query = $pdo->prepare("SELECT cursos.nome as curso, alunos.nome from estagiarios
        join alunos on alunos.id=estagiarios.id_aluno
        join cursos on cursos.id=alunos.id_curso
        where estagiarios.id=?");
    $query->execute([$id]);
    $info = $query->fetch(PDO::FETCH_ASSOC);
    return $info;
  }

  public function importEvaluationForms($file_bin, $intern_id, $evaluation_form_path, $replace = false)
  {

    global $pdo;

    $this->intern_id = $intern_id;
    $this->file_bin = $file_bin;
    $this->evaluation_form_path = $evaluation_form_path;

    try {
      $studentId = $pdo->prepare("SELECT id_aluno from estagiarios where id=?");
      $studentId->execute([$this->intern_id]);
      $studentId = $studentId->fetchColumn();

      $query = $pdo->prepare("SELECT id_curso from alunos where id=?");
      $query->execute([$studentId]);
      $studentCourseId = $query->fetchColumn();

      $query = $pdo->prepare("SELECT id_area_estagio from estagiarios where id_aluno=?");
      $query->execute([$studentId]);
      $areaId = $query->fetchColumn();

      $query = $pdo->prepare("SELECT curdate()");
      $query->execute();
      $currentDate = $query->fetchColumn();
      $currentDate = new DateTime($currentDate);

      $query = $pdo->prepare("SELECT datas_estagio.fim from empresas
            join estagiarios on estagiarios.id_empresa=empresas.id
            join alunos on alunos.id=estagiarios.id_aluno
            join datas_estagio on datas_estagio.id_empresa=empresas.id
            join areas_estagio on areas_estagio.id=datas_estagio.id_area_estagio
            where estagiarios.id=? and datas_estagio.id_curso=? and datas_estagio.id_area_estagio=?
            order by datas_estagio.fim desc limit 1");
      $query->execute([$intern_id, $studentCourseId, $areaId]);

      $farthestEndDate = $query->fetchColumn();
      $farthestEndDate = new DateTime($farthestEndDate);

      if ($currentDate < $farthestEndDate)
        return [
          "success" => false,
          "message" => "Avaliações só são enviadas após a conclusão geral do estágio: " . $farthestEndDate->format("d/m/Y")
        ];

      $evaluationExists = $pdo->prepare("SELECT 1 from avaliacoes where id_aluno=?");
      $evaluationExists->execute([$studentId]);
      $evaluationExists = $evaluationExists->fetchColumn();

      if ($evaluationExists && $replace == false)
        return ["success" => false, "found" => true, "message" => "Uma avalição já existe. Deseja substituí-la?"];

      if ($replace === true) {
        $remove = $pdo->prepare("DELETE from avaliacoes where id_aluno=?");
        $remove->execute([$studentId]);
      }

      $stmt = $pdo->prepare("INSERT INTO avaliacoes (bin,id_aluno, caminho_arquivo) VALUES(?,?,?)");
      $stmt->execute([
        $this->file_bin,
        $studentId,
        $this->evaluation_form_path
      ]);

      $query = $pdo->prepare("UPDATE alunos set id_status_estagio=? where id=?");
      $query->execute([3, $studentId]);

      return ['success' => true, 'message' => 'Avaliação enviada com sucesso!'];
    } catch (Exception $ex) {
      return ['success' => false, 'message' => "Ocorreu algum erro. Tente novamente!"];
    }

  }

  public function exportEvaluationForms($course)
  {

    global $pdo;

    try {
      $getCourse = $pdo->prepare("SELECT id from cursos where nome like '%($course)%'");
      $getCourse->execute();
      $courseId = $getCourse->fetchColumn();

      $stmt = $pdo->prepare("SELECT alunos.id,alunos.nome,avaliacoes.caminho_arquivo,ano_letivo.ano as year from avaliacoes
            join alunos on alunos.id=avaliacoes.id_aluno
            join cursos on cursos.id=alunos.id_curso
            join ano_letivo on ano_letivo.id=alunos.id_ano_letivo
            where cursos.id=?");
      $stmt->execute([$courseId]);

      $exportedEvaluationForm = $stmt->fetchAll(PDO::FETCH_ASSOC);

      return ['success' => true, 'data' => $exportedEvaluationForm];
    } catch (Exception $ex) {
      return ["success" => false, "message" => "Ocorreu algum erro. Tente novamente!"];
    }
  }
  public function exportEnterpriseEvaluations($id)
  {
    global $pdo;
    try {
      $query = $pdo->prepare("SELECT id_empresa from usuarios where id=?");
      $query->execute([$id]);
      $enterpriseId = $query->fetchColumn();

      $query = $pdo->prepare("SELECT alunos.nome,avaliacoes.caminho_arquivo,ano_letivo.ano as year from avaliacoes
            join alunos on alunos.id=avaliacoes.id_aluno
            join ano_letivo on ano_letivo.id=alunos.id_ano_letivo
            join estagiarios on alunos.id=estagiarios.id_aluno
            where estagiarios.id_empresa=?");
      $query->execute([$enterpriseId]);
      $evaluations = $query->fetchAll(PDO::FETCH_ASSOC);

      return ["success" => true, "data" => $evaluations];
    } catch (Exception $ex) {
      return ["success" => false, "message" => "Ocorreu algum erro. Tente novamente!"];
    }
  }
}
