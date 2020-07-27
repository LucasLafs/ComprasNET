<?php 


require_once ("../ajax/conexao.php");

$con = bancoMysqli();

$display = 'none';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM usuarios WHERE email = '$email' LIMIT 1";

    $query = mysqli_query($con, $sql);
    $rows = mysqli_num_rows($query);

    if ($rows > 0) {
        $user = mysqli_fetch_array($query);

        if ($user['senha'] == md5($senha)) {
            session_start();

            $arrUser = [];
            $arrUser['id'] = $user['id'];
            $arrUser['nome'] = $user['nome'];
            $arrUser['email'] = $user['email'];
            $arrUser['gestor'] = $user['gestor'];

            if ( $user['bloqueado'] == 'Y' ){
                $display = 'block';
                $msg = "Usuário bloqueado!";
            } else {
                $_SESSION['user'] = $arrUser;
                header("Location: ./dashboard.php");
            }

        } else {
            $display = 'block';
            $msg = "A senha está incorreta!";
        }
    } else {
        $display = 'block';
        $msg = "Usuário não cadastrado.";
    }    
}


?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>COMPRASNET | Log in</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Font Awesome -->
        <link rel="stylesheet" href="../layout/plugins/fontawesome-free/css/all.min.css">
         <!-- Ionicons -->
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <!-- icheck bootstrap -->
        <link rel="stylesheet" href="../layout/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
        <link rel="stylesheet" href="../layout/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="../layout/dist/css/adminlte.min.css">       
         <!-- Google Font: Source Sans Pro -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700"> 
        <!--<script src="./request_comprasnet.js"></script>-->
    </head>
   <!-- <body>
        <h1>Text X</h1>
        <button onClick="searchComprasNet()">Click Here</button>
    </body> -->

    <body class="hold-transition login-page">
        <div class="login-box">
            <div class="login-logo">
                <a href=""><b>COMPRAS</b>NET</a>
            </div>
            <!-- /.login-logo -->
            <div class="card">
                <div class="card-body login-card-body">
                <p class="login-box-msg">Entre com seus dados para acessar o sistema</p> 
                
                <p class="text-danger" style="margin-left: 26%; display: <?=$display?>;">
                    <strong id="msgLogin"><?=isset($msg) ? $msg : ''?></strong>
                </p>

                <form method="post" id="formLogin">
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="Email" name="email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                            <!--<span class="fas fa-envelope"></span>-->
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Senha" name="senha">
                    <div class="input-group-append">
                        <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                        </div>
                    </div>
                    </div>
                    <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                        <input type="checkbox" id="remember">
                        <label for="remember">
                            Remember Me
                        </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <button type="submit" id="login" class="btn btn-primary btn-block" name="login">Entrar</button>
                    </div>
                    <!-- /.col -->
                    </div>
                </form>

                <!-- <div class="social-auth-links text-center mb-3">
                    <p>- OR -</p>
                    <a href="#" class="btn btn-block btn-primary">
                    <i class="fab fa-facebook mr-2"></i> Sign in using Facebook
                    </a>
                    <a href="#" class="btn btn-block btn-danger">
                    <i class="fab fa-google-plus mr-2"></i> Sign in using Google+
                    </a>
                </div> -->
                <!-- /.social-auth-links -->

                <p class="mb-1">
                    <!-- <a href="forgot-password.html">I forgot my password</a> -->
                </p>
                <p class="mb-0">
                    <!-- <a href="register.html" class="text-center">Register a new membership</a> -->
                </p>
                </div>
                <!-- /.login-card-body -->
            </div>
        </div>
        <!-- /.login-box -->

        <!-- jQuery -->
        <script src="layout/plugins/jquery/jquery.min.js"></script>
        <!-- Bootstrap 4 -->
        <script src="layout/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- AdminLTE App -->
        <script src="layout/dist/js/adminlte.min.js"></script>
    </body>
</html>
