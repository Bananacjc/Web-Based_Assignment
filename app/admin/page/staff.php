<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="../css/staff.css" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management</title>
</head>

<body>
    <?php
    include 'adminHeader.php';
    require_once '../lib/SimplePager.php'; // Include the SimplePager class

    // Fields for sorting and displaying
    $fields = [
        '',
        'employee_id' => 'Employee ID',
        'employee_name' => 'Employee Name',
        'email' => 'Email',
        'role' => 'Role',
        'profile_image' => 'Profile Image',
        'Action'
    ];

    // Retrieve inputs with validation
    $search = req('search'); // Search term for employee name, ID, or email
    $role = req('role'); // Role filter
    $sort = req('sort');
    $dir = req('dir');
    $page = req('page', 1); // Current page
    $limit = 10; // Number of items per page
    $offset = ($page - 1) * $limit;
    $bannedFilter = req('banned_filter'); // Retrieve the banned filter value
    $bannedFilter = in_array($bannedFilter, ['0', '1', 'all']) ? $bannedFilter : 'all'; // Default to 'all'

    // Validate sorting field and direction
    $sort = array_key_exists($sort, $fields) ? $sort : 'employee_id';
    $dir = in_array($dir, ['asc', 'desc']) ? $dir : 'asc';

    // Build the WHERE clause and parameters for filtering
    $whereClause = "WHERE (employee_name LIKE ? OR employee_id LIKE ? OR email LIKE ?)";
    $params = ["%$search%", "%$search%", "%$search%"];

    if ($role) {
        $whereClause .= " AND role = ?";
        $params[] = $role;
    }
    if ($bannedFilter !== 'all') {
        $whereClause .= " AND banned = ?";
        $params[] = $bannedFilter;
    }

    // Count total records for pagination
    $totalEmployeesQuery = "SELECT COUNT(*) FROM employees $whereClause";
    $totalEmployeesStmt = $_db->prepare($totalEmployeesQuery);
    $totalEmployeesStmt->execute($params);
    $total_employees = $totalEmployeesStmt->fetchColumn();
    $totalPages = ceil($total_employees / $limit);

    // Fetch employee records for the current page
    $query = "
    SELECT employee_id, employee_name, email,password, role, profile_image, banned 
    FROM employees
    $whereClause
    ORDER BY $sort $dir
    LIMIT $limit OFFSET $offset
