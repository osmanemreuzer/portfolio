<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header('Location: login.php'); exit; }

require_once '../db/connection.php';

$projects = $conn->query('SELECT * FROM projects ORDER BY created_at DESC')->fetch_all(MYSQLI_ASSOC);
$conn->close();

$success = $_GET['success'] ?? '';
$error   = $_GET['error']   ?? '';
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Projects | Admin</title>
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
      <a href="dashboard.php" class="sidebar-link">📊 Dashboard</a>
      <a href="projects.php"  class="sidebar-link active">🚀 Projects</a>
      <a href="add_project.php" class="sidebar-link">➕ Add Project</a>
      <a href="messages.php"  class="sidebar-link">📬 Messages</a>
    </nav>
    <div class="sidebar-footer">
      <span>👤 <?= htmlspecialchars($_SESSION['admin_user']) ?></span>
      <a href="logout.php" class="btn-logout">Logout</a>
    </div>
  </aside>

  <main class="admin-main">
    <header class="admin-topbar">
      <button class="sidebar-toggle" id="sidebarToggle">☰</button>
      <h2>Projects</h2>
      <div class="topbar-actions">
        <a href="add_project.php" class="btn btn-primary">➕ Add New</a>
        <button class="theme-toggle" id="themeToggle" title="Toggle theme">
          <span class="icon-sun">☀️</span><span class="icon-moon">🌙</span>
        </button>
      </div>
    </header>

    <div class="admin-content">
      <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
      <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="projects-filter" style="margin-bottom:1.5rem;">
        <button class="filter-btn active" data-filter="all">All</button>
        <button class="filter-btn" data-filter="csharp">C#</button>
        <button class="filter-btn" data-filter="aspnet">ASP.NET</button>
        <button class="filter-btn" data-filter="html">HTML/CSS</button>
        <button class="filter-btn" data-filter="javascript">JavaScript</button>
      </div>

      <?php if (empty($projects)): ?>
        <div class="empty-state">
          <p>No projects yet. <a href="add_project.php">Add your first project →</a></p>
        </div>
      <?php else: ?>
      <div class="table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Title</th>
              <th>Tech Stack</th>
              <th>Created</th>
              <th>Links</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($projects as $i => $p): ?>
            <?php
              $lower = strtolower($p['tech_stack'] ?? '');
              $filters = [];
              if (str_contains($lower, 'c#'))                                      $filters[] = 'csharp';
              if (str_contains($lower, 'asp.net') || str_contains($lower, 'aspnet')) $filters[] = 'aspnet';
              if (str_contains($lower, 'javascript') || str_contains($lower, 'js')) $filters[] = 'javascript';
              if (str_contains($lower, 'html') || str_contains($lower, 'css') || str_contains($lower, 'scss')) $filters[] = 'html';
              $filterAttr = implode(' ', $filters) ?: 'all';
            ?>
            <tr data-filter="<?= htmlspecialchars($filterAttr) ?>">
              <td><?= $i + 1 ?></td>
              <td>
                <strong><?= htmlspecialchars($p['title']) ?></strong>
                <small><?= htmlspecialchars(mb_strimwidth($p['description'], 0, 60, '...')) ?></small>
              </td>
              <td>
                <?php foreach (explode(',', $p['tech_stack'] ?? '') as $tech): ?>
                  <span class="tech-tag"><?= htmlspecialchars(trim($tech)) ?></span>
                <?php endforeach; ?>
              </td>
              <td><?= date('M d, Y', strtotime($p['created_at'])) ?></td>
              <td>
                <?php if ($p['github_url']): ?>
                  <a href="<?= htmlspecialchars($p['github_url']) ?>" target="_blank" class="table-link">GitHub</a>
                <?php endif; ?>
                <?php if ($p['live_url']): ?>
                  <a href="<?= htmlspecialchars($p['live_url']) ?>" target="_blank" class="table-link">Live</a>
                <?php endif; ?>
              </td>
              <td>
                <div class="table-actions">
                  <a href="edit_project.php?id=<?= $p['id'] ?>" class="btn-edit">Edit</a>
                  <button class="btn-delete" onclick="confirmDelete(<?= $p['id'] ?>, '<?= htmlspecialchars($p['title'], ENT_QUOTES) ?>')">Delete</button>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </main>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal" style="display:none;">
  <div class="modal-content">
    <h3>Delete Project</h3>
    <p>Are you sure you want to delete <strong id="deleteTitle"></strong>? This cannot be undone.</p>
    <div class="modal-actions">
      <button onclick="closeModal()" class="btn btn-outline">Cancel</button>
      <a id="deleteConfirmLink" href="#" class="btn btn-danger">Delete</a>
    </div>
  </div>
</div>

<script src="../js/admin.js"></script>
<script>
document.querySelectorAll('.projects-filter .filter-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.projects-filter .filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const f = btn.dataset.filter;
    document.querySelectorAll('tbody tr[data-filter]').forEach(row => {
      row.style.display = f === 'all' || row.dataset.filter.includes(f) ? '' : 'none';
    });
  });
});

function confirmDelete(id, title) {
  document.getElementById('deleteTitle').textContent = title;
  document.getElementById('deleteConfirmLink').href = 'delete_project.php?id=' + id;
  document.getElementById('deleteModal').style.display = 'flex';
}
function closeModal() {
  document.getElementById('deleteModal').style.display = 'none';
}
</script>
</body>
</html>
