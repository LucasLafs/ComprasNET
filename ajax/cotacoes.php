<?php
require_once ("../ajax/conexao.php");

if($_REQUEST['act']){
    if ( $_REQUEST['act'] == 'getLicitacoes'){
      if (isset($_REQUEST['filtro'])) {
        return getLicitacoes(1);
      }
        return getLicitacoes();
    } else if ( $_REQUEST['act'] == 'getItensLicitacao'){
        return getItensLicitacao();
    } else {
        echo "404 Not Found";
    }
}

function getLicitacoes($filtro = '')
{

    $con = bancoMysqli();

    $inner = '';
    $filtros = 3;

    if ($filtro != '') {
      $filtro = 'WHERE ';

      if ($_REQUEST['data'] != '') {
        $data_abertura = $_REQUEST['data'];

        $filtro .= "li.data_entrega_edital LIKE '%$data_abertura%' OR ";
        $filtro .= "li.data_abertura_proposta LIKE '%$data_abertura%' OR ";
        $filtro .= "li.data_entrega_proposta LIKE '%$data_abertura%' OR ";
        $filtro .= "li.data_publicacao LIKE '%$data_abertura%' AND ";

        $filtros--;

      }

      if ($_REQUEST['nome_produto'] != '') {
        $nome_produto = $_REQUEST['nome_produto'];
        $inner = "INNER JOIN licitacao_itens AS i ON i.lic_id = li.identificador ";
        $filtro .= "i.descricao_item LIKE '%$nome_produto%' AND i.valid = true";
        $filtros--;
      }

      if ($_REQUEST['desc_obj'] != '') {
        $desc_obj = $_REQUEST['desc_obj'];
        $filtro .= "li.objeto LIKE '%$desc_obj%' AND ";
        $filtros--;
      }

      $filtro = substr($filtro, 0, -4);

      if ($filtros == 3) {
        $filtro = '';
      }

    }

    $sql = "SELECT li.uasg, identificador, DATE_FORMAT(data_entrega_proposta, '%d/%m/%Y')
                AS data_entrega_prop, informacoes_gerais, objeto, situacao_aviso, DATE_FORMAT(data_abertura_proposta, '%d/%m/%Y') AS data_abertura_proposta, o.lic_estado AS uf 
                FROM licitacoes_cab AS li 
                LEFT JOIN licitacao_orgao AS o ON o.uasg = li.uasg
                $inner $filtro order by data_entrega_proposta desc limit 1000";

    $query = mysqli_query($con, $sql);
    if($query){
        $offset = mysqli_num_rows($query);
        if( $offset > 0){

            $obj = [];
            while($licitacoes = mysqli_fetch_assoc($query)){

                $obj[] = [
                    $licitacoes['identificador'],
                    '',
                    $licitacoes['uasg'] ?? '-',
                    $licitacoes['uf'] ?? '-',
                    $licitacoes['data_entrega_prop'] ?? '-',
                    $licitacoes['data_abertura_proposta'] ?? '-',
                    $licitacoes['informacoes_gerais'] ?? '-',
                    $licitacoes['objeto'] ?? '-',
                    $licitacoes['situacao_aviso'] ?? '-',
                    "<button target title='Gerar PDF' style='float:left; margin-left: -20px; min-width: 31px;' class='btn btn-sm btn-edit pdfLicitacao' id='".$licitacoes['identificador']."'><i class='far fa-file-pdf'></i></button>
                    <button title='Imprimir' style='float:right; margin-right: -10px;width: 30px;' class='btn btn-sm btn-edit printLicitacao' id='".$licitacoes['identificador']."'><i style='padding-right: 6px;' class='fa fa-print'></i></button>"
                ];
            }
    // float: left;
    // margin-left: -12px;
    // width: 30px;
    // height: 30px;
    //     float: right;
    // margin-right: -10px;
    // width: 30px;
    // height: 30px;
            echo json_encode($obj);
        } else {
            echo json_encode(0);
        }

    } else {
        echo 'Query Error';
      echo $sql;
    }
}

function getItensLicitacao(){

  $con = bancoMysqli();
  $identificador = $_REQUEST['identificador'];

  $sql = "SELECT 
        i.id,
        i.lic_id as lic_id,
        num_aviso,
        i.num_item_licitacao,
        cod_item_servico,
        cod_item_material,
        descricao_item,
        sustentavel,
        quantidade,
        unidade,
        cnpj_fornecedor,
        cpf_vencedor,
        beneficio,
        valor_estimado,
        decreto_7174,
        criterio_julgamento,
        pf.cod_jd_produto AS cod_produto,
        pf.desc_produto_jd as desc_produto,       
        pf.id AS produto_id,        
        f.id AS idFabricante,        
        f.Nome AS fabricante
        FROM
        licitacao_itens AS i
        LEFT JOIN produtos_futura AS pf ON pf.item_id = i.id
        LEFT JOIN fabricantes AS f ON f.id = pf.fabricante_id
        WHERE
        i.lic_id = $identificador AND i.valid = true
    ";

  $query = mysqli_query($con, $sql);
  if($query){
    if(mysqli_num_rows($query) > 0){

      $obj = [];
      $arr = [];
      $emailEnviados = [];
      $datas_envio = [];


      while($itens = mysqli_fetch_assoc($query)){
        $arr[] = $itens;
      }

      $obj['itens'] = $arr;

      //Buscando itens disponiveis para enviar e-mail
      $sql = "SELECT item_id FROM produtos_futura";
      $query = mysqli_query($con, $sql);

      $itensComProduto = [];
      while ($itens = mysqli_fetch_assoc($query)) {
        $itensComProduto[] =  $itens['item_id'];
      }

      $obj['itensComProduto'] = $itensComProduto;


      //Buscando quais itens já foram enviado e-mail
      $sql = "SELECT item_id, fabricante_id, produto_id, DATE_FORMAT(data_envio, '%d/%m/%Y %H:%i:%s') AS data_envio 
                    FROM email_enviados WHERE email_enviado = 'Y'";

      $query = mysqli_query($con, $sql);

      $emailEnviados = [];
      $fabricantes = [];

      if (mysqli_num_rows($query) > 0) {
        while ($itens = mysqli_fetch_assoc($query)) {
          $item_id = $itens['item_id'];
          $produto_id = $itens['produto_id'];
          $fabricante_id = $itens['fabricante_id'];

          $emailEnviados[$produto_id] =  $fabricante_id;
          $fabricantes[$produto_id] =  $fabricante_id;
          $datas_envio[$item_id] = $itens['data_envio'];
        }
      }

      $obj['email_enviados'] = $emailEnviados;
      $obj['datas_envio'] = $datas_envio;
      $obj['fabricantes'] = $fabricantes;

      echo json_encode($obj);
      exit;
    } else {
      echo 0;
      exit;
      echo $sql;
    }
  } else {
    echo $sql;
  }
}



