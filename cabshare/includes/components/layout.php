<?php
if (!isset($BASE_URL)) {
    require_once dirname(dirname(__FILE__)) . '/config.php';
}

// Get page title from the calling file or set a default
$page_title = $page_title ?? 'CabShare';
?>

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - CabShare</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php if (isset($extra_head)) echo $extra_head; ?>
</head>
<body class="flex flex-col min-h-screen bg-gray-100">
    <?php include dirname(__FILE__) . '/navbar.php'; ?>
    
    <main class="flex-grow">
        <?php if (isset($content)) echo $content; ?>
    </main>
    
    <?php include dirname(__FILE__) . '/footer.php'; ?>
    
    <?php if (isset($extra_scripts)) echo $extra_scripts; ?>
</body>
</html> 