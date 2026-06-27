<?php
require_once __DIR__ . ("/../../config/conection.php");
header("content-type: application/json");
class UserAutenticator
{

  private $email;
  private $password;

  public function authenticateUser($email, $password)
  {

    global $pdo;

    $this->email = $email;
    $this->password = $password;

    $stmtEnterprise = $pdo->prepare("SELECT usuarios.id,tipos_usuario.tipo as role, 
    usuarios.senha, empresas.email,empresas.nome as name FROM usuarios 
    JOIN tipos_usuario on usuarios.id_tipo_usuario = tipos_usuario.id 
    join empresas on empresas.id=usuarios.id_empresa 
    WHERE empresas.email = ?");

    $stmtAdmin = $pdo->prepare("SELECT usuarios.id,tipos_usuario.tipo as role, usuarios.senha, 
    escola.email,escola.nome as name FROM usuarios JOIN tipos_usuario on usuarios.id_tipo_usuario = tipos_usuario.id 
    join escola on escola.id=usuarios.id_escola 
    WHERE escola.email = ?");

    $stmtAdmin->execute([$this->email]);
    $stmtEnterprise->execute([$this->email]);

    $enterprise = $stmtEnterprise->fetch(PDO::FETCH_ASSOC);
    $admin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);

    $user = $enterprise ? $enterprise : $admin;

    if ($user) {
      if (password_verify($this->password, $user['senha'])) {

        $_SESSION['id'] = $user['id'];

        //Para mexer mais tarde
        $_SESSION['role'] = $user['role'];
        $_SESSION["name"] = $user["name"];

        if ($user['role'] == 'Escola') {
          //retorna o usuário logado para a tela de admnistrador...
          return ['success' => true, 'id' => $user['id'], 'role' => $user['role'], 'message' => 'Login realizado com sucesso!'];
        }
        //retorna o usuário logado para a tela de empresas...
        return ['success' => true, 'id' => $user['id'], 'role' => $user['role'], 'message' => 'Login realizado com sucesso!'];
      }
      //retonra a mensagem de erro dizendo que a senha está incorreta...
      return ['success' => false, 'message' => 'Email ou senha inválidos'];
    }
    //retorna mensagem de erro dizendo que este usuário não existe...
    return ['success' => false, 'message' => 'Email ou senha inválidos'];
  }
}
