<?php

set_time_limit(0);
ignore_user_abort(false);
require_once ("/var/www/html/ComprasNET/ajax/conexao.php");
require_once ("/var/www/html/ComprasNET/api/request_produtos.php");
require_once ("/var/www/html/ComprasNET/api/request_api_comprasnet_beta.php");

//getLicitacoesApp();

/*if($_REQUEST['act']){
    if ($_REQUEST['act'] == 'firebase'){
        return getFirebase();
    } else if ($_REQUEST['act'] == 'jwt') {
        return getTokenJwt();
    } else if ($_REQUEST['act'] == 'parame'){
        return parametrizacao();
    } else if ($_REQUEST['act'] == 'getlic'){
        return getLicitacoesApp();
    } else {
        echo "404 NOT FOUND";
    }
}*/

function getLicitacoesApp() {

    $verificaProcesso = shell_exec('ps aux | grep "ComprasNET/api/timeout-v2.php" | grep -v "grep" | grep -v "/var/log/comprasnet" | awk \'{print$2}\'');
    if(strlen($verificaProcesso) > 8){
        echo "\n"; echo date('d-m-Y H:i:s'); echo " ===== Processo já em execução ===== \n";
        exit;
    }

    $ini = 1;
    $tamPag = 10;

    $url = "https://cnetmobile.estaleiro.serpro.gov.br/comprasnet-oportunidade/api/v1/oportunidades/licitacoes?tamanhoPagina=$tamPag&primeiroRegistro=$ini";

    $result = makeCurlGet($url, 1);
    $data = explode("\n",$result);

    if (strpos($data[0], "Unauthorized")) {
        $result = makeCurlGet($url, 1, true);
    }

    $data = explode("\n",$result);

    $contentRange = explode("|", $data[16]);
    $total = $contentRange[1];

    $ini = 1;

    while ($ini <= $total) {
        $url = "https://cnetmobile.estaleiro.serpro.gov.br/comprasnet-oportunidade/api/v1/oportunidades/licitacoes?tamanhoPagina=$tamPag&primeiroRegistro=$ini";

        $result = makeCurlGet($url, 0);

        $licitacoes = json_decode($result);

       /* echo "<pre>";
        print_r($licitacoes);
        echo "</pre>";

        exit;*/

        foreach($licitacoes AS $lic) {
            $uasg       = $lic->numeroUasg;
            $modalidade = $lic->modalidade;
            $numero     = $lic->numero;
            $ano        = $lic->ano;

            $urlPart = sprintf("%06s%02s%05s%04s", $uasg, $modalidade, $numero, $ano);
            $identificador = $urlPart;

            $urlDetails = "https://cnetmobile.estaleiro.serpro.gov.br/comprasnet-oportunidade/api/v1/licitacoes/$urlPart";

           // echo $urlDetails; exit;

            $detalhesLic = json_decode(makeCurlGet($urlDetails));

            saveLici($identificador, $detalhesLic);

            $con = bancoMysqli();

            $searchItens = false;
            // quando o evento for Alterado
            if (isset($detalhesLic->ultimoEvento)) {
                if ($detalhesLic->ultimoEvento == 5) {
                    $searchItens = true;
                }
            }
            // quando não houver itens ainda importados
            $sqlVerificaNumItens = "SELECT numero_itens FROM licitacoes_cab WHERE identificador = $identificador and numero_itens = 0";
            $queryCheckNumItens = mysqli_query($con, $sqlVerificaNumItens);
            if (mysqli_num_rows($queryCheckNumItens) > 0) {
                $searchItens = true;
            }

            if ($searchItens == false) {
                continue;
            }

            $iniItens = 1;
            $urlItens = "https://cnetmobile.estaleiro.serpro.gov.br/comprasnet-oportunidade/api/v1/oportunidades/licitacoes/$urlPart/itens?tamanhoPagina=$tamPag&primeiroRegistro=$iniItens";

            $itensLic = makeCurlGet($urlItens, 1);

            $dataItens = explode("\n",$itensLic);

            $contentItens = explode("|", $dataItens[16]);
            $totalItens = $contentItens[1];

            $sql = "UPDATE licitacoes_cab SET numero_itens = $totalItens WHERE identificador = $identificador";

            $sqlConsult = "SELECT COUNT(*) as total FROM licitacao_itens WHERE lic_id = $identificador and valid = true";
            $queryConsult = mysqli_query($con, $sqlConsult);
            $consultItens_lic = mysqli_fetch_assoc($queryConsult);
            $totalConsult_itens = $consultItens_lic['total'];

            $doUpdate = false;
            $sqlVerificaUpd = "SELECT * FROM licitacao_itens WHERE identificador = $identificador and updated = false";
            $queryCheckUpd = mysqli_query($con, $sqlVerificaUpd);
            if (is_object($queryCheckUpd)) {
                if (mysqli_num_rows($queryCheckUpd) > 0) {
                    $doUpdate = true;
                }
            }

            if (!mysqli_query($con, $sql)) {
                echo "erro na query "; exit;
            };

            $iniItens = 1;

            if (($totalItens != $totalConsult_itens) or ($doUpdate == true)) {
                while ($iniItens <= $totalItens){
                    $urlItens = "https://cnetmobile.estaleiro.serpro.gov.br/comprasnet-oportunidade/api/v1/oportunidades/licitacoes/$urlPart/itens?tamanhoPagina=$tamPag&primeiroRegistro=$iniItens";

                    $itensLic = json_decode(makeCurlGet($urlItens));

                    foreach($itensLic AS $item) {
                        processItemLic($identificador, $uasg, $item, $doUpdate);
                    }

                    $iniItens += 10;
                }
            }

            if ($doUpdate == true) {
                $sql = "UPDATE licitacao_itens SET
                        updated = true
                        WHERE
                        lic_id = $identificador
                     ";
                if (!mysqli_query($con, $sql)) {
                    echo "<br>";
                    echo "ERROR: " . mysqli_error($con);
                    echo "<br>";
                    echo $sql;
                    exit;
                }
            }

            $orgao_licitacao = requestParseOrgaosGov($uasg);
            if(count($orgao_licitacao) > 0) {
                saveOrgao($uasg, $orgao_licitacao);
            }
        }

        $ini += 10;
//        echo "pag lic $ini <br>";
//        echo "total lic $total <br>";
    }

    return true;
}

