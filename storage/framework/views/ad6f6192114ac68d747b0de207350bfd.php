<!-- resources/views/components/forum-layout.blade.php -->
<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EduSpark Forum</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="font-sans antialiased bg-gray-50">

<div class="flex min-h-screen">

    
    <aside class="w-64 bg-blue-200 text-gray-900 flex flex-col justify-between shadow-lg sticky top-0 h-screen p-6">

        
        <div class="mb-6">
            <h2 class="text-2xl font-bold mb-8 text-center">EduSpark</h2>

            
            <nav class="space-y-3">
                <a href="<?php echo e(route('forum.index')); ?>" 
                   class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-400 hover:text-white transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z"/>
                    </svg>
                    Forum
                </a>
                <a href="#" 
                   class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-400 hover:text-white transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                    </svg>
                    Performance
                </a>
                <a href="#" 
                   class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-400 hover:text-white transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4 4 4-4m0-10l-4-4-4 4"/>
                    </svg>
                    Games
                </a>
                <a href="#" 
                   class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-400 hover:text-white transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m4-4H8"/>
                    </svg>
                    Lessons
                </a>
                <a href="#" 
                   class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-400 hover:text-white transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    Quiz
                </a>
                <a href="#" 
                   class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-400 hover:text-white transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A9 9 0 1118.803 5.122M12 7v5l3 3"/>
                    </svg>
                    Profile
                </a>
            </nav>
        </div>

        
        <div>
            <a href="#"
               class="flex items-center justify-center px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition shadow-sm">
               Logout
            </a>
        </div>

    </aside>

    
    <main class="flex-1 p-8">
        <?php echo e($slot); ?>

    </main>

</div>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\eduspark_laravel\resources\views/components/forum-layout.blade.php ENDPATH**/ ?>