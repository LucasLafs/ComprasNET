<?php
require_once("../header/cabecalho.php");

?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Cotações Gerais</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class='col-md-12'>

                    <!-- Default box -->
                    <div class="card">
                         <div class="card-header" style="border-bottom: none !important; margin-bottom: -30px;">
                           <div class="alert alert-success alert-sincronismo" role="alert" style="max-width: 240px; margin-bottom: 15px;">
                             <i class="fa fa-check-circle text-green">&nbsp;Sincronismo realizado com sucesso.</i>
                           </div>
                          </div>
                        <div class="card-body">
                          <div class="tab1-loading overlay loadTable" style="display: none"></div>
                          <div class="tab1-loading loading-img loadTable" style="display: none"></div>
                            <div class='row'>
                                <div class='col-md-12'>
                                    <button style="display: none; margin-top: 15px; float: right;" class="btn btn-tool" id="btnForceSincronismo" onClick='getLicGerais();' title="Forçar sincronismo">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                  <form id="formFiltros">
                                    <div id="filtroData" style="display: none; margin-left: -2%;">
                                      <label>Datas
                                        <input type="date" name="data" id="inputfiltroData" style="width: 120%" class="input form-control form-control-sm inputFiltro"></label>
                                    </div>
                                    <div id="filtroNomeProduto">
                                      <label>Nome do Produto
                                        <input type="text" name="nome_produto" id="nome_produto" class="input form-control form-control-sm inputFiltro" style="width: 150%"></label>
                                    </div>
                                    <div id="filtroObjDesc" style="display: none">
                                      <label>Objeto Descrição
                                        <input type="text" name="desc_obj" id="desc_obj" style="width: 150%" id="desc_obj" class="input form-control form-control-sm inputFiltro"></label>
                                    </div>
                                    <div id="lupaFiltro" style="display: none">
                                      <input type="hidden" name="act" value="getLicitacoes">
                                      <button style="margin-left: -40px;margin-top: 15px;" name="filtro" value="1" type="button" class="btn btn-link" id="filtrarCotacoes"><span class="fa fa-search"></span></button>
                                    </div>
                                  </form>

                                    <table id="table-data-licitacoes" class="table table-responsive table-hover vertical-align"  style="width: 100% !important;">
                                        <thead>
                                            <tr>
                                                <th scope="col"></th>
                                                <th scope="col" class="vertical-align"></th>
                                                <th scope="col">UASG</th>
                                                <th scope="col">UF</th>
                                                <th scope="col">Data Entrega</th>
                                                <th scope="col">Data Abertura</th>
                                                <th scope="col">Pregão</th>
                                                <th scope="col">Descrição</th>
                                                <th scope="col">Objeto</th>
                                                <th scope="col">Situação</th>
                                                <th style='text-align: right;' scope="col">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody id='licitacao-itens'>

                                            <!--  <div class="tab1-loading overlay"></div>
                                    <div class="tab1-loading loading-img"></div>-->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <!-- <div class="card-footer">
                          </div> -->
                        <!-- /.card-footer-->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </section>
</div>
<!-- /.box -->
<script>
$(document).ready(function() {

    /* Imports  */

    // var cotacoes = document.createElement('script');
    // cotacoes.src = '../js/cotacoes.js';
    // document.head.appendChild(cotacoes);

    getCotacoes();
    // getLicGerais();

    // $(window).on('load', function() {
    //     $('.tab1-loading').hide();
    //     $("#loadingAllEmails").hide();
    // });

    $(function() {
        $(".sidebar-light-orange").find('.nav-pills').find('a[href="./cotacoes.php"]').addClass('active');
    });

});

function getLicGerais(){
  $.ajax({
    type: "GET",
    url: '../api/request_licitacoes.php',
    data: 'act=requestLicitacoes',
    cache: false,
    success: function (data){
      if (Array.isArray(data)){
        $(".alert-sincronismo").fadeIn();


        window.setTimeout(function() {
          $(".alert-sincronismo").fadeOut();
        }, 2000);
      }
    }
    // require_once ("../api/request_licitacoes.php");
    // $_REQUEST['act'] = '&requestLicitacoes';
    // requestLicGeraisComprasNet();
  });

    $.ajax({
        type: "GET",
        url: '../api/request_licitacoes.php',
        data: 'act=requestLicitacoesApp',
        cache: false,
        success: function (data){
            if (Array.isArray(data)){
                $(".alert-sincronismo").fadeIn();
                window.setTimeout(function() {
                    $(".alert-sincronismo").fadeOut();
                }, 2000);
            }
        }
        // require_once ("../api/request_licitacoes.php");
        // $_REQUEST['act'] = '&requestLicitacoes';
        // requestLicGeraisComprasNet();
    });
}