function processItemLic($identificador, $uasg, $item, $doUpdate) {
    $con = bancoMysqli();

    $decreto7174 = 0;
    if (isset($item->decreto7174)) {
        if ($item->decreto7174 != false) {
            $decreto7174 = 1;
        }
    }
    $beneficio = null;
    $itemVinculado = 0;
    if (isset($item->numeroItemVinculado)) {
        $itemVinculado = $item->numeroItemVinculado;
        $beneficio = "Beneficio Tipo III - Item Participacao Aberta do item ".$itemVinculado;
    }
    if (isset($item->tratamentoDiferenciado)) {
        switch ($item->tratamentoDiferenciado) {
            case 1:
                $beneficio = "Beneficio Tipo I - Participacao Exclusiva de ME/EPP/COOPERATIVA";
                break;
            case 2:
                $beneficio = "Beneficio Tipo II - Exigencia de Sub-contratacao de ME/EPP/COOPERATIVA";
                break;
            case 3:
                if ($itemVinculado > 0) {
                    $beneficio = "Beneficio Tipo III - Cota Exclusiva para ME/EPP/COOPERATIVA do item ".$itemVinculado;
                } else {
                    $beneficio = "Beneficio Tipo III";
                }
                break;
            default:
                $beneficio = "-";
                break;
        }
    }

    $sqlVerificaItens = "SELECT id FROM licitacao_itens WHERE lic_id = $identificador AND num_item_licitacao = " . $item->numero;
    $queryCheckItens = mysqli_query($con, $sqlVerificaItens);

    if (mysqli_num_rows($queryCheckItens) == 0) {
        $sql = "INSERT INTO licitacao_itens (
                        lic_uasg,
                        lic_id,
                        num_item_licitacao,
                        descricao_item,
                        quantidade,
                        unidade,
                        beneficio,
                        decreto_7174
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                    ";
        if ($stmt = mysqli_prepare($con, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "isisssss",
                        $uasg,
                        $identificador,
                        $item->numero,
                        $item->descricaoDetalhada,
                        $item->quantidade,
                        $item->unidadeFornecimento,
                        $beneficio,
                        $decreto7174 
                    );
    
            if (!mysqli_stmt_execute($stmt)) {
                echo "<br>";
                echo "ERROR: " . mysqli_error($con);
                echo "<br>";
                echo $sql;
                exit;
            } else {
                echo "\n";echo date('d-m-Y H:i:s');echo " [====] Cadastrando Itens do Portal Comprasnet da Licitacao: \n";  echo "\n Id Licitação: " . $identificador ;  echo "\n UASG: " . $uasg ; echo "\n Descrição Item: " . $item->descricaoDetalhada ; echo "\n Numero do item: " . $item->numero ;echo "\n Quantidade do Item: " . $item->quantidade . "\n";
            }
            mysqli_stmt_close($stmt);
        } else { 
                echo "<br>";
                echo "ERROR: " . mysqli_error($con);
                echo "<br>";
                echo $sql;
                exit;
        }

        $sql = 'SELECT MAX(id) as id FROM licitacao_itens';
        $query = mysqli_query($con, $sql);
        if($query){
            $last_id = mysqli_fetch_assoc($query);
            $last_id = $last_id['id'];
        }
    } else {
        if ($doUpdate == true) {
            $sql = "UPDATE licitacao_itens SET
                    lic_uasg = ?,
                    descricao_item = ?,
                    quantidade = ?,
                    unidade = ?,
                    beneficio = ?,
                    decreto_7174 = ?
                    valid = true,
                    updated = true
                    WHERE
                    lic_id = ? AND
                    num_item_licitacao = ?
                 ";

            if ($stmt = mysqli_prepare($con, $sql)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "ssssssii",
                            $uasg,
                            $item->descricaoDetalhada,
                            $item->quantidade,
                            $item->unidadeFornecimento,
                            $beneficio,
                            $decreto7174,
                            $identificador,
                            $item->numero
                        );
        
                if (!mysqli_stmt_execute($stmt)) {
                    echo "<br>";
                    echo "ERROR: " . mysqli_error($con);
                    echo "<br>";
                    echo $sql;
                    exit;
                } else {
                    echo "\n";echo date('d-m-Y H:i:s');echo " [====] Atualizando Itens do Portal Comprasnet da Licitacao: \n";  echo "\n Id Licitação: " . $identificador ;  echo "\n UASG: " . $uasg ; echo "\n Descrição Item: " . $item->descricaoDetalhada ; echo "\n Numero do item: " . $item->numero ;echo "\n Quantidade do Item: " . $item->quantidade . "\n";
                }
                mysqli_stmt_close($stmt);
            } else { 
                    echo "<br>";
                    echo "ERROR: " . mysqli_error($con);
                    echo "<br>";
                    echo $sql;
                    exit;
            }
        }
        $item = mysqli_fetch_assoc($queryCheckItens);
        $last_id = $item['id'];
    }

    if ($item->descricaoDetalhada != '' && $item->descricaoDetalhada != null && $item->descricaoDetalhada != 'null' ){
        $ret = reqApiFutura($item->numero, $item->descricaoDetalhada, $item->quantidade, $item->unidadeFornecimento);
        if(count($ret) > 0){
            saveDadosFutura($identificador, $last_id, $ret, $doUpdate);
        }
    }
}


