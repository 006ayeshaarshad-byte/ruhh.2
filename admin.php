
<?php
session_start();

// Only admins allowed
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once "config.php";

$success = '';
$error   = '';

// ── DELETE USER ──────────────────────────────────────────────────────────────
if (isset($_POST['delete_user'])) {
    $uid = intval($_POST['user_id']);
    if ($uid === $_SESSION['user_id']) {
        $error = "You cannot delete your own admin account.";
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $success = "User deleted successfully.";
    }
}

// ── DELETE POST ──────────────────────────────────────────────────────────────
if (isset($_POST['delete_post'])) {
    $pid  = intval($_POST['post_id']);
    $stmt = $conn->prepare("DELETE FROM community_posts WHERE id = ?");
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $success = "Post deleted successfully.";
}

// // ── FETCH USERS ──────────────────────────────────────────────────────────────
// $users_result = $conn->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
// $users = $users_result->fetch_all(MYSQLI_ASSOC);
$users_result = $conn->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");

if (!$users_result) {
    die("SQL Error: " . $conn->error);
}

$users = $users_result->fetch_all(MYSQLI_ASSOC);

// ── FETCH POSTS ──────────────────────────────────────────────────────────────
$posts_result = $conn->query("SELECT cp.id, cp.content, cp.created_at, u.name as author_name
                               FROM community_posts cp
                               JOIN users u ON u.id = cp.user_id
                               ORDER BY cp.created_at DESC");
$posts = $posts_result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <title>Admin Panel — Ruhh</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<!-- LOGO -->
<a href="landing.html" class="logo"><h1>Ruhh<span>.</span></h1></a>

<!-- LOGOUT -->
<a href="logout.php" class="logout-btn" title="Logout">
    <i class="fas fa-sign-out-alt"></i>
</a>

<div class="admin-wrapper">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <h1 class="page-title">Admin Panel</h1>
        <p class="subtitle">Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?> 💜</p>
    </div>

    <!-- STATS ROW -->
    <div class="stats-row">
        <div class="stat-card">
            <i class="fa-solid fa-users"></i>
            <div>
                <span class="stat-num"><?php echo count($users); ?></span>
                <span class="stat-label">Total Users</span>
            </div>
        </div>
        <div class="stat-card">
            <i class="fa-solid fa-comment-dots"></i>
            <div>
                <span class="stat-num"><?php echo count($posts); ?></span>
                <span class="stat-label">Community Posts</span>
            </div>
        </div>
        <div class="stat-card">
            <i class="fa-solid fa-shield-halved"></i>
            <div>
                <span class="stat-num"><?php echo count(array_filter($users, fn($u) => $u['role'] === 'admin')); ?></span>
                <span class="stat-label">Admins</span>
            </div>
        </div>
    </div>

    <!-- MESSAGES -->
    <?php if ($success): ?>
        <div class="alert success"><i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert error"><i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- TABS -->
    <div class="tabs">
        <button class="tab-btn active" onclick="switchTab('users', this)">
            <i class="fa-solid fa-users"></i> Users
        </button>
        <button class="tab-btn" onclick="switchTab('posts', this)">
            <i class="fa-solid fa-comment-dots"></i> Community Posts
        </button>
    </div>

    <!-- ── USERS TAB ── -->
    <div id="tab-users" class="tab-content active">
        <div class="section-card">
            <div class="section-header">
                <h2><i class="fa-solid fa-users"></i> All Users</h2>
                <span class="count-badge"><?php echo count($users); ?></span>
            </div>

            <?php if (empty($users)): ?>
                <div class="empty-state">
                    <i class="fa-solid fa-user-slash"></i>
                    <p>No users found.</p>
                </div>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Joined</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $i => $user): ?>
                            <tr class="<?php echo $user['role'] === 'admin' ? 'admin-row' : ''; ?>">
                                <td><?php echo $i + 1; ?></td>
                                <td>
                                    <div class="user-name">
                                        <div class="avatar"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></div>
                                        <?php echo htmlspecialchars($user['name']); ?>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="role-badge <?php echo $user['role']; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                    <form method="POST" onsubmit="return confirmDelete('user', '<?php echo htmlspecialchars($user['name']); ?>')">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="delete_user" class="delete-btn">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                    <?php else: ?>
                                        <span class="you-badge">You</span>
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

    <!-- ── POSTS TAB ── -->
    <div id="tab-posts" class="tab-content">
        <div class="section-card">
            <div class="section-header">
                <h2><i class="fa-solid fa-comment-dots"></i> Community Posts</h2>
                <span class="count-badge"><?php echo count($posts); ?></span>
            </div>

            <?php if (empty($posts)): ?>
                <div class="empty-state">
                    <i class="fa-solid fa-comment-slash"></i>
                    <p>No posts yet.</p>
                </div>
            <?php else: ?>
                <div class="posts-list">
                    <?php foreach ($posts as $post): ?>
                    <div class="post-card">
                        <div class="post-meta">
                            <div class="post-author">
                                <i class="fa-solid fa-user-secret"></i>
                                <span>Posted by <?php echo htmlspecialchars($post['author_name']); ?></span>
                            </div>
                            <div class="post-right">
                                <span class="post-date">
                                    <i class="fa-regular fa-calendar"></i>
                                    <?php echo date('d M Y', strtotime($post['created_at'])); ?>
                                </span>
                                <form method="POST" onsubmit="return confirmDelete('post', '')">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" name="delete_post" class="delete-btn">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="post-content"><?php echo htmlspecialchars($post['content']); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div><!-- end admin-wrapper -->

<script>
function switchTab(tab, btn) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    btn.classList.add('active');
}

function confirmDelete(type, name) {
    const msg = type === 'user'
        ? `Delete user "${name}" and all their data? This cannot be undone.`
        : `Delete this post? This cannot be undone.`;
    return confirm(msg);
}
</script>
</body>
</html>
