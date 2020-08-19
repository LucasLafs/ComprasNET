<?php

require_once ("./conexao.php");

if($_REQUEST['act']){
    if ($_REQUEST['act'] == 'buscaCotacoes'){
        return buscaCotacoes();
    } else if ($_REQUEST['act'] == 'buscaItensLicitacao') {
        return buscaItensLicitacao();
    } else if ($_REQUEST['act'] == 'buscaContFiltros') {
        return buscaContFiltros();
    } else {
        echo "404 NOT FOUND";
    }
}

function buscaContFiltros(){

    $con = bancoMysqli();

    $date = date('Y-m-d');
    $sql = "SELECT * FROM licitacoes_cab WHERE data_abertura_proposta > '$date'";
    $query = mysqli_query($con, $sql);
    if($query){
        $data['vigentes'] = mysqli_num_rows($query); 
    }

    $sql = "SELECT COUNT(*) AS ncotacoes FROM licitacoes_cab";
    $query = mysqli_query($con, $sql);
    if($query){
        while($row = mysqli_fetch_assoc($query)){
            if($row['ncotacoes'] != null){
                $data['cotacoes'] = $row['ncotacoes'];
            }
        }; 
    }

    $sql = "SELECT DISTINCT pf.lic_id as lic_id, ee.item_id as ids_enviados, pf.item_id as nao_enviados, pf.item_id as itens_relacionados FROM email_enviados AS ee RIGHT JOIN produtos_futura AS pf 
            ON pf.item_id = ee.item_id 
            ORDER BY pf.item_id ASC";
    
    $query = mysqli_query($con, $sql);

    $ids_nao_enviados = array();
    $itens_relacionados = array();

    if($query){

        while ($rows = mysqli_fetch_assoc($query)){
            if($rows['ids_enviados'] != null){
                $ids_enviados[] = $rows['ids_enviados'];
                $idents_enviados[] = $rows['lic_id'];
            } else {
                $ids_nao_enviados[] = $rows['nao_enviados'];
                $idents_nao_enviados[] = $rows['lic_id'];
            }
            $itens_relacionados[] = $rows['itens_relacionados'];
        }

        $idents_enviados = array_unique($idents_enviados);
        $idents_nao_enviados = array_unique($idents_nao_enviados);

        foreach ($idents_enviados as $enviado) {
            $key = array_search($enviado, $idents_enviados);
            unset($idents_nao_enviados[$key]);
        }

        $data['nao-enviados'] = $ids_nao_enviados ? count($ids_nao_enviados) : 0;
        $data['recomendadas'] = $itens_relacionados ? count($itens_relacionados) : 0;
        $data['SemEnvios'] = $idents_nao_enviados ? count($idents_nao_enviados) : 0;
    }

    $sql = "SELECT COUNT(*) AS total FROM licitacoes_cab AS lc LEFT JOIN licitacao_orgao AS lo ON lc.uasg = lo.uasg WHERE lo.lic_estado IN ('SP', 'DF', 'RJ') ";

    $query = mysqli_query($con, $sql);
    if($query){
        $total = mysqli_fetch_assoc($query);
    }

    $data['estados'] = $total['total'];

    echo json_encode($data);
    exit;
}

