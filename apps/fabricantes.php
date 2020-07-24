<?php
require_once("../header/cabecalho.php");
?>


<style>
  div.dataTables_wrapper div.dataTables_filter input {
    display: block !important;
    margin-left: 0 !important;
    width: 510px !important;
  }
  div.table-responsive > div.dataTables_wrapper > div.row {
    margin: 0;
    max-width: 100% !important;
  }

  div.dataTables_wrapper div.dataTables_filter label {
    font-weight: bold !important;
    text-align: center !important;
  }

</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Fabricantes</h1>
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
                      <div class="card-header" style="margin-bottom: -30px; border-bottom: none !important;">
                        <div class="alert alert-success alert-success-Fabri" role="alert" style="margin-bottom: 15px;">
                          <i class="fa fa-check-circle text-green"></i>
                        </div>

                      </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12" >
                                    <button style="margin-top: 0; float: right; margin-bottom: -45px;" class="btn text-info btnCadastrarFabricante" id="btnCadastrarFabricante" data-toggle="modal" data-target="#modalCadastroFabri" title="Adicionar Fabricante">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <h4 id='msgSemFabricante' style='display: none;' class='text-info text-center'>Nenhum fabricante cadastrado.</h4>
                                  <div class="table-responsive" id="divTblFabricantes" style="display: none; ">
                                    <table id="tblFabricantes" class="table table-hover table-responsive" style="width: 100%; ">
                                      <thead>
                                      <tr>
                                        <th scope="col">Nome</th>
                                        <th scope="col">E-mail</th>
                                        <th scope="col">Descrição</th>
                                        <th style='text-align:right;' scope="col">Ações</th>
                                      </tr>
                                      </thead>
                                      <tbody>
                                      </tbody>
                                    </table>
                                  </div>

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
        <?php
        include ('modais/cadastroFabricante.php');
        include ('modais/editarFabricante.php');
        include ('modais/exclusao.php');
        ?>
    </section>
</div>
<!-- /.box -->

<script>

$(document).ready(function() {

    /* Imports  */

    getFabris();

    $(window).on('load', function () {
        $('.tab1-loading').hide();
    });

    $(function () {
        $(".sidebar-light-orange").find('.nav-pills').find('a[href="./fabricantes.php"]').addClass('active');
    });

});

function getFabris() {

    $.ajax({
        type: 'GET',
        url: '../ajax/fabricantes.php',
        data: 'act=allFabris',
        cache: false,
        beforeSend: function () {
            $(".loadTable").show();
        },
        success: function(data) {

            if (data == 0) {
                $('.btnCadastrarFabricante').removeClass('none');
                $("#msgSemFabricante").show();
                $("#divTblFabricantes").hide();
                $(".loadTable").hide();
              
                return false;
            } else {
                $("#divTblFabricantes").show();
                $("#msgSemFabricante").hide();
            }

            data = JSON.parse(data);           

            var fabricantes = [];

            $.each(data, function (i, d) {
                var descricao = d.descricao != '' ? d.descricao : '-';

                fabricantes.push([
                    d.nome || '-',
                    d.email || '-',
                    descricao || '-',
                    " <button data-toggle='modal' data-target='#modalEditaFabri' class='btn btn-sm btn-edit text-info'\n" +
                    "      title='Editar Fabricante' data-id='" + d.id + "'>\n" +
                    "                                <span class='fa fa-edit'/>\n" +
                    "          </button>  " +
                    "<button class='btn btn-sm btn-trash' title='Excluir Fabricante' style='float:right' data-toggle='modal' data-target='#exclusao' " +
                    "data-id='" + d.id + "' data-item='o fabricante'  data-nome='" + d.nome + "'>\n" +
                    "                 <span class='fa fa-trash'/> </button>",
                ])
            });

            var table = $("#tblFabricantes");         

            if ( $.fn.DataTable.isDataTable( '#tblFabricantes' )) {
                table.dataTable().fnClearTable();
                table.dataTable().fnDestroy();
            }

            $(".loadTable").hide();

            table.DataTable({
                data: fabricantes,
                "autoWidth": false,
                "responsive": true,
                "columns": [
                    {
                        className: "vertical-align",
                        width: '1000px',
                    },
                    {
                        className: "vertical-align",
                        width: '1000px',
                    },
                    {
                        className: "vertical-align",
                        width: '1000px',
                    },
                    {
                        "orderable": false,
                        width: '150px',
                    },
                ],
                "language": {
                  "paginate": {
                    "previous": 'Anterior',
                    "next": 'Próximo',
                  },
                  "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                  "zeroRecords": "Nenhum registro encontrado.",
                  "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                  "sSearch": "Pesquisa Geral",
                  "oAria": {
                    "sSortAscending": ": Ordenar colunas de forma ascendente",
                    "sSortDescending": ": Ordenar colunas de forma descendente"
                  }
                },
                "dom": "<'row'<'offset-4 col-sm-4 pull-left'f><'col-sm-4 pull-right cadastrarFabricante'>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-1'i><'offset-4 col-sm-7 text-right'p>>",
                fnInitComplete: function () {
                    $('div.cadastrarFabricante').html('<button style="margin-top: 0; float: right; margin-bottom: -45px;" class="btn text-info btnCadastrarFabricante" id="btnCadastrarFabricante" data-toggle="modal" data-target="#modalCadastroFabri" title="Adicionar Fabricante">\n' +
                        '                                        <i class="fas fa-plus"></i>\n' +
                        '                                    </button>');
                }
            });

            console.log('chega aqui');
            console.log($('.btnCadastrarFabricante'));
        }
    });
}

