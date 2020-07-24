<?php

function reqApiFutura($num_item_comprasnet = '', $desc_item_comprasnet = '', $qtd_item_comprasnet = '', $un_item_comprasnet = ''){

    if( ! ( $num_item_comprasnet == '' || $desc_item_comprasnet == '' || $qtd_item_comprasnet == '' ) ){
        $nome_portal = 'COMPRASNET';

        // $req =  [$nome_portal, $num_item_comprasnet, $desc_item_comprasnet];

        $req = "{ \"body\": {\"lista\" : [ [\"$nome_portal\",$num_item_comprasnet,\"$desc_item_comprasnet\",$qtd_item_comprasnet,\"\", \"$un_item_comprasnet\"] ] } }";
        // $req = "{ \"body\": {\"lista\" : [ [\"$nome_portal\",$num_item_comprasnet,\"BENZILPENICINA 12000000ui\",$qtd_item_comprasnet,\"\"] ] } }";
        
        // $req = '{ "body": {"lista" : [ ["COMPRASNET",2,"BENZILPENICINA 120000UI",3000, ""] ] } }'; 
       // echo $req;

        $ch = curl_init('http://192.168.1.5:3000/de_para');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        )); 

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        
        $obj = json_decode($response, true);
        
        if (count($obj['body']['lista'])){
            $ret = $obj['body']['lista'];
            return $ret;
        } else {
            if ($err) {
                echo "  ===# ERROR: Não foi retornado informações da API do Medicamentos Futura. #=== \n";
                echo "  ===# Curl ERROR: " . $err . "#=== ";
                exit;
            } 
        };
        

        // echo json_decode($response);
        // echo $response->lista;
        // echo sizeof($response);
        

    }


}

?>
