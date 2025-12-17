<?php
session_start();
require_once __DIR__ . '/../src/controllers/AuthController.php';

// 这个页面仅用于测试，生产环境应该删除
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $result = AuthController::forgotPassword($email);
    echo "<h2>Test Result:</h2>";
    echo "<pre>" . print_r($result, true) . "</pre>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Forgot Password</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="email"] { width: 300px; padding: 8px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Test Forgot Password Function</h1>
    <form method="post">
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>
        <button type="submit">Test Forgot Password</button>
    </form>
    
    <h2>Instructions:</h2>
    <ol>
        <li>Enter an email address that exists in your users table</li>
        <li>Click "Test Forgot Password" to see the result</li>
        <li>If successful, you'll get a reset link for testing</li>
        <li>Use the reset link to test the password reset functionality</li>
    </ol>
    
    <p><a href="login.php">Back to Login</a></p>
</body>
</html> 