<?php
/**
 * Application Constants
 * BIO-FISH Bioplastic Formation System
 */

// Application Info
define('APP_NAME', 'BIO-FISH');
define('APP_FULL_NAME', 'BIO-FISH Bioplastic Formation System');
define('APP_VERSION', '1.0.0');
define('APP_DESCRIPTION', 'Bioplastic Sheet Production from Fish Scales');

// Environment
define('ENVIRONMENT', 'development'); // 'development' or 'production'
define('DEBUG_MODE', ENVIRONMENT === 'development');

// Paths
define('ROOT_PATH', dirname(__DIR__));
define('BASE_URL', 'http://localhost/biofish');

// File paths
define('ASSETS_PATH', ROOT_PATH . '/assets');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('PAGES_PATH', ROOT_PATH . '/pages');
define('FUNCTIONS_PATH', ROOT_PATH . '/functions');
define('TEMPLATES_PATH', ROOT_PATH . '/templates');

// URLs
define('ASSETS_URL', BASE_URL . '/assets');
define('CSS_URL', ASSETS_URL . '/css');
define('JS_URL', ASSETS_URL . '/js');
define('IMAGES_URL', ASSETS_URL . '/images');

// Process stages
define('STAGE_EXTRACTION', 'extraction');
define('STAGE_FILTRATION', 'filtration');
define('STAGE_FORMULATION', 'formulation');
define('STAGE_FILM_FORMATION', 'film_formation');

// Batch statuses
define('STATUS_IDLE', 'idle');
define('STATUS_RUNNING', 'running');
define('STATUS_PAUSED', 'paused');
define('STATUS_COMPLETED', 'completed');
define('STATUS_STOPPED', 'stopped');
define('STATUS_CLEANING', 'cleaning');

// Material statuses
define('MATERIAL_AVAILABLE', 'available');
define('MATERIAL_LOW_STOCK', 'low_stock');
define('MATERIAL_DEPLETED', 'depleted');

// Pagination
define('ITEMS_PER_PAGE', 10);

// Process parameters
define('EXTRACTION_TEMP_MIN', 60);
define('EXTRACTION_TEMP_MAX', 80);
define('EXTRACTION_DURATION_HOURS', 4);
define('FORMULATION_TEMP', 80);
define('FILTRATION_TEMP', 25);
define('DRYING_DAYS', 3);

// Alert thresholds
define('LOW_STOCK_THRESHOLD_KG', 1.0);
define('MATERIAL_MIN_QUANTITY', 0.01);

// Timezone
date_default_timezone_set('Asia/Manila');

// Error reporting
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', ROOT_PATH . '/logs/error.log');
}
?>