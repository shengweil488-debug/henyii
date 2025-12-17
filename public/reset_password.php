<?php
session_start();
require_once __DIR__ . '/../src/controllers/AuthController.php';
$lang = $_SESSION['lang'] ?? 'en';
$langArr = require __DIR__ . '/../lang/' . $lang . '.php';

$error = '';
$success = '';
$token = $_GET['token'] ?? '';

if (empty($token)) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if ($newPassword !== $confirmPassword) {
        $error = 'Passwords do not match';
    } elseif (strlen($newPassword) < 6) {
        $error = 'Password must be at least 6 characters long';
    } else {
        $result = AuthController::resetPassword($token, $newPassword);
        if ($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            position: relative;
        }

        .form-container {
            padding: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #333;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .input-group {
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 16px;
        }

        .form-control {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-link {
            background: none;
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            display: block;
            text-align: center;
        }

        .btn-link:hover {
            color: #5a5fdb;
            text-decoration: underline;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo i {
            font-size: 48px;
            color: #667eea;
            margin-bottom: 10px;
        }

        .password-strength {
            margin-top: 5px;
            font-size: 12px;
            color: #666;
        }

        .strength-weak { color: #dc3545; }
        .strength-medium { color: #ffc107; }
        .strength-strong { color: #28a745; }

        @media (max-width: 480px) {
            .container {
                margin: 10px;
                border-radius: 15px;
            }
            
            .form-container {
                padding: 30px 20px;
            }
            
            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="logo">
                <i class="fas fa-key"></i>
            </div>
            <div class="header">
                <h1>Reset Password</h1>
                <p>Enter your new password below</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
                <a href="login.php" class="btn btn-link">Back to Login</a>
            <?php else: ?>
                <form method="post">
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="new_password" name="new_password" class="form-control" required minlength="6">
                        </div>
                        <div class="password-strength" id="password-strength"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required minlength="6">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Reset Password
                    </button>
                    
                    <a href="login.php" class="btn btn-link">Back to Login</a>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Password strength indicator
        document.getElementById('new_password').addEventListener('input', function() {
            const password = this.value;
            const strengthDiv = document.getElementById('password-strength');
            
            if (password.length === 0) {
                strengthDiv.textContent = '';
                strengthDiv.className = 'password-strength';
                return;
            }
            
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            let strengthText = '';
            let strengthClass = '';
            
            if (strength <= 2) {
                strengthText = 'Weak';
                strengthClass = 'strength-weak';
            } else if (strength <= 3) {
                strengthText = 'Medium';
                strengthClass = 'strength-medium';
            } else {
                strengthText = 'Strong';
                strengthClass = 'strength-strong';
            }
            
            strengthDiv.textContent = `Password strength: ${strengthText}`;
            strengthDiv.className = `password-strength ${strengthClass}`;
        });

        // Add interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.querySelector('i').style.color = '#667eea';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.querySelector('i').style.color = '#999';
                });
            });
        });
    </script>
</body>
</html> 