<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Attendee Pass — <?php echo e($registrant->name); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
    <style>
        body{font-family:'Inter',system-ui,sans-serif}
        .msd-grad{background:linear-gradient(135deg,#ff3d6e,#e91e63)}
    </style>
</head>
<body class="bg-[#050d2a] min-h-screen flex items-center justify-center p-4 sm:p-8">

    <div class="w-full max-w-3xl">
        
        <div class="relative overflow-hidden rounded-t-3xl shadow-2xl bg-[#0e2461]">
            <img src="<?php echo e(asset('img/QRHeader.png')); ?>" alt="Metrodata Solution Day 2026" class="w-full h-auto block">
            <div class="absolute bottom-0 left-0 right-0 h-2 msd-grad"></div>
        </div>

        
        <div class="bg-white rounded-b-3xl shadow-2xl overflow-hidden">
            <div class="grid sm:grid-cols-2">
                
                <div class="p-6 sm:p-8 flex flex-col items-center justify-center text-center border-b sm:border-b-0 sm:border-r border-gray-200">
                    <p class="text-[10px] uppercase tracking-[0.25em] text-gray-400 font-semibold">Attendee Pass</p>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900 mt-2"><?php echo e($registrant->name); ?></h2>
                    <p class="text-sm font-medium text-gray-500 mt-0.5"><?php echo e($registrant->company ?: $registrant->job_title); ?></p>
                    <div class="mt-5 bg-white p-3 rounded-2xl border-2 border-dashed border-[#ff3d6e]/40">
                        <img src="<?php echo e(str_replace('size=150x150', 'size=400x400', $registrant->qr_code_url)); ?>" alt="QR Code" class="w-44 h-44 mx-auto">
                    </div>
                    <p class="mt-3 text-xs text-gray-400 font-mono"><?php echo e($registrant->unique_code); ?></p>
                    <p class="text-[11px] text-gray-400 mt-1">Show this QR Code at the registration desk for check-in.</p>
                </div>

                
                <div class="p-6 sm:p-8">
                    <h3 class="text-lg font-extrabold text-gray-900 leading-snug">Your Pass for Metrodata Solution Day 2026</h3>
                    <p class="text-[11px] text-gray-500 mt-1.5">Important: Please have this visible on your device as you enter the venue.</p>

                    <div class="mt-6">
                        <p class="text-[10px] uppercase tracking-[0.25em] font-bold text-[#e91e63]">When</p>
                        <p class="text-sm font-bold text-gray-900 mt-1.5">Thursday, 20 August 2026</p>
                        <ul class="mt-2 space-y-1 text-xs text-gray-600">
                            <li>• Event check-in opens from 08:00 WIB</li>
                            <li>• Keynote presentation starts at 08:30 WIB</li>
                        </ul>
                    </div>

                    <div class="mt-6">
                        <p class="text-[10px] uppercase tracking-[0.25em] font-bold text-[#e91e63]">Where</p>
                        <p class="text-sm font-bold text-gray-900 mt-1.5">Shangri-La Hotel Jakarta</p>
                        <p class="text-xs text-gray-600 mt-1">Enter via the main lobby of the hotel.</p>
                        <a href="https://maps.google.com/?q=Shangri-La+Hotel+Jakarta" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-1 mt-2 text-xs font-semibold text-[#e91e63] hover:underline">
                            View map
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            
            <div class="msd-grad px-6 py-3 text-center">
                <p class="text-white text-[10px] font-semibold uppercase tracking-widest">Metrodata Solution Day 2026</p>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/qr-share.blade.php ENDPATH**/ ?>