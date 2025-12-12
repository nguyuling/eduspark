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
    <h1 class="text-3xl font-bold mb-6">Forum Posts</h1>

    <div class="flex justify-between mb-6">
        <h1 class="text-2xl font-bold">Forum</h1>
        <a href="<?php echo e(route('forum.create')); ?>"
           class="flex items-center gap-2 px-5 py-3 bg-blue-600 text-white rounded hover:bg-blue-700 shadow-sm transition">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
           </svg>
           Create Post
        </a>
    </div>

    
    <div class="mb-6">
        <form method="GET" action="<?php echo e(route('forum.index')); ?>" class="flex items-center gap-2">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                   placeholder="Search posts..." 
                   class="border rounded px-4 py-2 w-full md:w-1/3 text-lg">
            <button type="submit" 
                    class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 shadow-sm transition">
                Search
            </button>
            <?php if(request('search')): ?>
                <a href="<?php echo e(route('forum.index')); ?>" 
                   class="px-5 py-2 bg-gray-400 text-white rounded hover:bg-gray-500 shadow-sm transition">
                   Clear
                </a>
            <?php endif; ?>
        </form>
    </div>

    <div class="space-y-6">
        <?php $__empty_1 = true; $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-white shadow-lg rounded-lg border border-gray-200 p-6">

                
                <div class="flex items-center mb-4">
                    <img src="<?php echo e($post->author_avatar); ?>" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <p class="font-semibold text-lg"><?php echo e($post->author_name); ?></p>
                        <p class="text-sm text-gray-500"><?php echo e($post->created_at->diffForHumans()); ?></p>
                    </div>
                </div>

                
                <h2 class="text-2xl font-bold mb-2">
                    <a href="<?php echo e(route('forum.show', $post->id)); ?>" class="text-blue-700 hover:underline">
                        <?php echo e($post->title); ?>

                    </a>
                </h2>
                <p class="text-gray-700 text-lg mt-2"><?php echo e(Str::limit($post->content, 200)); ?></p>

                
                <div class="mt-4 flex flex-wrap gap-3">

                    
                    <a href="<?php echo e(route('forum.edit', $post->id)); ?>" 
                       class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 shadow-sm transition">
                       Edit
                    </a>

                    
                    <form action="<?php echo e(route('forum.destroy', $post->id)); ?>" method="POST" onsubmit="return confirm('Are you sure?')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" 
                                class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 shadow-sm transition">
                            Delete
                        </button>
                    </form>

                    
                    <button onclick="toggleReplyForm(<?php echo e($post->id); ?>)"
                            class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 shadow-sm transition">
                        Reply
                    </button>

                </div>

                
                <div id="reply-form-<?php echo e($post->id); ?>" class="mt-4 hidden">
                    <form action="<?php echo e(route('forum.reply', $post->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <textarea name="content" class="w-full border p-3 rounded mb-2 text-lg" rows="3" placeholder="Write your reply..."></textarea>
                        <button type="submit" 
                                class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 shadow-sm transition">
                            Submit Reply
                        </button>
                    </form>
                </div>

            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-gray-500 text-lg">No posts found.</p>
        <?php endif; ?>
    </div>

    <script>
        function toggleReplyForm(postId) {
            const form = document.getElementById('reply-form-' + postId);
            form.classList.toggle('hidden');
        }
    </script>

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
<?php /**PATH C:\xampp\htdocs\eduspark_laravel\resources\views/forum/index.blade.php ENDPATH**/ ?>