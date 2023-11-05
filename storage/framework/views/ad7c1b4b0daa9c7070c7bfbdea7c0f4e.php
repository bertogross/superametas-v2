<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.file-manager'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('url'); ?>
            <?php echo e(url('settings')); ?>

        <?php $__env->endSlot(); ?>
        <?php $__env->slot('li_1'); ?>
            <?php echo app('translator')->get('translation.settings'); ?>
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            <?php echo app('translator')->get('translation.file-manager'); ?>
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <?php echo $__env->make('components.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="card">
        <div class="card-body">
            <?php if( getDropboxToken() ): ?>
                <div id="folder-list" class="mb-2">
                    <div class="row justify-content-beetwen g-2 mb-3">
                        <div class="col-auto">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-2 d-block d-lg-none">
                                    <button type="button" class="btn btn-soft-success btn-icon btn-sm fs-16 file-menu-btn">
                                        <i class="ri-menu-2-fill align-bottom"></i>
                                    </button>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="ri-database-2-line fs-17"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3 overflow-hidden">
                                            <div class="progress mb-2 progress-sm">
                                                <div class="progress-bar <?php echo e($storageInfo['percentageUsed'] <= 80 ? 'bg-info' : 'bg-danger'); ?>" role="progressbar" style="width: <?php echo e($storageInfo['percentageUsed'] ?? ''); ?>%" aria-valuenow="<?php echo e($storageInfo['percentageUsed'] ?? ''); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span class="text-muted fs-12 d-block text-truncate"><b><?php echo e(formatSize($storageInfo['used']) ?? ''); ?></b> utilizados de <b><?php echo e($storageInfo['total'] ?? ''); ?></b>GB alocados</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col"></div>
                        <div class="col-auto text-end">
                            <form id="upload-form" method="post" enctype="multipart/form-data" autocomplete="off">
                                <?php echo csrf_field(); ?>
                                <div class="input-group text-theme">
                                    <input type="file" name="file[]" required class="form-control" id="inputGroupFile" multiple>
                                    <label class="input-group-text btn-theme" for="inputGroupFile" id="btn-upload-file">Enviar Arquivos</label>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!--end row-->
                    <div class="row" id="folderlist-data">
                        <div class="row" id="folderlist-data">
                            <?php $__currentLoopData = $folders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $folder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-lg-3 col-xxl-2 col-6 folder-card">
                                    <div class="card bg-light shadow-none" id="folder-<?php echo e($folder['id'] ?? ''); ?>">
                                        <div class="card-body">
                                            <div class="d-flex mb-1">
                                                <div class="form-check form-check-danger mb-3 fs-15 flex-grow-1">

                                                </div>
                                                <div class="dropdown">
                                                    <button class="btn btn-ghost-theme btn-icon btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="ri-more-2-fill fs-16 align-bottom"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item" href="<?php echo e(route('DropboxBrowseFolderURL', ['path' => ltrim($folder['path_display'], '/')])); ?>">
                                                                Abrir
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="<?php echo e(route('DropboxDeleteFolderURL', ['path' => ltrim($folder['path_display'], '/')])); ?>" onclick="return confirm('Are you sure you want to delete this folder and all its contents?')">
                                                                Deletar
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="text-center">
                                                <div class="mb-2">
                                                    <a class="dropdown-item" href="<?php echo e(route('DropboxBrowseFolderURL', ['path' => ltrim($folder['path_display'], '/')])); ?>">
                                                        <i class="ri-folder-2-fill align-bottom text-warning display-5"></i>
                                                    </a>
                                                </div>
                                                <h6 class="fs-15 folder-name">
                                                    <a href="<?php echo e(route('DropboxBrowseFolderURL', ['path' => ltrim($folder['path_display'], '/')])); ?>">
                                                        <?php echo e($folder['name'] ?? ''); ?>

                                                    </a>
                                                </h6>
                                            </div>

                                            <div class="row">
                                                <div class="col">
                                                    <span class="me-auto"><b><?php echo e($folder['file_count'] ?? ''); ?></b> Arquivo<?php echo e(intval($folder['file_count']) > 1 ? 's' : ''); ?></span>
                                                </div>
                                                <div class="col text-end">
                                                    <span><b><?php echo e($folder['size'] ?? ''); ?></b>GB</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <!--end row-->
                </div>
                <div>

                    <div class="row mb-3">
                        <h5 class="col fs-16 mb-0" id="filetype-title">
                            <?php if($currentFolderPath && $currentFolderPath != '/'): ?>
                                <a href="<?php echo e(url()->previous()); ?>" class="btn btn-sm btn-outline-theme"><i class="ri-arrow-go-back-fill align-middle"></i> Voltar</a>
                            <?php else: ?>
                                <?php echo e(isset($files) && is_array($files) && count($files) > 1 ? 'Arquivos' : 'Listagem'); ?>

                            <?php endif; ?>
                        </h5>
                        <div class="col-auto text-end">
                            <div class="d-flex gap-2 align-items-start">
                                <select class="form-select" id="file-type" onchange="filterByFileType()">
                                    <option value="" disabled>Tipos de Arquivo</option>
                                    <option value="All" selected>Todos</option>
                                    <option value="Video">Vídeo</option>
                                    <option value="Images">Imagens</option>
                                    <option value="Music">Múscia</option>
                                    <option value="Documents">Documentos</option>
                                </select>
                                
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive table-card mt-2">
                        <table class="table align-middle table-hover table-striped table-nowrap mb-0">
                            <thead class="table-active text-uppercase">
                                <tr>
                                    <th scope="col">Nome</th>
                                    <th scope="col">Peso</th>
                                    <th scope="col">Data</th>
                                    <th scope="col" class="text-end"></th>
                                </tr>
                            </thead>
                            <tbody id="file-list">
                                <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr data-file-id="<?php echo e($file['id']); ?>">
                                        <td>
                                            <?php
                                            $fileType = pathinfo($file['name'], PATHINFO_EXTENSION);

                                            $serverModified = DateTime::createFromFormat('Y-m-d\TH:i:s\Z', $file['server_modified']);
                                            $formattedDate = $serverModified->format('d/m/Y');

                                            ?>

                                            <?php if($fileType == 'jpg' || $fileType == 'jpeg' || $fileType == 'png' || $fileType == 'gif'): ?>
                                                <i class="ri-gallery-fill align-bottom text-success"></i>
                                            <?php elseif($fileType == 'pdf'): ?>
                                                <i class="ri-file-pdf-fill align-bottom text-danger"></i>
                                            <?php elseif($fileType == 'txt' || $fileType == 'doc' || $fileType == 'docx'): ?>
                                                <i class="ri-file-text-fill align-bottom text-secondary"></i>
                                            <?php else: ?>
                                                <i class="ri-file-fill align-bottom text-primary"></i>
                                            <?php endif; ?>
                                            <?php echo e($file['name'] ?? ''); ?>

                                        </td>
                                        <td><?php echo e(formatSize($file['size']) ?? ''); ?></td>
                                        <td><?php echo e($formattedDate ?? ''); ?></td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <a href="<?php echo e($file['link'] ?? '#'); ?>" download class="btn btn-sm btn-outline-dark" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Download"><i class="ri-download-2-line"></i></a>

                                                <button class="btn btn-sm btn-outline-dark btn-delete-file" data-path="<?php echo e($file['path_display']); ?>" data-id="<?php echo e($file['id']); ?>" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Deletar">
                                                    <i class="ri-delete-bin-5-line text-danger"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if(intval($totalFiles) > 1 && $perPage < intval($totalFiles)): ?>
                        <div class="align-items-center mt-2 row g-3 text-center text-sm-start">
                            <div class="col-sm">
                                <div class="text-muted">
                                    Exibindo de <span class="fw-semibold"><?php echo e($page * $perPage - $perPage + 1); ?></span> até <span class="fw-semibold"><?php echo e(min($page * $perPage, $totalFiles)); ?></span> <?php echo e(intval($totalFiles) > 1 ? 'dos' : 'de'); ?> <span class="fw-semibold"><?php echo e($totalFiles); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-auto">
                                <ul class="pagination pagination-separated pagination-sm justify-content-center justify-content-sm-start mb-0">
                                    <li class="page-item <?php echo e($page == 1 ? 'disabled' : ''); ?>">
                                        <a href="<?php echo e(route('DropboxIndexURL', ['page' => $page - 1])); ?>" class="page-link">←</a>
                                    </li>
                                    <?php for($i = 1; $i <= ceil($totalFiles / $perPage); $i++): ?>
                                        <li class="page-item <?php echo e($page == $i ? 'active' : ''); ?>">
                                            <a href="<?php echo e(route('DropboxIndexURL', ['page' => $i])); ?>" class="page-link"><?php echo e($i); ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?php echo e($page == ceil($totalFiles / $perPage) ? 'disabled' : ''); ?>">
                                        <a href="<?php echo e(route('DropboxIndexURL', ['page' => $page + 1])); ?>" class="page-link">→</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show">
                    <i class="ri-alert-line label-icon"></i> Necessário estabelecer a conexão com seu Driver. Acesse as configurações em <a href="<?php echo e(route('settingsApiKeysURL')); ?>" class="text-decoration-underline" title="Acessar configurações <?php echo app('translator')->get('translation.api-keys'); ?>"><?php echo app('translator')->get('translation.api-keys'); ?></a>.
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <?php if(getDropboxToken()): ?>
        <script>
            var DropboxAccessToken = '<?php echo e(getDropboxToken()); ?>';
            var DropboxUploadURL = "<?php echo e(route('DropboxUploadURL')); ?>";
            var DropboxDeleteURL = "<?php echo e(route('DropboxDeleteURL')); ?>";
            var DropboxCurrentFolderPath = "<?php echo e($currentFolderPath); ?>";
        </script>
        <script src="<?php echo e(URL::asset('build/js/settings-storage.js')); ?>" type="module"></script>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/settings/dropbox.blade.php ENDPATH**/ ?>