function saveFabri(action) {
    var data = $("#form" + action).serializeArray();

    var erro = 0;

    var msg = action == 'CadastroFabri' ? 'Cadastrado' : 'Editado';

    $.each(data, function (i, campo) {
      if(campo.name == 'email') {
        if (!validaEmail(campo.value) && campo.value != ''){
          erro++;
        }
      }

      if (campo.name == 'nome' && campo.value != '') {
          campo.value = campo.value.toUpperCase();
      }

    });

    if (erro > 0) {
      Swal.fire({
        icon: 'error',
        title: 'E-mail invalído!',
        text: "Digite um e-mail válido para salvar!"
      });

      return false;
    }


    $.ajax({
        type: 'POST',
        url: '../ajax/fabricantes.php',
        data: data,
        beforeSend: function () {
            $("#form" + action).hide();
            $(".loadModal").show();
        },
        success: function (data) {
            if (!data) {
                $("#msgStoreFabri").html('Não foi possivel cadastrar').show();
                return false;
            }

            $(".alert-success-Fabri i").html("");
            $(".alert-success-Fabri i").append("   " + msg + " com Sucesso");
            $(".alert-success-Fabri").fadeIn();
            $(".loadModal").hide();
            $("#modal" + action).modal('hide');

            if (action == 'CadastroFabri') {
                $('#form' +action+' input').each(function() {
                    if ($(this).attr('name') != 'act' ) {
                        $(this).val('');
                    }
                });
            }

           getFabris();

            window.setTimeout(function() {
                $(".alert-success-Fabri").fadeOut();
            }, 2000);


            $("#form" + action).show();
        },
    });
}



function excluirFabricante (idFabri)
{
    var data = {};

    data = {
        act: 'excluir',
        idFabri: idFabri,
    };


    $.ajax({
        type: 'POST',
        url: '../ajax/fabricantes.php',
        data: data,
        beforeSend: function () {
            $(".loadModal").show();
        },
        success: function (data) {
            if (!data) {
                alert('Nao foi possivel excluir');
                return false;
            }

            getFabris();

            $("#exclusao").modal('hide');

            $(".alert-success-Fabri i").html("");
            $(".alert-success-Fabri i").append("Excluído com Sucesso");
            $(".alert-success-Fabri").show();
            $(".loadModal").hide();

            window.setTimeout(function() {
                $(".alert-success-Fabri i").html("");
                $(".alert-success-Fabri").hide();

            }, 2000);
        },
    });
}


$("#modalEditaFabri").on('show.bs.modal', function (e) {
    let id = $(e.relatedTarget).attr('data-id');

    let form = $(this).find('form');
    form.find('#id').val(id);

    $.ajax({
        type: 'GET',
        url: '../ajax/fabricantes.php?act=getFabri&id=' + id,
        beforeSend: function () {
            $(".loadModal").show();
        },
        success: function (data) {

            data = JSON.parse(data);

            $.each(data, function (campo, value) {
                form.find('input').each(function(){

                    if ($(this).attr('name') == campo) {
                        $(this).val(value);
                    }


                });
            });

            $(".loadModal").hide();
        },
    });

});

function validaEmail(field) {
  usuario = field.substring(0, field.indexOf("@"));
  dominio = field.substring(field.indexOf("@")+ 1, field.length);

  if ((usuario.length >=1) &&
    (dominio.length >=3) &&
    (usuario.search("@")==-1) &&
    (dominio.search("@")==-1) &&
    (usuario.search(" ")==-1) &&
    (dominio.search(" ")==-1) &&
    (dominio.search(".")!=-1) &&
    (dominio.indexOf(".") >=1)&&
    (dominio.lastIndexOf(".") < dominio.length - 1)) {
    //document.getElementById("msgemail").innerHTML="E-mail válido";
    return true;
  }
  else{
    //  document.getElementById("msgemail").innerHTML="<font color='red'>E-mail inválido </font>";
    return false;
  }
}



</script>
