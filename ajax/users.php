<?php

require_once ("../ajax/conexao.php");

if ($_REQUEST['act']) {
    $request = $_REQUEST['act'];

    if ($request == 'getUsers') {
        return getUsers();
    } else if ($request == 'saveUser') {
        return saveUser();
    } else if ($request == 'delUser') {
        return delUser($_POST['idUser']);
    }

}

function getUsers() {
    $con = bancoMysqli();
    $id = $_REQUEST['id'];
    
    if ($id){
        $sql = "SELECT id, nome, descricao, email, bloqueado, gestor  FROM usuarios WHERE id=$id";
    } else {
        $sql = "SELECT id, nome, descricao, email, bloqueado, gestor FROM usuarios";
    }

    $query = mysqli_query($con, $sql);
    if (mysqli_num_rows($query) > 0) {

        $obj = [];
        while($usuarios = mysqli_fetch_assoc($query)){
            $obj[] = $usuarios;
        }

        echo json_encode($obj);
    } else {
        echo json_encode(0);
    }
}

function saveUser() {
    $con = bancoMysqli();

    $id_user = $_POST['id'];
    $nome = $_POST['name'];
    $email = $_POST['email'];
    $description = $_POST['description'] != '' ? $_POST['description'] : '';
    $current_pass = $_POST['current-pass'] != '' ? $_POST['current-pass'] : '';
    $pass = $_POST['pass'] != '' ? $_POST['pass'] : '';
    $confirm_pass = $_POST['confirm-pass'] ? $_POST['confirm-pass'] : '';
    $check_admin = $_POST['check-admin'] ? 'Y' : 'N';
    $check_block = $_POST['check-block'] ? 'Y' : 'N';

    $updatePass = '';
    if ($id_user){
        if ($pass != '') {

            if ($id_user && $current_pass) {
                $sql = "SELECT * FROM usuarios where id = $id_user and senha = '" . md5($current_pass) . "'";

                if (mysqli_num_rows(mysqli_query($con, $sql)) == 0 ) {
                echo json_encode(['response' => ' A senha atual está incorreta', 'status' => 'error'], 200);
                return false;
                }
            }

            if ($pass != $confirm_pass) {
            echo json_encode(['response' => ' As senhas não conferem', 'status' => 'error'], 200);
            return false;
            }

            $updatePass = ", senha = '" . md5($pass) . "'";
        }

        $sql = "SELECT email FROM usuarios WHERE email = '$email'";
        if ( mysqli_num_rows(mysqli_query($con, $sql)) > 1 ){
            echo json_encode(['response' => ' Email já cadastrado', 'status' => 'error'], 200);
            return false;
        } 

        $sql = "UPDATE usuarios SET nome = '$nome', email = '$email', descricao = '$description', bloqueado='$check_block', gestor='$check_admin' $updatePass WHERE id = $id_user";
        if (mysqli_query($con, $sql)) {
            echo  json_encode(['response' => ' Editado com sucesso', 'status' => 'ok'], 200);
            return true;

        } else {
            echo  json_encode($sql, 200);
            return false;
        }
    
    } else {
        $sql = "SELECT email FROM usuarios WHERE email = '$email'";
        if ( mysqli_num_rows(mysqli_query($con, $sql)) > 0 ){
            echo json_encode(['response' => ' Email já cadastrado', 'status' => 'error'], 200);
            return false;
        } 
        

        if ($pass != $confirm_pass) {
            echo json_encode(['response' => ' As senhas não conferem', 'status' => 'error'], 200);
            return false;
        }

        $pass = md5($pass);
        $sql = "INSERT INTO usuarios (nome, descricao, email, senha, gestor, bloqueado) VALUES ('$nome', '$description', '$email', '$pass', '$check_admin', '$check_block')";
        if (mysqli_query($con, $sql)){
            echo  json_encode(['response' => ' Cadastrado com sucesso', 'status' => 'ok'], 200);
            return true;
        } else {
            echo  json_encode($sql, 200);
            return false;
        }
    }

}

function delUser($id_user) {

    $con = bancoMysqli();

    $sql = "DELETE FROM usuarios WHERE id = $id_user";

    if (mysqli_query($con, $sql)) {
        echo json_encode(true);

    } else {
        echo $sql;
        echo json_encode(false);
    }
}

?>