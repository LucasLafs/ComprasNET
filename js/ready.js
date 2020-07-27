$(document).ready(function() {

    /* Imports  */

    var DataTables = document.createElement('script');
    DataTables.src = '../js/DataTables.js';
    document.head.appendChild(DataTables);

    var sendMail = document.createElement('script');
    sendMail.src = '../js/sendMail.js';
    document.head.appendChild(sendMail);

    getTimeout();

    $(function () {

        $('#exclusao').on('show.bs.modal', function (e) {
            let id = $(e.relatedTarget).attr('data-id');
            let item = $(e.relatedTarget).attr('data-item');
            let nome = $(e.relatedTarget).attr('data-nome');

            $(this).find('p').text(`Tem certeza que deseja excluir ${item} ${nome} ?`);
            $(this).find('#idFabri').attr('value', `${id}`);
        });

        $("#btnAlterarSenha").click(function () {
          if ($("#divAlterarSenha").is(":visible")) {
            $("#divAlterarSenha").slideUp();
          } else {
            $("#divAlterarSenha").slideDown();
          }
        });

    });

});

function getTimeout(){
    $.ajax({
        type: 'POST',
        url: '../api/request_licitacoes.php',
        data: 'act=getTimeout',
        cache: false,
        success: function(data){
            data = JSON.parse(data);
            if(Array.isArray(data)){
              $('#time').val(data[0]);
            }
        }
    })
}

function saveTimeout() {

    let time = $('#time').val();
    $.ajax({
        type: 'POST',
        url: '../api/request_licitacoes.php',
        data: 'act=saveTimeout&time=' + time,
        cache: false,
        success: function(data){
          if(data == '1'){
            $(".alert-success i").html("");
            $(".alert-success i").append(' Editado com sucesso!');
            $(".alert-success").fadeIn();
              
            window.setTimeout(function() {
              $(".alert-success").fadeOut();
            }, 4000);
          }
        }
    })

}

function saveUser(idUser)
{

  let form = $("#formChangeUser").serializeArray();

  let data = {};

  let emptys = [];
  $.each(form, function (i, d) {

    if (d.name == 'nome') {
      if (d.value == '') {
        $("#msgSaveUser").html("O Nome é obrigatório").show();
        emptys.push('Nome');
      }
      $("#spanNameUser").html(d.value);
    }

    if (d.name == 'email' && d.value == '') {
      $("#msgSaveUser").html("O E-mail é obrigatório").show();
      emptys.push('E-mail');
    }

    data[d.name] = d.value;

  });


  if (emptys.length > 0) {
    $("#formChangeUser").show();
    $(".loadModal").hide();
    $.each(emptys, function (i, d) {
      $("#msgSaveUser").html("O "+d+" é obrigatório").show();

    });

    return false;
  }


  $.ajax({
    type: 'POST',
    url: '../ajax/user.php',
    data: data,
    beforeSend: function () {
      $("#formChangeUser").hide();
      $(".loadModal").show();
    },
    success: function (data) {
      data = JSON.parse(data);

      if (data.status != 'ok') {
        $("#formChangeUser").show();
        $(".loadModal").hide();
        $("#msgSaveUser").html(data.response).show();
        return false;
      }

      $("#modalConfsUser").modal('hide');

      $("#msgSaveUser").html("");
      $(".alert-success i").html("");
      $(".alert-success i").append(data.response);
      $(".alert-success").fadeIn();
      $("#formChangeUser").show();
      $("#divAlterarSenha").hide();
      $(".loadModal").hide();


      window.setTimeout(function() {
        $(".alert-success").fadeOut();
      }, 4000);
    },
  });



}
