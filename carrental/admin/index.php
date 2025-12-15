<?php
session_start();
include('includes/config.php');

$error = '';
if (isset($_POST['login'])) {
    $email = $_POST['username'];
    $password = md5($_POST['password']); // Consider using password_hash in production

    $sql = "SELECT UserName,Password FROM admin WHERE UserName=:email AND Password=:password";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->execute();

    if ($query->rowCount() > 0) {
        $_SESSION['alogin'] = $_POST['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = 'Invalid username or password';
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | Car Rental Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --dark-color: #1a1a2e;
            --light-color: #f8f9fa;
            --danger-color: #ef233c;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(rgba(26, 26, 46, 0.7), rgba(26, 26, 46, 0.7)),
                        url(img/driver.jpg) no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            0% {opacity: 0; transform: translateY(30px);}
            100% {opacity: 1; transform: translateY(0);}
        }

        .login-container {
            width: 100%;
            max-width: 460px;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 18px 48px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .login-card:hover {
            transform: scale(1.01);
        }

        .login-header {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 30px;
            text-align: center;
        }

        .login-header h2 {
            font-weight: 700;
            font-size: 1.75rem;
            margin: 0;
        }

        .login-body {
            padding: 35px;
            background-color: white;
        }

        .form-control {
            border-radius: 10px;
            padding: 14px 16px;
            border: 1px solid #ced4da;
            background-color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }

        .btn-login {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-weight: 600;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .input-group-text {
            background-color: #f1f1f1;
            border: 1px solid #ced4da;
            border-right: 0;
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 5;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: var(--secondary-color);
        }

        .alert {
            animation: fadeIn 0.5s ease-in-out;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <h2><i class="fas fa-user-shield me-2"></i>Admin Portal</h2>
        </div>

        <div class="login-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="post" autocomplete="off">
                <div class="mb-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" class="form-control" placeholder="Username" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="input-group password-container">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword()"></i>
                    </div>
                </div>

                <button type="submit" name="login" class="btn btn-login w-100 mb-3">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </button>

                <div class="text-center">
                    <a href="../index.php" class="back-link">
                        <i class="fas fa-arrow-left me-2"></i>Back to Home
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function togglePassword() {
        const password = document.querySelector('input[name="password"]');
        const icon = document.querySelector('.toggle-password');
        
        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    // Clear alert on input
    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('input', () => {
            const errorAlert = document.querySelector('.alert');
            if (errorAlert) {
                errorAlert.remove();
            }
        });
    });
</script>
</body>
</html>
