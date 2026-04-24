<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZZY Rental</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .modal-overlay { background-color: rgba(0, 0, 0, 0.5); }
    </style>
</head>
<body class="bg-gray-50 text-gray-900">
    <nav class="bg-white shadow-sm p-4 flex justify-between items-center">
        <a href="<?= APP_URL ?>/" class="text-2xl font-bold text-blue-600">ZZY Rental</a>
        <div class="flex items-center gap-4">
            <?php if(isset($_SESSION['user_id'])): ?>
                <span class="text-sm text-gray-500 font-medium"><?= $_SESSION['role'] ?></span>
                <a href="<?= APP_URL ?>/auth/logout" class="text-red-500 hover:text-red-700 font-semibold">Logout</a>
            <?php endif; ?>
        </div>
    </nav>
    <main class="container mx-auto p-6">
