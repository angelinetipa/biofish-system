<?php
/**
 * Login Page
 * BIO-FISH Bioplastic Formation System
 */

require_once __DIR__ . '/../../config/init.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect(BASE_URL . '/pages/dashboard/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (attempt_login($username, $password)) {
        redirect(BASE_URL . '/pages/dashboard/index.php');
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo get_page_title('Login'); ?></title>
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/main.css">
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/components.css">
    <style>
        body {
            overflow: hidden;
        }
        
        .login-wrapper {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 28px 32px;
            border-radius: 28px;
            width: 100%;
            max-width: 360px;
            position: relative;
            z-index: 1;
            box-shadow: 
                20px 20px 60px rgba(0, 0, 0, 0.15),
                -20px -20px 60px rgba(255, 255, 255, 0.7),
                inset 2px 2px 8px rgba(255, 255, 255, 0.9),
                inset -2px -2px 8px rgba(0, 0, 0, 0.05);
            backdrop-filter: blur(10px);
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .logo-clay {
            width: 52px;
            height: 52px;
            margin: 0 auto 12px;
            background: linear-gradient(145deg, #5BA5BA, #3D8899);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 
                12px 12px 24px rgba(0, 0, 0, 0.2),
                -12px -12px 24px rgba(255, 255, 255, 0.5),
                inset 4px 4px 8px rgba(255, 255, 255, 0.3),
                inset -4px -4px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 28px;
        }
        
        .logo-clay:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 
                16px 16px 32px rgba(0, 0, 0, 0.25),
                -16px -16px 32px rgba(255, 255, 255, 0.6),
                inset 4px 4px 8px rgba(255, 255, 255, 0.4),
                inset -4px -4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .logo-section h1 {
            font-size: 20px;
            color: #2C7873;
            margin-bottom: 4px;
            font-weight: 800;
            text-shadow: 2px 2px 4px rgba(255, 255, 255, 0.8);
            letter-spacing: 0.5px;
        }
        
        .logo-section p {
            color: #5a6c7d;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.3px;
        }
        
        .alert {
            padding: 10px 14px;
            border-radius: 14px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-error {
            background: linear-gradient(145deg, #fee, #fdd);
            color: #c33;
            border: 1px solid #fcc;
            box-shadow: 
                4px 4px 12px rgba(204, 51, 51, 0.1),
                inset 1px 1px 3px rgba(255, 255, 255, 0.5);
        }
        
        .alert svg {
            flex-shrink: 0;
        }
        
        .form-group {
            margin-bottom: 16px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #374151;
            font-weight: 600;
            font-size: 12px;
            letter-spacing: 0.3px;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            font-size: 13px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: white;
            box-shadow: 
                inset 2px 2px 5px rgba(0, 0, 0, 0.03),
                inset -2px -2px 5px rgba(255, 255, 255, 0.5);
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #5BA5BA;
            box-shadow: 
                0 0 0 4px rgba(91, 165, 186, 0.1),
                inset 2px 2px 5px rgba(91, 165, 186, 0.05);
            transform: translateY(-1px);
        }
        
        .form-group input::placeholder {
            color: #9ca3af;
            font-size: 12px;
        }
        
        .btn {
            padding: 11px 20px;
            border: none;
            border-radius: 14px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
            letter-spacing: 0.3px;
        }
        
        .btn-primary {
            background: linear-gradient(145deg, #5BA5BA, #3D8899);
            color: white;
            box-shadow: 
                8px 8px 16px rgba(0, 0, 0, 0.15),
                -8px -8px 16px rgba(255, 255, 255, 0.5),
                inset 1px 1px 2px rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 
                10px 10px 20px rgba(0, 0, 0, 0.2),
                -10px -10px 20px rgba(255, 255, 255, 0.6),
                inset 1px 1px 2px rgba(255, 255, 255, 0.3);
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 
                4px 4px 8px rgba(0, 0, 0, 0.2),
                inset 2px 2px 6px rgba(0, 0, 0, 0.1);
        }
        
        .demo-clay-card {
            margin-top: 18px;
            padding: 14px;
            background: linear-gradient(145deg, #e8f4f8, #d8eef5);
            border-radius: 18px;
            box-shadow: 
                inset 4px 4px 12px rgba(0, 0, 0, 0.08),
                inset -4px -4px 12px rgba(255, 255, 255, 0.9);
        }
        
        .demo-clay-card h3 {
            font-size: 10px;
            color: #2C7873;
            margin-bottom: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .demo-clay-card h3 svg {
            width: 13px;
            height: 13px;
            fill: #2C7873;
        }
        
        .credential-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 9px 12px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 
                4px 4px 8px rgba(0, 0, 0, 0.06),
                -4px -4px 8px rgba(255, 255, 255, 0.8);
            transition: all 0.2s ease;
        }
        
        .credential-item:hover {
            transform: translateX(2px);
            box-shadow: 
                6px 6px 12px rgba(0, 0, 0, 0.08),
                -6px -6px 12px rgba(255, 255, 255, 0.9);
        }
        
        .credential-item:last-child {
            margin-bottom: 0;
        }
        
        .cred-label {
            font-size: 11px;
            color: #6b7280;
            font-weight: 600;
            letter-spacing: 0.2px;
        }
        
        .cred-value {
            font-family: 'Courier New', monospace;
            font-size: 11px;
            color: #2C7873;
            font-weight: 700;
            background: linear-gradient(145deg, #f0f9ff, #e0f2fe);
            padding: 4px 9px;
            border-radius: 8px;
            box-shadow: 
                inset 2px 2px 4px rgba(0, 0, 0, 0.06),
                inset -2px -2px 4px rgba(255, 255, 255, 0.9);
        }
        
        /* Responsive adjustments */
        @media (max-height: 700px) {
            .login-container {
                padding: 24px 28px;
                max-width: 340px;
            }
            .logo-clay {
                width: 48px;
                height: 48px;
                font-size: 26px;
            }
            .logo-section h1 {
                font-size: 18px;
            }
            .form-group {
                margin-bottom: 14px;
            }
        }
        
        @media (max-width: 400px) {
            .login-container {
                max-width: 320px;
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <div class="logo-section">
                <div class="logo-clay">🐟</div>
                <h1><?php echo APP_NAME; ?></h1>
                <p><?php echo APP_DESCRIPTION; ?></p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M13,13H11V7H13M13,17H11V15H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/>
                    </svg>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required autofocus placeholder="Enter your username">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>
                
                <button type="submit" class="btn btn-primary">Sign In</button>
            </form>
            
            <div class="demo-clay-card">
                <h3>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12,17A2,2 0 0,0 14,15C14,13.89 13.1,13 12,13A2,2 0 0,0 10,15A2,2 0 0,0 12,17M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6A2,2 0 0,1 4,20V10C4,8.89 4.9,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z"/>
                    </svg>
                    Demo Credentials
                </h3>
                <div class="credential-item">
                    <span class="cred-label">Admin</span>
                    <span class="cred-value">admin / password123</span>
                </div>
                <div class="credential-item">
                    <span class="cred-label">Operator</span>
                    <span class="cred-value">operator1 / password123</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>