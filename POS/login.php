<?php
session_start();
include('config/db.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Log In | Pharmacy</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <!-- <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css"> -->
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">

  <style>
    .body-box,
    .login-page {
      background-color: #1B8F08;
    }

    .body-box {
      background-color: #105704;
    }

    .main-name {
      color: black !important;
      font-size: 60px;
      font-weight: bold;
    }

    .label-wight {
      color: #fff !important;
      font-size: 20px;
    }

    .text-field {
      color: black !important;
      font-size: 20px !important;
    }

    .main-h1 {
      font-weight: bold;
      font-size: 150px !important;
      font-size: 4rem;
      display: flex;
      justify-content: center;
      align-items: center;
      color: #fff;
    }

    .main-h1 span {
      display: inline-block;
      animation: shine 3s infinite;
      text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3), 0 0 10px #00f, 0 0 20px #00f, 0 0 30px #00f, 0 0 40px #00f;
      color: #05540D;
      /* Change text color to blue */
      transform: perspective(500px) rotateX(15deg) rotateY(0deg) rotateZ(0deg);
      margin: 0 5px;
      /* Adjust margin as needed */
    }


    @keyframes shine {
      0% {
        text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3), 0 0 10px #ff6, 0 0 20px #ff6, 0 0 30px #ff6, 0 0 40px #ff6;
      }

      50% {
        text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3), 0 0 10px #ffd700, 0 0 20px #ffd700, 0 0 30px #ffd700, 0 0 40px #ffd700;
      }

      100% {
        text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3), 0 0 10px #ff6, 0 0 20px #ff6, 0 0 30px #ff6, 0 0 40px #ff6;
      }
    }
  </style>

</head>

<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <!--<a href="#" class="main-name"><b>Siddha.lk</b></a>-->
      <div class="main-h1">
        <span>S</span>
        <span>I</span>
        <span>D</span>
        <span>D</span>
        <span>H</span>
        <span>A</span>
        <span>.</span>
        <span>L</span>
        <span>K</span>
      </div>

    </div>
    <!-- /.login-logo -->
    <div class="card">
      <div class="card-body login-card-body body-box">
        <p class="login-box-msg label-wight">Sign in to start Your Day</p>

        <form action="actions/login.php" method="post">
          <div class="input-group mb-3">
            <input type="text" class="form-control" class="text-field" placeholder="Username" name="username" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" class="text-field" placeholder="Password" name="password" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <!-- <div class="col-8">
              <div class="icheck-primary">
                <input type="checkbox" id="remember">
                <label for="remember">
                  Remember Me
                </label>
              </div>
            </div> -->
            <!-- /.col -->
            <div class="col-4">
              <button type="submit" class="btn btn-primary btn-block" name="btnLogin">Sign In</button>
            </div>
            <!-- /.col -->
          </div>
        </form>

        <!--<div class="social-auth-links text-center mb-3">-->
        <!--  <p>- OR -</p>-->
        <!--  <a href="#" class="btn btn-block btn-primary">-->
        <!--    <i class="fab fa-facebook mr-2"></i> Sign in using Facebook-->
        <!--  </a>-->
        <!--  <a href="#" class="btn btn-block btn-danger">-->
        <!--    <i class="fab fa-google-plus mr-2"></i> Sign in using Google+-->
        <!--  </a>-->
        <!--</div>-->
        <!-- /.social-auth-links -->

        <!-- <p class="mb-1">
          <a href="#">I forgot my password</a>
        </p>
        <p class="mb-0">
          <a href="#" class="text-center">Register a new membership</a>
        </p> -->
      </div>
      <!-- /.login-card-body -->
    </div>
  </div>

  <?php include("part/alert.php"); ?>
  <!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="dist/js/adminlte.min.js"></script>
</body>

</html>