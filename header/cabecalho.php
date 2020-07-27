<?php
  ini_set('session.gc_maxlifetime', 60 * 60); // 60 minutos
  session_start();
  date_default_timezone_set("America/Sao_Paulo");
  if (!isset ($_SESSION['user']) == true) //verifica se há uma sessão, se não, volta para área de login
  {
    unset($_SESSION['user']);
    header('location:../index.php');
    exit;
  } else {


    $logado = $_SESSION['user'];

    $idUser = $logado['id'];
    $nameUser = $logado['nome'];
    $emailUser = $logado['email'];
    $gestorUser = $logado['gestor'];
    $blockUser = $logado['bloqueado'];

    if($blockUser == 'Y'){
      unset($_SESSION['user']);
      header('location: ../index.php');
      exit;
    }
  }

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>COMPRASNET | Dashboard</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../layout/css/geral.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../layout/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- DataTable -->
  <link rel="stylesheet" href="../layout/plugins/datatables-bs4/css/dataTables.bootstrap4.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="../layout/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="../layout/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="../layout/plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../layout/dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../layout/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="../layout/plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="../layout/plugins/summernote/summernote-bs4.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition sidebar-mini layout-fixed sidebar-collapse">
<div class="wrapper">

  <div class="tab1-loading overlay"></div>
  <div class="tab1-loading loading-img"></div>
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>
      <!-- <li class="nav-item d-none d-sm-inline-block">
        <a href="index3.html" class="nav-link">Home</a>
      </li> -->
    </ul>

    <div class="alert alert-success" role="alert" style="margin-bottom: 15px;">
      <i class="fa fa-check-circle text-green"></i>
    </div>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fas fa-stopwatch"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">Recorrência API</span>
          <div class="dropdown-divider"></div>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <div class='row'>
              <form id="form-timout" name="form-timout">
                <div class='col-md-12'>
                  <label>Minutos:&nbsp;&nbsp;</label>
                  <input id='time' type="number" class='input text-center'/>
                </div>
              </form>
            </div>
          </a>
          <div class="dropdown-divider"></div>
          <div>
            <button class="dropdown-item dropdown-footer" onClick='saveTimeout();'>Salvar</button>
          </div>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fas fa-user-cog"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <!--<span class="dropdown-item dropdown-header">Timeout</span>-->
            <div class='row'>

              <div class='col-md-12 text-center'>

                <div class="card-body">
                  <span class="fa fa-user"></span><br>
                  <span id="spanNameUser"><?= $nameUser ?></span>

                </div>

              </div>

          <div class="dropdown-divider"></div>
              <div class="card-footer" style="width: 100%;">
                <div style="float:left;">
                  <button class="btn btn-tool" data-toggle="modal" data-target="#modalConfsUser"><span
                      class="fa fa-edit"></span></button>
                </div>
                <div>
                  <a style="font-size: 17px; float: right;  margin-top: -2px;" href="../apps/logout.php" class="btn btn-tool" title="Sair">
                    <i style="float: right" class="fas fa-sign-out-alt"></i>
                  </a>
                </div>
              </div>

        </div>
        <!--          -->
        <div>
          <!--<button class="dropdown-item dropdown-footer" onClick='saveTimeout();'>Salvar</button>-->
        </div>
