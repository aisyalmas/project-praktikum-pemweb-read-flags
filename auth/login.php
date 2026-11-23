<?php
    session_start();
    
    // Ambil error login dari session 
    $loginErrors = $_SESSION['login_errors'] ?? [];
    // Ambil email lama untuk prefill form 
    $oldEmail    = $_SESSION['login_old_email'] ?? '';
    // Ambil pesan sukses registrasi 
    $registerOk  = $_SESSION['register_success'] ?? null;
    
    // Hapus data sementara dari session setelah ditampilkan
    unset(
        $_SESSION['login_errors'],
        $_SESSION['login_old_email'],
        $_SESSION['register_success']
    );
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Page</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&family=Caprasimo&display=swap" rel="stylesheet">
    </head>
  <body>
    <main class="d-flex flex-row vh-100" >

        <section class="login-section mt-5 mx-auto" >
            
            <div class="login-container p-4 rounded-4 ">
                <div class="d-flex justify-content-between">
                    <a href="../index.php" class="backToback">back</a>
                    <a href="register.php" class="createAccount">create account</a>
                </div>
                <header class="m-2">
                    <h1 class="font-bricolage display-5">Welcome Back <br> readflags~</h1>
                    <p class="font-bricolage" 
                    style="
                    color: var(--muted);
                    font-weight:300;
                    font-size:small;
                    ">so your TBR pile stop judging you --</p>
                </header>

                <?php if ($registerOk): ?>
                    <!--  ALERT SUKSES REGISTRASI  -->
                    <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                        <?= htmlspecialchars($registerOk) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!--  FORM LOGIN  -->
                <form action="proses_login.php" method="post">
                <!-- Email Input -->
                <label for="email" class="form-label roboto-mono" 
                style="
                    font-size:small;
                ">email</label>
                <div class="mb-3">
                    <input type="email" class="form-control <?= isset($loginErrors['email']) ? 'is-invalid' : '' ?>" id="inputEmail" placeholder="name@example.com" name="email" value="<?= htmlspecialchars($oldEmail) ?>">
                    <?php if (isset($loginErrors['email'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($loginErrors['email']) ?></div>
                    <?php endif; ?>
                </div>
                <!-- Password Input dengan Toggle Show/Hide -->
                <label for="password" class="form-label roboto-mono"
                style="
                    font-size:small;
                "
                >password</label>
                <div class="mb-3">
                    <div class="input-group has-validation">
                        <input type="password" class="form-control <?= isset($loginErrors['password']) ? 'is-invalid' : '' ?>" id="inputPassword" name="password" placeholder="type your password">
                        <button class="btn btn-outline-secondary toggle-password" type="button"
                                data-target="inputPassword" aria-label="show password" aria-pressed="false">
                            <i class="bi bi-eye"></i>
                        </button>
                        <?php if (isset($loginErrors['password'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($loginErrors['password']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary sign-in rounded-pill w-100 py-2 font-bricolage " name="signIn">sign in</button>
                    
                </form>
            </div>
        </section>
        
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

    <script>
      document.querySelector('.toggle-password').addEventListener('click', function() {
        var input = document.getElementById('inputPassword');
        var icon = this.querySelector('i');
        input.type = input.type === 'password' ? 'text' : 'password';
        icon.className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
      });   
    </script>

  </body>
</html>
