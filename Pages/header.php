<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get user data if logged in
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LoveConnect</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Navbar Styles */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            background-color: #ffffff;
            color: #333333;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar-brand {
            font-size: 24px;
            font-weight: 600;
            color: #ff4757;
        }

        /* User Menu Styles */
        .user-menu {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ff4757;
        }

        .username {
            font-weight: 500;
            font-size: 16px;
        }

        .dropdown-arrow {
            font-size: 12px;
            transition: transform 0.3s ease;
        }

        .user-menu:hover .dropdown-arrow {
            transform: rotate(180deg);
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 10px 0;
            min-width: 180px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
            z-index: 1001;
        }

        .user-menu:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-menu a {
            display: block;
            padding: 10px 20px;
            color: #333333;
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .dropdown-menu a:hover {
            background-color: #f8f9fa;
            color: #ff4757;
        }

        /* Auth Buttons */
        .auth-buttons {
            display: flex;
            gap: 12px;
        }

        .btn-login,
        .btn-register {
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .btn-login {
            color: #ff4757;
            border: 1px solid #ff4757;
        }

        .btn-register {
            background-color: #ff4757;
            color: #fff;
            border: 1px solid #ff4757;
        }

        .btn-login:hover {
            background-color: #ff4757;
            color: white;
        }

        .btn-register:hover {
            background-color: #e84141;
            border-color: #e84141;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .navbar {
                padding: 12px 20px;
            }

            .navbar-brand {
                font-size: 20px;
            }

            .username {
                font-size: 14px;
            }

            .dropdown-menu {
                min-width: 160px;
            }

            .btn-login,
            .btn-register {
                padding: 6px 12px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-brand">LoveConnect</div>

        <?php ($user) ?>
        <div class="user-menu">
            <img src="<?= htmlspecialchars($user['profile_photo'] ?? 'https://randomuser.me/api/portraits/men/1.jpg') ?>" class="user-avatar" alt="Profile Photo">
            <span class="username"><?= htmlspecialchars($user['username']) ?></span>
            <i class="fas fa-chevron-down dropdown-arrow"></i>
            <div class="dropdown-menu">
                <a href="profile.php">My Profile</a>
                <a href="settings.php">Settings</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>
