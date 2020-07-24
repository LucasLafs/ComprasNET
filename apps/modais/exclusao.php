<!--.modal exclusao-->
<div class="modal fade" id="exclusao" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirma Exclusão</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post">

                <div class="modal-body">
                    <p class="text-center">Deseja Realmente Excluir?</p>
                </div>
                <div class="tab1-loading overlay loadModal" style="display: none"></div>
                <div class="tab1-loading loading-img loadModal" style="display: none"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="button"  class="btn btn-danger" id="idFabri" onclick="excluirFabricante($(this).val())">Apagar</button>
                </div>
            </form>
        </div>
    </div>
</div>