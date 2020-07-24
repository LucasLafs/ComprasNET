<?php 
require_once ("../ajax/conexao.php");

if($_REQUEST['act']){
    if ( $_REQUEST['act'] == 'saveSmtp'){
        return saveSmtp();
    } else if ($_REQUEST['act'] == 'getSmtp') {
        return getSmtp();
    } else if ($_REQUEST['act'] == 'saveBody') {
        return saveBody();
    } else if ($_REQUEST['act'] == 'getBody'){ 
        return getBody();
    } else {
        echo "404 Not Found";
    }
}

function saveSmtp() {

    $con = bancoMysqli();
    
    $rem = $_REQUEST['remetente_conf_conta'];
    $server_smtp = $_REQUEST['smtp_conf_conta'];
    $port_smtp = $_REQUEST['port_conf_conta'];
    $usuario = $_REQUEST['nome_conf_conta'];
    $senha = $_REQUEST['senha_conf_conta'];
    $copia = $_REQUEST['copia_conf_conta'];

    //if tudo vier ok, se nao volta campo com erro para marcação;(fazer)

    if( $rem && $server_smtp && $port_smtp && $usuario && $senha && $copia ){

        $sql = 'DELETE FROM CONN_smtp';
        $query = mysqli_query($con, $sql);

        if(empty($id)){
            
            if(!empty($rem)){
                $fields = 'remetente';
                $values = "'" . $rem . "'";
            }

            if(!empty($server_smtp)){
                $fields .= ', server_smtp';
                $values .= ", '" . $server_smtp . "'";
            }

            if(!empty($port_smtp)){
                $fields .= ', port_smtp';
                $values .= ", '" . $port_smtp . "'";
            }
            
            if(!empty($usuario)){
                $fields .= ', usuario';
                $values .= ", '" . $usuario . "'";
            }

            if(!empty($senha)){
                $fields .= ', senha';
                $values .= ", '" . $senha . "'";
            }

            if(!empty($copia)){
                $fields .= ', cop_email ';
                $values .= ", '" . $copia . "'";
            }

        }

        $sql = "INSERT INTO conn_smtp ($fields) VALUES ($values)";
        $query = mysqli_query($con, $sql);
        if($query){
            echo 1;
        } else {
            echo 0;
        }

    }
    
    exit;
}

function getSmtp()
{
    
    $con = bancoMysqli();
    $sql = 'SELECT remetente, server_smtp, port_smtp, usuario, senha, cop_email FROM conn_smtp';

    $query = mysqli_query($con, $sql);
    $rows = mysqli_num_rows($query);

    $conf_conta = array();
    if ($rows > 0) {
        $conf_conta = mysqli_fetch_assoc($query);
        echo json_encode($conf_conta);
    } else {
        echo "0";
    }
}

function saveBody(){
    $con = bancoMysqli();

    $assunto = $_REQUEST['assunto_email'];
    $corpo = $_REQUEST['corpo_email'];


      $sql = "DELETE FROM smtp_body";
      $query = mysqli_query($con, $sql);

      $sql = "INSERT INTO smtp_body (smtp_assunto, smtp_corpo) VALUES ('$assunto', '$corpo')";
      $query = mysqli_query($con, $sql);

      if ($query){
          echo 1;
      } else {
          echo json_encode($sql);
      }


}

function getBody(){

    $con = bancoMysqli();
    $sql = "SELECT smtp_assunto, smtp_corpo FROM smtp_body";

    $query = mysqli_query($con, $sql);
    $rows = mysqli_num_rows($query);

    if ($rows > 0){
        $body_smtp = mysqli_fetch_assoc($query);    
        echo json_encode($body_smtp);
    } else {
        echo 0;
    }
}

?>
