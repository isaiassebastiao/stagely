<?php
require_once __DIR__ . "/../../config/conection.php";
class UserService
{
    private $email, $name, $status, $vacancies, $hood, $street, $password, $actingArea, $imagePath;
    public function registerUser($email, $name, $vacancies, $hood, $street, $actingArea, $password, $imagePath = null)
    {
        global $pdo;
        $this->email = $email;
        $this->name = $name;
        $this->vacancies = $vacancies;
        $this->hood = $hood;
        $this->street = $street;
        $this->password = $password;
        $this->actingArea = $actingArea;
        $this->imagePath = $imagePath;
        $passwordHash = password_hash($this->password, PASSWORD_DEFAULT);

        //validações
        $nameExists = $pdo->prepare("SELECT 1 from empresas where nome=?");
        $nameExists->execute([$this->name]);
        $nameExists = $nameExists->fetchColumn();
        if ($nameExists)
            return ["success" => false, "message" => "Nome já cadastrado"];

        $emailExists = $pdo->prepare("SELECT 1 from empresas where email=?");
        $emailExists->execute([$this->email]);
        $emailExists = $emailExists->fetchColumn();
        if ($emailExists)
            return ["success" => false, "message" => "Email já cadastrado"];
        //

        try {
            $location = $pdo->prepare("INSERT into localizacao (rua,bairro) values (?,?)");
            $location->execute([$this->street, $this->hood]);

            $location = $pdo->prepare("SELECT id from localizacao where rua=? and bairro=?");
            $location->execute([$this->street, $this->hood]);

            $location = $location->fetchColumn();

            $enterprise = $pdo->prepare("INSERT into empresas (nome,email,id_localizacao) values (?,?,?)");
            $enterprise->execute([$this->name, $this->email, $location]);

            $enterpriseId = $pdo->prepare("SELECT id from empresas where email=?");
            $enterpriseId->execute([$this->email]);
            $enterpriseId = $enterpriseId->fetchColumn();

            $availableVacancies = $pdo->prepare("INSERT into vagas_estagio (id_empresa,quantidade) values (?,?)");
            $availableVacancies->execute([$enterpriseId, $this->vacancies]);

            //ramos de atuação da empresa
            foreach ($actingArea as $area) {
                $query = $pdo->prepare("INSERT into ramos_empresa (id_empresa,id_ramo_atuacao) values (?,?)");
                $query->execute([$enterpriseId, $area]);
            }


            $user = $pdo->prepare("INSERT into usuarios(senha,id_tipo_usuario,id_empresa,caminho_imagem_perfil) values (?,?,?,?)");
            $user->execute([$passwordHash, 2, $enterpriseId, $this->imagePath]);

            return ["success" => true, "message" => "Empresa cadastrada com sucesso"];
        } catch (Exception $ex) {
            return ["success" => false, "message" => "Ocorreu algum erro. Tente novamente!"];
        }
    }

