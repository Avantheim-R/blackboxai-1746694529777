<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check if user is logged in and has admin role
function isAdmin() {
    return isset($_SESSION['user_id']) && 
           isset($_SESSION['user_role']) && 
           $_SESSION['user_role'] === 'admin';
}

// Function to check if user is logged in and has teacher role
function isTeacher() {
    return isset($_SESSION['user_id']) && 
           isset($_SESSION['user_role']) && 
           $_SESSION['user_role'] === 'teacher';
}

// Function to check if user is logged in as student
function isStudent() {
    return isset($_SESSION['user_id']) && 
           isset($_SESSION['user_role']) && 
           $_SESSION['user_role'] === 'student';
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // If accessing API endpoint
    if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required']);
        exit;
    }
    // If accessing regular page
    header('Location: /auth/login.php');
    exit;
}

// For admin routes, check admin role
if (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false && !isAdmin()) {
    if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
        header('Content-Type: application/json');
        http_response_code(403);
        echo json_encode(['error' => 'Admin access required']);
        exit;
    }
    header('Location: /dashboard.php');
    exit;
}

// For teacher routes, check teacher role
if (strpos($_SERVER['REQUEST_URI'], '/teacher/') !== false && !isTeacher()) {
    if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
        header('Content-Type: application/json');
        http_response_code(403);
        echo json_encode(['error' => 'Teacher access required']);
        exit;
    }
    header('Location: /dashboard.php');
    exit;
}
