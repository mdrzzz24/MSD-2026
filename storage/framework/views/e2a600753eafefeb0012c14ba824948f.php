<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QR Code — <?php echo e($registrant->name); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body{font-family:'Inter',sans-serif}</style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-lg max-w-sm w-full overflow-hidden">
        <div class="text-center">
            <img src="<?php echo e(asset('img/QRHeader.png')); ?>" alt="Metrodata Solution Day 2026" class="w-full h-auto">
        </div>
        <div class="p-6 text-center">
            <img src="<?php echo e($registrant->qr_code_url); ?>" alt="QR Code" class="w-56 h-56 mx-auto rounded-lg border border-gray-200">
            <p class="text-sm text-gray-500 mt-4">Show this QR Code at the registration desk for check-in.</p>
            <p class="text-xs text-gray-400 mt-2">Jakarta, 20 August 2026 · Shangri-La Hotel</p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\2026-Testing\resources\views/admin/qr-share.blade.php ENDPATH**/ ?>