    public function listEnterprises()
    {
        global $pdo;
        try {
            $query = $pdo->prepare("SELECT empresas.id,empresas.nome,empresas.email,
            localizacao.rua, usuarios.caminho_imagem_perfil as imagem_perfil,
            localizacao.bairro,status_estagio.status,vagas_estagio.quantidade as vagas from empresas
            join localizacao on localizacao.id=empresas.id_localizacao
            join status_estagio on status_estagio.id=empresas.id_status_empresa
            join usuarios on usuarios.id_empresa=empresas.id
            join vagas_estagio on vagas_estagio.id_empresa=empresas.id
            order by FIELD(status_estagio.status,'Activo','Inactivo'), empresas.nome asc");
            $query->execute();
            $enterprises = $query->fetchAll(PDO::FETCH_ASSOC);

            //ramos de atuação
            foreach ($enterprises as $e_index => $value) {
                $fields = $pdo->prepare("SELECT cursos.nome as ramo_atuacao from ramos_empresa
                join empresas on empresas.id=ramos_empresa.id_empresa
                join ramos_atuacao on ramos_atuacao.id=ramos_empresa.id_ramo_atuacao
                join cursos on cursos.id=ramos_atuacao.id_curso
                where empresas.id=?");
                $fields->execute([$enterprises[$e_index]["id"]]);
                $enterprises[$e_index]["ramos_atuacao"] = $fields->fetchAll(PDO::FETCH_ASSOC);

                $interners = $pdo->prepare("SELECT count(*) from estagiarios where id_empresa=?");
                $interners->execute([$enterprises[$e_index]["id"]]);
                $interners = $interners->fetchColumn();

                //mostrar quantidade atual de vagas
                $enterprises[$e_index]["vagas"] -= $interners;
            }

            return $enterprises;
        } catch (Exception $ex) {
            return ["success" => false, "message" => "Ocorreu algum erro. Tente novamente!"];
        }
    }

    public static function editEnterpriseInfo($id, $email, $name, $vacancies, $hood, $street, $actingArea, $image = null)
    {

        //vars
        global $pdo;
        //code

        //enterprise
        try {
            $updateEnterprise = $pdo->prepare("UPDATE empresas set nome=?,email=? where id=?");
            $updateEnterprise->execute([$name, $email, $id]);

            //location
            $updateLocation = $pdo->prepare("UPDATE localizacao 
            join empresas on empresas.id_localizacao=localizacao.id 
            set rua=?,bairro=? 
            where empresas.id=?");
            $updateLocation->execute([$street, $hood, $id]);

            //profile pic
            if ($image !== null) {
                $profilePic = $pdo->prepare("UPDATE usuarios set caminho_imagem_perfil=? where id_empresa=?");
                $profilePic->execute([$image, $id]);
            }

            //status

            //vacancies
            $updateVacancies = $pdo->prepare("UPDATE vagas_estagio 
            join empresas on empresas.id=vagas_estagio.id_empresa set quantidade=? 
            where empresas.id=?");
            $updateVacancies->execute([$vacancies, $id]);

            //acting area
            $clsArea = $pdo->prepare("DELETE FROM ramos_empresa where id_empresa=?");
            $clsArea->execute([$id]);

            foreach ($actingArea as $area) {
                $updateActingArea = $pdo->prepare("INSERT into ramos_empresa (id_ramo_atuacao,id_empresa) values (?,?)");
                $updateActingArea->execute([$area, $id]);
            }

            return ["success" => true, "message" => "Empresa actualizada com sucesso"];
        } catch (Exception $ex) {
            return ["success" => false, "message" => "Ocorreu algum erro. Tente novamente!"];
        }

    }

    public static function removeEnterprise($id)
    {
        global $pdo;
        try {
            $query = $pdo->prepare("DELETE from status_estagio_empresa where id_empresa=?")->execute([$id]);

            $query = $pdo->prepare("DELETE from empresas where id=?");
            $query->execute([$id]);

            $query = $pdo->prepare("UPDATE alunos
            join estagiarios on estagiarios.id_aluno=alunos.id
            set alunos.id_status_estagio=? where estagiarios.id_empresa=?")->execute([6, $id]);

            $query = $pdo->prepare("DELETE from estagiarios where id_empresa=?")->execute([$id]);


            return ["success" => true, "message" => "Empresa removida com sucesso"];
        } catch (Exception $ex) {
            return ["success" => false, "message" => "Ocorreu algum erro. Tente novamente!"];
        }
    }
    public function getAllStaticData()
    {
        global $pdo;
        try {
            $query = $pdo->prepare("SELECT * from ramos_atuacao");
            $query->execute();
            $ramo_atuacao = $query->fetchAll(PDO::FETCH_ASSOC);

            $query = $pdo->prepare("SELECT * from status_estagio");
            $query->execute();
            $status = $query->fetchAll(PDO::FETCH_ASSOC);

            $query = $pdo->prepare("SELECT id,ano as year from ano_letivo");
            $query->execute();
            $lectiveYears = $query->fetchAll(PDO::FETCH_ASSOC);

            return ["status" => $status, "actingArea" => $ramo_atuacao, "years" => $lectiveYears];
        } catch (Exception $ex) {
            return ["success" => false, "message" => "Ocorreu algum erro. Tente novamente!"];
        }

    }
    public function getDashboard()
    {
        global $pdo;
        $query = $pdo->prepare("SELECT status_estagio");
    }
    public static function changePassword($current, $new)
    {
        global $pdo;

        try {
            switch ($_SESSION["role"]) {
                case "Escola":
                    $query = $pdo->prepare("SELECT senha from usuarios where id=?");
                    $query->execute([$_SESSION["id"]]);
                    if (!password_verify($current, $query->fetchColumn()))
                        return ["success" => false, "message" => "Senha actual incorreta"];
                    $query = $pdo->prepare("UPDATE usuarios set senha=? where id=?");
                    $query->execute([password_hash($new, PASSWORD_DEFAULT), $_SESSION["id"]]);
                    return ["success" => true, "message" => "Senha alterada com sucesso"];
                    break;
                case "Empresa":
                    $query = $pdo->prepare("SELECT senha from usuarios where id=?");
                    $query->execute([$_SESSION["id"]]);
                    if (!password_verify($current, $query->fetchColumn()))
                        return ["success" => false, "message" => "Senha actual incorreta"];
                    $query = $pdo->prepare("UPDATE usuarios set senha=? where id=?");
                    $query->execute([password_hash($new, PASSWORD_DEFAULT), $_SESSION["id"]]);
                    return ["success" => true, "message" => "Senha alterada com sucesso"];
                    break;

            }
        } catch (Exception $ex) {
            return ["success" => false, "message" => "Ocorreu algum erro, tente novamente"];
        }
    }
    public static function getEnterpriseInfo($id)
    {
        global $pdo;
        try {
            $query = $pdo->prepare("SELECT empresas.id,empresas.nome as name,empresas.email as email,localizacao.rua as street,localizacao.bairro as hood,
            usuarios.caminho_imagem_perfil as photo,vagas_estagio.quantidade as vacancies,status_estagio.status as status,
            status_estagio.id as status_id from empresas
            join localizacao on localizacao.id=empresas.id_localizacao
            join usuarios on empresas.id=usuarios.id_empresa
            join vagas_estagio on empresas.id=vagas_estagio.id_empresa
            join status_estagio on empresas.id_status_empresa=status_estagio.id
            where empresas.id=?");
            $query->execute([$id]);
            $enterprise = $query->fetch(PDO::FETCH_ASSOC);

            $query = $pdo->prepare("SELECT ramos_atuacao.id as field_id,ramos_atuacao.nome as area from ramos_empresa
            join empresas on empresas.id=ramos_empresa.id_empresa
            join ramos_atuacao on ramos_atuacao.id=ramos_empresa.id_ramo_atuacao
            where empresas.id=?");
            $query->execute([$id]);
            $enterprise["area_activity"] = $query->fetchAll(PDO::FETCH_ASSOC);

            $query = $pdo->prepare("SELECT count(*) from estagiarios where id_empresa=?");
            $query->execute([$id]);
            $internersQtd = $query->fetchColumn();

            //little update
            $enterprise["vacancies"] -= $internersQtd;

            return ["success" => true, "data" => $enterprise];
        } catch (Exception $ex) {
            return ["success" => false, "message" => "Ocorreu algum erro. Tente novamente!"];
        }
    }
    public static function getProfile($id)
    {
        global $pdo;
        $query = $pdo->prepare("SELECT caminho_imagem_perfil from usuarios where id=?");
        $query->execute([$id]);
        $pic = $query->fetchColumn();

        $query1 = $pdo->prepare("SELECT empresas.nome,empresas.email from usuarios 
        join empresas on empresas.id=usuarios.id_empresa 
        where usuarios.id=?");
        $query1->execute([$id]);
        $query1 = $query1->fetch(PDO::FETCH_ASSOC);

        $query2 = $pdo->prepare("SELECT escola.nome,escola.email from usuarios 
        join escola on escola.id=usuarios.id_escola 
        where usuarios.id=?");
        $query2->execute([$id]);
        $query2 = $query2->fetch(PDO::FETCH_ASSOC);

        $user = $query1 ?: $query2;

        return ["success" => true, "profile_pic" => $pic, "name" => $user["nome"], "email" => $user["email"]];
    }
    public static function editUserInfo($userId, $name, $email, $pic = null)
    {
        global $pdo;
        try {
            $query = $pdo->prepare("SELECT id_escola from usuarios where id=?");
            $query->execute([$userId]);
            $schoolId = $query->fetchColumn();

            $query1 = $pdo->prepare("SELECT id_empresa from usuarios where id=?");
            $query1->execute([$userId]);
            $enterpriseId = $query1->fetchColumn();

            if ($schoolId) {
                $changeName = $pdo->prepare("UPDATE escola set nome=? where id=?");
                $changeName->execute([$name, $schoolId]);

                $changeEmail = $pdo->prepare("UPDATE escola set email=? where id=?");
                $changeEmail->execute([$email, $schoolId]);
            } else {
                $changeName = $pdo->prepare("UPDATE empresas set nome=? where id=?");
                $changeName->execute([$name, $enterpriseId]);

                $changeEmail = $pdo->prepare("UPDATE empresas set email=? where id=?");
                $changeEmail->execute([$email, $enterpriseId]);
            }
            $_SESSION["name"] = $name;

            if ($pic != null) {
                $changePic = $pdo->prepare("UPDATE usuarios set caminho_imagem_perfil=? where id=?");
                $changePic->execute([$pic, $userId]);
            }
            return ["success" => true, "message" => "Perfil actualizado com sucesso"];

        } catch (Exception $ex) {
            return ["success" => false, "message" => "Ocorreu algum erro. Tente novamente!"];
        }
    }
    public function getGeneralInfo()
    {
        global $pdo;
        try {
            $query = $pdo->prepare("SELECT empresas.nome,empresas.id,cursos.nome as curso,
            status_estagio.status from empresas 
            join status_estagio_empresa on status_estagio_empresa.id_empresa=empresas.id
            join status_estagio on status_estagio.id=status_estagio_empresa.id_status_estagio
            join areas_estagio on status_estagio_empresa.id_area_estagio=areas_estagio.id
            join cursos on cursos.id=areas_estagio.id_curso
            ");
            $query->execute();
            $enterprisesStats = $query->fetchAll(PDO::FETCH_ASSOC);

            $query = $pdo->prepare("SELECT alunos.id,ano_letivo.ano as year, cursos.nome,status_estagio.status from alunos 
            join status_estagio on status_estagio.id=alunos.id_status_estagio
            join ano_letivo on ano_letivo.id=alunos.id_ano_letivo
            join cursos on alunos.id_curso=cursos.id");
            $query->execute();
            $studentsStats = $query->fetchAll(PDO::FETCH_ASSOC);

            $query = $pdo->prepare("SELECT * from cursos");
            $query->execute();
            $courses = $query->fetchAll(PDO::FETCH_ASSOC);

            $query = $pdo->prepare("SELECT * from areas_estagio");
            $query->execute();
            $internshipAreas = $query->fetchAll(PDO::FETCH_ASSOC);


            return [
                "internshipStats" => $enterprisesStats,
                "internersStats" => $studentsStats,
                "courses" => $courses,
                "areas" => $internshipAreas
            ];
        } catch (Exception $ex) {
            return ["success" => false, "message" => "Ocorreu algum erro. Tente novamente!"];
        }
    }
    public static function myEnterpriseGeneralInformation($id)
    {
        global $pdo;

        try {
            $query = $pdo->prepare("SELECT curdate()");
            $query->execute();
            $currentDate = new DateTime($query->fetchColumn());

            $query = $pdo->prepare("SELECT id_empresa from usuarios where id=?");
            $query->execute([$id]);
            $enterpriseId = $query->fetchColumn();

            $query = $pdo->prepare("SELECT cursos.nome,status_estagio.status,ano_letivo.ano as year,areas_estagio.nome as area from estagiarios 
            join areas_estagio on areas_estagio.id=estagiarios.id_area_estagio
            join alunos on alunos.id=estagiarios.id_aluno
            join ano_letivo on ano_letivo.id=alunos.id_ano_letivo 
            join status_estagio on alunos.id_status_estagio=status_estagio.id 
            join cursos on alunos.id_curso=cursos.id 
            join empresas on empresas.id=estagiarios.id_empresa 
            where empresas.id=?");
            $query->execute([$enterpriseId]);
            $stats = $query->fetchAll(PDO::FETCH_ASSOC);

            $query = $pdo->prepare("SELECT areas_estagio.nome from areas_empresa 
            join empresas on empresas.id=areas_empresa.id_empresa 
            join areas_estagio on areas_estagio.id=areas_empresa.id_area_estagio where empresas.id=?");
            $query->execute([$enterpriseId]);
            $areas = $query->fetchAll(PDO::FETCH_ASSOC);

            $query = $pdo->prepare("SELECT datas_estagio.inicio,datas_estagio.fim,cursos.nome from datas_estagio 
            join cursos on cursos.id=datas_estagio.id_curso 
            join empresas on empresas.id=datas_estagio.id_empresa 
            where empresas.id=?");
            $query->execute([$enterpriseId]);
            $dates = $query->fetchAll(PDO::FETCH_ASSOC);

            foreach ($dates as $i => $date) {
                $startDate = new DateTime($dates[$i]["inicio"]);
                $endDate = new DateTime($dates[$i]["fim"]);
                if ($currentDate < $startDate)
                    $dates[$i]["status"] = "Pendente";
                else if ($currentDate >= $startDate && $currentDate < $endDate)
                    $dates[$i]["status"] = "Em Execução";
                else if ($currentDate >= $endDate)
                    $dates[$i]["status"] = "Concluído";
            }

            return [
                "internersStats" => $stats,
                "areas" => $areas,
                "internshipStats" => $dates
            ];
        } catch (Exception $ex) {
            return ["success" => false, "message" => "Ocorreu algum erro. Tente novamente!"];
        }
    }
    public static function changeEnterpriseStatus()
    {
        global $pdo;

        try {
            $currentDate = $pdo->prepare("SELECT curdate()");
            $currentDate->execute();
            $currentDate = $currentDate->fetchColumn();
            $currentDate = new DateTime($currentDate);

            $query = $pdo->prepare("SELECT id_empresa as id,id_area_estagio as area_id,
            id_status_estagio as status_id 
            from status_estagio_empresa
            ");
            $query->execute();
            $enterprises = $query->fetchAll(PDO::FETCH_ASSOC);

            //isso começa aqui
            $query = $pdo->prepare("SELECT empresas.id,status_estagio_empresa.id_status_estagio as status_estagio from empresas
            join status_estagio_empresa on status_estagio_empresa.id_empresa=empresas.id");
            $query->execute();
            $isNullArray = $query->fetchAll(PDO::FETCH_ASSOC);

            //não deixar que empresas sem estágio mudem status para "ativo"
            foreach ($isNullArray as $i => $isNull) {
                if (!$isNullArray[$i]["status_estagio"]) {
                    $query = $pdo->prepare("UPDATE status_estagio_empresa set id_status_estagio=? where id_empresa=?");
                    $query->execute([5, $isNullArray[$i]["id"]]);
                }
            }
            //e acaba aqui

            foreach ($enterprises as $i => $v) {

                $start = $pdo->prepare("SELECT inicio from datas_estagio where id_empresa=? and id_area_estagio=? order by inicio asc");
                $start->execute([$enterprises[$i]["id"], $enterprises[$i]["area_id"]]);
                $start = $start->fetchColumn();

                $end = $pdo->prepare("SELECT fim from datas_estagio where id_empresa=? and id_area_estagio=? order by fim desc");
                $end->execute([$enterprises[$i]["id"], $enterprises[$i]["area_id"]]);
                $end = $end->fetchColumn();

                $startDate = new DateTime($start);
                $endDate = new DateTime($end);


                if ($currentDate >= $startDate && $currentDate < $endDate) {
                    /*$query = $pdo->prepare("UPDATE empresas set id_status_empresa=? where id=?");
                    $query->execute([4, $enterprises[$i]["id"]]);*/

                    $query = $pdo->prepare("UPDATE status_estagio_empresa set id_status_estagio=? where id_empresa=? 
                    and id_area_estagio=?");
                    $query->execute([2, $enterprises[$i]["id"], $enterprises[$i]["area_id"]]);

                    $query = $pdo->prepare("UPDATE alunos 
                    join estagiarios on estagiarios.id_aluno=alunos.id
                    set alunos.id_status_estagio=? where estagiarios.id_empresa=? and estagiarios.id_area_estagio=?");
                    $query->execute([$enterprises[$i]["status_id"], $enterprises[$i]["id"], $enterprises[$i]["area_id"]]);

                } else if ($currentDate >= $endDate) {
                    /*$query = $pdo->prepare("UPDATE empresas set id_status_empresa=? where id=?");
                    $query->execute([5, $enterprises[$i]["id"]]);*/

                    $query = $pdo->prepare("UPDATE status_estagio_empresa set id_status_estagio=? where id_empresa=? 
                    and id_area_estagio=?");
                    $query->execute([3, $enterprises[$i]["id"], $enterprises[$i]["area_id"]]);

                    $query = $pdo->prepare("UPDATE alunos 
                    join estagiarios on estagiarios.id_aluno=alunos.id
                    set alunos.id_status_estagio=? where estagiarios.id_empresa=? and estagiarios.id_area_estagio=?");
                    $query->execute([$enterprises[$i]["status_id"], $enterprises[$i]["id"], $enterprises[$i]["area_id"]]);

                } else if ($currentDate < $startDate) {
                    /*$query = $pdo->prepare("UPDATE empresas set id_status_empresa=? where id=?");
                    $query->execute([5, $enterprises[$i]["id"]]);*/

                    $query = $pdo->prepare("UPDATE status_estagio_empresa set id_status_estagio=? where id_empresa=? 
                    and id_area_estagio=?");
                    $query->execute([1, $enterprises[$i]["id"], $enterprises[$i]["area_id"]]);

                    $query = $pdo->prepare("UPDATE alunos 
                    join estagiarios on estagiarios.id_aluno=alunos.id
                    set alunos.id_status_estagio=? where estagiarios.id_empresa=? and estagiarios.id_area_estagio=?");
                    $query->execute([$enterprises[$i]["status_id"], $enterprises[$i]["id"], $enterprises[$i]["area_id"]]);
                }

                //se os estágios não estiverem todos concluídos, não para
                $inactiveDateEnd = $pdo->prepare("SELECT fim from datas_estagio where id_empresa=? order by fim desc");
                $inactiveDateEnd->execute([$enterprises[$i]["id"]]);
                $inactiveDateEnd = new DateTime($inactiveDateEnd->fetchColumn());

                $inactiveDateStart = $pdo->prepare("SELECT inicio from datas_estagio where id_empresa=? order by inicio asc");
                $inactiveDateStart->execute([$enterprises[$i]["id"]]);
                $inactiveDateStart = new DateTime($inactiveDateStart->fetchColumn());

                $recentStatus = $pdo->prepare("SELECT id_status_estagio from status_estagio_empresa where id_empresa=?");
                $recentStatus->execute([$enterprises[$i]["id"]]);
                $recentStatus = $recentStatus->fetchAll(PDO::FETCH_COLUMN);

                if ($currentDate >= $inactiveDateStart && $currentDate < $inactiveDateEnd && in_array(2, $recentStatus))
                    $query = $pdo->prepare("UPDATE empresas set id_status_empresa=? where id=?")->execute([4, $enterprises[$i]["id"]]);
                else
                    $query = $pdo->prepare("UPDATE empresas set id_status_empresa=? where id=?")->execute([5, $enterprises[$i]["id"]]);

            }
            return $enterprises;
        } catch (Exception $ex) {
            return ["success" => false, "message" => "Ocorreu algum erro. Tente novamente!"];
        }
    }
}