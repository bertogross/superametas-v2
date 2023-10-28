<?php
    //APP_print_r($storageInfo);
    //APP_print_r($folders);
    //APP_print_r($files);
?>

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

    <?php echo $__env->make('error.alert-errors', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->make('error.alert-success', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="chat-wrapper d-lg-flex gap-1 mx-n4 mt-1 p-1">
        <div class="file-manager-sidebar">
            <div class="p-3 d-flex flex-column h-100">
                <div class="mb-3">
                    <h5 class="mb-0 fw-bold">My Drive</h5>
                </div>
                <div class="search-box">
                    <input type="text" class="form-control bg-light border-light" placeholder="Search here...">
                    <i class="ri-search-2-line search-icon"></i>
                </div>
                <div class="mt-3 mx-n4 px-4 file-menu-sidebar-scroll" data-simplebar>
                    <ul class="list-unstyled file-manager-menu">
                        <li>
                            <a data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="true" aria-controls="collapseExample">
                                <i class="ri-folder-2-line align-bottom me-2"></i> <span class="file-list-link">My Drive</span>
                            </a>
                            <div class="collapse show" id="collapseExample">
                                <ul class="sub-menu list-unstyled">
                                    <li>
                                        <a href="#!">Assets</a>
                                    </li>
                                    <li>
                                        <a href="#!">Marketing</a>
                                    </li>
                                    <li>
                                        <a href="#!">Personal</a>
                                    </li>
                                    <li>
                                        <a href="#!">Projects</a>
                                    </li>
                                    <li>
                                        <a href="#!">Templates</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li>
                            <a href="#!"><i class="ri-file-list-2-line align-bottom me-2"></i> <span class="file-list-link">Documents</span></a>
                        </li>
                        <li>
                            <a href="#!"><i class="ri-image-2-line align-bottom me-2"></i> <span class="file-list-link">Media</span></a>
                        </li>
                        <li>
                            <a href="#!"><i class="ri-history-line align-bottom me-2"></i> <span class="file-list-link">Recents</span></a>
                        </li>
                        <li>
                            <a href="#!"><i class="ri-star-line align-bottom me-2"></i> <span class="file-list-link">Important</span></a>
                        </li>
                        <li>
                            <a href="#!"><i class="ri-delete-bin-line align-bottom me-2"></i> <span class="file-list-link">Deleted</span></a>
                        </li>
                    </ul>
                </div>
                <div class="mt-auto">
                    <h6 class="fs-11 text-muted text-uppercase mb-3">Storage Status</h6>
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="ri-database-2-line fs-17"></i>
                        </div>
                        <div class="flex-grow-1 ms-3 overflow-hidden">
                            <div class="progress mb-2 progress-sm">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo e($storageInfo['percentageUsed'] ?? ''); ?>%" aria-valuenow="<?php echo e($storageInfo['percentageUsed'] ?? ''); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="text-muted fs-12 d-block text-truncate"><b><?php echo e($storageInfo['used'] ?? ''); ?></b>GB used of <b><?php echo e($storageInfo['total'] ?? ''); ?></b>GB</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="file-manager-content w-100 p-3 py-0">
            <div class="mx-n3 pt-4 px-4 file-manager-content-scroll" data-simplebar>
                <div id="folder-list" class="mb-2">
                    <div class="row justify-content-beetwen g-2 mb-3">
                        <div class="col">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-2 d-block d-lg-none">
                                    <button type="button" class="btn btn-soft-success btn-icon btn-sm fs-16 file-menu-btn">
                                        <i class="ri-menu-2-fill align-bottom"></i>
                                    </button>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="fs-16 mb-0">Folders</h5>
                                </div>
                            </div>
                        </div>
                        <!--end col-->
                        <div class="col-auto">
                            <div class="d-flex gap-2 align-items-start">
                                <select class="form-control" data-choices data-choices-search-false name="choices-single-default" id="file-type">
                                    <option value="">File Type</option>
                                    <option value="All" selected>All</option>
                                    <option value="Video">Video</option>
                                    <option value="Images">Images</option>
                                    <option value="Music">Music</option>
                                    <option value="Documents">Documents</option>
                                </select>

                                <button class="btn btn-success w-sm create-folder-modal flex-shrink-0" data-bs-toggle="modal" data-bs-target="#createFolderModal"><i class="ri-add-line align-bottom me-1"></i> Create Folders</button>
                            </div>
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                    <div class="row" id="folderlist-data">
                        <div class="row" id="folderlist-data">
                            
                            <?php $__currentLoopData = $folders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $folder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-xxl-3 col-6 folder-card">
                                    <div class="card bg-light shadow-none" id="folder-<?php echo e($folder['id'] ?? ''); ?>">
                                        <div class="card-body">
                                            <div class="d-flex mb-1">
                                                <div class="form-check form-check-danger mb-3 fs-15 flex-grow-1">
                                                    <input class="form-check-input" type="checkbox" value="" id="folderlistCheckbox_1" checked="">
                                                    <label class="form-check-label" for="folderlistCheckbox_1"></label>
                                                </div>
                                                <div class="dropdown">
                                                    <button class="btn btn-ghost-primary btn-icon btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="ri-more-2-fill fs-16 align-bottom"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a class="dropdown-item view-item-btn" href="javascript:void(0);">Open</a></li>
                                                        <li><a class="dropdown-item edit-folder-list" href="#createFolderModal" data-bs-toggle="modal" role="button">Rename</a></li>
                                                        <li><a class="dropdown-item" href="#removeFolderModal" data-bs-toggle="modal" role="button">Delete</a></li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="text-center">
                                                <div class="mb-2">
                                                    <i class="ri-folder-2-fill align-bottom text-warning display-5"></i>
                                                </div>
                                                <h6 class="fs-15 folder-name"><?php echo e($folder['name'] ?? ''); ?></h6>
                                            </div>
                                            <div class="hstack mt-4 text-muted">
                                                <span class="me-auto"><b><?php echo e($folder['file_count'] ?? ''); ?></b> Files</span>
                                                <span><b><?php echo e($folder['size'] ?? ''); ?></b>GB</span>
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
                    <div class="d-flex align-items-center mb-3">
                        <h5 class="flex-grow-1 fs-16 mb-0" id="filetype-title">Recent File</h5>
                        <div class="flex-shrink-0">
                            <button class="btn btn-success createFile-modal" data-bs-toggle="modal" data-bs-target="#createFileModal"><i class="ri-add-line align-bottom me-1"></i> Upload File</button>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap mb-0">
                            <thead class="table-active">
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">File Item</th>
                                    <th scope="col">File Size</th>
                                    <th scope="col">Recent Date</th>
                                    <th scope="col" class="text-end"></th>
                                </tr>
                            </thead>
                            <tbody id="file-list">
                                <?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            
                                            <i class="ri-file-text-fill align-bottom text-secondary"></i>
                                            <?php echo e($file['name'] ?? ''); ?>

                                        </td>
                                        <td>
                                            <?php echo e($file['path_lower'] ?? ''); ?>

                                        </td>
                                        <td><?php echo e($file['size'] ?? ''); ?></td>
                                        <td><?php echo e($file['server_modified'] ?? ''); ?></td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <a href="<?php echo e($file['link'] ?? '#'); ?>" download class="btn btn-sm btn-outline-dark" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" aria-label="Visualizar" data-bs-original-title="Download"><i class="ri-download-2-line"></i></a>

                                                <a href="<?php echo e(route('DropboxDeleteFileURL', ['path' => $file['path_display']])); ?>" onclick="return confirm('Are you sure you want to delete this file?')" class="btn btn-sm btn-outline-dark" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" aria-label="Visualizar" data-bs-original-title="Deletar"><i class="ri-delete-bin-5-line text-danger"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- <a href="javascript:prevPage()" id="btn_prev">Prev</a> -->
                    <ul id="pagination" class="pagination pagination-lg"></ul>
                    <!-- <a href="javascript:nextPage()" id="btn_next">Next</a> -->
                    <div class="align-items-center mt-2 row g-3 text-center text-sm-start">
                        <div class="col-sm">
                            <div class="text-muted">
                                Showing<span class="fw-semibold">4</span> of <span class="fw-semibold">125</span> Results
                            </div>
                        </div>
                        <div class="col-sm-auto">
                            <ul class="pagination pagination-separated pagination-sm justify-content-center justify-content-sm-start mb-0">
                                <li class="page-item disabled">
                                    <a href="#" class="page-link">←</a>
                                </li>
                                <li class="page-item">
                                    <a href="#" class="page-link">1</a>
                                </li>
                                <li class="page-item active">
                                    <a href="#" class="page-link">2</a>
                                </li>
                                <li class="page-item">
                                    <a href="#" class="page-link">3</a>
                                </li>
                                <li class="page-item">
                                    <a href="#" class="page-link">→</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <!-- apexcharts -->
    <script src="<?php echo e(URL::asset('build/libs/apexcharts/apexcharts.min.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/settings/files.blade.php ENDPATH**/ ?>