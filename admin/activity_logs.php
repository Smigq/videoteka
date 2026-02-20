<?php
require_once '../lib/database.php';
require_once '../templates/functions.php';
session_start();

if(!isAdmin()) {
    header("Location: ../index.php");
    exit();
}

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 50;
$offset = ($page - 1) * $per_page;

$filterUser = isset($_GET['user']) ? clean($_GET['user']) : '';
$filterAction = isset($_GET['action']) ? clean($_GET['action']) : '';
$filterDate = isset($_GET['date']) ? clean($_GET['date']) : '';

$query = "SELECT al.*, f.title as film_title 
          FROM activity_logs al 
          LEFT JOIN films f ON al.film_id = f.id 
          WHERE 1=1";

if($filterUser) {
    $query .= " AND al.username LIKE '%$filterUser%'";
}
if($filterAction) {
    $query .= " AND al.action = '$filterAction'";
}
if($filterDate) {
    $query .= " AND DATE(al.created_at) = '$filterDate'";
}

$query .= " ORDER BY al.created_at DESC LIMIT $per_page OFFSET $offset";

$result = $db->query($query);
$logs = [];
while($row = $result->fetch_assoc()) {
    $logs[] = $row;
}

$actionsQuery = "SELECT DISTINCT action FROM activity_logs ORDER BY action";
$actionsResult = $db->query($actionsQuery);
$actions = [];
while($row = $actionsResult->fetch_assoc()) {
    $actions[] = $row['action'];
}

$countQuery = "SELECT COUNT(*) as total FROM activity_logs WHERE 1=1";
if($filterUser) $countQuery .= " AND username LIKE '%$filterUser%'";
if($filterAction) $countQuery .= " AND action = '$filterAction'";
if($filterDate) $countQuery .= " AND DATE(created_at) = '$filterDate'";
$countResult = $db->query($countQuery);
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $per_page);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Activity Logs - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .logs-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .logs-table th, .logs-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #444;
        }
        .logs-table th {
            background: #333;
        }
        .action-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            background: #555;
            color: white;
        }
        .action-LOGIN { background: #4caf50; }
        .action-LOGOUT { background: #ff9800; }
        .action-ADD_FILM { background: #2196f3; }
        .action-DELETE_FILM { background: #f44336; }
        .action-RENT_FILM { background: #9c27b0; }
        .action-RETURN_FILM { background: #00bcd4; }
        .filter-form {
            background: #2a2a2a;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .filter-form input, .filter-form select {
            margin-right: 10px;
        }
        .stats-box {
            background: #2a2a2a;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
            display: flex;
            justify-content: space-around;
        }
        .stat-item {
            text-align: center;
        }
        .stat-number {
            font-size: 32px;
            color: #e50914;
        }
        .stat-label {
            color: #999;
            margin-top: 5px;
        }
    </style>
</head>
<body>
<?php include '../templates/header.php'; ?>

<div class="container" style="margin-top: 80px;">
    <h1>Activity Logs</h1>
        <div class="stats-box">
        <div class="stat-item">
            <div class="stat-number"><?php echo $totalRows; ?></div>
            <div class="stat-label">Total Activities</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">
                <?php 
                $todayCount = $db->query("SELECT COUNT(*) as cnt FROM activity_logs WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['cnt'];
                echo $todayCount;
                ?>
            </div>
            <div class="stat-label">Today's Activities</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">
                <?php 
                $uniqueUsers = $db->query("SELECT COUNT(DISTINCT username) as cnt FROM activity_logs")->fetch_assoc()['cnt'];
                echo $uniqueUsers;
                ?>
            </div>
            <div class="stat-label">Active Users</div>
        </div>
    </div>
    <div class="filter-form">
        <form method="GET">
            <input type="text" name="user" placeholder="Search by username..." value="<?php echo $filterUser; ?>">
            
            <select name="action">
                <option value="">All Actions</option>
                <?php foreach($actions as $action): ?>
                    <option value="<?php echo $action; ?>" <?php echo ($filterAction == $action) ? 'selected' : ''; ?>>
                        <?php echo $action; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <input type="date" name="date" value="<?php echo $filterDate; ?>">
            
            <button type="submit" class="btn">Filter</button>
            <a href="activity_logs.php" class="btn">Clear</a>
        </form>
    </div>
    <table class="logs-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date/Time</th>
                <th>User</th>
                <th>Role</th>
                <th>Action</th>
                <th>Description</th>
                <th>Film</th>
                <th>IP Address</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($logs as $log): ?>
                <tr>
                    <td><?php echo $log['id']; ?></td>
                    <td><?php echo date('d.m.Y H:i:s', strtotime($log['created_at'])); ?></td>
                    <td><?php echo $log['username']; ?></td>
                    <td><?php echo $log['user_role']; ?></td>
                    <td>
                        <span class="action-badge action-<?php echo $log['action']; ?>">
                            <?php echo $log['action']; ?>
                        </span>
                    </td>
                    <td><?php echo $log['description']; ?></td>
                    <td><?php echo $log['film_title'] ?: '-'; ?></td>
                    <td><?php echo $log['ip_address']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if($totalPages > 1): ?>
        <div class="pagination" style="text-align: center; margin: 30px 0;">
            <?php if($page > 1): ?>
                <a href="?page=<?php echo $page-1; ?>&user=<?php echo $filterUser; ?>&action=<?php echo $filterAction; ?>&date=<?php echo $filterDate; ?>" class="btn">← Previous</a>
            <?php endif; ?>
            
            <span style="margin: 0 20px;">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
            
            <?php if($page < $totalPages): ?>
                <a href="?page=<?php echo $page+1; ?>&user=<?php echo $filterUser; ?>&action=<?php echo $filterAction; ?>&date=<?php echo $filterDate; ?>" class="btn">Next →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <div style="margin: 20px 0;">
        <a href="admin.php" class="btn">Back to Admin Panel</a>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
</body>
</html>