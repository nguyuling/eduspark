<?php if (isset($component)) { $__componentOriginald3d68474712fd8abb799172f03c549ef = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald3d68474712fd8abb799172f03c549ef = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.forum-layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('forum-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

    <h1 class="text-3xl font-bold mb-2"><?php echo e($post->title); ?></h1>

    <div class="flex items-center gap-2 mb-4">
        <img src="<?php echo e($post->author_avatar); ?>" class="w-10 h-10 rounded-full">
        <span class="font-semibold"><?php echo e($post->author_name); ?></span>
    </div>

    <p class="mb-6"><?php echo e($post->content); ?></p>

    
    <div class="mb-6 flex gap-2">
        <a href="<?php echo e(route('forum.edit', $post->id)); ?>"
           class="px-3 py-2 bg-yellow-500 text-white rounded">Edit</a>

        <form action="<?php echo e(route('forum.destroy', $post->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <button class="px-3 py-2 bg-red-600 text-white rounded"
                    onclick="return confirm('Delete this post?')">Delete</button>
        </form>
    </div>

    <hr class="my-4">

    <h2 class="text-xl font-bold mb-2">Replies</h2>

    <?php $__currentLoopData = $post->replies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reply): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="border p-3 rounded mb-2 bg-gray-50">
            <div class="flex items-center gap-2">
                <img src="<?php echo e($reply->author_avatar); ?>" class="w-8 h-8 rounded-full">
                <strong><?php echo e($reply->author_name); ?></strong>
            </div>
            <p class="ml-10"><?php echo e($reply->content); ?></p>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <form action="<?php echo e(route('forum.reply', $post->id)); ?>" method="POST" class="mt-4">
        <?php echo csrf_field(); ?>

        <textarea name="reply" class="w-full border p-2 rounded mb-2" placeholder="Write a reply..."></textarea>

        <button class="px-4 py-2 bg-green-600 text-white rounded">Reply</button>
    </form>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald3d68474712fd8abb799172f03c549ef)): ?>
<?php $attributes = $__attributesOriginald3d68474712fd8abb799172f03c549ef; ?>
<?php unset($__attributesOriginald3d68474712fd8abb799172f03c549ef); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald3d68474712fd8abb799172f03c549ef)): ?>
<?php $component = $__componentOriginald3d68474712fd8abb799172f03c549ef; ?>
<?php unset($__componentOriginald3d68474712fd8abb799172f03c549ef); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\eduspark_laravel\resources\views/forum/show.blade.php ENDPATH**/ ?>