<?php 

set_time_limit(0);
ignore_user_abort(false);
require_once ("/var/www/html/ComprasNET/ajax/conexao.php");
require_once ("/var/www/html/ComprasNET/api/request_produtos.php");


function requestLicGeraisComprasNet(){
    libxml_use_internal_errors(true);   
    // header("Vary: Origin");

    $verificaProcesso = shell_exec('ps aux | grep "ComprasNET/api/timeout-v1.php" | grep -v "grep" | grep -v "/var/log/comprasnet" | awk \'{print$2}\'');
    if(strlen($verificaProcesso) > 8){
        echo "\n"; echo date('d-m-Y H:i:s'); echo " ===== Processo já em execução ===== \n";
        exit;
    }

    $con = bancoMysqli();
  

    $sql = "SELECT COUNT(*) as total FROM licitacoes_cab";
    $query = mysqli_query($con, $sql);
    $offset = mysqli_fetch_assoc($query);
    $offsetBanco = $offset['total'] ? $offset['total'] : 0;

    $offset = $offsetBanco;

    $sql = "SELECT identificador FROM licitacoes_cab ORDER BY id ASC LIMIT 1";
    $query = mysqli_query($con, $sql);

    if($query){
        $info = mysqli_fetch_assoc($query);

        $curl = curl_init();
//        curl_setopt_array($curl, [
//            CURLOPT_RETURNTRANSFER => 1,
//            CURLOPT_CONNECTTIMEOUT => 600,
//            CURLOPT_TIMEOUT => 600,
//            CURLOPT_URL => "http://compras.dados.gov.br/licitacoes/v1/licitacoes.json?order_by=data_entrega_proposta&offset=0"
//        ]);

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 600,
            CURLOPT_TIMEOUT => 600,
            CURLOPT_URL => "http://compras.dados.gov.br/licitacoes/v1/licitacoes.json?order_by=data_publicacao&data_publicacao_min=".(date('Y')-3)."-".date('m')."-01&offset=0"
        ]);

        $result = json_decode(curl_exec($curl));
        
        $licitacoes = $result->_embedded->licitacoes;

        $count = 0;

        foreach ($licitacoes AS $licitacao) {
            if ($licitacao->identificador != $info['identificador']) {
                $count++;
            } else {
                break;
            }
        }

        if ($count > 0) {
            $diff = $count;
            $offset = 0;            
        }
    }

    $curl = curl_init();
