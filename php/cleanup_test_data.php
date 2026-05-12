<?php
/**
 * Test Cleanup Script for Padel App
 * This script removes test data from the database.
 * USE WITH CAUTION: This will permanently delete users and their associated data.
 */

require_once 'db_connect.php';

// Define the criteria for test users (e.g., email starts with 'test' or ends with 'example.com')
$test_email_pattern = 'test%';
$test_email_domain = '%example.com';

try {
    // 1. Get IDs of test users to clean up related data first if necessary
    $stmt = $conn->prepare("SELECT id FROM users WHERE email LIKE ? OR email LIKE ?");
    $stmt->bind_param("ss", $test_email_pattern, $test_email_domain);
    $stmt->execute();
    $result = $stmt->get_result();
    $ids = [];
    while ($row = $result->fetch_assoc()) {
        $ids[] = $row['id'];
    }

    if (empty($ids)) {
        echo "No test users found to clean up.\n";
        exit;
    }

    $id_list = implode(',', $ids);
    $count = count($ids);

    echo "Found $count test users. Starting cleanup...\n";

    // 2. Delete from related tables (if any, e.g., reservations, registrations)
    // Assuming table names based on common Padel app features
    $tables_to_clean = ['reservations', 'tournament_registrations', 'feedback', 'reports'];
    
    foreach ($tables_to_clean as $table) {
        // Check if table exists before deleting
        $check_table = $conn->query("SHOW TABLES LIKE '$table'");
        if ($check_table->num_rows > 0) {
            $delete_stmt = "DELETE FROM $table WHERE user_id IN ($id_list)";
            if ($conn->query($delete_stmt)) {
                echo "Cleaned up records from table: $table\n";
            }
        }
    }

    // 3. Finally, delete the users
    $delete_users = "DELETE FROM users WHERE id IN ($id_list)";
    if ($conn->query($delete_users)) {
        echo "Successfully deleted $count test users.\n";
    }

} catch (Exception $e) {
    echo "Error during cleanup: " . $e->getMessage() . "\n";
}

$conn->close();
?>
