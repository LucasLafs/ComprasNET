<?php 
    require_once("../header/cabecalho.php");

    if ($gestorUser == 'N'){
        echo "<script>window.location.href='./dashboard.php';</script>";
    }

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

<div class='content-wrapper'>
    <div class='content-header'>
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Usúarios</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class='col-md-12'>
                    <!-- Default box -->
                    <div class="card">
                      <div class="card-header" style="margin-bottom: -30px; border-bottom: none !important;">
                        <div class="alert alert-success alert-success-Usuario" role="alert" style="margin-bottom: 15px;">
                          <i class="fa fa-check-circle text-green"></i>
                        </div>

                      </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12" >
                                    <button style="margin-top: 0; float: right; margin-bottom: -45px;" class="btn text-info btnCadastrarUsuario" id="btnCadastrarUsuario" data-toggle="modal" data-target="#modalCadastroUsuario" title="Adicionar Usuario">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                    <h4 id='msgSemUsuario' style='display: none;' class='text-info text-center'>Nenhum usuário cadastrado.</h4>
                                  <div class="table-responsive" id="divTblUsuarios" style="display: none; ">
                                    <table id="tblUsuarios" class="table table-hover table-responsive" style="width: 100%; ">
                                      <thead>
                                      <tr>
                                        <th scope="col">Nome</th>
                                        <th scope="col">E-mail</th>
                                        <!-- <th scope="col">Descrição</th> -->
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
            include ('modais/users/createUser.php');
            // include ('modais/editarUsuario.php');
            include ('modais/users/removeUser.php');
        ?>
    </section>
</div>


