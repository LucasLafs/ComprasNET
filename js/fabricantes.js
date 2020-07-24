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
                    " <button  data-toggle='modal' data-target='#modalEditaFabri' class='btn btn-sm btn-edit text-info pull-left'\n" +
                    "      title='Editar Fabricante' data-id='" + d.id + "'>\n" +
                    "                                <span class='fa fa-edit'/>\n" +
                    "          </button>  " +
                    "<button class='btn btn-sm btn-trash' title='Excluir Fabricante' data-toggle='modal' data-target='#exclusao' " +
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
                retrieve: true,
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
                "dom": "<'row'<'offset-4 col-sm-4 pull-left'f><'col-sm-4 pull-right cadastrarFabricante'>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-1'i><'offset-4 col-sm-7 text-right'p>>",
                /*fnInitComplete: function () {
                    $('div.cadastrarFabricante').html($('.btnCadastrarFabricante').removeClass('none'));
                }*/
            });
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

            $(".alert-success i").html("");
            $(".alert-success i").append("   " + msg + " com Sucesso");
            $(".alert-success").fadeIn();
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
                $(".alert-success").fadeOut();
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

            $(".alert-success i").html("");
            $(".alert-success i").append("Excluído com Sucesso");
            $(".alert-success").show();
            $(".loadModal").hide();

            window.setTimeout(function() {
                $(".alert-success i").html("");
                $(".alert-success").hide();

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

