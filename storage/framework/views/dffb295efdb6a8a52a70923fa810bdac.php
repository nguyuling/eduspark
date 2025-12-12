<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8 max-w-md">
    <h2 class="text-2xl font-bold mb-4">Login</h2>

    <?php if($errors->any()): ?>
        <div class="bg-red-100 text-red-800 p-3 mb-4"><?php echo e($errors->first()); ?></div>
    <?php endif; ?>

    <form method="POST" action="/login">
        <?php echo csrf_field(); ?>
        <div class="mb-4">
            <label class="block text-sm font-medium">Email</label>
            <input type="email" name="email" value="<?php echo e(old('email')); ?>" required class="w-full border rounded px-3 py-2" />
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium">Password</label>
            <input type="password" name="password" required class="w-full border rounded px-3 py-2" />
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Login</button>
            <a href="/register" class="text-sm text-blue-600">Register</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/nguyuling/eduspark/resources/views/auth/login.blade.php ENDPATH**/ ?>