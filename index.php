<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  
  <!-- ===== CSS ===== -->
  <link rel="stylesheet" href="assets/css/styles.css" />
  
  <!-- ===== BOX ICONS ===== -->
  <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.5/css/boxicons.min.css" rel="stylesheet" />
  
  <title>TugasKu</title>
</head>
<body>
  <div class="login">
    <div class="login__content">
      
      <!-- Ilustrasi login -->
      <div class="login__img">
        <img src="assets/img/img-login.svg" alt="Login Illustration" />
      </div>
      
      <div class="login__forms">
        <!-- SIGN IN FORM -->
        <form action="includes/login.php" method="POST" class="login__registre" id="login-in" autocomplete="off" novalidate>
          <h1 class="login__title">Sign In</h1>

          <div class="login__box">
            <i class="bx bx-user login__icon"></i>
            <input type="text" name="username" placeholder="Username" class="login__input" required autocomplete="username" />
          </div>

          <div class="login__box">
            <i class="bx bx-lock-alt login__icon"></i>
            <input type="password" name="password" placeholder="Password" class="login__input" required autocomplete="current-password" />
          </div>

          <a href="#" class="login__forgot">Forgot password?</a>

          <button type="submit" class="login__button">Sign In</button>

          <div class="login__toggle-text">
            <span class="login__account">Don't have an Account?</span>
            <span class="login__signin" id="sign-up">Sign Up</span>
          </div>
        </form>

        <!-- SIGN UP FORM -->
        <form action="includes/register.php" method="POST" class="login__create none" id="login-up" autocomplete="off" novalidate>
          <h1 class="login__title">Create Account</h1>

          <div class="login__box">
            <i class="bx bx-user login__icon"></i>
            <input type="text" name="username" placeholder="Username" class="login__input" required autocomplete="username" />
          </div>

          <div class="login__box">
            <i class="bx bx-at login__icon"></i>
            <input type="email" name="email" placeholder="Email" class="login__input" required autocomplete="email" />
          </div>

          <div class="login__box">
            <i class="bx bx-lock-alt login__icon"></i>
            <input type="password" name="password" placeholder="Password" class="login__input" required autocomplete="new-password" />
          </div>

          <button type="submit" class="login__button">Sign Up</button>

          <div class="login__toggle-text">
            <span class="login__account">Already have an Account?</span>
            <span class="login__signup" id="sign-in">Sign In</span>
          </div>

          <div class="login__social">
            <a href="#" class="login__social-icon"><i class="bx bxl-facebook"></i></a>
            <a href="#" class="login__social-icon"><i class="bx bxl-twitter"></i></a>
            <a href="#" class="login__social-icon"><i class="bx bxl-google"></i></a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Notification Overlay -->
  <div id="notifOverlay" class="notif-overlay hidden">
    <div class="notif-box">
      <p id="notifMessage">Pesan notifikasi</p>
      <button onclick="closeNotif()" class="btn-notif-close">&times;</button>
    </div>
  </div>

  <!--===== MAIN JS =====-->
  <script src="assets/js/main.js"></script>

  <?php if (isset($_GET['error'])): ?>
  <script>
    showNotif("<?= htmlspecialchars($_GET['error']) ?>");
  </script>
  <?php endif; ?>
</body>
</html>
