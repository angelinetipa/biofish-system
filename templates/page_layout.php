<?php
/**
 * Page Layout Template
 * BIO-FISH Bioplastic Formation System
 * 
 * Usage:
 * $page_title = 'Add Batch';
 * $page_content = 'path/to/content.php';
 * include ROOT_PATH . '/templates/page_layout.php';
 */

if (!isset($page_title)) $page_title = '';
if (!isset($page_content)) die('Error: $page_content not set');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo get_page_title($page_title); ?></title>
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/auth.css">
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/variables.css">
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/main.css">
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/components.css">
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/page-specific.css">
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/logout-modal.css">
    <script src="<?php echo JS_URL; ?>/logout-modal.js"></script>
    <?php if (isset($extra_css)): ?>
        <?php foreach ($extra_css as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <?php include ROOT_PATH . '/includes/header.php'; ?>
    
    <div class="container">
        <?php display_flash(); ?>
        
        <?php include $page_content; ?>
    </div>
    
    <script src="<?php echo JS_URL; ?>/main.js"></script>
    <?php if (isset($extra_js)): ?>
        <?php foreach ($extra_js as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>