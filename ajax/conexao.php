<?php

function bancoMysqli()
{
	$servidor = 'localhost';
	$usuario = 'root';
	$senha = '1qaz!QAZ';
	$banco = 'comprasnet_db';
	$con = mysqli_connect($servidor,$usuario,$senha,$banco);
	//testando new comment
	//mysqli_set_charset($con,"utf8");
	if (!mysqli_set_charset($con, 'utf8')) {
    printf('Error ao usar utf8: %s', mysqli_error($con));
    exit;
}
	return $con;
}



?>