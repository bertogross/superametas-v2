<div class="nav nav-pills flex-column nav-pills-tab verti-nav-pills custom-verti-nav-pills nav-pills-theme" aria-orientation="vertical">
    <a href="<?php echo e(route('surveysIndexURL')); ?>" class="nav-link text-uppercase <?php echo e($url == route('surveysIndexURL') ? 'active' : ''); ?> mt-0 mb-2" title="Listar Vistorias"><i class="ri-list-check-2 text-muted fs-16 align-middle me-1"></i> Vistorias</a>

    <a href="<?php echo e(route('surveysCreateURL')); ?>" class="nav-link text-uppercase <?php echo e($url == route('surveysCreateURL') || $url == route('surveysEditURL') ? 'active' : ''); ?> mt-0 mb-2" title="Adicionar Vistoria"><i class="ri-add-line text-muted fs-16 align-middle me-1"></i> Vistoria</a>

    <hr class="m-3 mt-2">

    <a href="<?php echo e(route('surveysComposeIndexURL')); ?>" class="nav-link text-uppercase <?php echo e($url == route('surveysComposeIndexURL') ? 'active' : ''); ?> mt-0 mb-2" title="Listar Formulários"><i class="ri-list-check-2 text-muted fs-16 align-middle me-1"></i> Formulários</a>

    <a href="<?php echo e(route('surveysComposeCreateURL', ['type'=>'default'])); ?>" class="nav-link text-uppercase <?php echo e($url == route('surveysComposeCreateURL', ['type'=>'default']) || $url == route('surveysComposeEditURL', ['type'=>'default']) ? 'active' : ''); ?> mt-0 mb-2" title="Adicionar Formulário Departamentos"><i class="ri-add-line text-muted fs-16 align-middle me-1"></i> Formulário Departamentos</a>

    <a href="<?php echo e(route('surveysComposeCreateURL', ['type'=>'custom'])); ?>" class="nav-link text-uppercase <?php echo e($url == route('surveysComposeCreateURL', ['type'=>'custom']) || $url == route('surveysComposeEditURL', ['type'=>'custom']) ? 'active' : ''); ?> mt-0 mb-2" title="Adicionar Formulário Customizado"><i class="ri-add-line text-muted fs-16 align-middle me-1"></i> Formulário Customizado</a>
</div>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/surveys/components/nav-pills.blade.php ENDPATH**/ ?>