//    curl_setopt_array($curl, [
//        CURLOPT_RETURNTRANSFER => 1,
//        CURLOPT_CONNECTTIMEOUT => 600,
//        CURLOPT_TIMEOUT => 600,
//        CURLOPT_URL => "http://compras.dados.gov.br/licitacoes/v1/licitacoes.json?order_by=data_entrega_proposta&offset=" . $offset
//    ]);

    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_CONNECTTIMEOUT => 600,
        CURLOPT_TIMEOUT => 600,
        CURLOPT_URL => "http://compras.dados.gov.br/licitacoes/v1/licitacoes.json?order_by=data_publicacao&data_publicacao_min=".(date('Y')-3)."-".date('m')."-01&offset=".$offset
    ]);

    $offset_total = json_decode(curl_exec($curl));
    // $licitacoes = $offset_total->_embedded->licitacoes;
    
    // echo json_encode($licitacoes);
    // exit;
    if($offset_total){
        $offset_total = $offset_total->count;
    } else {
        echo 'API (COMPRASNET) FORA DO AR';
        exit;
    }
    // $offset_total = 5000;
    $i = $offset;
    // $i = 474;

    while ($i < $offset_total) {

        $curl = curl_init();
//        curl_setopt_array($curl, [
//            CURLOPT_RETURNTRANSFER => 1,
//            CURLOPT_CONNECTTIMEOUT => 600,
//            CURLOPT_TIMEOUT => 600,
//            CURLOPT_URL => "http://compras.dados.gov.br/licitacoes/v1/licitacoes.json?order_by=data_entrega_proposta&offset=" . $i
//        ]);

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 600,
            CURLOPT_TIMEOUT => 600,
            CURLOPT_URL => "http://compras.dados.gov.br/licitacoes/v1/licitacoes.json?order_by=data_publicacao&data_publicacao_min=".(date('Y')-3)."-".date('m')."-01&offset=".$i
        ]);

    
        $result = json_decode(curl_exec($curl));
        if (!is_object($result)) {
            continue;
        }
        $licitacoes = $result->_embedded->licitacoes;

        foreach($licitacoes as $licitacao){
            
            $identificador = $licitacao->identificador;
            $uasg = $licitacao->uasg;

            foreach ($licitacao AS $campo => $value) {

                if (!is_object($value)) {
                    // $licitacao->$campo = $value != null ? "'$value'" : 'null';
                    if($value != null){
                        if($campo != 'data_entrega_edital' && $campo != 'data_abertura_edital' && $campo != 'data_entrega_proposta' && $campo != 'data_publicacao' && $campo != 'importador_ultima_atualizacao'  && $campo != 'objeto')
                        $value = str_replace("\"", "'", $value);
                        $value = str_replace("\\", "/", $value);
                        $licitacao->$campo = '"' . $value . '"';
                    } else {
                        $licitacao->$campo = 'null';
                    }
                }
            }

            $sqlVerifica = "SELECT * FROM licitacoes_cab WHERE identificador = $identificador";
            $queryVerifica = mysqli_query($con, $sqlVerifica);
            if (mysqli_num_rows($queryVerifica) == 0) {
                $sql = "INSERT INTO licitacoes_cab (
                        uasg,
                        identificador,
                        cod_modalidade,
                        numero_aviso,
                        tipo_pregao,
                        numero_processo,
                        numero_itens,
                        situacao_aviso,
                        objeto,
                        informacoes_gerais,
                        tipo_recurso,
                        nome_responsavel,
                        funcao_responsavel,
                        data_entrega_edital,
                        endereco_entrega_edital,
                        data_abertura_proposta,
                        data_entrega_proposta,
                        data_publicacao
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ";
                if ($stmt = mysqli_prepare($con, $sql)) {
                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "isiississsssssssss",
                            $licitacao->uasg,
                            $licitacao->identificador,
                            $licitacao->modalidade,
                            $licitacao->numero_aviso,
                            $licitacao->tipo_pregao,
                            $licitacao->numero_processo,
                            $licitacao->numero_itens,
                            $licitacao->situacao_aviso,
                            $licitacao->objeto,
                            $licitacao->informacoes_gerais,
                            $licitacao->tipo_recurso,
                            $licitacao->nome_responsavel,
                            $licitacao->funcao_responsavel,
                            $licitacao->data_entrega_edital,
                            $licitacao->endereco_entrega_edital,
                            $licitacao->data_abertura_proposta,
                            $licitacao->data_entrega_proposta,
                            $licitacao->data_publicacao
                        );
                    if (!mysqli_stmt_execute($stmt)) {
                        echo "<br>";
                        echo "ERROR: " . mysqli_error($con);
                        echo "<br>";
                        echo $sql;
                        exit;
                    } else {
                        echo "\n";echo date('d-m-Y H:i:s'); echo " [====]  Identificador Licitação: " . $identificador ;echo ' [====] UASG: ' . $uasg ; echo "\n";
                    }
                } else {
                    echo "<br>";
                    echo "ERROR: " . mysqli_error($con);
                    echo "<br>";
                    echo $sql;
                    exit;
                }
                mysqli_stmt_close($stmt);
            } else {

                $sqlVerificaUpd = "SELECT * FROM licitacoes_cab WHERE identificador = $identificador and data_abertura_proposta > NOW() and situacao_aviso != '". $licitacao->situacao_aviso ."'";
                $queryVerificaUpd = mysqli_query($con, $sqlVerificaUpd);
                
                if (mysqli_num_rows($queryVerificaUpd) > 0) {
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
                            tipo_pregao = ?,
                            numero_processo = ?,
                            numero_itens = ?,
                            situacao_aviso = ?,
                            objeto = ?,
                            informacoes_gerais = ?,
                            tipo_recurso = ?,
                            nome_responsavel = ?,
                            funcao_responsavel = ?,
                            data_entrega_edital = ?,
                            endereco_entrega_edital = ?,
                            data_abertura_proposta = ?,
                            data_entrega_proposta = ?,
                            data_publicacao = ?,
                            importador_ultima_atualizacao = NOW()
                            WHERE identificador = ?
                        ";
                    if ($stmt = mysqli_prepare($con, $sql)) {
                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt, "ssissssssssssss",
                                $licitacao->tipo_pregao,
                                $licitacao->numero_processo,
                                $licitacao->numero_itens,
                                $licitacao->situacao_aviso,
                                $licitacao->objeto,
                                $licitacao->informacoes_gerais,
                                $licitacao->tipo_recurso,
                                $licitacao->nome_responsavel,
                                $licitacao->funcao_responsavel,
                                $licitacao->data_entrega_edital,
                                $licitacao->endereco_entrega_edital,
                                $licitacao->data_abertura_proposta,
                                $licitacao->data_entrega_proposta,
                                $licitacao->data_publicacao,
                                $licitacao->identificador
                            );
                        if (!mysqli_stmt_execute($stmt)) {
                            echo "<br>";
                            echo "ERROR: " . mysqli_error($con);
                            echo "<br>";
                            echo $sql;
                            exit;
                        } else {
                            echo "\n";echo date('d-m-Y H:i:s'); echo " [====]  Licitação Atualizada [====] "; echo "\n [====] Identificador Licitação: " . $identificador ;echo ' [====] UASG: ' .  $uasg ; echo "\n";
                        }
                    } else {
                        echo "<br>";
                        echo "ERROR: " . mysqli_error($con);
                        echo "<br>";
                        echo $sql;
                        exit;
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    echo "\n";echo date('d-m-Y H:i:s'); echo " [====]  Licitação já Cadastrada [====] "; echo "\n [====] Identificador Licitação: " . $identificador ;echo ' [====] UASG: ' . $uasg ; echo "\n";
                }
            }

            $itens_licitacao = requestItensLicitacao($identificador);

            $sqlConsult = "SELECT COUNT(*) as total FROM licitacao_itens WHERE lic_id = $identificador and valid = true";
            $queryConsult = mysqli_query($con, $sqlConsult);
            $consultItens_lic = mysqli_fetch_assoc($queryConsult);
            $totalConsult_itens = $consultItens_lic['total'];

            $doUpdate = false;
            $sqlVerificaUpd = "SELECT * FROM licitacao_itens WHERE identificador = $identificador and updated = false";
            $queryVerificaUpd = mysqli_query($con, $sqlVerificaUpd);
            if (is_object($queryVerificaUpd)) {
                if (mysqli_num_rows($queryVerificaUpd) > 0) {
                    $doUpdate = true;
                }
            }

            if ((count($itens_licitacao) != $totalConsult_itens) or ($doUpdate == true)) {

                if (count($itens_licitacao) > 0) { //EX Count = 3
                    $itens_licitacao = json_decode($itens_licitacao);

                    foreach ($itens_licitacao as $item_licitacao) {

                        foreach ($item_licitacao AS $campo => $value) {
                            // relacionamentos serão feitos pela Lic_id;
                            if (!is_object($value)) {
                                // $item_licitacao->$campo = $value != null ? "\"$value\"" : 'null';
                                if ($value != null) {
                                    $value = str_replace("\"", "'", $value);
                                    $value = str_replace("\\", "/", $value);
                                    $item_licitacao->$campo = '"' . "$value" . '"';
                                } else {
                                    $item_licitacao->$campo = 'null';
                                }
                            }
                        }

                        $sqlVerificaItens = "SELECT id FROM licitacao_itens WHERE lic_id = $identificador AND num_item_licitacao = " . $item_licitacao->numero_item_licitacao;
                        $queryCheckItens = mysqli_query($con, $sqlVerificaItens);

                        if (mysqli_num_rows($queryCheckItens) == 0) {
                            $sql = "INSERT INTO licitacao_itens (
                                    lic_uasg,
                                    lic_id,
                                    num_aviso,
                                    num_item_licitacao,
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
                                    criterio_julgamento
                                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                                ";
                            if ($stmt = mysqli_prepare($con, $sql)) {
                                // Bind variables to the prepared statement as parameters
                                mysqli_stmt_bind_param($stmt, "isiiiisissssssis",
                                        $item_licitacao->uasg,
                                        $item_licitacao->numero_licitacao,
                                        $item_licitacao->numero_aviso,
                                        $item_licitacao->numero_item_licitacao,
                                        $item_licitacao->codigo_item_servico,
                                        $item_licitacao->codigo_item_material,
                                        $item_licitacao->descricao_item,
                                        $item_licitacao->sustentavel,
                                        $item_licitacao->quantidade,
                                        $item_licitacao->unidade,
                                        $item_licitacao->cnpj_fornecedor,
                                        $item_licitacao->cpfVencedor,
                                        $item_licitacao->beneficio,
                                        $item_licitacao->valor_estimado,
                                        $item_licitacao->decreto_7174,
                                        $item_licitacao->criterio_julgamento
                                    );
                                if (!mysqli_stmt_execute($stmt)) {
                                    echo "<br>";
                                    echo "ERROR: " . mysqli_error($con);
                                    echo "<br>";
                                    echo $sql;
                                    exit;
                                } else {
                                    echo "\n";
                                    echo date('d-m-Y H:i:s');
                                    echo " [====] Cadastrando Itens do Portal Comprasnet da Licitacao: \n";
                                    echo "\n Id Licitação: " . $identificador;
                                    echo "\n UASG: " . $uasg;
                                    echo "\n Descrição Item: " . $item_licitacao->descricao_item;
                                    echo "\n Numero do item: " . $item_licitacao->numero_item_licitacao;
                                    echo "\n Quantidade do Item: " . $item_licitacao->quantidade . "\n";
                                }
                            } else {
                                echo "<br>";
                                echo "ERROR: " . mysqli_error($con);
                                echo "<br>";
                                echo $sql;
                                exit;
                            }
                            mysqli_stmt_close($stmt);
 
                            $sql = 'SELECT MAX(id) as id FROM licitacao_itens';
                            $query = mysqli_query($con, $sql);
                            if ($query) {
                                $last_id = mysqli_fetch_assoc($query);
                                $last_id = $last_id['id'];
                            }

                        } else {
                            if ($doUpdate == true) {
                                $sql = "UPDATE licitacao_itens SET
                                        lic_uasg = ?,
                                        num_aviso = ?,
                                        cod_item_servico = ?,
                                        cod_item_material = ?,
                                        descricao_item = ?,
                                        sustentavel = ?,
                                        quantidade = ?,
                                        unidade = ?,
                                        cnpj_fornecedor = ?,
                                        cpf_vencedor = ?,
                                        beneficio = ?,
                                        valor_estimado = ?,
                                        decreto_7174 = ?,
                                        criterio_julgamento = ?,
                                        valid = true,
                                        updated = true
                                        WHERE
                                        lic_id = ? AND
                                        num_item_licitacao = ?
                                    ";
                                if ($stmt = mysqli_prepare($con, $sql)) {
                                    // Bind variables to the prepared statement as parameters
                                    mysqli_stmt_bind_param($stmt, "iiiisissssssissi",
                                            $item_licitacao->uasg,
                                            $item_licitacao->numero_aviso,
                                            $item_licitacao->codigo_item_servico,
                                            $item_licitacao->codigo_item_material,
                                            $item_licitacao->descricao_item,
                                            $item_licitacao->sustentavel,
                                            $item_licitacao->quantidade,
                                            $item_licitacao->unidade,
                                            $item_licitacao->cnpj_fornecedor,
                                            $item_licitacao->cpfVencedor,
                                            $item_licitacao->beneficio,
                                            $item_licitacao->valor_estimado,
                                            $item_licitacao->decreto_7174,
                                            $item_licitacao->criterio_julgamento,
                                            $identificador,
                                            $item_licitacao->numero_item_licitacao
                                        );
                                    if (!mysqli_stmt_execute($stmt)) {
                                        echo "<br>";
                                        echo "ERROR: " . mysqli_error($con);
                                        echo "<br>";
                                        echo $sql;
                                        exit;
                                    } else {
                                        echo "\n";
                                        echo date('d-m-Y H:i:s');
                                        echo " [====] Atualizando Itens do Portal Comprasnet da Licitacao: \n";
                                        echo "\n Id Licitação: " . $identificador;
                                        echo "\n UASG: " . $uasg;
                                        echo "\n Descrição Item: " . $item_licitacao->descricao_item;
                                        echo "\n Numero do item: " . $item_licitacao->numero_item_licitacao;
                                        echo "\n Quantidade do Item: " . $item_licitacao->quantidade . "\n";
                                    }
                                } else {
                                    echo "<br>";
                                    echo "ERROR: " . mysqli_error($con);
                                    echo "<br>";
                                    echo $sql;
                                    exit;
                                }
                                mysqli_stmt_close($stmt);
                            } else {
                                echo "\n";echo date('d-m-Y H:i:s'); echo " [====]  Item já Cadastrado [====] "; echo "\n [====] Identificador Licitação: " . $identificador ;echo ' [====] UASG: ' . $uasg ; echo "\n Numero do item: " . $item_licitacao->numero_item_licitacao; echo "\n";
                            }

                            $item = mysqli_fetch_assoc($queryCheckItens);
                            $last_id = $item['id'];
                        }

                        if ($item_licitacao->descricao_item != '' && $item_licitacao->descricao_item != null && $item_licitacao->descricao_item != 'null') {

                            $ret = reqApiFutura($item_licitacao->numero_item_licitacao, $item_licitacao->descricao_item, $item_licitacao->quantidade, $item_licitacao->unidade);

                            if (count($ret) > 0) {
                                saveDadosFutura($identificador, $last_id, $ret, $doUpdate);
                            }
                        }
                    }
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
//               echo "Possui data";
//          } else {
//                echo "Não possui item";
//          }

            $orgao_licitacao = requestParseOrgaosGov($uasg);
            if(count($orgao_licitacao) > 0) {
                saveOrgao($uasg, $orgao_licitacao);
            }

            if (isset($diff)) {
                $count--;
            }

            if (isset($diff) && $count == 0) {
                break;
            }
         //logs
        }

        if (isset($diff) && $count == 0) {
            $i = $offsetBanco - $diff;
        }

        echo '<pre>'; echo "\n==== GRAVADO $i Licitacoes ====\n"; echo '</pre>';

        if (!isset($diff)) {
            $i += 500;
        } else {
            unset($diff);
        }
        
    }

    echo '1';
    exit;
    // print_r($licitacoes);
}

