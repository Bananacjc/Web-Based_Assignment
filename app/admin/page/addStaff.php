<?php
require '../_base.php';

if (is_post()) {
    global $_err;

    $employee_name = req('employee_name');
    $email = req('email');
    $password = req('password');
    $role = req('role');
    $profile_image = get_file('profile_image'); 

    $_err = [];

    if (empty($employee_name)) {
        $_err['employee_name'] = "Employee Name is required for Employee ID: $employee_id.";
    }

    if (empty($email)) {
        $_err['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_err['email'] = "Invalid email format.";
    } else {
        $stmt = $_db->prepare("SELECT employee_id FROM employees WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $_err['email'] = "Email is already in use.";
        }
    }

    if (empty($password)) {
        $_err['password'] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $_err['password'] = "Password must be at least 8 characters.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        $_err['password'] = "Password must include at least one uppercase letter, one lowercase letter, one number, and one special character.";
    }

    if ($profile_image) {
        if (!str_starts_with($profile_image->type, 'image/')) {
            $_err['profile_image'] = "Profile image must be a valid image file.";
        } elseif ($profile_image->size > 3 * 1024 * 1024) { 
            $_err['profile_image'] = "Profile image exceeds the size limit (3MB).";
        }
    }

        if ($profile_image) {
            $profile_image_path = save_photo($profile_image, '../uploads/profile_images');
        } else {
            $profile_image_path = null;
        }
    if (!$_err) {

        $hashed_password = sha1($password);

        $employee_id = generate_unique_id('EMP', 'employees', 'employee_id', $_db);

        try {
            $stmt = $_db->prepare("
                INSERT INTO employees (employee_id, employee_name, email, password, role, profile_image)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $employee_id,
                $employee_name,
                $email,
                $hashed_password, 
                $role,
                $profile_image_path 
            ]);

            if ($_user && isset($_user->employee_id)) {
                $employeeId = $_user->employee_id;
                log_action($employeeId, 'Add Staff', 'Added new staff: ' . $employee_name, $_db);
            }

            temp('info', "Employee added successfully!");
            redirect('staff.php');
        } catch (PDOException $e) {
            $_err['error'] = 'Error adding employee: ' . $e->getMessage();
        }
    } else {
        temp('error', $_err);
        redirect('staff.php');
    }
}
?>
