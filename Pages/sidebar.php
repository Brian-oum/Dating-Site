<?php
function getProfilePhoto($photo, $size = 'normal') {
    $classes = [
        'normal' => ['container' => 'user-avatar', 'icon' => 'fa-2x'],
        'small' => ['container' => 'sidebar-user-avatar profile-icon', 'icon' => 'fa-lg'],
        'message' => ['container' => 'message-avatar profile-icon', 'icon' => 'fa-2x']
    ];
    
    $sizeClass = $classes[$size] ?? $classes['normal'];
    
    if (empty($photo)) {
        return '<div class="'.$sizeClass['container'].'"><i class="fas fa-user-circle '.$sizeClass['icon'].'"></i></div>';
    } else {
        return '<img src="'.htmlspecialchars($photo).'" alt="Profile Photo" class="'.$sizeClass['container'].'">';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
/* Sidebar specific styles */
.sidebar {
  width: 250px;
  height: 100vh;
  background: linear-gradient(135deg, var(--primary), #3a0ca3);
  color: white;
  position: fixed;
  top: 0;
  left: 0;
  transition: all 0.3s ease;
  z-index: 1000;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

.sidebar.collapsed {
  width: 70px;
}

.sidebar-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 15px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-brand {
  display: flex;
  align-items: center;
  font-size: 1.2rem;
  font-weight: 600;
  white-space: nowrap;
}

.sidebar.collapsed .sidebar-brand span {
  display: none;
}

.sidebar-brand i {
  margin-right: 10px;
  font-size: 1.5rem;
}

.sidebar.collapsed .sidebar-brand i {
  margin-right: 0;
  font-size: 1.8rem;
}

.toggle-btn {
  background: none;
  border: none;
  color: white;
  font-size: 1.2rem;
  cursor: pointer;
  padding: 5px;
  border-radius: 4px;
  transition: all 0.3s;
}

.toggle-btn:hover {
  background: rgba(255, 255, 255, 0.1);
}

.sidebar-menu {
  padding: 15px 0;
  list-style: none;
}

.menu-item {
  display: flex;
  align-items: center;
  padding: 12px 15px;
  color: white;
  text-decoration: none;
  transition: all 0.3s;
  border-left: 3px solid transparent;
  white-space: nowrap;
  position: relative;
}

.menu-item:hover, 
.menu-item.active {
  background: rgba(255, 255, 255, 0.1);
  border-left: 3px solid white;
}

.menu-item i {
  margin-right: 10px;
  font-size: 1.1rem;
  width: 20px;
  text-align: center;
}

.sidebar.collapsed .menu-item span {
  display: none;
}

.sidebar.collapsed .menu-item {
  justify-content: center;
  padding: 15px 0;
}

.sidebar.collapsed .menu-item i {
  margin-right: 0;
  font-size: 1.3rem;
}

.unread-badge {
  background: #4361ee;
  color: white;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.7em;
  margin-left: 10px;
}

/* Hamburger icon */
.hamburger {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  width: 24px;
  height: 18px;
  cursor: pointer;
}

.hamburger-line {
  width: 100%;
  height: 2px;
  background-color: white;
  transition: all 0.3s ease;
}

/* Responsive styles */
@media (max-width: 992px) {
  .sidebar {
    width: 70px;
  }
  
  .sidebar-header, 
  .sidebar-brand span, 
  .menu-item span {
    display: none;
  }
  
  .sidebar-menu li a {
    justify-content: center;
    padding: 1rem 0;
  }
}

@media (max-width: 768px) {
  .sidebar {
    width: 100%;
    height: auto;
    position: relative;
    display: flex;
    padding: 0;
  }
  
  .sidebar-header {
    display: none;
  }
  
  .sidebar-menu {
    display: flex;
    width: 100%;
    padding: 0;
  }
  
  .sidebar-menu li {
    flex: 1;
    text-align: center;
  }
  
  .sidebar-menu li a {
    flex-direction: column;
    padding: 0.75rem 0.5rem;
    font-size: 0.8rem;
    border-left: none;
    border-bottom: 3px solid transparent;
  }
  
  .sidebar-menu li a:hover, 
  .sidebar-menu li a.active {
    border-left: none;
    border-bottom: 3px solid white;
  }
  
  .sidebar-menu li a i {
    font-size: 1.2rem;
    margin-bottom: 0.25rem;
  }
}
</style>
</head>
<body>
    
<div class="sidebar">
  <div class="sidebar-header">
    <div class="sidebar-brand">
      <i class="fas fa-heart"></i>
      <span>LoveConnect</span>
    </div>
    <button class="toggle-btn" id="toggleSidebar">
      <div class="hamburger">
        <div class="hamburger-line"></div>
        <div class="hamburger-line"></div>
        <div class="hamburger-line"></div>
      </div>
    </button>
  </div>
  
  <div class="sidebar-menu">
    <a href="dashboard.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
      <i class="fas fa-home"></i>
      <span>Dashboard</span>
    </a>
    <a href="matches.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'matches.php' ? 'active' : '' ?>">
      <i class="fas fa-heart"></i>
      <span>Matches</span>
    </a>
    <a href="messages.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : '' ?>">
      <i class="fas fa-envelope"></i>
      <span>Messages</span>
      <?php if (isset($stats['unread_messages']) && $stats['unread_messages'] > 0): ?>
        <span class="unread-badge"><?= $stats['unread_messages'] ?></span>
      <?php endif; ?>
    </a>
    <a href="discover.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'discover.php' ? 'active' : '' ?>">
      <i class="fas fa-search"></i>
      <span>Discover</span>
    </a>
    <a href="view_profile.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'view_profile.php' ? 'active' : '' ?>">
      <i class="fas fa-user"></i>
      <span>My Profile</span>
    </a>
    <a href="settings.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : '' ?>">
      <i class="fas fa-cog"></i>
      <span>Settings</span>
    </a>
  </div>
</div>
<script>
// Sidebar toggle functionality
document.addEventListener('DOMContentLoaded', function() {
  const toggleBtn = document.getElementById('toggleSidebar');
  if (toggleBtn) {
    toggleBtn.addEventListener('click', function() {
      document.querySelector('.sidebar').classList.toggle('collapsed');
      
      // Store sidebar state in localStorage
      const isCollapsed = document.querySelector('.sidebar').classList.contains('collapsed');
      localStorage.setItem('sidebarCollapsed', isCollapsed);
    });
    
    // Check for saved sidebar state
    if (localStorage.getItem('sidebarCollapsed') === 'true') {
      document.querySelector('.sidebar').classList.add('collapsed');
    }
  }
});
</script>
</body>
</html>