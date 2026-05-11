<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header('Location: login.php'); exit; }

require_once '../db/connection.php';

// Mark all as read
$conn->query('UPDATE contacts SET is_read = 1');

$contacts = $conn->query('SELECT * FROM contacts ORDER BY created_at DESC')->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Messages | Admin</title>
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="../css/admin.css" />
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
</head>
<body>
<div class="admin-layout">
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <a href="../index.html" class="nav-logo">OEÜ<span>.</span></a>
      <span class="sidebar-subtitle">Admin Panel</span>
    </div>
    <nav class="sidebar-nav">
      <a href="dashboard.php"   class="sidebar-link">📊 Dashboard</a>
      <a href="projects.php"    class="sidebar-link">🚀 Projects</a>
      <a href="add_project.php" class="sidebar-link">➕ Add Project</a>
      <a href="messages.php"    class="sidebar-link active">📬 Messages</a>
    </nav>
    <div class="sidebar-footer">
      <span>👤 <?= htmlspecialchars($_SESSION['admin_user']) ?></span>
      <a href="logout.php" class="btn-logout">Logout</a>
    </div>
  </aside>

  <main class="admin-main">
    <header class="admin-topbar">
      <button class="sidebar-toggle" id="sidebarToggle">☰</button>
      <h2>Messages</h2>
      <div class="topbar-actions">
        <button class="theme-toggle" id="themeToggle" title="Toggle theme">
          <span class="icon-sun">☀️</span><span class="icon-moon">🌙</span>
        </button>
      </div>
    </header>

    <div class="admin-content">
      <?php if (empty($contacts)): ?>
        <div class="empty-state"><p>No messages yet.</p></div>
      <?php else: ?>
      <div class="messages-list">
        <?php foreach ($contacts as $c): ?>
        <div class="message-card">
          <div class="message-header">
            <div>
              <strong><?= htmlspecialchars($c['name']) ?></strong>
              <a href="mailto:<?= htmlspecialchars($c['email']) ?>" class="table-link">
                <?= htmlspecialchars($c['email']) ?>
              </a>
            </div>
            <span class="message-date"><?= date('M d, Y H:i', strtotime($c['created_at'])) ?></span>
          </div>
          <div class="message-subject"><strong><?= htmlspecialchars($c['subject']) ?></strong></div>
          <div class="message-body"><?= nl2br(htmlspecialchars($c['message'])) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </main>
</div>
<script src="../js/admin.js"></script>
</body>
</html>
