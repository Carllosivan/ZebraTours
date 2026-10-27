<section class="bg-navy py-16 px-4 sm:px-6 lg:px-8 text-white">
    <div class="max-w-4xl mx-auto text-center space-y-4">
        <h1 class="text-3xl sm:text-5xl font-serif font-bold text-white tracking-tight"><?= e($page['title']) ?></h1>
        <?php if (!empty($page['excerpt'])): ?>
            <p class="text-slate-300 text-sm sm:text-base leading-relaxed max-w-2xl mx-auto"><?= e($page['excerpt']) ?></p>
        <?php endif; ?>
    </div>
</section>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200 shadow-sm prose prose-slate max-w-none text-slate-700 leading-relaxed text-sm sm:text-base">
        <?= nl2br(e($page['content'])) ?>
    </div>
</div>