function saveLici($identificador, $licitacao) {
    $pregao = sprintf("%5s%04s", $licitacao->numero, $licitacao->ano);
    $situacao = null;
    if (isset($detalhesLic->ultimoEvento)) {
        switch ($licitacao->ultimoEvento) {
            case 2:
                $situacao = "Adiado";
                break;
            case 3:
                $situacao = "Revogado";
                break;
            case 4:
                $situacao = "Anulado";
                break;
            case 5:
                $situacao = "Alterado";
                break;
            case 7:
                $situacao = "Suspenso";
                break;
            case 8:
                $situacao = "Reaberto";
                break;
            default:
                $situacao = "Divulgado";
                break;
        }
    } else {
        $situacao = "Divulgado";
    }

    $con = bancoMysqli();
    $sqlVerifica = "SELECT * FROM licitacoes_cab WHERE identificador = $identificador";
    $queryVerifica = mysqli_query($con, $sqlVerifica);
    if (mysqli_num_rows($queryVerifica) == 0) {
        $sql = "INSERT INTO licitacoes_cab (
                uasg,
                identificador,
                numero_aviso,
                situacao_aviso,
                cod_modalidade,
                objeto,
                data_abertura_proposta,
                data_entrega_proposta
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ";
        if ($stmt = mysqli_prepare($con, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "isisisss",
                    $licitacao->numeroUasg,
                    $identificador,
                    $pregao,
                    $situacao,
                    $licitacao->modalidade,
                    $licitacao->objeto,
                    $licitacao->dataHoraAberturaSessaoPublica,
                    $licitacao->dataHoraInicioEntregaProposta 
                );
   
            if (!mysqli_stmt_execute($stmt)) {
                echo "<br>";
                echo "ERROR: " . mysqli_error($con);
                echo "<br>";
                echo $sql;
                exit;
            }
        } else {
            echo "<br>";
            echo "ERROR: " . mysqli_error($con);
            echo "<br>";
            echo $sql;
            exit;
        }
        mysqli_stmt_close($stmt);
        echo "\n";echo date('d-m-Y H:i:s'); echo " [====]  Identificador Licitação: " . $identificador ;echo ' [====] UASG: ' . $licitacao->numeroUasg ; echo "\n";
        return true;
    } else {
        $sqlVerificaUpd = "SELECT * FROM licitacoes_cab WHERE identificador = $identificador and data_abertura_proposta > NOW() and situacao_aviso != $situacao";
        $queryCheckUpd = mysqli_query($con, $sqlVerificaUpd);
        if (mysqli_num_rows($queryCheckUpd) > 0) {
            $sql = "UPDATE licitacao_itens SET
                    updated = false,
                    valid = false
                    WHERE lic_id = $identificador
                ";
            if (!mysqli_query($con, $sql)) {
                echo "ERROR: " . mysqli_error($con);
                echo "<br>";
                echo $sql;
                exit;
            }
            $sql = "UPDATE licitacoes_cab SET
                    situacao_aviso = ?,
                    objeto = ?,
                    data_abertura_proposta = ?,
                    data_entrega_proposta = ?,
                    importador_ultima_atualizacao = NOW()
                    WHERE identificador = ?
                ";
            if ($stmt = mysqli_prepare($con, $sql)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "sssss",
                        $situacao,
                        $licitacao->objeto,
                        $licitacao->dataHoraAberturaSessaoPublica,
                        $licitacao->dataHoraInicioEntregaProposta,
                        $identificador
                    );
       
                if (!mysqli_stmt_execute($stmt)) {
                    echo "<br>";
                    echo "ERROR: " . mysqli_error($con);
                    echo "<br>";
                    echo $sql;
                    exit;
                }
            } else {
                echo "<br>";
                echo "ERROR: " . mysqli_error($con);
                echo "<br>";
                echo $sql;
                exit;
            }
            mysqli_stmt_close($stmt);
            echo "\n";echo date('d-m-Y H:i:s'); echo " [====]  Licitação Atualizada [====] "; echo "\n [====] Identificador Licitação: " . $identificador ;echo ' [====] UASG: ' .  $licitacao->numeroUasg ; echo "\n";
            return true;
        } else {
            echo "\n";echo date('d-m-Y H:i:s'); echo " [====]  Licitação já Cadastrada [====] "; echo "\n [====] Identificador Licitação: " . $identificador ;echo ' [====] UASG: ' .  $licitacao->numeroUasg ; echo "\n";
        }
    }
    return false;
}