function getProdGerais() {
  $.ajax({
    type: 'POST',
    url: '../api/request_produtos.php',
    data: '',
    cache: false,
    success: function (data){
      if(Array.isArray){
        console.log('Success');

      } else {
        console.log('fail');
      }
    }
  });

}

function getCotacoes(stop = 0) {
    $('.tab1-loading').show();
    $("#loadingAllEmails").show();
    $(".loadTable").show();

    $.when(
        $.ajax('../ajax/cotacoes.php?act=getLicitacoes'),
    ).then(function (data) {
        if (data != 0) {
            data = JSON.parse(data);

            makeTblLicitacoes(data);

            if (data.length < 100) {
                getLicGerais();
            }

            $('.tab1-loading').hide();
            $("#loadingAllEmails").hide();
            return;
        }

        if (stop == 0) {
            getLicGerais();
        }

        setTimeout(getCotacoes(1), 5000);

    });

 /* $.ajax({
    type: "GET",
    url: "../ajax/cotacoes.php?act=getLicitacoes",
    // data: "act=getLicitacoes",
    // dataType: 'json',
    cache: false,
    beforeSend: function () {
      $(".loadTable").show();
    },
    success: function(data) {
        console.log(data);
        if (Array.isArray(data)) {
            data = JSON.parse(data);

            makeTblLicitacoes(data);

            return;
        }


        getLicGerais();

        setTimeout(getCotacoes, 5000);

    }

  }).done(function () {
      $('.tab1-loading').hide();
      $("#loadingAllEmails").hide();
  });*/
}

