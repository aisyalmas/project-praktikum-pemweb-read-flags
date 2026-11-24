<?php 
    session_start(); 

    // Ambil error general dari session 
    $regError       = $_SESSION['register_error'] ?? null;
    // Ambil error per field dari session 
    $registerErrors = $_SESSION['register_errors'] ?? [];
    
    // Hapus data sementara dari session setelah ditampilkan
    unset($_SESSION['register_error'], $_SESSION['register_errors'], $_SESSION['register_old_name'], $_SESSION['register_old_email']);
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | Read Flags</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/register.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&family=Caprasimo&display=swap" rel="stylesheet">
  </head>
  <body>
    <main class="d-flex flex-row vh-100">
        <section class="login-section my-4 mx-auto" >
            
            <div class="login-container p-4 rounded-4 ">
                <div class="d-flex justify-content-between">
                    <a href="../index.php" class="backToback">back</a> 
                    <a href="login.php" class="signInInstead">sign in instead</a> 
                </div>
                <header class="m-2">
                    <h1 class="font-bricolage display-5">make your library~</h1>
                    <p class="font-bricolage" 
                    style="
                    color: var(--muted);
                    font-weight:300;
                    font-size:small;
                    "
                    >so your TBR pile stop judging you --</p>
                </header>

                <?php if ($regError): ?>
                    <!--  ALERT ERROR GENERAL  -->
                    <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
                        <?= htmlspecialchars($regError) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!--  FORM REGISTER  -->
                <form action="proses_register.php" method="post" class="box-shadow-lg">
                
                    <!-- Name Input -->
                    <label for="inputName" class="form-label roboto-mono" style="
                        font-size:small;
                    ">name</label> 
                    <div class="mb-3 roboto-mono">
                        <input type="text" class="form-control <?= isset($registerErrors['name']) ? 'is-invalid' : '' ?>" id="inputName" placeholder="How should we greet you?" name="name">
                        <?php if (isset($registerErrors['name'])): ?>
                            <div class="invalid-feedback roboto-mono"><?= htmlspecialchars($registerErrors['name']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Email Input -->
                    <label for="inputEmail" class="form-label roboto-mono" style="
                        font-size:small;
                    ">email</label> 
                    <div class="mb-3">
                        <input type="email" class="form-control roboto-mono <?= isset($registerErrors['email']) ? 'is-invalid' : '' ?>" id="inputEmail" placeholder="name@example.com" name="email">
                        <?php if (isset($registerErrors['email'])): ?>
                            <div class="invalid-feedback roboto-mono"><?= htmlspecialchars($registerErrors['email']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Password Input dengan Toggle Show/Hide -->
                    <label for="inputPassword" class="form-label roboto-mono" style="
                        font-size:small;
                    ">password</label> 
                    <div class="mb-3">
                        <div class="input-group has-validation">
                            <input type="password" class="form-control roboto-mono <?= isset($registerErrors['password']) ? 'is-invalid' : '' ?>" id="inputPassword" name="password" placeholder="type you secret">
                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                    data-target="inputPassword" aria-label="show password" aria-pressed="false">
                                <i class="bi bi-eye"></i>
                            </button>
                            <?php if (isset($registerErrors['password'])): ?>
                                <div class="invalid-feedback roboto-mono"><?= htmlspecialchars($registerErrors['password']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Confirm Password Input dengan Toggle Show/Hide -->
                    <label for="inputConfirm" class="form-label roboto-mono" style="
                        font-size:small;
                    ">confirm password</label>
                    <div class="mb-3">
                        <div class="input-group has-validation">
                            <input type="password" class="form-control roboto-mono <?= isset($registerErrors['confirm_password']) ? 'is-invalid' : '' ?>" id="inputConfirm" name="confirm_password" placeholder="type you secret">
                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                    data-target="inputConfirm" aria-label="show password" aria-pressed="false">
                                <i class="bi bi-eye"></i>
                            </button>
                            <?php if (isset($registerErrors['confirm_password'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($registerErrors['confirm_password']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary register rounded-pill w-100 py-2" name="register" value="register">create account</button>
                    
                </form>

            </div>
        </section>
        
    </main>
    <script>
        document.querySelectorAll('.toggle-password').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.getElementById(this.dataset.target);
                var icon  = this.querySelector('i');
                input.type = input.type === 'password' ? 'text' : 'password';
                icon.className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

  </body>
</html>
