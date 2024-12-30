<?php
require '../_base.php';

if (is_post()) {
    global $_err;

    // Get form values
    $employee_id = req('employee_id');
    $employee_name = req('employee_name');
    $email = req('email');
    $role = req('role');
    $profile_image = get_file('profile_image'); 

    $_err = [];

    if (empty($employee_name)) {
        $_err['employee_name'] = "Employee Name is required for Employee ID: $employee_id.";
    }

    if (empty($email)) {
        $_err['email'] = "Email is required for Employee ID: $employee_id.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_err['email'] = "Invalid email format for Employee ID: $employee_id.";
    } else {
        $stmt = $_db->prepare("SELECT employee_id FROM employees WHERE email = ? AND employee_id != ?");
        $stmt->execute([$email, $employee_id]);
        if ($stmt->rowCount() > 0) {
            $_err['email'] = "Email is already in use by another employee.";
        }
    }

    if (empty($role)) {
        $_err['role'] = "Role is required for Employee ID: $employee_id.";
    }

    if ($profile_image) {
        if (!str_starts_with($profile_image->type, 'image/')) {
            $_err['profile_image'] = "Profile image must be a valid image file for Employee ID: $employee_id.";
        } elseif ($profile_image->size > 2 * 1024 * 1024) { // Limit to 2MB
            $_err['profile_image'] = "Profile image exceeds the size limit (2MB) for Employee ID: $employee_id.";
        }
    }

    if (!$_err) {
        if ($profile_image) {
            $profile_image_path = save_photo($profile_image, '../uploads/profile_images');
        } else {
            // If no image is uploaded, keep the existing profile image
            $stmt = $_db->prepare("SELECT profile_image FROM employees WHERE employee_id = ?");
            $stmt->execute([$employee_id]);
            $profile_image_path = $stmt->fetchColumn(); // Use the existing image path
        }

        $stmt = $_db->prepare("
            UPDATE employees SET
                employee_name = ?, 
                email = ?, 
                role = ?, 
                profile_image = ? 
            WHERE employee_id = ?
        ");

        $stmt->execute([
            $employee_name,
            $email,
            $role,
            $profile_image_path,
            $employee_id
        ]);

        if ($_user && isset($_user->employee_id)) {
            $employeeId = $_user->employee_id;
            log_action($employeeId, 'Updated Staff', "Updated Staff: $employee_id", $_db);
        }

        temp('info', 'Employee updated successfully!');
        redirect('staff.php');
    } else {
        temp('error', $_err);
        redirect('staff.php');
    }
}
?>