function saveDadosFutura($lic_id, $last_id, $ret, $doUpdate) {
    $con = bancoMysqli();

    foreach($ret as $arrays){
        $nome_portal                     = $arrays[0];
        $num_item_licitacao              = $arrays[1];
        $cod_jd_produto                  = $arrays[2];
        $desc_licitacao_portal           = $arrays[3];
        $quantidade_item_licitacao       = $arrays[4];
        $desc_licitacao_jd               = $arrays[5];
        $cod_produto_jd                  = $arrays[6];
        $quantidade_embalagem_produto_jd = $arrays[7];
        $desc_produto_jd                 = $arrays[8];
        $cod_fabricante_jd               = $arrays[9];
        $nome_fabricante                 = $arrays[10];
        $estoque_disp_jd                 = $arrays[11];

        if($cod_fabricante_jd && $nome_fabricante){
            $sql = "SELECT id FROM fabricantes WHERE cod_fabricante = $cod_fabricante_jd";
            $query = mysqli_query($con, $sql);

            if (mysqli_num_rows($query) == 0) {
                $sql = "INSERT INTO fabricantes (
                         nome,
                         descricao,
                         cod_fabricante
                        ) VALUES (?, ?, ?)
                    ";
                if ($stmt = mysqli_prepare($con, $sql)) {
                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "ssi",
                            $nome_fabricante,
                            $nome_fabricante,
                            $cod_fabricante_jd
                        );
                    if (!mysqli_stmt_execute($stmt)) {
                        echo "<br>";
                        echo "ERROR: " . mysqli_error($con);
                        echo "<br>";
                        echo $sql;
                        exit;
                    } else {
                        echo "\n";echo date('d-m-Y H:i:s');echo " [====] Cadastrando Fabricantes Medicamentos Futura: \n"; echo "\n Descrição Fabricante: " . $nome_fabricante ; echo "\n Código do Fabricante: " . $cod_fabricante_jd . "\n";
                    }
                } else {
                    echo "<br>";
                    echo "ERROR: " . mysqli_error($con);
                    echo "<br>";
                    echo $sql;
                    exit;
                }
                mysqli_stmt_close($stmt);

                $sql = 'SELECT MAX(id) as id FROM fabricantes';
                if($query = mysqli_query($con, $sql)){
                    $last_fab_id = mysqli_fetch_assoc($query);
                    $last_fab_id = $last_fab_id['id'];
                }
            } else {
                $fabri = mysqli_fetch_assoc($query);
                $last_fab_id = $fabri['id'];
            }
        }

        if ($doUpdate == false) {
            $sql = "INSERT INTO produtos_futura (
                     item_id,
                     fabricante_id,
                     lic_id,
                     nome_portal,
                     num_item_licitacao,
                     cod_jd_produto,
                     desc_licitacao_portal,
                     quantidade_item_licitacao,
                     desc_licitacao_jd,
                     cod_produto_jd,
                     quantidade_embalagem_produto_jd,
                     desc_produto_jd,
                     cod_fabricante_jd,
                     nome_fabricante,
                     estoque_disp_jd
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ";
            if ($stmt = mysqli_prepare($con, $sql)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "iissiisisiisiss",
                        $last_id,
                        $last_fab_id,
                        $lic_id,
                        $nome_portal,
                        $num_item_licitacao,
                        $cod_jd_produto,
                        $desc_licitacao_portal,
                        $quantidade_item_licitacao,
                        $desc_licitacao_jd,
                        $cod_produto_jd,
                        $quantidade_embalagem_produto_jd,
                        $desc_produto_jd,
                        $cod_fabricante_jd,
                        $nome_fabricante,
                        $estoque_disp_jd
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
        } else {
            $sql = "UPDATE produtos_futura SET
                    fabricante_id = ?,
                    nome_portal = ?,
                    num_item_licitacao = ?,
                    cod_jd_produto = ?,
                    desc_licitacao_portal = ?,
                    quantidade_item_licitacao = ?,
                    desc_licitacao_jd = ?,
                    cod_produto_jd = ?,
                    quantidade_embalagem_produto_jd = ?,
                    desc_produto_jd = ?,
                    cod_fabricante_jd = ?,
                    nome_fabricante = ?,
                    estoque_disp_jd = ?,
                    WHERE
                    lic_id = ?
                ";
            if ($stmt = mysqli_prepare($con, $sql)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "isiisisiisisss",
                        $last_fab_id,
                        $nome_portal,
                        $num_item_licitacao,
                        $cod_jd_produto,
                        $desc_licitacao_portal,
                        $quantidade_item_licitacao,
                        $desc_licitacao_jd,
                        $cod_produto_jd,
                        $quantidade_embalagem_produto_jd,
                        $desc_produto_jd,
                        $cod_fabricante_jd,
                        $nome_fabricante,
                        $estoque_disp_jd,
                        $lic_id
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
        }
    }
}

