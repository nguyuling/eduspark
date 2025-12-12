<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Laravel')); ?></title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

<?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    
    
    <style>
        .sidebar-container {
            width: 250px; /* Fixed width for the sidebar */
            z-index: 1000;
        }
        .content-offset {
            margin-left: 250px; /* Push main content past the fixed sidebar */
            width: calc(100% - 250px);
            min-height: 100vh; /* Ensure main content is full height */
        }
        .sidebar-link {
            padding: 10px 15px;
            display: block;
            color: #333; /* Default text color */
            text-decoration: none;
            transition: background-color 0.2s;
        }
        .sidebar-link:hover {
            background-color: #f8f9fa; /* Light hover background */
            color: #007bff; /* Primary color on hover */
        }
    </style>
</head>
<body>
    <div id="app" style="display: flex;">
        
        
        <div class="sidebar-container bg-white shadow-sm border-end vh-100 position-fixed">
            <div class="p-3">
                
                
                <a class="navbar-brand d-block mb-4 text-center text-primary fw-bold" href="<?php echo e(url('/')); ?>" style="font-size: 1.5rem;">
                    <?php echo e(config('app.name', 'Laravel')); ?>

                </a>

                <hr class="mb-4">
                
                
                <ul class="nav flex-column">
                    <?php if(auth()->guard()->check()): ?>
                        
                        
                        <li class="nav-item mb-1">
                            <a class="sidebar-link" href="<?php echo e(route('lessons.index')); ?>"><i class="bi bi-book me-2"></i> Lessons</a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="sidebar-link" href="<?php echo e(route('forum.index')); ?>"><i class="bi bi-chat-dots me-2"></i> Forum</a>
                        </li>

                        
                        <?php if(Auth::user()->role === 'teacher'): ?>
                            <li class="nav-item mb-1">
                                <a class="sidebar-link" href="<?php echo e(route('teacher.quizzes.index')); ?>"><i class="bi bi-patch-question me-2"></i> Quiz</a>
                            </li>
                            <li class="nav-item mb-1">
                                <a class="sidebar-link" href="<?php echo e(route('performance.teacher_view')); ?>"><i class="bi bi-bar-chart me-2"></i> Performance</a>
                            </li>
                        <?php else: ?> 
                            <li class="nav-item mb-1">
                                <a class="sidebar-link" href="<?php echo e(route('student.quizzes.index')); ?>"><i class="bi bi-patch-question me-2"></i>Quiz</a>
                            </li>
                            <li class="nav-item mb-1">
                                <a class="sidebar-link" href="<?php echo e(route('games.index')); ?>"><i class="bi bi-joystick me-2"></i> Game</a>
                            </li>
                            <li class="nav-item mb-1">
                                <a class="sidebar-link" href="<?php echo e(route('performance.student_view')); ?>"><i class="bi bi-graph-up me-2"></i> Performance</a>
                            </li>
                        <?php endif; ?>
                        
                        <hr class="my-3">
                        
                        
                        <li class="nav-item mb-1">
                            <a class="sidebar-link" href="<?php echo e(route('profile.show')); ?>"><i class="bi bi-person-circle me-2"></i> <?php echo e(Auth::user()->name); ?></a>
                        </li>
                        <li class="nav-item mb-1">
                            <a class="sidebar-link text-danger" href="<?php echo e(route('logout')); ?>"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right me-2"></i> <?php echo e(__('Logout')); ?>

                            </a>
                        </li>
                        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                            <?php echo csrf_field(); ?>
                        </form>

                    <?php else: ?>
                        
                        <li class="nav-item">
                            <a class="sidebar-link" href="<?php echo e(route('login')); ?>"><i class="bi bi-box-arrow-in-right me-2"></i> <?php echo e(__('Login')); ?></a>
                        </li>
                        <li class="nav-item">
                            <a class="sidebar-link" href="<?php echo e(route('register')); ?>"><i class="bi bi-person-add me-2"></i> <?php echo e(__('Register')); ?></a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        


        
        <main class="py-4 content-offset"> 
            <?php echo $__env->yieldContent('content'); ?>
        </main>
        

    </div>
</body>
</html><?php /**PATH /Users/nguyuling/eduspark/resources/views/layouts/app.blade.php ENDPATH**/ ?>