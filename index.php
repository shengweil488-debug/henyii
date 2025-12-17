<?php
session_start();
require_once __DIR__ . '/config/database.php';

// Handle language switch
if ((isset($_POST['set_lang']) && in_array($_POST['set_lang'], ['zh', 'en', 'ms'])) ||
    (isset($_GET['set_lang']) && in_array($_GET['set_lang'], ['zh', 'en', 'ms']))) {
    $lang = $_POST['set_lang'] ?? $_GET['set_lang'];
    $_SESSION['lang'] = $lang;
    header('Location: index.php');
    exit;
}

// 如果用户已登录，重定向到dashboard
if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

// 加载语言包
$lang = $_SESSION['lang'] ?? 'zh';
$langArr = require __DIR__ . '/lang/' . $lang . '.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $langArr['system_name'] ?? '恒毅活动报告系统'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Montserrat', 'Segoe UI', Arial, sans-serif;
            transition: background 0.5s, color 0.5s;
        }
        body.dark-mode {
            background: linear-gradient(135deg, #232946 0%, #1a1a2e 100%);
            color: #f3f6fa;
        }
        .welcome-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(16px) saturate(180%);
            border-radius: 28px;
            box-shadow: 0 8px 32px 0 rgba(76, 110, 245, 0.18), 0 1.5px 8px 0 rgba(120, 120, 180, 0.10);
            padding: 48px 36px 40px 36px;
            text-align: center;
            max-width: 520px;
            width: 100%;
            position: relative;
            border: 1.5px solid rgba(180,180,255,0.13);
            transition: box-shadow 0.3s, transform 0.2s, background 0.5s;
        }
        body.dark-mode .welcome-card {
            background: rgba(35,41,70,0.92);
            color: #f3f6fa;
            box-shadow: 0 8px 32px #7f7fd588, 0 0 24px 4px #7f7fd5, 0 0 0 2.5px #7f7fd544 inset, 0 1.5px 1.5px 0 #fff8 inset;
        }
        .welcome-card h1 {
            color: #2a2a44;
            font-size: 2.7rem;
            font-weight: 800;
            margin-bottom: 22px;
            letter-spacing: 1px;
            text-shadow: 0 2px 8px rgba(100,120,255,0.07);
        }
        body.dark-mode .welcome-card h1 {
            color: #7f7fd5;
            text-shadow: 0 2px 8px #7f7fd5, 0 1px 2px #fff2;
        }
        .welcome-card p {
            color: #5a5a7a;
            font-size: 1.18rem;
            margin-bottom: 36px;
            font-weight: 500;
            line-height: 1.7;
        }
        body.dark-mode .welcome-card p {
            color: #bfc8e6;
        }
        .btn {
            border-radius: 22px;
            font-weight: 700;
            font-size: 1.08rem;
            padding: 8px 28px;
            margin: 0 4px 8px 0;
            box-shadow: 0 2px 8px rgba(76,110,245,0.08);
            transition: filter 0.2s, transform 0.2s, background 0.5s;
        }
        .btn-primary {
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border: none;
        }
        .btn-success {
            background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
            color: #fff;
            border: none;
        }
        body.dark-mode .btn-primary {
            background: linear-gradient(90deg, #232946 0%, #7f7fd5 100%);
            color: #fff;
        }
        body.dark-mode .btn-success {
            background: linear-gradient(90deg, #232946 0%, #43e97b 100%);
            color: #fff;
        }
        .btn:hover {
            filter: brightness(1.08);
            transform: translateY(-2px) scale(1.03);
        }
        /* 夜间模式切换按钮 */
        .night-toggle {
            position: fixed;
            top: 32px;
            right: 40px;
            z-index: 1000;
            border: none;
            background: linear-gradient(90deg, #7f7fd5 0%, #86a8e7 100%);
            color: #fff;
            border-radius: 22px;
            font-weight: 700;
            font-size: 1.08rem;
            padding: 8px 22px;
            box-shadow: 0 2px 8px rgba(120,120,255,0.10);
            transition: filter 0.2s, transform 0.2s, background 0.5s;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .night-toggle:hover {
            filter: brightness(1.10);
            transform: scale(1.04);
        }
        body.dark-mode .night-toggle {
            background: linear-gradient(90deg, #232946 0%, #7f7fd5 100%);
        }
        .btn:before {
            content: '';
            position: absolute;
            left: 50%;
            top: 50%;
            width: 0;
            height: 0;
            background: rgba(255,255,255,0.18);
            border-radius: 100%;
            transform: translate(-50%, -50%);
            transition: width 0.4s cubic-bezier(.4,2,.6,1), height 0.4s cubic-bezier(.4,2,.6,1);
            z-index: 0;
        }
        .btn:hover:before {
            width: 220%;
            height: 600%;
        }
        .btn:hover, .btn:focus {
            transform: translateY(-2px) scale(1.045);
            box-shadow: 0 6px 24px rgba(76, 110, 245, 0.18), 0 2px 8px rgba(120, 120, 180, 0.10);
        }
        .btn > * {
            position: relative;
            z-index: 1;
        }
        .logo {
            font-size: 4.2rem;
            color: #1976d2;
            margin-bottom: 24px;
            filter: drop-shadow(0 2px 8px rgba(100,120,255,0.10));
            transition: transform 0.25s;
        }
        .welcome-card:hover .logo {
            transform: scale(1.08) rotate(-3deg);
        }
        /* Language Switcher Styles */
        .lang-switcher-global {
            position: fixed;
            top: 32px;
            right: 40px;
            z-index: 100;
            display: flex;
            gap: 10px;
            background: rgba(255,255,255,0.35);
            box-shadow: 0 4px 24px 0 rgba(80,80,180,0.10);
            border-radius: 40px;
            padding: 6px 16px;
            backdrop-filter: blur(8px);
            border: 1.5px solid rgba(180,180,255,0.18);
            align-items: center;
        }
        .lang-btn {
            border: none;
            outline: none;
            background: transparent;
            color: #333;
            font-weight: 600;
            font-size: 1.08rem;
            padding: 8px 18px 8px 14px;
            border-radius: 30px;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s, transform 0.15s;
            display: flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            box-shadow: none;
        }
        .lang-btn .lang-flag {
            font-size: 1.2em;
        }
        .lang-btn.active, .lang-btn:hover {
            background: linear-gradient(90deg, #667eea 60%, #42a5f5 100%);
            color: #fff;
            box-shadow: 0 2px 12px rgba(100,120,255,0.13);
            transform: translateY(-2px) scale(1.04);
        }
        @media (max-width: 600px) {
            .lang-switcher-global {
                top: 10px;
                right: 10px;
                padding: 4px 8px;
            }
            .lang-btn {
                font-size: 0.98rem;
                padding: 6px 10px 6px 8px;
            }
            .welcome-card {
                padding: 28px 8px 24px 8px;
                max-width: 98vw;
            }
            .welcome-card h1 {
                font-size: 1.5rem;
            }
            .logo {
                font-size: 2.5rem;
            }
            .btn {
                font-size: 0.98rem;
                padding: 10px 18px;
            }
        }
        .hero-section {
            text-align: center;
            padding: 80px 0 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        .hero-content {
            position: relative;
            z-index: 2;
        }
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            text-shadow: 0 4px 8px rgba(0,0,0,0.3);
            background: linear-gradient(45deg, #fff, #e3f2fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-subtitle {
            font-size: 1.4rem;
            margin-bottom: 2rem;
            opacity: 0.95;
            font-weight: 300;
        }
        .features-section {
            padding: 80px 0;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .feature-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s;
        }
        .feature-card:hover::before {
            left: 100%;
        }
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(31, 38, 135, 0.25);
        }
        .feature-icon {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .feature-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #2c3e50;
        }
        .feature-description {
            color: #6c757d;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        .btn-feature {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        .btn-feature:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            color: white;
            text-decoration: none;
        }
        .language-section {
            padding: 60px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .language-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        .language-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));
            opacity: 0;
            transition: opacity 0.3s;
        }
        .language-card:hover::before {
            opacity: 1;
        }
        .language-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        .language-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
        }
        .language-name {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .language-desc {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .night-mode-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 50%;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .night-mode-toggle:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }
        body.dark-mode {
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 50%, #16213e 100%);
            color: #e3f2fd;
        }
        body.dark-mode .hero-section {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        }
        body.dark-mode .features-section {
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%);
        }
        body.dark-mode .feature-card {
            background: rgba(26, 26, 46, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #e3f2fd;
        }
        body.dark-mode .feature-title {
            color: #ffffff;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        body.dark-mode .feature-description {
            color: #e3f2fd;
            font-weight: 400;
        }
        body.dark-mode .language-section {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        }
        body.dark-mode .language-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        body.dark-mode .language-name {
            color: #ffffff;
            font-weight: 600;
        }
        body.dark-mode .language-desc {
            color: #e3f2fd;
            font-weight: 400;
        }
    </style>
    <script>
        // 语言切换淡出动画，动画后用window.location跳转，彻底避免表单递归和事件冲突
        document.addEventListener('DOMContentLoaded', function() {
            var langForm = document.querySelector('.lang-switcher-global');
            var card = document.querySelector('.welcome-card');
            if(langForm && card) {
                langForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    var btn = document.activeElement;
                    var lang = btn && btn.value;
                    if (!lang) return;
                    card.style.transition = 'opacity 0.45s cubic-bezier(.4,2,.6,1)';
                    card.style.opacity = 0;
                    setTimeout(function() {
                        window.location = '?set_lang=' + encodeURIComponent(lang);
                    }, 420);
                });
            }
        });
    </script>
</head>
<body>
    <!-- Global Language Switcher -->
    <form method="post" class="lang-switcher-global">
        <button type="submit" name="set_lang" value="zh" class="lang-btn<?php if($lang==='zh') echo ' active'; ?>" title="中文">
            <span class="lang-flag">🇨🇳</span> <?php echo $langArr['zh'] ?? '中文'; ?>
        </button>
        <button type="submit" name="set_lang" value="en" class="lang-btn<?php if($lang==='en') echo ' active'; ?>" title="English">
            <span class="lang-flag">🇬🇧</span> <?php echo $langArr['en'] ?? 'English'; ?>
        </button>
        <button type="submit" name="set_lang" value="ms" class="lang-btn<?php if($lang==='ms') echo ' active'; ?>" title="Malay">
            <span class="lang-flag">🇲🇾</span> <?php echo $langArr['ms'] ?? 'Malay'; ?>
        </button>
    </form>
    <!-- End Global Language Switcher -->
    <button class="night-toggle" id="nightToggleBtn" title="夜间/白天模式"><i class="fas fa-moon"></i></button>
    <div class="welcome-card position-relative">
        <div class="logo">
            <i class="fas fa-school"></i>
        </div>
        <h1><?php echo $langArr['system_name'] ?? '恒毅活动报告系统'; ?></h1>
        <p><?php echo $langArr['welcome'] ?? '欢迎使用恒毅中学活动管理系统'; ?></p>
        <div>
            <a href="login.php" class="btn btn-primary">
                <i class="fas fa-sign-in-alt me-2"></i><?php echo $langArr['login'] ?? '登录'; ?>
            </a>
            <a href="register.php" class="btn btn-success">
                <i class="fas fa-user-plus me-2"></i><?php echo $langArr['register'] ?? '注册'; ?>
            </a>
        </div>
    </div>
    <script>
        // 夜间模式切换
        function setDarkMode(on) {
            if(on) {
                document.body.classList.add('dark-mode');
                localStorage.setItem('henyii_dark', '1');
            } else {
                document.body.classList.remove('dark-mode');
                localStorage.setItem('henyii_dark', '0');
            }
        }
        document.getElementById('nightToggleBtn').onclick = function() {
            setDarkMode(!document.body.classList.contains('dark-mode'));
        };
        // 自动读取夜间模式
        if(localStorage.getItem('henyii_dark')==='1') setDarkMode(true);
    </script>
</body>
</html> 