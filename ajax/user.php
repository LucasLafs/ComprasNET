<?php
require("conexao.php");

if($_REQUEST['act']){
  if ($_REQUEST['act'] == 'editUser') {
    $idUser = $_REQUEST['idUser'];
    return editUser($idUser);
  }
}


function editUser($idUser)
{
  $con = bancoMysqli();

  $nome = $_POST['nome'];
  $email = $_POST['email'];
  $passAtual = $_POST['passAtual'] != '' ? $_POST['passAtual'] : '';
  $newPass = $_POST['newPass'] != '' ? $_POST['newPass'] : '';
  $confirmPass = $_POST['confirmPass'] ? $_POST['confirmPass'] : '';

  $updatePass = '';

  if ($newPass != '') {
    $sql = "SELECT * FROM usuarios where senha = '" . md5($passAtual) . "'";

    if (mysqli_num_rows(mysqli_query($con, $sql)) == 0 ) {
      echo json_encode(['response' => ' A senha atual está incorreta', 'status' => 'error'], 200);
      return false;
    }


    if ($newPass != $confirmPass) {
      echo json_encode(['response' => ' As senhas não conferem', 'status' => 'error'], 200);
      return false;
    }

    $updatePass = ", senha = '" . md5($newPass) . "'";

  }


  $sql = "UPDATE usuarios SET nome = '$nome', email = '$email' $updatePass WHERE id = $idUser";
  if (mysqli_query($con, $sql)) {
    echo  json_encode(['response' => ' Editado com sucesso', 'status' => 'ok'], 200);
    return true;

  } else {
    echo  json_encode($sql, 200);
  }

}
