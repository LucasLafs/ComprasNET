<?php
require_once ("../ajax/conexao.php");

if ($_REQUEST['act']) {
    $request = $_REQUEST['act'];

    if ($request == 'allFabris') {
        return allFabris();

    } else if ($request == 'cadastro') {
        return save($_REQUEST);
    } else if ($request == 'editar') {
        return update($_REQUEST);

    }else if ($request == 'excluir') {

        $idFabri = $_REQUEST['idFabri'];

        return destroy($idFabri);
    } else if ($request == 'getFabri') {
        $id = $_REQUEST['id'];
        return getFabri($id);
    }

}

function allFabris()
{
    $con = bancoMysqli();
    $sql = "SELECT * FROM fabricantes";
    $query = mysqli_query($con, $sql);
    if (mysqli_num_rows($query) > 0) {

        $obj = [];
        while($fabricantes = mysqli_fetch_assoc($query)){
            $obj[] = $fabricantes;
        }

        echo json_encode($obj);
    } else {
        echo json_encode(0);
    }
}

function getFabri($id)
{
    $con = bancoMysqli();
    $sql = "SELECT * FROM fabricantes WHERE id = $id";
    $query = mysqli_query($con, $sql);
    if (mysqli_num_rows($query) > 0) {
        $fabricante = mysqli_fetch_assoc($query);

        echo json_encode($fabricante);
    }

}


function save($request)
{
    $nome = $request['nome'];
    $email = $request['email'] != null ? $request['email'] : '';
    $descricao = $request['descricao'] != null ? $request['descricao'] : '';

    $con = bancoMysqli();

    $sql = "INSERT INTO fabricantes (nome, email, descricao) VALUES ('$nome', '$email', '$descricao')";

    if (mysqli_query($con, $sql)) {
        echo json_encode(true);
    } else {
        echo $sql;
        echo json_encode(false);
    }
}

function update($request)
{
    $id = $request['id'];
    $nome = $request['nome'];
    $email = $request['email'] != null ? $request['email'] : '';
    $descricao = $request['descricao'] != null ? $request['descricao'] : '';

    $con = bancoMysqli();

    $sql = "UPDATE  fabricantes SET 
                        nome = '$nome', 
                        email = '$email', 
                        descricao = '$descricao'
                    WHERE id = $id";

    if (mysqli_query($con, $sql)) {
        echo json_encode(true);

    } else {
        echo $sql;
        echo json_encode(false);
    }

}



function destroy($idFabri)
{
    $con = bancoMysqli();

    $sql = "DELETE FROM fabricantes WHERE id = $idFabri";

    if (mysqli_query($con, $sql)) {
        echo json_encode(true);

    } else {
        echo $sql;
        echo json_encode(false);
    }
}
