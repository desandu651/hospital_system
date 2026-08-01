<?php
session_start();
include('db.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, username, password FROM app_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($password === $row['password'] || password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Incorrect username or password. Please try again.";
        }
    } else {
        $error = "Incorrect username or password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medi Lanka | Secure Portal</title>
    <!-- Premium Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-blue: #3b82f6; /* Brighter blue for dark mode contrast */
            --primary-hover: #2563eb;
            --bg-color: #0b1121; /* Deep eye-care background */
            --card-bg: #111827; /* Dark card background */
            --input-bg: #1f2937; /* Input field background */
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: #374151;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 2rem;
            position: relative;
            overflow: hidden;
            color: var(--text-main);
        }

        /* Ambient Glow Effect */
        .bg-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            z-index: 0;
            opacity: 0.15;
        }
        .shape-1 {
            top: -10%; left: -10%;
            width: 40vw; height: 40vw;
            background: #3b82f6;
        }
        .shape-2 {
            bottom: -20%; right: -10%;
            width: 30vw; height: 30vw;
            background: #0ea5e9;
        }

        /* Modern Enterprise Container */
        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 1050px;
            background: var(--card-bg);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05);
            display: flex;
            overflow: hidden;
            min-height: 600px;
        }

        /* Left Side - Brand (Darker Blue Blend) */
        .brand-section {
            flex: 1.1;
            background: linear-gradient(145deg, #1e3a8a, #0f172a);
            padding: 4rem;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            border-right: 1px solid rgba(255,255,255,0.05);
        }

        .brand-section::after {
            content: '+';
            position: absolute;
            font-size: 800px;
            font-weight: 900;
            line-height: 1;
            color: rgba(255,255,255,0.02);
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
        }

        .brand-logo {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.8rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            z-index: 1;
        }
        
        .brand-logo i {
            background: rgba(255,255,255,0.1);
            color: #60a5fa;
            padding: 10px;
            border-radius: 12px;
            font-size: 1.4rem;
            backdrop-filter: blur(5px);
        }

        .brand-content { z-index: 1; }

        .brand-content h1 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            font-size: 2.5rem;
            line-height: 1.2;
            margin-bottom: 1.5rem;
        }

        .brand-content p {
            font-size: 1.05rem;
            color: #cbd5e1;
            line-height: 1.6;
        }

        .trust-badges {
            display: flex;
            gap: 1rem;
            margin-top: 3rem;
            z-index: 1;
        }

        .badge-item {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            padding: 0.7rem 1rem;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 500;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border: 1px solid rgba(255,255,255,0.05);
        }

        /* Right Side - Clean Form */
        .form-section {
            flex: 1;
            padding: 4rem;
            background: var(--card-bg);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header { margin-bottom: 2.5rem; }

        .form-header h2 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 0.5rem;
        }

        .form-header p { color: var(--text-muted); font-size: 0.95rem;}

        /* Dark Mode Input Fields */
        .form-group { margin-bottom: 1.5rem; }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #cbd5e1;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-control {
            background-color: var(--input-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 0.8rem 1.25rem;
            height: 54px;
            font-size: 1rem;
            color: var(--text-main);
            transition: all 0.2s ease;
            box-shadow: none;
        }

        .form-control:focus {
            background-color: var(--input-bg);
            border-color: var(--primary-blue);
            color: white;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        }
        
        .form-control::placeholder { color: #64748b; }

        /* Button */
        .btn-primary-custom {
            width: 100%;
            background: var(--primary-blue);
            color: white;
            border: none;
            border-radius: 10px;
            height: 54px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.05rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .btn-primary-custom:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
        }

        /* Custom Checkbox */
        .form-check-input {
            background-color: var(--input-bg);
            border-color: var(--border-color);
        }
        .form-check-input:checked {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
        }

        .alert-custom {
            background-color: rgba(220, 38, 38, 0.1);
            border: 1px solid rgba(220, 38, 38, 0.3);
            color: #ef4444;
            border-radius: 10px;
            padding: 1rem;
            font-size: 0.95rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        @media (max-width: 992px) {
            .login-wrapper { flex-direction: column; }
            .brand-section { padding: 3rem 2rem; flex: none; border-right: none; border-bottom: 1px solid rgba(255,255,255,0.05); }
            .form-section { padding: 3rem 2rem; }
            .brand-content h1 { font-size: 2rem; }
        }
    </style>
</head>
<body>

    <!-- Ambient Glow Background -->
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>

    <div class="login-wrapper">
        
        <!-- Left Panel -->
        <div class="brand-section">
            <div class="brand-logo">
                <i class="bi bi-hospital"></i>
                Medi Lanka
            </div>

            <div class="brand-content">
                <h1>Next-Generation<br>Healthcare Intelligence.</h1>
                <p>Empowering medical professionals with secure, real-time data and advanced patient care management systems.</p>
                
                <div class="trust-badges">
                    <div class="badge-item">
                        <i class="bi bi-shield-check"></i> HIPAA Compliant
                    </div>
                    <div class="badge-item">
                        <i class="bi bi-lock"></i> 256-bit Encrypted
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="form-section">
            <div class="form-header">
                <h2>Welcome Back</h2>
                <p>Please enter your credentials to access the portal.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-custom mb-4" role="alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                
                <div class="form-group">
                    <label for="username" class="form-label">Staff ID / Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="e.g. admin" required autocomplete="off">
                </div>

                <div class="form-group mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4 px-1">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="rememberMe">
                        <label class="form-check-label" for="rememberMe" style="color: var(--text-muted); font-size: 0.9rem; font-weight: 500;">
                            Remember me
                        </label>
                    </div>
                    <a href="#" class="text-decoration-none" style="color: #60a5fa; font-size: 0.9rem; font-weight: 500;">Forgot password?</a>
                </div>

                <button type="submit" class="btn-primary-custom">
                    Sign In <i class="bi bi-arrow-right-short fs-4"></i>
                </button>
            </form>

        </div>
    </div>

</body>
</html>