function buscaCotacoes(){
    
    $con = bancoMysqli();

    if($_REQUEST['filtro'] == 'vigentes'){
        $date = date('Y-m-d');
        // $date = '01/01/2005';
        // mysqli_query('SET CHARACTER SET utf8');

        $sql = "SELECT lic.uasg, 
        identificador, 
        lic.data_entrega_proposta as data_entrega_proposta, 
        lic.numero_aviso as numero_aviso,
        DATE_FORMAT(lic.data_entrega_proposta, '%d/%m/%Y') AS data_entrega_proposta_ord, 
        informacoes_gerais, 
        objeto, 
        situacao_aviso,
        lic.data_abertura_proposta as data_abertura_proposta,
        DATE_FORMAT(lic.data_abertura_proposta, '%d/%m/%Y') AS data_abertura_proposta_ord,
        o.lic_estado AS uf 
        FROM licitacoes_cab AS lic
        LEFT JOIN licitacao_orgao AS o ON o.uasg = lic.uasg
        WHERE data_abertura_proposta > '$date' 
        order by data_abertura_proposta desc limit 5000";

        $query = mysqli_query($con, $sql);
        if($query){
            $total = mysqli_num_rows($query);

            if( $total > 0 ){
                $obj = [];
                //$ret = array();
                
                while($ret = mysqli_fetch_assoc($query)){

                    $obj[] = $ret;

                    // $obj = [
                    //     $ret['identificador'],
                    //     '',
                    //     $ret['uasg'],
                    //     $ret['data_entrega_proposta_ord'],
                    //     $ret['informacoes_gerais'],
                    //     $ret['objeto'],
                    //     $ret['situacao_aviso'],
                    //     "<button class='btn btn-sm btn-edit'><i class='fa fa-print'></i></button>",


                    // ];

                }

                $data[0] = $obj;
                $data[1] = $total;
                echo json_encode($data);

            //  echo json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
                
            }
        }
    } else if ($_REQUEST['filtro'] == 'nao-enviados') {

        $sql = "SELECT DISTINCT ee.item_id as ids_enviados, pf.item_id as nao_enviados FROM email_enviados AS ee RIGHT JOIN produtos_futura AS pf 
                ON pf.item_id = ee.item_id 
                ORDER BY pf.item_id ASC";
        
        $query = mysqli_query($con, $sql);

        if($query){

            while ($rows = mysqli_fetch_assoc($query)){
                if($rows['ids_enviados'] != null){
                    $ids_enviados[] = $rows['ids_enviados'];
                } else {
                    $ids_nao_enviados[] = $rows['nao_enviados'];
                }
            }

        }

        $sql = "SELECT DISTINCT lic.identificador as identificador,
        lic.uasg as uasg, 
        lic.numero_aviso as numero_aviso,
        lic.data_entrega_proposta AS data_entrega_proposta, 
        DATE_FORMAT(lic.data_entrega_proposta, '%d/%m/%Y') AS data_entrega_proposta_ord, 
        lic.informacoes_gerais as informacoes_gerais, 
        lic.objeto as objeto, 
        lic.situacao_aviso as situacao_aviso,
        lic.data_abertura_proposta as data_abertura_proposta, 
        DATE_FORMAT(lic.data_abertura_proposta, '%d/%m/%Y') AS data_abertura_proposta_ord,
        o.lic_estado AS uf 
        FROM licitacoes_cab AS lic
        LEFT JOIN licitacao_orgao AS o ON o.uasg = lic.uasg 
        LEFT JOIN licitacao_itens ON lic.identificador = licitacao_itens.lic_id
        WHERE licitacao_itens.id IN (" . implode(',', $ids_nao_enviados) . ") AND licitacao_itens.valid = true ORDER BY data_abertura_proposta DESC ";  // order by data_entrega_proposta_ord limit 5000";

        $query = mysqli_query($con, $sql);
        if($query){
            $total = mysqli_num_rows($query);

            if( $total > 0 ){
                $obj = [];
                //$ret = array();
                
                while($ret = mysqli_fetch_assoc($query)){

                    $obj[] = $ret;

                    // $obj = [
                    //     $ret['identificador'],
                    //     '',
                    //     $ret['uasg'],
                    //     $ret['data_entrega_proposta_ord'],
                    //     $ret['informacoes_gerais'],
                    //     $ret['objeto'],
                    //     $ret['situacao_aviso'],
                    //     "<button class='btn btn-sm btn-edit'><i class='fa fa-print'></i></button>",


                    // ];

                }

                $data[0] = $obj;
                $data[1] = $total;
                echo json_encode($data);
            }
        } else {
            echo $sql;
        }
    } else if ($_REQUEST['filtro'] == 'recomendadas'){
        $date = date('Y-m-d', strtotime('01/01/1999'));

        $sql = "SELECT DISTINCT pf.item_id as ids_relacionados FROM produtos_futura AS pf             
                ORDER BY pf.item_id ASC";
        
        $query = mysqli_query($con, $sql);

        if($query){

            while ($rows = mysqli_fetch_assoc($query)){
                $ids_relacionados[] = $rows['ids_relacionados'];
            }

        }

        $sql = "SELECT DISTINCT lic.identificador as identificador,
        lic.uasg as uasg, 
        lic.numero_aviso as numero_aviso,
        lic.data_entrega_proposta AS data_entrega_proposta, 
        DATE_FORMAT(lic.data_entrega_proposta, '%d/%m/%Y') AS data_entrega_proposta_ord, 
        lic.informacoes_gerais as informacoes_gerais, 
        lic.objeto as objeto, 
        lic.situacao_aviso as situacao_aviso,
        lic.data_abertura_proposta as data_abertura_proposta, 
        DATE_FORMAT(lic.data_abertura_proposta, '%d/%m/%Y') AS data_abertura_proposta_ord,
        o.lic_estado AS uf 
        FROM licitacoes_cab AS lic
        LEFT JOIN licitacao_orgao AS o ON o.uasg = lic.uasg
        LEFT JOIN licitacao_itens ON lic.identificador = licitacao_itens.lic_id
        WHERE licitacao_itens.id IN (" . implode(',', $ids_relacionados) . ") AND licitacao_itens.valid = true ORDER BY data_abertura_proposta DESC ";  // order by data_entrega_proposta_ord limit 5000";

        $query = mysqli_query($con, $sql);
        if($query){
            $total = mysqli_num_rows($query);

            if( $total > 0 ){
                $obj = [];
                //$ret = array();
                
                while($ret = mysqli_fetch_assoc($query)){

                    $obj[] = $ret;

                }

                $data[0] = $obj;
                $data[1] = $total;
                echo json_encode($data);
            }
        }
    } else if ($_REQUEST['filtro'] == 'estados'){
        $date = date('Y-m-d', strtotime('01/01/1999'));
        // $date = '01/01/2005';
        // mysqli_query('SET CHARACTER SET utf8');

        $sql = "SELECT DISTINCT lc.identificador as identificador, 
        lc.uasg as uasg, 
        lc.numero_aviso as numero_aviso,
        lc.data_entrega_proposta AS data_entrega_proposta, 
        DATE_FORMAT(lc.data_entrega_proposta, '%d/%m/%Y') AS data_entrega_proposta_ord, 
        lc.informacoes_gerais as informacoes_gerais, 
        lc.objeto as objeto, 
        lc.situacao_aviso as situacao_aviso,
        lc.data_abertura_proposta as data_abertura_proposta, 
        DATE_FORMAT(lc.data_abertura_proposta, '%d/%m/%Y') AS data_abertura_proposta_ord,
        lo.lic_estado AS uf 
        FROM licitacoes_cab AS lc LEFT JOIN licitacao_orgao AS lo ON lc.uasg = lo.uasg WHERE lo.lic_estado IN ('SP', 'DF', 'RJ') ORDER BY data_abertura_proposta DESC ";

        $query = mysqli_query($con, $sql);
        if($query){
            $total = mysqli_num_rows($query);

            if( $total > 0 ){
                $obj = [];
                
                while($ret = mysqli_fetch_assoc($query)){
                    $obj[] = $ret;
                }

                $sql = "SELECT COUNT(*) AS total FROM licitacoes_cab AS lc LEFT JOIN licitacao_orgao AS lo ON lc.uasg = lo.uasg WHERE lo.lic_estado IN ('SP', 'DF', 'RJ') ";

                $query = mysqli_query($con, $sql);
                if($query){
                    $count = mysqli_fetch_assoc($query);
                } else {
                    echo $sql;
                }

                $data[0] = $obj;
                $data[1] = $count['total'];
                echo json_encode($data);
            } else {
                echo $sql;
            }
        } else {
            echo $sql;
        }
        
    } else if ($_REQUEST['filtro'] == 'SemEnvios') {

        $sql = "SELECT DISTINCT pf.lic_id as lic_id, ee.item_id as ids_enviados, pf.item_id as nao_enviados FROM email_enviados AS ee RIGHT JOIN produtos_futura AS pf 
                ON pf.item_id = ee.item_id 
                ORDER BY pf.item_id ASC";
        
        $query = mysqli_query($con, $sql);

        if($query){

            while ($rows = mysqli_fetch_assoc($query)){
                if($rows['ids_enviados'] != null){
                    $ids_enviados[] = $rows['ids_enviados'];
                    $idents_enviados[] = $rows['lic_id'];
                } else {
                    $ids_nao_enviados[] = $rows['nao_enviados'];
                    $idents_nao_enviados[] = $rows['lic_id'];
                }
                $itens_relacionados[] = $rows['itens_relacionados'];
            }

            $idents_enviados = array_unique($idents_enviados);
            $idents_nao_enviados = array_unique($idents_nao_enviados);

            foreach ($idents_enviados as $enviado) {
                $key = array_search($enviado, $idents_enviados);
                unset($idents_nao_enviados[$key]);
            }

        }

        $sql = "SELECT DISTINCT lic.identificador as identificador,
        lic.uasg as uasg, 
        lic.numero_aviso as numero_aviso, 
        lic.data_entrega_proposta AS data_entrega_proposta, 
        DATE_FORMAT(lic.data_entrega_proposta, '%d/%m/%Y') AS data_entrega_proposta_ord, 
        lic.informacoes_gerais as informacoes_gerais, 
        lic.objeto as objeto, 
        lic.situacao_aviso as situacao_aviso,
        lic.data_abertura_proposta as data_abertura_proposta, 
        DATE_FORMAT(lic.data_abertura_proposta, '%d/%m/%Y') AS data_abertura_proposta_ord,
        o.lic_estado AS uf 
        FROM licitacoes_cab AS lic
        LEFT JOIN licitacao_orgao AS o ON o.uasg = lic.uasg 
        LEFT JOIN licitacao_itens ON lic.identificador = licitacao_itens.lic_id
        RIGHT JOIN produtos_futura as pf ON lic.identificador = pf.lic_id 
        WHERE lic.identificador NOT IN (" . implode(',', $idents_enviados) . ") AND licitacao_itens.valid = true ORDER BY data_abertura_proposta DESC";  // order by data_entrega_proposta_ord limit 5000";
        // echo $sql; exit;
        $query = mysqli_query($con, $sql);
        if($query){
            $total = mysqli_num_rows($query);

            if( $total > 0 ){
                $obj = [];
                //$ret = array();
                
                while($ret = mysqli_fetch_assoc($query)){

                    $obj[] = $ret;

                    // $obj = [
                    //     $ret['identificador'],
                    //     '',
                    //     $ret['uasg'],
                    //     $ret['data_entrega_proposta_ord'],
                    //     $ret['informacoes_gerais'],
                    //     $ret['objeto'],
                    //     $ret['situacao_aviso'],
                    //     "<button class='btn btn-sm btn-edit'><i class='fa fa-print'></i></button>",


                    // ];

                }

                $data[0] = $obj;
                $data[1] = $total;
                echo json_encode($data);
            }
        } else {
            echo $sql;
        }
    }

    exit;
}

function buscaItensLicitacao(){

    $con = bancoMysqli();
    $identificador = $_REQUEST['identificador'];

    $sql = "SELECT 
            i.id,
            i.lic_id as lic_id,
            lic.numero_aviso as num_aviso,
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
            RIGHT JOIN licitacoes_cab as lic ON i.lic_id = lic.identificador 
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
            $sql = "SELECT item_id, fabricante_id, produto_id, DATE_FORMAT(data_envio, '%d/%m/%Y %H:%i:%s') AS data_envio FROM email_enviados WHERE email_enviado = 'Y'";
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
                $datas_envio[$produto_id] = $itens['data_envio'];
              }
            }

            $obj['email_enviados'] = $emailEnviados;
            $obj['datas_envio'] = $datas_envio;
            $obj['fabricantes'] = $fabricantes;

            echo json_encode($obj);
        } else {
            echo 0;
        }
    }

}
