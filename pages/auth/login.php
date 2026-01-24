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
            overflow: hidden; /* Prevent scrolling */
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
            padding: 40px 45px;
            border-radius: 40px;
            width: 100%;
            max-width: 450px;
            position: relative;
            z-index: 1;
            box-shadow: 
                20px 20px 60px rgba(0, 0, 0, 0.15),
                -20px -20px 60px rgba(255, 255, 255, 0.7),
                inset 2px 2px 8px rgba(255, 255, 255, 0.9),
                inset -2px -2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 25px;
        }
        
        .logo-clay {
            width: 70px;
            height: 70px;
            margin: 0 auto 15px;
            background: linear-gradient(145deg, #5BA5BA, #3D8899);
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 
                12px 12px 24px rgba(0, 0, 0, 0.2),
                -12px -12px 24px rgba(255, 255, 255, 0.5),
                inset 4px 4px 8px rgba(255, 255, 255, 0.3),
                inset -4px -4px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }
        
        .logo-clay:hover {
            transform: translateY(-3px);
            box-shadow: 
                16px 16px 32px rgba(0, 0, 0, 0.25),
                -16px -16px 32px rgba(255, 255, 255, 0.6),
                inset 4px 4px 8px rgba(255, 255, 255, 0.4),
                inset -4px -4px 8px rgba(0, 0, 0, 0.15);
        }
        
        /* SVG Icon styling */
        .logo-clay svg {
            width: 40px;
            height: 40px;
            fill: white;
            filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.2));
        }
        
        .logo-section h1 {
            font-size: 28px;
            color: #2C7873;
            margin-bottom: 5px;
            font-weight: 800;
            text-shadow: 2px 2px 4px rgba(255, 255, 255, 0.8);
        }
        
        .logo-section p {
            color: #5a6c7d;
            font-size: 13px;
            font-weight: 500;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .demo-clay-card {
            margin-top: 20px;
            padding: 18px;
            background: #e8f4f8;
            border-radius: 20px;
            box-shadow: 
                inset 4px 4px 12px rgba(0, 0, 0, 0.08),
                inset -4px -4px 12px rgba(255, 255, 255, 0.9);
        }
        
        .demo-clay-card h3 {
            font-size: 11px;
            color: #2C7873;
            margin-bottom: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .demo-clay-card h3 svg {
            width: 14px;
            height: 14px;
            fill: #2C7873;
        }
        
        .credential-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 10px 14px;
            background: white;
            border-radius: 12px;
            box-shadow: 
                4px 4px 8px rgba(0, 0, 0, 0.06),
                -4px -4px 8px rgba(255, 255, 255, 0.8);
        }
        
        .credential-item:last-child {
            margin-bottom: 0;
        }
        
        .cred-label {
            font-size: 12px;
            color: #6b7280;
            font-weight: 600;
        }
        
        .cred-value {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #2C7873;
            font-weight: 700;
            background: #f0f9ff;
            padding: 5px 10px;
            border-radius: 8px;
            box-shadow: 
                inset 2px 2px 4px rgba(0, 0, 0, 0.06),
                inset -2px -2px 4px rgba(255, 255, 255, 0.9);
        }
        
        /* Responsive adjustments */
        @media (max-height: 700px) {
            .login-container {
                padding: 30px 35px;
            }
            .logo-clay {
                width: 60px;
                height: 60px;
            }
            .logo-clay svg {
                width: 32px;
                height: 32px;
            }
            .logo-section h1 {
                font-size: 24px;
            }
            .form-group {
                margin-bottom: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <div class="logo-section">
                <div class="logo-clay">
                    <!-- Fish/Wave Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12,20L15.46,14H8.54M8.41,13H15.59C17.77,13 19.75,12.34 21.16,11.32C19.75,10.3 17.77,9.64 15.59,9.64H14.57L16.35,6.5C14.76,5.5 11.54,4.91 9.63,6C10.07,6.89 10.53,7.77 11,8.66V9.64H8.41C6.23,9.64 4.25,10.3 2.84,11.32C4.25,12.34 6.23,13 8.41,13M15.59,10.59C17.34,10.59 19,10.97 20.4,11.66C19,12.35 17.34,12.73 15.59,12.73H12.1L13.88,9.59C14.5,9.95 15.1,10.23 15.59,10.59Z"/>
                    </svg>
                </div>
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
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Sign In</button>
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