function saveOrgao ($uasg, $orgao_licitacao) {

    $con = bancoMysqli();

    $orgao_licitacao = json_decode($orgao_licitacao);

    foreach ($orgao_licitacao AS $campo => $value) {

        if (!is_object($value)) {
            $value = $value != null ?  html_entity_decode($value) : null;
            $orgao_licitacao->$campo = $value != null ? "\"$value\"" : 'null';
        }

    }

    $sqlConsult = "SELECT * FROM licitacao_orgao WHERE uasg = $uasg AND lic_orgao = $orgao_licitacao->orgao";
    $queryConsult = mysqli_query($con, $sqlConsult);
    if (mysqli_num_rows($queryConsult) == 0) {
        $sql = "INSERT INTO licitacao_orgao (uasg, lic_orgao, lic_estado) VALUES ($uasg, $orgao_licitacao->orgao, $orgao_licitacao->estado)";
        if(!mysqli_query($con, $sql)){
            print_r(mysqli_error($con));
            echo $sql;
            exit;
        }
    }
}

function saveLicAndItem($licitacao, $app = false) {
    $con = bancoMysqli();

    $identificador = $licitacao->identificador;

    $sqlVerifica = "SELECT * FROM licitacoes_cab WHERE identificador = $identificador";
    $queryVerifica = mysqli_query($con, $sqlVerifica);
    if (mysqli_num_rows($queryVerifica) == 0) {
        $sql = "INSERT INTO licitacoes_cab (
                uasg,
                identificador,
                cod_modalidade,
                numero_aviso,
                tipo_pregao,
                numero_processo,
                numero_itens,
                situacao_aviso,
                objeto,
                informacoes_gerais,
                tipo_recurso,
                nome_responsavel,
                funcao_responsavel,
                data_entrega_edital,
                endereco_entrega_edital,
                data_abertura_proposta,
                data_entrega_proposta,
                data_publicacao
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ";
        if ($stmt = mysqli_prepare($con, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "isiississsssssssss",
                    $licitacao->uasg,
                    $licitacao->identificador,
                    $licitacao->modalidade,
                    $licitacao->numero_aviso,
                    $licitacao->tipo_pregao,
                    $licitacao->numero_processo,
                    $licitacao->numero_itens,
                    $licitacao->situacao_aviso,
                    $licitacao->objeto,
                    $licitacao->informacoes_gerais,
                    $licitacao->tipo_recurso,
                    $licitacao->nome_responsavel,
                    $licitacao->funcao_responsavel,
                    $licitacao->data_entrega_edital,
                    $licitacao->endereco_entrega_edital,
                    $licitacao->data_abertura_proposta,
                    $licitacao->data_entrega_proposta,
                    $licitacao->data_publicacao
                );
            if (!mysqli_stmt_execute($stmt)) {
                echo "<br>";
                echo "ERROR: " . mysqli_error($con);
                echo "<br>";
                echo $sql;
                exit;
            } else {
                echo "\n";echo date('d-m-Y H:i:s'); echo " [====]  Identificador Licitação: " . $identificador ;echo ' [====] UASG: ' . $licitacao->uasg ; echo " [====] Origem: saveLicAndItem\n";
            }
        } else {
            echo "<br>";
            echo "ERROR: " . mysqli_error($con);
            echo "<br>";
            echo $sql;
            exit;
        }
        mysqli_stmt_close($stmt);
    } else {
        $sqlVerificaUpd = "SELECT * FROM licitacoes_cab WHERE identificador = $identificador and data_abertura_proposta > NOW() and situacao_aviso != '". $licitacao->situacao_aviso ."'";
        $queryVerificaUpd = mysqli_query($con, $sqlVerificaUpd);
        if (mysqli_num_rows($queryVerificaUpd) > 0) {
            $sql = "UPDATE licitacoes_cab SET
                    tipo_pregao = ?,
                    numero_processo = ?,
                    numero_itens = ?,
                    situacao_aviso = ?,
                    objeto = ?,
                    informacoes_gerais = ?,
                    tipo_recurso = ?,
                    nome_responsavel = ?,
                    funcao_responsavel = ?,
                    data_entrega_edital = ?,
                    endereco_entrega_edital = ?,
                    data_abertura_proposta = ?,
                    data_entrega_proposta = ?,
                    data_publicacao = ?,
                    importador_ultima_atualizacao = NOW()
                    WHERE identificador = ?
                ";
            if ($stmt = mysqli_prepare($con, $sql)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "ssissssssssssss",
                        $licitacao->tipo_pregao,
                        $licitacao->numero_processo,
                        $licitacao->numero_itens,
                        $licitacao->situacao_aviso,
                        $licitacao->objeto,
                        $licitacao->informacoes_gerais,
                        $licitacao->tipo_recurso,
                        $licitacao->nome_responsavel,
                        $licitacao->funcao_responsavel,
                        $licitacao->data_entrega_edital,
                        $licitacao->endereco_entrega_edital,
                        $licitacao->data_abertura_proposta,
                        $licitacao->data_entrega_proposta,
                        $licitacao->data_publicacao,
                        $licitacao->identificador
                    );
                if (!mysqli_stmt_execute($stmt)) {
                    echo "<br>";
                    echo "ERROR: " . mysqli_error($con);
                    echo "<br>";
                    echo $sql;
                    exit;
                } else {
                    echo "\n";echo date('d-m-Y H:i:s'); echo " [====]  Licitação Atualizada [====] "; echo "\n [====] Identificador Licitação: " . $identificador ;echo ' [====] UASG: ' .  $licitacao->uasg ; echo " [====] Origem: saveLicAndItem\n";
                }
            } else {
                echo "<br>";
                echo "ERROR: " . mysqli_error($con);
                echo "<br>";
                echo $sql;
                exit;
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "\n";echo date('d-m-Y H:i:s'); echo " [====]  Licitação já Cadastrada [====] "; echo "\n [====] Identificador Licitação: " . $identificador ;echo ' [====] UASG: ' . $licitacao->uasg ; echo " [====] Origem: saveLicAndItem\n";
        }
    }

    if (!$app) {
        $itens_licitacao = requestItensLicitacao($identificador);
    }

    if(count($itens_licitacao) > 0 ){ //EX Count = 3
        $itens_licitacao = json_decode($itens_licitacao);

        foreach($itens_licitacao as $item_licitacao){

            $num_item_comprasnet = $item_licitacao->numero_item_licitacao;
            $descricao_item = $item_licitacao->descricao_item;
            $qtd_item_comprasnet = $item_licitacao->quantidade;
            $un_item_comprasnet = $item_licitacao->unidade;

            $sqlVerificaItens = "SELECT id FROM licitacao_itens WHERE lic_id = $identificador AND num_item_licitacao = $num_item_comprasnet";
            $queryCheckItens = mysqli_query($con, $sqlVerificaItens);

            if (mysqli_num_rows($queryCheckItens) == 0) {
                $sql = "INSERT INTO licitacao_itens (
                        lic_uasg,
                        lic_id,
                        num_aviso,
                        num_item_licitacao,
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
                        criterio_julgamento
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ";
                if ($stmt = mysqli_prepare($con, $sql)) {
                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "isiiiisissssssis",
                            $item_licitacao->uasg,
                            $item_licitacao->numero_licitacao,
                            $item_licitacao->numero_aviso,
                            $item_licitacao->numero_item_licitacao,
                            $item_licitacao->codigo_item_servico,
                            $item_licitacao->codigo_item_material,
                            $item_licitacao->descricao_item,
                            $item_licitacao->sustentavel,
                            $item_licitacao->quantidade,
                            $item_licitacao->unidade,
                            $item_licitacao->cnpj_fornecedor,
                            $item_licitacao->cpfVencedor,
                            $item_licitacao->beneficio,
                            $item_licitacao->valor_estimado,
                            $item_licitacao->decreto_7174,
                            $item_licitacao->criterio_julgamento
                        );
                    if (!mysqli_stmt_execute($stmt)) {
                        echo "<br>";
                        echo "ERROR: " . mysqli_error($con);
                        echo "<br>";
                        echo $sql;
                        exit;
                    } else {
                        echo "\n";echo date('d-m-Y H:i:s');echo " [====] Cadastrando Itens do Portal Comprasnet da Licitacao: \n";  echo "\n Id Licitação: " . $identificador ;  echo "\n UASG: " . $licitacao->uasg ; echo "\n Descrição Item: " . $descricao_item ; echo "\n Numero do item: " . $num_item_comprasnet ;echo "\n Quantidade do Item: " . $qtd_item_comprasnet ; echo " [====] Origem: saveLicAndItem\n";
                    }
                } else {
                    echo "<br>";
                    echo "ERROR: " . mysqli_error($con);
                    echo "<br>";
                    echo $sql;
                    exit;
                }
                mysqli_stmt_close($stmt);
 
                $sql = 'SELECT MAX(id) as id FROM licitacao_itens';
                $query = mysqli_query($con, $sql);
                if($query){
                    $last_id = mysqli_fetch_assoc($query);
                    $last_id = $last_id['id'];
                }

            } else {
                $item = mysqli_fetch_assoc($queryCheckItens);
                $last_id = $item['id'];
            }

            if ($descricao_item != '' && $descricao_item != null && $descricao_item != 'null' ){

                $ret = reqApiFutura($num_item_comprasnet, $descricao_item, $qtd_item_comprasnet, $un_item_comprasnet);

                if (count($ret) > 0) {
                    saveDadosFutura($identificador, $last_id, $ret, $doUpdate);
                }

            }
        }
    }
