<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header('Location: login.php'); exit; }

require_once '../db/connection.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: projects.php'); exit; }

$errors = [];

$stmt = $conn->prepare('SELECT * FROM projects WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$project) {
    header('Location: projects.php?error=' . urlencode('Project not found.'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title']       ?? '');
    $description = trim($_POST['description'] ?? '');
    $tech_stack  = trim($_POST['tech_stack']  ?? '');
    $github_url  = trim($_POST['github_url']  ?? '');
    $live_url    = trim($_POST['live_url']    ?? '');
    $image_url   = trim($_POST['image_url']   ?? '');

    if (strlen($title) < 2)        $errors[] = 'Title is required.';
    if (strlen($description) < 10) $errors[] = 'Description must be at least 10 characters.';
    if (!empty($github_url) && !filter_var($github_url, FILTER_VALIDATE_URL)) $errors[] = 'Invalid GitHub URL.';
    if (!empty($live_url)   && !filter_var($live_url,   FILTER_VALIDATE_URL)) $errors[] = 'Invalid Live URL.';

    if (empty($errors)) {
        $stmt = $conn->prepare(
            'UPDATE projects SET title=?, description=?, tech_stack=?, image_url=?, github_url=?, live_url=? WHERE id=?'
        );
        $stmt->bind_param('ssssssi', $title, $description, $tech_stack, $image_url, $github_url, $live_url, $id);
        if ($stmt->execute()) {
            header('Location: projects.php?success=' . urlencode('Project updated successfully!'));
            exit;
        } else {
            $errors[] = 'Database error. Please try again.';
        }
        $stmt->close();
    }
    // Repopulate from POST on error
    $project = array_merge($project, compact('title','description','tech_stack','github_url','live_url','image_url'));
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Project | Admin</title>
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
      <a href="projects.php"    class="sidebar-link active">🚀 Projects</a>
      <a href="add_project.php" class="sidebar-link">➕ Add Project</a>
      <a href="messages.php"    class="sidebar-link">📬 Messages</a>
    </nav>
    <div class="sidebar-footer">
      <span>👤 <?= htmlspecialchars($_SESSION['admin_user']) ?></span>
      <a href="logout.php" class="btn-logout">Logout</a>
    </div>
  </aside>

  <main class="admin-main">
    <header class="admin-topbar">
      <button class="sidebar-toggle" id="sidebarToggle">☰</button>
      <h2>Edit Project</h2>
      <div class="topbar-actions">
        <a href="projects.php" class="btn btn-outline">← Back</a>
        <button class="theme-toggle" id="themeToggle" title="Toggle theme">
          <span class="icon-sun">☀️</span><span class="icon-moon">🌙</span>
        </button>
      </div>
    </header>

    <div class="admin-content">
      <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
          <ul style="margin:0;padding-left:20px;">
            <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="form-card">
        <form method="POST" action="" class="admin-form">
          <div class="form-group">
            <label for="title">Project Title <span class="required">*</span></label>
            <input type="text" id="title" name="title" required
                   value="<?= htmlspecialchars($project['title']) ?>" />
          </div>
          <div class="form-group">
            <label for="description">Description <span class="required">*</span></label>
            <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($project['description']) ?></textarea>
          </div>
          <div class="form-group">
            <label for="tech_stack">Tech Stack</label>
            <input type="text" id="tech_stack" name="tech_stack"
                   value="<?= htmlspecialchars($project['tech_stack'] ?? '') ?>"
                   placeholder="C#, ASP.NET Core, HTML, CSS, JavaScript" />
            <small class="help-text">Separate technologies with commas</small>
          </div>
          <div class="form-row-2">
            <div class="form-group">
              <label for="github_url">GitHub URL</label>
              <input type="url" id="github_url" name="github_url"
                     value="<?= htmlspecialchars($project['github_url'] ?? '') ?>" />
            </div>
            <div class="form-group">
              <label for="live_url">Live Demo URL</label>
              <input type="url" id="live_url" name="live_url"
                     value="<?= htmlspecialchars($project['live_url'] ?? '') ?>" />
            </div>
          </div>
          <div class="form-group">
            <label for="image_url">Image URL</label>
            <input type="url" id="image_url" name="image_url"
                   value="<?= htmlspecialchars($project['image_url'] ?? '') ?>" />
          </div>
          <div class="form-actions">
            <a href="projects.php" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Project</button>
          </div>
        </form>
      </div>
    </div>
  </main>
</div>
<script src="../js/admin.js"></script>
</body>
</html>
