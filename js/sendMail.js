$(function () {

    /*  function sendMail (id_item) {
          var idItem = $(this).val();
          console.log(idItem);
            $.get('../ajax/email.php?act=get_infos&id=' + idItem)
                .done(function (data) {
                    console.log(data);
                })
                .fail(function () {
                    console.log('fail');
                });
        }*/

    $(document).on('click', '.sendMail', function () {
        var idItem = $(this).val();
        var idFabricante = $(this).attr('data-fabricante');
        var produto_id = $(this).attr('data-pf_id');

        $.get('../ajax/email.php?act=get_infos&id=' + idItem + '&idFabricante=' + idFabricante + '&pf_id=' + produto_id)
            .done(function (data) {
                console.log(data);
                data = JSON.parse(data);
                if (data != 0) {
                    $.each(data, function (i, d) {
                        Swal.fire({
                            //title: 'Are you sure?',
                            text: "Enviar e-mail para o fabricante " + d.nome + " sobre esse item?",
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            showLoaderOnConfirm: true,
                            confirmButtonText: 'Enviar',
                          preConfirm: () => {
                            return fetch('../ajax/email.php?act=sendEmail&id=' + d.id)
                              .then(response => {
                                return response
                              })
                              .catch(error => {
                                Swal.showValidationMessage(
                                  `Request failed: ${error}`
                                )
                              })
                          },
                          allowOutsideClick: () => !Swal.isLoading()
                        }).then((result) => {

                            if (!result.dismiss) {
                              $("#flag" + produto_id).show();
                              Swal.fire({
                                title: 'E-mail enviado com sucesso.',
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: '<i class="fa fa-thumbs-up"></i>'
                              });

                            } else if (!result){
                              Swal.fire({
                                title: 'E-mail não enviado.',
                                text: 'Tente novamente ou entre em contato com os desenvolvedores.',
                                icon: 'error',
                                showCancelButton: false,
                                confirmButtonColor: '#d33',
                                confirmButtonText: '<i class="fa fa-thumbs-down"></i>'
                              });
                            }
                        });
                    });
                } else {
                    Swal.fire({
                        title: 'Esse item não possui fabricante.',
                        text: 'Não é possivel enviar o e-mail.',
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: '<i class="fa fa-thumbs-up"></i>'
                    });
                }
            })
            .fail(function () {
                console.log('fail');
            });
    });

  $(document).on('click', '.enviarVariosEmails', function () {
    $("#loadingAllEmails").show();

    let id = $(this).val();

    let produtos = [];
    let i = 0;
    let x = 0;
    var produto_id = '';

    let idFabri = '';
    let fabrisItens = [];

    $.each($(".checkOneItem" + id), function () {
      if ($(this).prop('checked') == true) {

        idFabri = $(this).attr("data-idFabri");
        produto_id = $(this).attr('data-pf_id');

        produtos.push(produto_id);


        if (fabrisItens[idFabri] != undefined) {
            fabrisItens[idFabri].push(produto_id);
        } else {
            fabrisItens[idFabri] = [produto_id];
            x++;
        }

        i++;
      }

    });

    console.log(fabrisItens);

    $.each(fabrisItens, function (idFabricante, ids_pf) {
        if (ids_pf != undefined) {

            console.log(idFabricante);
            console.log(ids_pf);

            $.when(
                $.ajax('../ajax/email.php?act=sendMailMultiFabri&idFabri=' + idFabricante + '&ids_pf=' + ids_pf),
            ).then(function () {
                x--;
                $.each(ids_pf, function (i, pf_id) {
                    $("#flag" + pf_id).show();
                });
                if (x == 0) {
                    Swal.fire({
                        title: 'Os e-mails foram enviados com sucesso.',
                        icon: 'success',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: '<i class="fa fa-thumbs-up"></i>'
                    }).then(() => {

                        $("#loadingAllEmails").hide();
                    });
                }
            });
        }
    });

   /* $.each(produtos, function (index, pf_id) {
      $.when(
        $.ajax('../ajax/email.php?act=sendEmail&id=' + pf_id),
      ).then(function(){
        i--;
        $("#flag" + pf_id).show();
        if (i == 0) {
          Swal.fire({
            title: 'Os e-mails foram enviados com sucesso.',
            icon: 'success',
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            confirmButtonText: '<i class="fa fa-thumbs-up"></i>'
          }).then(()=> {

            $("#loadingAllEmails").hide();
          });
        }
      });
    });
*/


    console.log(produtos);


  });

});