//               echo "Possui data";
//          } else {
//                echo "Não possui item";
//          }

    $orgao_licitacao = requestParseOrgaosGov($licitacao->uasg);
    if(count($orgao_licitacao) > 0) {
        $orgao_licitacao = json_decode($orgao_licitacao);

        foreach ($orgao_licitacao AS $campo => $value) {

            if (!is_object($value)) {
                $value = $value != null ?  html_entity_decode($value) : null;
                $orgao_licitacao->$campo = $value != null ? "\"$value\"" : 'null';
            }

        }

        $sqlConsult = "SELECT * FROM licitacao_orgao WHERE uasg = $licitacao->uasg AND lic_orgao = $orgao_licitacao->orgao";
        $queryConsult = mysqli_query($con, $sqlConsult);
        if (mysqli_num_rows($queryConsult) == 0) {
            $sql = "INSERT INTO licitacao_orgao (uasg, lic_orgao, lic_estado) VALUES ($licitacao->uasg, $orgao_licitacao->orgao, $orgao_licitacao->estado)";
            if(!mysqli_query($con, $sql)){
                print_r(mysqli_error($con));
                echo $sql;
                exit;
            }
        }
    }

}

function requestItensLicitacao($identificador = '') {

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_CONNECTTIMEOUT => 600,
        CURLOPT_TIMEOUT => 600,
        CURLOPT_URL => "http://compras.dados.gov.br/licitacoes/doc/licitacao/$identificador/itens.json"
    ]);


    $result = json_decode(curl_exec($curl));
        
    if (!is_object($result)) {
        return null;
    }

    $itens = $result->_embedded->itensLicitacao;

    return json_encode($itens);

}

