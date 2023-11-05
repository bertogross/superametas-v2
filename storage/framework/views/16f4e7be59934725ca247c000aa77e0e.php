<?php $__env->startSection('title'); ?>
    Visualização de Tópicos Departamentos
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('url'); ?>
            <?php echo e(route('auditsDepartmentsIndexURL')); ?>

        <?php $__env->endSlot(); ?>
        <?php $__env->slot('li_1'); ?>
            Tópicos dos Departamentos
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Visualização de Tópicos dos Departamentos
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div id="content" class="rounded rounded-2 mb-4">
        <div class="bg-warning-subtle position-relative">

            <div class="card-body p-5 text-center">
                <h3>Tópicos dos Departamentos</h3>
                <div class="mb-0 text-muted">
                    Atualizado em:
                    
                </div>
            </div>
            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs" width="1440" height="60" preserveAspectRatio="none" viewBox="0 0 1440 60">
                    <g mask="url(&quot;#SvgjsMask1001&quot;)" fill="none">
                        <path d="M 0,4 C 144,13 432,48 720,49 C 1008,50 1296,17 1440,9L1440 60L0 60z" style="fill: var(--vz-secondary-bg);"></path>
                    </g>
                    <defs>
                        <mask id="SvgjsMask1001">
                            <rect width="1440" height="60" fill="#ffffff"></rect>
                        </mask>
                    </defs>
                </svg>
            </div>
        </div>

        <div>
            <?php if($composes->isEmpty()): ?>
                <?php
                    $default = file_get_contents(resource_path('views/audits/components/default-departments-topics.json'));
                    $default = json_decode($default, true);
                ?>
                <?php $__currentLoopData = $getDepartmentsActive; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                    $topicData = [
                        $department->id => [
                            'stepData' => [
                                'step_name' => $department->department_alias,
                            ],
                            'topicData' => $default['topics']
                        ]
                    ];
                    ?>
                    <?php $__env->startComponent('audits.components.steps-card'); ?>
                        <?php $__env->slot('data', $topicData); ?>
                    <?php echo $__env->renderComponent(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


                
                <?php
                    // appPrintR($getDepartmentsActive);
                    appPrintR($topicData);
                ?>

            <?php else: ?>
            <?php endif; ?>
        </div>
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/audits/departments/index.blade.php ENDPATH**/ ?>