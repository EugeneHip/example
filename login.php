<?php

//EJB SOLUTIONS - BATUTO 2025

require_once("include/initialize.php");
  if(isset($_SESSION['UID'])){
    redirect("index.php");
    header("Location: index.php");
  }
 ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SANTE Solutions</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo  WEB_ROOT;?>plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="<?php echo  WEB_ROOT;?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo  WEB_ROOT;?>dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <center>
       <img src="csr-scc.png" width="50%"></center>
      <p class="login-box-msg">Login to start your session</p>

      <form action="#" method="post">
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="username" placeholder="Username">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" name="userpass" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              
            </div>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" name="btnLogin" class="btn btn-success btn-block"><i class="fas fa-sign-in-alt">Log in</i></button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      
      <!-- /.social-auth-links -->

    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo  WEB_ROOT;?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo  WEB_ROOT;?>dist/js/adminlte.min.js"></script>

</body>
</html>
<?php 

if(isset($_POST['btnLogin'])){

 
  $email = trim($_POST['username']);
  $upass  = trim($_POST['userpass']);
  $h_upass = sha1($upass);
  

     if ($email == '' OR $upass == '') {

        message("Invalid Username and Password!", "error");
        redirect("login.php");
           
      } else {  
    //it creates a new objects of member
      $user = new User();
      //make use of the static function, and we passed to parameters
      
        $res = $user::AuthenticateUser($email, $h_upass);

        if ($res==true) { 
            echo "<script type='text/javascript'>alert('You logon as'".$_SESSION['TYPE']."')</script>";
           //message("You logon as ".$_SESSION['ACCOUNT_TYPE'].".","success");
          if ($_SESSION['TYPE']=='Administrator' || $_SESSION['TYPE']=='Doctor'|| $_SESSION['TYPE']=='Staff'){

            $_SESSION['UID']       = $_SESSION['UID'];
            $_SESSION['DISPLAYNAME'] = $_SESSION['DISPLAYNAME'] ;
            $_SESSION['USERNAME']   = $_SESSION['USERNAME'];
            $_SESSION['TYPE']       = $_SESSION['TYPE'];

            ?>
               <script language="javascript">
                  window.location.href = "index.php"
              </script>
              <?php
          } 
        }else{
          echo "<script type='text/javascript'>alert('Account does not exist! Contact Administrator.'); window.location.href = 'index.php'</script>";
       }
      }
 }


 ?> 