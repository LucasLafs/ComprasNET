<!-- Modal -->
<div class="modal fade" id="modalCadastroFabri" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addFabriLabel">Cadastrar Fabricante</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-danger" style="display: none;" id="msgStoreFabri">
                    <strong></strong>
                </p>
                <form action="" class="form-group text-center" id="formCadastroFabri">
                    <div class="row">
                        <div class="offset-2 col-md-8">
                            <label for="">Nome</label>
                            <input type="text" class="form-control input uppercase" name="nome" required>
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
                            <input type="text" class="form-control input" name="descricao" maxlength="80">
                            <br>
                        </div>
                    </div>
                    <input type="hidden" name="act" value="cadastro">
                </form>

                <div class="tab1-loading overlay loadModal" style="display: none"></div>
                <div class="tab1-loading loading-img loadModal" style="display: none"></div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary"
                        onclick="saveFabri('CadastroFabri')">Cadastrar</button>
            </div>
        </div>
    </div>
</div>
