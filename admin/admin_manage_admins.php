<?php
session_start();
require_once dirname(__DIR__) . '/config/db_connection.php';
require_once dirname(__DIR__) . '/includes/auth.php';

requireLogin();
$user = getCurrentUser();

if (!isset($user['admin_role']) || $user['admin_role'] !== 'super_admin') {
    header("Location: admin_dashboard.php");
    exit();
}

// Handle promote/demote
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $member_id = (int)($_POST['member_id'] ?? 0);
    
    if ($action === 'promote') {
        $update = "UPDATE YouthMember SET admin_role = 'admin' WHERE member_id = ? AND admin_role = 'none'";
        $stmt = mysqli_prepare($conn, $update);
        mysqli_stmt_bind_param($stmt, 'i', $member_id);
        mysqli_stmt_execute($stmt);
    } elseif ($action === 'demote') {
        $update = "UPDATE YouthMember SET admin_role = 'none' WHERE member_id = ? AND admin_role = 'admin'";
        $stmt = mysqli_prepare($conn, $update);
        mysqli_stmt_bind_param($stmt, 'i', $member_id);
        mysqli_stmt_execute($stmt);
    }
}

// Get all users
$users = [];
$users_result = mysqli_query($conn, "SELECT member_id, full_name, username, email, admin_role FROM YouthMember ORDER BY admin_role DESC, member_id DESC");
while ($row = mysqli_fetch_assoc($users_result)) {
    $users[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Manage Admins - InnerCanvas</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', Times, serif; background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); }
        header { background: linear-gradient(135deg, #16a085 0%, #138d75 100%); color: white; padding: 20px; }
        .header-content { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .nav-links { display: flex; gap: 15px; }
        .nav-links a { color: white; text-decoration: none; font-weight: 600; padding: 8px 14px; border-radius: 5px; }
        .nav-links a:hover { background: rgba(255,255,255,0.2); }
        .logout-btn { background: rgba(255,255,255,0.3); }
        .container { max-width: 1000px; margin: 30px auto; padding: 0 20px; }
        .section { background: white; padding: 25px; border-radius: 10px; }
        .section-title { font-size: 18px; font-weight: 700; color: #2c3e50; margin-bottom: 20px; border-bottom: 2px solid #ecf0f1; padding-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f9fa; padding: 12px; text-align: left; font-weight: 700; color: #2c3e50; border-bottom: 2px solid #ecf0f1; }
        td { padding: 12px; border-bottom: 1px solid #ecf0f1; }
        tr:hover { background: #f8f9fa; }
        .role-badge { display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600; }
        .role-super { background: #9b59b6; color: white; }
        .role-admin { background: #3498db; color: white; }
        .role-none { background: #95a5a6; color: white; }
        .action-btns { display: flex; gap: 8px; }
        .btn { padding: 6px 12px; border: none; border-radius: 4px; font-size: 11px; font-weight: 600; cursor: pointer; }
        .btn-promote { background: #2ecc71; color: white; }
        .btn-promote:hover { background: #27ae60; }
        .btn-demote { background: #e74c3c; color: white; }
        .btn-demote:hover { background: #c0392b; }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .super-admin-note { background: #ecf9f7; padding: 12px; border-radius: 6px; margin-top: 8px; font-size: 12px; color: #2c3e50; }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <h1>🌿 InnerCanvas Admin</h1>
            <div class="nav-links">
                <a href="admin_dashboard.php">Dashboard</a>
                <a href="admin_manage_admins.php">Manage Admins</a>
               <a href="/InnerCanvas/config/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <div class="section">
            <div class="section-title">🔑 Administrator Management</div>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($u['username']); ?></td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td>
                                <?php if ($u['admin_role'] === 'super_admin'): ?>
                                    <span class="role-badge role-super">Super Admin</span>
                                <?php elseif ($u['admin_role'] === 'admin'): ?>
                                    <span class="role-badge role-admin">Admin</span>
                                <?php else: ?>
                                    <span class="role-badge role-none">Member</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <?php if ($u['admin_role'] === 'none'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="promote">
                                            <input type="hidden" name="member_id" value="<?php echo $u['member_id']; ?>">
                                            <button type="submit" class="btn btn-promote">Promote to Admin</button>
                                        </form>
                                    <?php elseif ($u['admin_role'] === 'admin'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="demote">
                                            <input type="hidden" name="member_id" value="<?php echo $u['member_id']; ?>">
                                            <button type="submit" class="btn btn-demote">Demote to Member</button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn" disabled>—</button>
                                    <?php endif; ?>
                                </div>
                                <?php if ($u['admin_role'] === 'super_admin'): ?>
                                    <div class="super-admin-note">Super Admin (cannot be demoted)</div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>