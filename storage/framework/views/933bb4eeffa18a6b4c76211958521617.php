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
    <h1 class="text-2xl font-bold mb-4">Create New Post</h1>

    <form action="<?php echo e(route('forum.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        <label class="block font-semibold">Title</label>
        <input name="title" class="w-full border p-2 rounded mb-3">

        <label class="block font-semibold">Content</label>
        <textarea name="content" class="w-full border p-2 rounded mb-3"></textarea>

        <button class="bg-blue-600 text-white px-4 py-2 rounded">Post</button>
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

<?php /**PATH C:\xampp\htdocs\eduspark_laravel\resources\views/forum/create.blade.php ENDPATH**/ ?>