function makeCurlGet($url, $header = 0, $new = false) {

    $token_jwt = getTokenJwt($new);

    parametrizacao($token_jwt);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL,$url);

    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 600);
    curl_setopt($curl, CURLOPT_TIMEOUT, 600);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($curl, CURLOPT_HEADER, $header);

    $headers = [
        'authorization: JWT ' . $token_jwt,
        'content-type: application/json',
        'accept: application/json, text/plain, */*'
    ];


    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($curl);

    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        echo "  ===# ERROR: Não foi retornado informações da API do Medicamentos Futura. #=== \n";
        echo "  ===# Curl ERROR: " . $err . "#=== ";
        exit;
    }

    return $result;

}

/*function detalhesLicitacao($urlPart) {

}*/

function parametrizacao($token_jwt) {

    /*  echo "<br> Token JWT \/<br>";
    echo $token_jwt;*/

    $CODMATERIAIS = "6405,6410,6415,6420,6425,6430,6435,6440,6445,6450,6455,6460,6465,6470,6475,6495,6505,6508,6509,6510,6515,6520,6525,6530,6532,6540,6545,6550,6555";

    $LISTAUF = "DF,GO,MT,MS,AL,BA,CE,MA,PB,PE,PI,RN,SE,AC,AP,AM,PA,RO,RR,TO,ES,MG,RJ,SP,PR,RS,SC";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_URL,"https://cnetmobile.estaleiro.serpro.gov.br/comprasnet-oportunidade/api/v1/oportunidades/parametrizacao");
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS,
        '{"materiais":" ' . $CODMATERIAIS . '","ufs":"'. $LISTAUF .'"}');

    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 600);
    curl_setopt($curl, CURLOPT_TIMEOUT, 600);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($curl, CURLOPT_HEADER, 1);


    $headers = [
        'authorization: JWT ' . $token_jwt,
        'content-type: application/json',
    ];

    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($curl);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    return true;

   /* echo "<pre>";
    print_r($result);
    echo "</pre>";*/
}

