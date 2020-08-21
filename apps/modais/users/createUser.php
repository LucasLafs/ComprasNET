<!-- Modal -->
<div class="modal fade" id="modalCadastroUsuario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUsuarioLabel">Cadastrar Usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onClick="cleanModal('CadastroUsuario')">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-danger" style="display: none;" id="msgStoreUsuario">
                    <strong></strong>
                </p>
                <form action="" class="form-group text-center" id="formCadastroUsuario">
                    <div class="row">
                        <div class="offset-2 col-md-8">
                            <label for="">Nome</label>
                            <input type="text" class="form-control input" name="name" required>
                            <br>
                        </div>
                    </div>
                    <div class="row">
                        <div class="offset-2 col-md-8">
                            <label>E-mail</label>
                            <input type="email" class="form-control input" name="email" required>
                            <br>
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="offset-2 col-md-8">
                            <label for="">Descrição</label>
                            <input type="text" class="form-control input" name="description" maxlength="80">
                            <br>
                        </div>
                    </div> -->

                    <div class="row" id='divBtnAlterarSenha' style='display: none;'>
                        <div class="offset-2 col-md-8">
                        <button class="btn btn-outline-info" type="button" id="btnAlterarSenhaUsuario">Alterar Senha</button>
                        </div>
                    </div>
                    <div class="row" style='display: none' id='divAlterarSenhaUsuario'>
                        <div class="offset-2 col-md-8"><br>
                            <label for="">Senha Atual</label>
                            <input type="password" class="form-control input" name="current-pass" value='******' maxlength="80">
                            <br>
                        </div>
                    </div>
                    <div class="row" style='display: none' id='divCadastrarSenhaUsuario'>
                        <div class="offset-2 col-md-8">
                            <label for="">Senha</label>
                            <input type="password" class="form-control input" name="pass" maxlength="80" required>
                            <br>
                        </div>
                        <div class="offset-2 col-md-8">
                            <label for="">Confirmação de Senha</label>
                            <input type="password" class="form-control input" name="confirm-pass" maxlength="80" required>
                        </div>
                    </div>

                    <br><br>
                    <div class="row">
                        <div class="form-check offset-2 col-md-4">
                            <input type="checkbox"  name="check-admin" id='check-admin'>&nbsp;
                            <label for="check-admin">Administrador</label>
                        </div>
                        <div class="form-check col-md-4">
                            <input type="checkbox"  name="check-block" id='check-block'>&nbsp;
                            <label for="check-block">Bloqueado</label>
                        </div>
                    </div>
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="act" value="saveUser">
                </form>

                <div class="tab1-loading overlay loadModal" style="display: none"></div>
                <div class="tab1-loading loading-img loadModal" style="display: none"></div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="cleanModal('CadastroUsuario')">Fechar</button>
                <button type="button" class="btn btn-primary" id='btnSalvarUser'
                        onclick="saveUser('CadastroUsuario')">Cadastrar</button>
            </div>
        </div>
    </div>
</div>