<script>

    $(document).ready(function() {

        /* Imports  */

        getUsers();

        $(window).on('load', function () {
            $('.tab1-loading').hide();
        });

        $(function () {
            $(".sidebar-light-orange").find('.nav-pills').find('a[href="./users.php"]').addClass('active');
        });

    });

    
    function getUsers() {

        $.ajax({
            type: 'GET',
            url: '../ajax/users.php',
            data: 'act=getUsers',
            cache: false,
            beforeSend: function () {
                $(".loadTable").show();
            },
            success: function(data) {

                if (data == 0) {
                    $('.btnCadastrarUsuario').removeClass('none');
                    $("#msgSemUsuario").show();
                    $("#divTblUsuarios").hide();
                    $(".loadTable").hide();
                
                    return false;
                } else {
                    $("#divTblUsuarios").show();
                    $("#msgSemUsuario").hide();
                }

                data = JSON.parse(data);           

                var usuarios = [];

                $.each(data, function (i, d) {
                    // var descricao = d.descricao != '' ? d.descricao : '-';

                    usuarios.push([
                        d.nome || '-',
                        d.email || '-',
                        // descricao || '-',
                        " <button data-toggle='modal' style='margin-left: 5px;' data-target='#modalCadastroUsuario' class='btn btn-sm btn-edit text-info'\n" +
                        "      title='Editar Usuario' data-id='" + d.id + "'>\n" +
                        "                                <span class='fa fa-edit'/>\n" +
                        "          </button>  " +
                        "<button class='btn btn-sm btn-trash' title='Excluir Usuario' style='float:right' data-toggle='modal' data-target='#modalDeletaUsuario' " +
                        "data-id='" + d.id + "' data-item='o usuario'  data-nome='" + d.nome + "'>\n" +
                        "                 <span class='fa fa-trash'/> </button>",
                    ])
                });

                var table = $("#tblUsuarios");         

                if ( $.fn.DataTable.isDataTable( '#tblUsuarios' )) {
                    table.dataTable().fnClearTable();
                    table.dataTable().fnDestroy();
                }

                $(".loadTable").hide();

                table.DataTable({
                    data: usuarios,
                    "autoWidth": false,
                    "responsive": true,
                    "columns": [
                        {
                            className: "vertical-align",
                            width: '45%',
                        },
                        {
                            className: "vertical-align",
                            width: '45%',
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
                    "dom": "<'row'<'offset-4 col-sm-4 pull-left'f><'col-sm-4 pull-right cadastrarUsuario'>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row'<'col-sm-1'i><'offset-4 col-sm-7 text-right'p>>",
                    fnInitComplete: function () {
                        $('div.cadastrarUsuario').html('<button style="margin-top: 0; float: right; margin-bottom: -45px;" class="btn text-info btnCadastrarUsuario" id="btnCadastrarUsuario" data-toggle="modal" data-target="#modalCadastroUsuario" title="Adicionar Usuario">\n' +
                            '                                        <i class="fas fa-plus"></i>\n' +
                            '                                    </button>');
                    }
                });

                console.log('chega aqui');
                console.log($('.btnCadastrarUsuario'));
            }
        });
    }

    function cleanModal(action) {
        $('#divCadastrarSenhaUsuario').hide();
        $('#divAlterarSenhaUsuario').hide();
        $('#divBtnAlterarSenha').hide();

        $('#form' +action+' input').each(function() {
            if ($(this).attr('type') == 'checkbox') {
                $(this).prop('checked', false);
            } else {
                if ($(this).attr('name') != 'act' ) {
                    $(this).val('');
                }
            }
        });

    }

    function saveUser(action) {
        var data = $("#form" + action).serializeArray();

        var erro = 0;

        var msg = action == 'CadastroUsuario' ? 'Cadastrado' : 'Editado';
        let erroName;
        $.each(data, function (i, campo) {
            if(campo.name == 'email') {
                if (!validaEmail(campo.value)){
                    erro++;
                }
            }

            if (campo.name == 'name' && campo.value == ""){
                erro++;
                erroName = 'name';
            }   

        });

        if (erro > 0) {

            if (erroName == 'name') {
                Swal.fire({
                    icon: 'error',
                    title: 'Nome invalído!',
                    text: "Digite um nome válido para salvar!"
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'E-mail invalído!',
                    text: "Digite um e-mail válido para salvar!"
                });
            }

            return false;
        }

        $.ajax({
            type: 'POST',
            url: '../ajax/users.php',
            data: data,
            async: true,
            beforeSend: function () {
                $("#form" + action).hide();
                $(".loadModal").show();
            },
            success: function (data) {
                data = JSON.parse(data);
                if ( data.status == 'error' ) {
                
                    Swal.fire({
                        icon: 'error',
                        title: data.response,
                        text: data.response == ' Email já cadastrado' ? "Digite um e-mail válido para salvar!" : ''
                    });

                    $("#form" + action).show();
                    $(".loadModal").hide();

                    return false;
                 
                } else {
                    $(".alert-success-Usuario i").html("");
                    $(".alert-success-Usuario i").append("   " + msg + " com Sucesso");
                    $(".alert-success-Usuario").fadeIn();
                    $(".loadModal").hide();
                    $("#modal" + action).modal('hide');

                    cleanModal(action);
                    getUsers();

                    window.setTimeout(function() {
                        $(".alert-success-Usuario").fadeOut();
                    }, 2000);

                    $("#form" + action).show();
                }
            },
        });
    }

    function delUser (idUser) {
        var data = {};

        data = {
            act: 'delUser',
            idUser: idUser,
        };

        $.ajax({
            type: 'POST',
            url: '../ajax/users.php',
            data: data,
            beforeSend: function () {
                $(".loadModal").show();
            },
            success: function (data) {
                if (!data) {
                    alert('Nao foi possivel excluir');
                    return false;
                }

                getUsers();

                $("#modalDeletaUsuario").modal('hide');

                $(".alert-success-Usuario i").html("");
                $(".alert-success-Usuario i").append("Excluído com Sucesso");
                $(".alert-success-Usuario").show();
                $(".loadModal").hide();

                window.setTimeout(function() {
                    $(".alert-success-Usuario i").html("");
                    $(".alert-success-Usuario").hide();

                }, 2000);
            },
        });
    }

    $('#modalDeletaUsuario').on('show.bs.modal', function (e) {
        let id = $(e.relatedTarget).attr('data-id');
        let item = $(e.relatedTarget).attr('data-item');
        let nome = $(e.relatedTarget).attr('data-nome');

        $(this).find('p').text(`Tem certeza que deseja excluir ${item} ${nome} ?`);
        $(this).find('#idUser').attr('value', `${id}`);
    });

    $("#modalCadastroUsuario").on('show.bs.modal', function (e) {
        
        let id = $(e.relatedTarget).attr('data-id');

        let form = $(this).find('form');
        form.find('#id').val(id);

        if (id){
            $('#divBtnAlterarSenha').show();
            $('#btnSalvarUser').html("Salvar");
            $('#addUsuarioLabel').html("Editar Usuário");

            $.ajax({
                type: 'GET',
                url: '../ajax/users.php?act=getUsers&id=' + id,
                beforeSend: function () {
                    $(".loadModal").show();
                },
                success: function (data) {
                    
                    let res = JSON.parse(data);
                    if (res){
                        form.find('[name=id]').val(res[0].id || '');
                        form.find('[name=name]').val(res[0].nome || '');
                        form.find('[name=email]').val(res[0].email || '');
                        // form.find('[name=description]').val(res[0].descricao || '');
                        if (res[0].gestor == 'Y') {
                            form.find('[name=check-admin]').prop('checked', true);
                        }

                        if (res[0].bloqueado == 'Y') {
                            form.find('[name=check-block]').prop('checked', true);
                        }
                    }

                    $(".loadModal").hide();
                },
            });
        } else {
            $('#divCadastrarSenhaUsuario').show();
            $('#btnSalvarUser').html("Cadastrar");
            $('#addUsuarioLabel').html("Cadastrar Usuário");
        }

    });

    
    $("#btnAlterarSenhaUsuario").click(function () {
        if ($("#divAlterarSenhaUsuario").is(":visible")) {
            $("#divAlterarSenhaUsuario").slideUp();
            $("#divCadastrarSenhaUsuario").slideUp();
        } else {
            $("#divAlterarSenhaUsuario").slideDown();
            $("#divCadastrarSenhaUsuario").slideDown();
        }
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