</div>
</li>
</ul>
</nav>
<!-- /.navbar -->

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-light-orange elevation-4" style='min-height: 940px; background: #F4F6F9'>
  <!-- Brand Logo -->
  <a href="./dashboard.php" class="brand-link" style="height: 57px;">
    <!-- 1c5581 <img src="../layout/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" -->
    <img src="../layout/images/logo-futura.png" alt="AdminLTE Logo" class="brand-image"
         style='width: 175px; height: 50px; margin-left: 2px'>
  </a>

  <!-- Sidebar -->
  <div class="sidebar sidebar-light-orange" style="background: #F4F6F9">
    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
             with font-awesome or any other icon font library -->
        <li class="nav-item">
          <a href="./dashboard.php" class="nav-link">
            <i class="fas fa-home nav-icon"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="./cotacoes.php" class="nav-link">
            <i class="fas fa-file-invoice" style="font-size: 17px; margin-right: 8px; margin-left: 6px;"></i>
            <p>Cotações</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="./fabricantes.php" class="nav-link">
            <i class="fas fa-archive nav-icon" style="font-size: 15px; margin-left: -1px; margin-right: 2px;"></i>
            <p>Fabricantes</p>
          </a>
        </li>
        <?php 
          if ($gestorUser == 'Y') {
            echo '<li class="nav-item">
              <a href="./conf-email.php" class="nav-link">
                <i class="fas fa-cog" style="margin-right: 7px; margin-left: 3px;"></i>
                <p>Configurações E-mail</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="./users.php" class="nav-link">
                <i class="fas fa-users" style="margin-right: 7px; margin-left: 3px;"></i>
                <p>Usúarios</p>
              </a>
            </li>';
          }
        ?>
       

      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>


<!-- Modal -->
<div class="modal fade" id="modalConfsUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addFabriLabel">Minha Conta</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p class="text-danger text-center" style="display: none;" id="msgSaveUser">
          <strong></strong>
        </p>
        <div class="tab1-loading overlay loadModal" style="display: none"></div>
        <div class="tab1-loading loading-img loadModal" style="display: none"></div>
        <form action="" class="form-group text-center" id="formChangeUser">
          <div class="row">
            <div class="offset-2 col-md-8">
              <label for="">Nome</label>
              <input type="text" class="form-control input" name="nome" value="<?=$nameUser?>" required>
              <br>
            </div>
          </div>
          <div class="row">
            <div class="offset-2 col-md-8">
              <label>E-mail</label>
              <input type="email" class="form-control input" name="email" value="<?=$emailUser?>" required>
              <br>
            </div>
          </div>
          <div class="row">
            <div class="offset-2 col-md-8">
              <button class="btn btn-outline-info" type="button" id="btnAlterarSenha">Alterar Senha</button>
            </div>
          </div>
          <br>
          <div class="row" style="display: none" id="divAlterarSenha">
            <div class="offset-2 col-md-8">
              <label>Senha Atual</label>
              <input type="password" class="form-control input" name="passAtual">
              <br>
            </div>
            <div class="offset-2 col-md-8">
              <label>Nova Senha</label>
              <input type="password" class="form-control input" name="newPass">
              <br>
            </div>
            <div class="offset-2 col-md-8">
              <label>Confirmar Senha</label>
              <input type="password" class="form-control input" name="confirmPass">
              <br>
            </div>
          </div>
          <input type="hidden" name="act" value="editUser">
          <input type="hidden" name="idUser" id="idUser" value="<?= $idUser ?>">
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            <button type="button" class="btn btn-primary"
                    onclick="saveUser(<?= $idUser ?>)">Salvar
            </button>
          </div>
        </form>

      </div>

    </div>
  </div>
</div>


</div>
<!-- jQuery -->
<script src="../layout/plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="../layout/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="../layout/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../layout/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTable -->
<script src="../layout/plugins/datatables/jquery.dataTables.js"></script>
<script src="../layout/plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<!-- ChartJS -->
<script src="../layout/plugins/chart.js/Chart.min.js"></script>
<!-- JQVMap -->
<script src="../layout/plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="../layout/plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="../layout/plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="../layout/plugins/moment/moment.min.js"></script>
<script src="../layout/plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="../layout/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="../layout/plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="../layout/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="../layout/dist/js/adminlte.js"></script>
<!-- Test -->
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<!-- DataTables -->
<script defer src="../layout/plugins/datatables/jquery.dataTables.js"></script>
<script defer src="../layout/plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<!-- Sweet Alert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<!-- Arquivo JS inicial  -->
<script src="../js/ready.js"></script>
</body>
</html>
