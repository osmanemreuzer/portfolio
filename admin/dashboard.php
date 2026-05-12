<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../db/connection.php';

$projectCount = $conn->query('SELECT COUNT(*) as c FROM projects')->fetch_assoc()['c'];
$contactCount = $conn->query('SELECT COUNT(*) as c FROM contacts')->fetch_assoc()['c'];
$unreadCount  = $conn->query('SELECT COUNT(*) as c FROM contacts WHERE is_read = 0')->fetch_assoc()['c'];

$contacts = $conn->query(
    'SELECT * FROM contacts ORDER BY created_at DESC LIMIT 10'
)->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard | Portfolio</title>
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="../css/admin.css" />
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
</head>
<body>
  <div class="admin-layout">

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <a href="../index.html" class="nav-logo">OEÜ<span>.</span></a>
        <span class="sidebar-subtitle">Admin Panel</span>
      </div>
      <nav class="sidebar-nav">
        <a href="dashboard.php" class="sidebar-link active">📊 Dashboard</a>
        <a href="projects.php" class="sidebar-link">🚀 Projects</a>
        <a href="add_project.php" class="sidebar-link">➕ Add Project</a>
        <a href="messages.php" class="sidebar-link">
          📬 Messages
          <?php if ($unreadCount > 0): ?>
            <span class="badge"><?= $unreadCount ?></span>
          <?php endif; ?>
        </a>
      </nav>
      <div class="sidebar-footer">
        <span>👤 <?= htmlspecialchars($_SESSION['admin_user']) ?></span>
        <a href="logout.php" class="btn-logout">Logout</a>
      </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="admin-main">
      <header class="admin-topbar">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">☰</button>
        <h2>Dashboard</h2>
        <div class="topbar-actions">
          <button class="theme-toggle" id="themeToggle" title="Toggle theme">
            <span class="icon-sun">☀️</span>
            <span class="icon-moon">🌙</span>
          </button>
        </div>
      </header>

      <div class="admin-content">

        <!-- STATS CARDS -->
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon">🚀</div>
            <div class="stat-info">
              <span class="stat-num"><?= $projectCount ?></span>
              <span class="stat-label">Total Projects</span>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">📬</div>
            <div class="stat-info">
              <span class="stat-num"><?= $contactCount ?></span>
              <span class="stat-label">Total Messages</span>
            </div>
          </div>
          <div class="stat-card accent">
            <div class="stat-icon">🔔</div>
            <div class="stat-info">
              <span class="stat-num"><?= $unreadCount ?></span>
              <span class="stat-label">Unread Messages</span>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon">👁️</div>
            <div class="stat-info">
              <span class="stat-num">∞</span>
              <span class="stat-label">Portfolio Views</span>
            </div>
          </div>
        </div>

        <!-- QUICK ACTIONS -->
        <div class="admin-section">
          <h3>Quick Actions</h3>
          <div class="quick-actions">
            <a href="add_project.php" class="btn btn-primary">➕ Add New Project</a>
            <a href="projects.php"    class="btn btn-outline">🚀 Manage Projects</a>
            <a href="messages.php"    class="btn btn-outline">📬 View Messages</a>
            <a href="../index.html" target="_blank" class="btn btn-outline">🌐 View Portfolio</a>
          </div>
        </div>

        <!-- RECENT MESSAGES TABLE -->
        <div class="admin-section">
          <div class="section-header-row">
            <h3>Recent Messages</h3>
            <a href="messages.php" class="view-all">View all →</a>
          </div>
          <?php if (empty($contacts)): ?>
            <p class="empty-msg">No messages yet.</p>
          <?php else: ?>
          <div class="table-wrapper">
            <table class="admin-table">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Subject</th>
                  <th>Date</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($contacts as $c): ?>
                <tr>
                  <td><?= htmlspecialchars($c['name']) ?></td>
                  <td><?= htmlspecialchars($c['email']) ?></td>
                  <td><?= htmlspecialchars(mb_strimwidth($c['subject'], 0, 40, '...')) ?></td>
                  <td><?= htmlspecialchars(date('M d, Y', strtotime($c['created_at']))) ?></td>
                  <td>
                    <?php if ($c['is_read']): ?>
                      <span class="badge-success">Read</span>
                    <?php else: ?>
                      <span class="badge-warning">New</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php endif; ?>
        </div>

      </div>
    </main>
  </div>

  <script src="../js/admin.js"></script>
</body>
</html>