function locationBrVal(valor) {
  return parseFloat(valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL', minimumFractionDigits: 2});
}


function makeTblLicitacoes(data) {

  var element = $("#table-data-licitacoes");


  var table = element.DataTable({
    retrieve: true,
    "autoWidth": false,
    "responsive": true,
    data: data,
    "columns": [
      {
        "visible": false,
      },
      {
        className: "details-control",
        "orderable": false,
        width: "5%",
      },
      {
        witdh: "6%",
        className: "vertical-align",
        "orderable": false,
      },
      {
        witdh: "6%",
        className: "vertical-align",
        "orderable": false,
      },
      {
        witdh: "6%",
        className: "vertical-align",
      },
      {
        witdh: "6%",
        className: "vertical-align",
      },
      {
        witdh: "7%",
        className: "vertical-align",
        "orderable": false,
      },
      {
        width: '18%',
        className: "vertical-align",
      },
      {
        width: '55%',
        className: "vertical-align",
      },
      {
        width: '10%',
        className: "vertical-align",
      },
      {
        className: "vertical-align",
        "orderable": false,
        width: "5%",
      },
    ],
    'aaSorting': [],
    "dom": "<'row'<'col-sm-2 pull-left'f><'col-sm-2 filtroData'><'col-sm-3 filtroNomeProduto'><'col-sm-3 filtroObjDesc'><'col-sm-1 lupa'><'col-sm-1 pull-right forceSincronismo'>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-1'i><'offset-4 col-sm-7 text-right'p>>",
    fnInitComplete: function () {
      $('div.forceSincronismo').html($('#btnForceSincronismo').show());
      $('div.filtroData').html($('#filtroData').show());
      $('div.filtroNomeProduto').html($('#filtroNomeProduto').show());
      $('div.filtroObjDesc').html($('#filtroObjDesc').show());
      $('div.lupa').html($('#lupaFiltro').show());
    },
    "fnDrawCallback": function () {

      $('#table-data-licitacoes tbody').off('click').on('click', 'td.details-control', function() {

        var tr = $(this).closest('tr');
        var row = table.row(tr);

        var checkbox = '';

        if (row.child.isShown()) {
          // This row is already open - close it
          row.child.hide();
          tr.removeClass('shown');
        } else {
          // Open this row
          var infos = row.data();
          var identificador = infos[0];
          tr.addClass('shown');

          $.ajax({
            type: 'GET',
            url: '../ajax/cotacoes.php',
            data: 'act=getItensLicitacao&identificador=' + identificador,
            cache: false,
            async: true,
            beforeSend: function () {
              $(".loadTable").show();
            },
            success: function(data) {
              var itens = [];

              if (data) {
                data = JSON.parse(data);

                var datas = data.datas_envio;
                var fabricantes = data.fabricantes;
                var itensComProduto = data.itensComProduto;
                var email_enviados = data.email_enviados;

                var flag;
                var info;
                var input;
                var value;
                var title;
                var disabled;
                var iconColor;
                var idFabricante;
                var produto_id;
                var display;

                $.each(data.itens, function(i, d) {
                  // value='"+d.id+"'
                  info = '';
                  flag = '';
                  input = '';
                  value = '';
                  display = 'none';
                  idFabricante = d.idFabricante;
                  produto_id = d.produto_id;
                  iconColor = '#495057';
                  disabled = 'disabled';

                  title = 'Esse item não possui fabricante';
                  if (itensComProduto.indexOf(d.id) > -1) {
                    disabled = '';
                    iconColor = '#17a2b8';
                    title = 'Enviar E-mail';
                    value = "value='"+d.id+"'";

                    input = '<label class="container" >\n' +
                      '  <input type="checkbox" style="background: white !important"  value="'+d.id+'" data-idFabri="'+idFabricante+'" data-ident="'+identificador+'" data-pf_id="'+produto_id+'" class="checkOne checkOneItem'+identificador+'">\n' +
                      '  <span class="checkmark"></span>\n' +
                      '</label>';
                  }

                  if (email_enviados[produto_id] != undefined) {
                    if (email_enviados[produto_id] == idFabricante) {
                      info = "E-mail enviado: " + datas[produto_id];
                      iconColor = '#17a2b8';
                      title = 'E-mail Enviado';
                      value = "value='" + d.id + "'";
                      display = 'block';

                    }
                  }

                  flag = "<i class='fa fa-check-square text-success' title='" + info + "'" + disabled + " id=flag" + produto_id + " style='display: "+display+"; float: right; margin-top: -21px;margin-left: 57px; font-size: 12px;'></i>";
                  itens.push([
                    input || '-',
                    d.lic_id  || '-',
                    d.num_aviso  || '-',
                    d.descricao_item || '-',
                    d.desc_produto || '-',
                    d.fabricante || '-',
                    d.cod_produto || '-',
                    d.quantidade || '-',
                    d.unidade || '-',
                    d.valor_estimado ? locationBrVal(d.valor_estimado) : '-',
                    " <button style='margin-left: 20px;color: "+iconColor+";' class='btn btn-sm btn-edit pull-left sendMail'\n" +
                    "      title='"+title+"' id='"+d.id+"'  data-pf_id='"+produto_id+"' data-fabricante='"+idFabricante+"' "+disabled+" "+value+" > <span class='fas fa-mail-bulk'/>\n" +
                    "          </button>" + flag,
                    //  " <i class='fa fa-thumbs-up text-info' style='float: right; margin-top: -14px; font-size: 13px;'></i>",
                  ]);
                });
              }

              $("table.tblItens").DataTable({
                retrieve: true,
                "responsive": true,
                "searching": false,
                "paginate": false,
                "bInfo" : false,
                data: itens,
                "language": {
                  "emptyTable": "Sem itens disponíveis",
                },
                "order": [4, 'desc'],
                "columns": [
                  {
                    className: "vertical-align",
                    "orderable": false,
                    width: "5%",
                  },
                  {
                    visible: false,
                  },
                  {
                    className: "vertical-align",
                    width: "9%"
                  },
                  {
                    className: "vertical-align",
                    width: "30%",
                  },
                  {
                    className: "vertical-align",
                    width: "30%",
                  },
                  {
                    className: "vertical-align",
                    width: "11%",
                  },
                  {
                    className: "vertical-align",
                    width: "9%",
                  },
                  {
                    className: "vertical-align",
                    width: "8%",
                  },
                  {
                    className: "vertical-align",
                    width: "7%",
                  },
                  {
                    className: "vertical-align",
                    width: "7%",
                  },
                  {
                    className: "vertical-align",
                    "orderable": false,
                    width: "9%",
                  }
                ],
              });
            }
          });

          row.child(format(row.data(), identificador)).show();

          $(".loadTable").hide();
        }
      });

      function format(d, id) {
        // `d` is the original data object for the row

        return '<div class="row"><div class="col-12"><button style="float: right; margin-right: -8px; margin-bottom: 10px;\n' +
          '    display: none;" class="btn btn-sm btn-edit text-info pull-left enviarVariosEmails" id="enviarVariosEmails'+id+'" value="'+id+'" title="Enviar E-mails" >' +
          '<span id="loadingAllEmails" style="display: none"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>&nbsp;&nbsp;</span> <span class=\'fas fa-mail-bulk\'/>\n' +
          '              </button></div><div class="table-responsive" style="background: #f5f5f5;">' +
          '<table style="width: 100% !important;" class="table table-responsive table-condesed tblItens" cellpadding="5" cellspacing="0" border="0"> <thead>' +
          '        <tr> ' +
          '         <th scope="col"><label class="container"><input type="checkbox"  value="'+id+'" class="checkAllItens"> <span class="checkmark"  ></span></label></th>' +
          '         <th scope="col">ID Licitação</th>' +
          '         <th scope="col">Número Aviso</th>' +
          '         <th scope="col">Descrição Original</th>' +
          '         <th scope="col">Descrição Futura</th>' +
          '         <th scope="col">Fabricante</th>' +
          '         <th scope="col">Cód. Item</th>' +
          '         <th scope="col">Qtd</th>' +
          '         <th scope="col">Un</th>' +
          '         <th scope="col">Valor Estimado</th>' +
          '         <th style="text-align: right" scope="col">Ações</th>' +
          '        </tr>' +
          '      </thead><tbody></tbody></table> </div>'
          ;
      }
    }
  });

  $(".loadTable").hide();
}

$(document).on('click', '#filtrarCotacoes', function () {

  var element = $("#table-data-licitacoes");
  $(".loadTable").show();
  element.hide();

  var obj = [
    {
      name: 'act',
      value: 'getLicitacoes',
    },
    {
      name: 'filtro',
      value: 1,
    },
    {
      name: 'data',
      value: $("#inputfiltroData").val(),
    },
    {
      name: 'nome_produto',
      value: $("#nome_produto").val(),
    },
    {
      name: 'desc_obj',
      value: $("#desc_obj").val(),
    }];

  $("#formFiltros").append($('div.forceSincronismo').html() + $('div.filtroData').html() + $('div.filtroNomeProduto').html() + $('div.filtroObjDesc').html()  +  $('div.lupa').html());


  if ( $.fn.DataTable.isDataTable( '#table-data-licitacoes' )) {
    element.dataTable().fnClearTable();
    element.dataTable().fnDestroy();
  }

  $.ajax({
    type: "POST",
    url: "../ajax/cotacoes.php",
    data: obj,
    // dataType: 'json',
    cache: false,
    beforeSend: function () {
      $(".loadTable").show();
    },
    success: function (data) {

      if (data != 0) {
        data = JSON.parse(data);
        makeTblLicitacoes(data);
        element.show();
        return true;
      }

      Swal.fire({
        icon: 'info',
        title: 'Nenhum registro encontrado.',
        text: "Não foram encontrados nenhum registro com os filtros desejado."
      }).then(function () {
        getCotacoes();
      });

      element.show();

    },
  });

});

$(document).on('click', '.pdfLicitacao', function () {
  var id = $(this).attr('id');

  window.open("../ajax/makePdf.php?act=createPdf&lic_id=" + id);
});

$(document).on('click', '.printLicitacao', function () {
  var id = $(this).attr('id');

  window.open("../ajax/makePdf.php?act=imprimir&lic_id=" + id);
});


$(document).on('click', '.checkOne', function () {

  var id = $(this).attr('data-ident');
  var clicked = 0;

  $.each($(".checkOne"), function () {
    if ($(this).prop('checked') == true) {
      clicked++;
    }
  });


  if (clicked > 1) {
    $("#enviarVariosEmails" + id).show();
    $("#loadingAllEmails").hide();
  } else {
    $("#enviarVariosEmails" + id).hide();
  }



});



$(document).on('click', '.checkAllItens', function () {
  var id = $(this).val();
  var atual = $(this).prop('checked');
  var Ncheked = 0;
  var checked = 0;
  var prop = false;

  $("#enviarVariosEmails" + id).hide();

  if (atual == true) {
    prop = true;
    if ($(".checkOneItem" + id).length > 1) {
      $("#enviarVariosEmails" + id).show();
    }
  }

  $.each($(".checkOneItem" + id), function () {

    $(this).prop('checked', prop);

    $(this).click(function () {
      checked = 0;
      Ncheked = 0;

      if ($(this).prop('checked') == false) {
        $(".checkAllItens").prop('checked', false);
      } else {
        $.each($(".checkOneItem" + id), function () {
          if ($(this).prop('checked') == false) {
            Ncheked++;
          } else {
            checked++;
          }
        });

        if (Ncheked == 0) {
          $(".checkAllItens").prop('checked', true);
        }

        if (checked > 1) {
          $("#enviarVariosEmails" + id).show();
          $("#loadingAllEmails").hide();
        } else {
          $("#enviarVariosEmails" + id).hide();
        }
      }
    });
  });
});
</script>