";
    $stm = $_db->prepare($query);
    $stm->execute($params);
    $employees = $stm->fetchAll();

    // Fetch available roles for filtering
    $rolesQuery = "SELECT DISTINCT role FROM employees";
    $roles = $_db->query($rolesQuery)->fetchAll(PDO::FETCH_COLUMN);

    // Display the employees and the filters
    ?>
    <div class="main">
        <h1>STAFF MANAGEMENT</h1>

        <!-- Search and Filter Form -->
        <form method="get">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" id="search" placeholder="Search by name, ID, or email">
            <select name="role">
                <option value="">Select Role</option>
                <?php foreach ($roles as $r): ?>
                    <option value="<?= $r ?>" <?= $role == $r ? 'selected' : '' ?>><?= $r ?></option>
                <?php endforeach; ?>
            </select>
            <select name="banned_filter" id="bannedFilter">
                <option value="all" <?= $bannedFilter === 'all' ? 'selected' : '' ?>>All</option>
                <option value="0" <?= $bannedFilter === '0' ? 'selected' : '' ?>>Active</option>
                <option value="1" <?= $bannedFilter === '1' ? 'selected' : '' ?>>Blocked</option>
            </select>
            <button type="submit">Search</button>
        </form>

        <!-- Manager Actions -->
        <?php if ($_user?->role === 'MANAGER'): ?>
            <form method="post" id="f">
                <button formaction="deleteStaff.php" onclick="return confirm('Are you sure you want to delete selected employees?')">Delete</button>
            </form>
        <?php endif; ?>

        <p><?= count($employees) ?> employee(s) on this page | Total: <?= $total_employees ?> employee(s)</p>

        <!-- Employees Table -->
        <table id="staffTable" class="data-table">
            <thead>
                <tr>
                    <?= table_headers($fields, $sort, $dir) ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $e): ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="id[]" value="<?= $e->employee_id ?>" form="f">
                        </td>
                        <td><?= $e->employee_id ?></td>
                        <td><?= $e->employee_name ?></td>
                        <td><?= $e->email ?></td>
                        <td><?= $e->role ?></td>
                        <td>
                            <img src="../uploads/profile_images/<?= $e->profile_image ?>" class="resized-image" alt="Profile Image">
                        </td>
                        <td>
                            <button class="button action-button" onclick="showUpdateEmployeeForm(
    '<?= $e->employee_id  ?>',
    '<?= $e->employee_name ?>',
    '<?= $e->email ?>',
    '<?= $e->role ?>'
)">Update</button>


                            <form action="deleteStaff.php" method="post" style="display: inline;">
                                <input type="hidden" name="id" value="<?= $e->employee_id ?>">
                                <button type="submit" class="button delete-action-button" onclick="return confirm('Are you sure you want to delete this employee?')">Delete</button>
                            </form>
                            <?php if ($e->banned == 0): ?>
                                <form action="banStaff.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="employee_id" value="<?= $e->employee_id ?>">
                                    <button type="submit" class="button ban-action-button" onclick="confirmBlock()">Ban</button>
                                </form>
                            <?php else: ?>
                                <form action="unbanStaff.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="employee_id" value="<?= $e->employee_id ?>">
                                    <button type="submit" class="button unban-action-button" onclick="confirmUnblock()">Unban</button>
                                </form>
                            <?php endif; ?>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=1" class="first-page">First</a>
                <a href="?page=<?= $page - 1 ?>" class="prev-page">Prev</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" class="page-number <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>" class="next-page">Next</a>
                <a href="?page=<?= $totalPages ?>" class="last-page">Last</a>
            <?php endif; ?>
        </div>


            <div style="margin: 30px;">
                <button id="addStaffBtn" class="action-button" onclick="showAddForm()">Add New Staff</button>
            </div>

            <div id="addStaffModal" class="modal" style="margin-top: 80px;">
                <div class="modal-content">
                    <span class="close-button" onclick="hideAddForm()">&times;</span>
                    <form id="addForm" action="addStaff.php" method="POST" enctype="multipart/form-data" class="add-form">
                        <h2>Add New Employee</h2>

                        <label for="employee_name">Employee Name:</label>
                        <?php html_text('employee_name'); ?>
                        <span class="error"><?php err('employee_name'); ?></span><br><br>

                        <label for="role">Role:</label>
                        <select name="role" required>
                            <option value="MANAGER">Manager</option>
                            <option value="STAFF">Staff</option>
                            <option value="DELIVERY_GUY">Delivery Guy</option>
                        </select><br><br>

                        <label for="email">Email:</label>
                        <?php html_text('email'); ?>
                        <span class="error"><?php err('email'); ?></span><br><br>

                        <label for="password">Password:</label>
                        <?php html_text('password'); ?>
                        <span class="error"><?php err('password'); ?></span><br><br>

                        <label for="profile_image">Profile Image:</label>
                        <input type="file" name="profile_image" id="profile_image"><br><br>

                        <input type="submit" value="Add Staff">
                    </form>
                </div>
            </div>

        <div id="updateEmployeeModal" class="modal">
            <div class="modal-content">
                <span class="close-button" onclick="hideUpdateEmployeeForm()">&times;</span>
                <form id="updateEmployeeForm" method="POST" action="updateStaff.php" enctype="multipart/form-data" class="update-form">
                    <h2>Update Employee</h2>

                    <label for="employee_id">Employee ID:</label>
                    <input id="employee_id" name="employee_id" value="" readonly>
                    <br>

                    <label for="updateEmployeeName">Employee Name:</label>
                    <?php html_text('employee_name'); ?>
                    <span class="error"><?php err('employee_name'); ?></span><br><br>

                    <label for="updateEmployeeEmail">Email:</label>
                    <?php html_text('email'); ?>
                    <span class="error"><?php err('email'); ?></span><br><br>

                    <label for="updateEmployeeRole">Role:</label>
                    <select name="role" id="role">
                        <option value="MANAGER" <?= $role === 'MANAGER' ? 'selected' : '' ?>>Manager</option>
                        <option value="STAFF" <?= $role === 'ADMIN' ? 'selected' : '' ?>>Staff</option>
                        <option value="DELIVERY_GUY" <?= $role === 'STAFF' ? 'selected' : '' ?>>Delivery_guy</option>
                    </select><br><br>


                    <label for="updateProfileImage">Profile Image:</label>
                    <input type="file" name="profile_image" id="updateProfileImage"><br><br>

                    <input type="submit" value="Update Employee">
                </form>
            </div>
        </div>
    </div>



</body>

<script>
    function showAddForm() {
        document.getElementById('addStaffModal').style.display = 'block';
    }

    function hideAddForm() {
        document.getElementById('addStaffModal').style.display = 'none';
    }

    function showUpdateEmployeeForm(id, name, email,role) {
        var modal = document.getElementById('updateEmployeeModal');
        var form = document.getElementById('updateEmployeeForm');
        modal.style.display = "block";

        form.elements['employee_id'].value = id;
        form.elements['employee_name'].value = name;
        form.elements['email'].value = email;
        form.elements['role'].value = role;

    }

    function hideUpdateEmployeeForm() {
        document.getElementById('updateEmployeeModal').style.display = 'none';
    }


    function showAccessDenied() {
        alert("You don't have permission to perform this action.");
    }

    function confirmDelete() {
        return confirm('Are you sure you want to delete this customer?');
    }

    function confirmBlock() {
        return confirm('Are you sure you want to block this customer?');
    }

    function confirmUnblock() {
        return confirm('Are you sure you want to unblock this customer?');
    }
</script>

</html>