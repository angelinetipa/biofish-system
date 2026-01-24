<?php
/**
 * Application Initialization
 * BIO-FISH Bioplastic Formation System
 * 
 * Include this file at the top of every page:
 * require_once __DIR__ . '/../config/init.php';
 */

// Load constants first
require_once __DIR__ . '/constants.php';

// Load database connection
require_once __DIR__ . '/database.php';

// Load session management
require_once __DIR__ . '/session.php';

// Load helper functions
require_once ROOT_PATH . '/functions/helpers.php';

// Load authentication functions
require_once ROOT_PATH . '/functions/auth.php';

// Load business logic functions
require_once ROOT_PATH . '/functions/batch.php';
require_once ROOT_PATH . '/functions/material.php';
require_once ROOT_PATH . '/functions/feedback.php';
?>