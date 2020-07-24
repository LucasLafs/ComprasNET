<!-- Modal -->
<div class="modal fade" id="modalEditaFabri" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addFuncLabel">Editar Fabricante <i></i></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-danger" style="display: none;" id=msgEditaFabri"">
                    <strong></strong>
                </p>
                <form class="form-group text-center" id="formEditaFabri">
                    <div class="row">
                        <div class="offset-2 col-md-8">
                            <label for="">Nome</label>
                            <input type="text" class="form-control input" name="nome" required>
                            <br>
                        </div>
                    </div>
                    <div class="row">
                        <div class="offset-2 col-md-8">
                            <label>E-mail</label>
                            <input type="email" class="form-control input" name="email">
                            <br>
                        </div>
                    </div>
                    <div class="row">
                        <div class="offset-2 col-md-8">
                            <label for="">Descrição</label>
                            <input type="text" class="form-control input" name="descricao">
                            <br>
                        </div>
                    </div>
                    <input type="hidden" name="id">
                    <input type="hidden" name="act" value="editar">
                </form>

                <div class="tab1-loading overlay loadModal" style="display: none"></div>
                <div class="tab1-loading loading-img loadModal" style="display: none"></div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="saveFabri('EditaFabri')">Salvar</button>
            </div>
        </div>
    </div>
</div>