function getFireBase(){

    $con = bancoMysqli();
    $sql = "SELECT * FROM config_api_app";
    $query = mysqli_query($con, $sql);

    if (mysqli_num_rows($query) > 0) {
        $infos = mysqli_fetch_assoc($query);

        $firebase = $infos['firebase'];
        $token_JWT = $infos['token_jwt'];

        return $firebase;

        return;
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_URL,"https://android.clients.google.com/c2dm/register3");
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS,
        "X-subtype=1023343371949&sender=1023343371949&X-app_ver=10050&X-osv=22&X-cliv=fiid-20.0.1&X-gmsv=201216008&X-appid=f6WpFcs2W2o&X-scope=*&X-gmp_app_id=1%3A1023343371949%3Aandroid%3A051650c153213ea0&X-app_ver_name=3.0.0&app=br.gov.serpro.comprasNetMobile&device=3701375969863611343&app_ver=10050&info=4xkbMaspnDoeENW7sZq6nvmCvIONGRc&gcm_ver=201216008&plat=0&cert=036d01cd5ded1fdae462c341065ccf44c3a4053c&target_ver=28");

    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 600);
    curl_setopt($curl, CURLOPT_TIMEOUT, 600);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $headers = [
        'Authorization: AidLogin 3701375969863611343:1910495745030104839',
        'app: br.gov.serpro.comprasNetMobile',
        'gcm_ver: 201216008',
        'User-Agent: Android-GCM/1.5 (j3xlte LMY47V)',
        'content-type: application/x-www-form-urlencoded',
        'Connection: Keep-Alive'
    ];

    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($curl);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    $parts = explode('=', $result);

    $token = $parts[1];

    $sql = "INSERT INTO config_api_app (firebase) VALUES ('$token')";
    mysqli_query($con, $sql);

    return $token;

}

function getTokenJwt($new = false) {
    $con = bancoMysqli();
    $sql = "SELECT * FROM config_api_app";
    $query = mysqli_query($con, $sql);

    if (mysqli_num_rows($query) > 0 && !$new) {
        $infos = mysqli_fetch_assoc($query);

        $token_JWT = $infos['token_jwt'];

        if ($token_JWT != '') {
            return $token_JWT;

        }
    }

    $firebase = getFirebase();

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_URL,"https://cnetmobile.estaleiro.serpro.gov.br/comprasnet-oportunidade/api/autenticacao/v2/login");
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS,
        '{"tokenFirebase":"' . $firebase . '","tokenAutenticadorExterno":null}');

    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 600);
    curl_setopt($curl, CURLOPT_TIMEOUT, 600);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $headers = [
        'sec-fetch-mode: cors',
        'sec-fetch-dest: empty',
        'sec-fetch-site: cross-site',
        'content-type: application/json'
    ];

    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($curl);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    $result = json_decode($result);

    $token_JWT = $result->tokenCnet;

    $sql = "UPDATE config_api_app SET token_jwt = '$token_JWT' WHERE firebase = '$firebase'";
    mysqli_query($con, $sql);

    return $token_JWT;


}