function requestParseOrgaosGov($uasg){
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_CONNECTTIMEOUT => 600,
        CURLOPT_TIMEOUT => 600,
        CURLOPT_URL => "http://comprasnet.gov.br/ConsultaLicitacoes/Pesquisar_UASG.asp?codUasg=" . $uasg
    ]);

    $result = curl_exec($curl);
    if($result){
        libxml_use_internal_errors(true);   

        $document = new DOMDocument();
        $document->loadHTML($result);
        $document = $document->saveHTML();

        $parse = 0;
        while ($parse < 3){
            $doc = $document;

            $doc = explode('<table border="0" width="100%" cellspacing="1" cellpadding="2" class="td"><tr bgcolor="#FFFFFF">', $doc);

            if (isset($doc[$parse])){
            $doc = explode('bgcolor="#FFFFFF" class="tex3a" align="center"', $doc[$parse]);
            }

            if(isset($doc[1])){
            $doc = explode('<td>', $doc[1]);
            }

            $data = array();

            if(isset($doc['2'])){
                $uasg = explode('</td>', $doc[2]);
                $data['uasg'] = trim($uasg[0]);
            }

            if(isset($doc[3])){
                $orgao = explode('</td>', $doc[3]);
                $data['orgao'] = trim($orgao[0]);
            }

            if(isset($doc[4])){
                $doc = explode('</td>', $doc[4]);
                $data['estado'] = trim($doc[0]);
            }

            $parse++;
            if(count($data) > 0){
                if ($data['orgao'] != '' && $data['orgao'] != 'undefined' && $data['estado'] != 'null'){
                    return json_encode($data);
                }
            }
        }
    }
}
