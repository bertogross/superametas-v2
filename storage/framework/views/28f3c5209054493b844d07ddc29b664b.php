<?php $__env->startSection('title'); ?>
    Scenex
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            <?php echo app('translator')->get('translation.session'); ?>
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?> Scenex  <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <form enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <input type="file" id="imageInput">
        <br>
        <textarea id="textInput" style="width: 500px;" rows="4"></textarea>
        <br>
        <button id="submitButton" style="display: none;">Submit</button>
    </form>

    <div id="results" style="width: 700px; height: 700px;"></div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script>
    const resultsDiv = document.getElementById('results');
    const inputFile = document.getElementById('imageInput');
    const inputText = document.getElementById('textInput');

    if (inputFile) {
        inputFile.addEventListener("change", function() {
            const file = inputFile.files[0];
            const reader = new FileReader();

            reader.addEventListener("load", function() {

                const img = new Image();
                img.src = reader.result;
                //console.log("Image source:", img.src);

                img.onload = function() {
                    //console.log("Image loaded with dimensions:", img.width, "x", img.height);

                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');

                    let targetWidth = img.width;
                    let targetHeight = img.height;

                    if (targetWidth > 1920 || targetHeight > 1920) {
                        const aspectRatio = targetWidth / targetHeight;
                        if (targetWidth > targetHeight) {
                            targetWidth = 1920;
                            targetHeight = targetWidth / aspectRatio;
                        } else {
                            targetHeight = 1920;
                            targetWidth = targetHeight * aspectRatio;
                        }
                    }

                    canvas.width = targetWidth;
                    canvas.height = targetHeight;
                    ctx.drawImage(img, 0, 0, targetWidth, targetHeight);


                    canvas.toBlob(function(blob) {
                        const formData = new FormData();
                        formData.append('file', blob, file.name);
                        formData.append('text', inputText.value);

                        //console.log("Blob size:", blob.size);

                        fetch('<?php echo e(route('ScenexSubmitURL')); ?>', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(response => {
                            console.log(response);

                            if (response.success) {
                                //toastAlert(response.message, 'success');
                            } else {
                                //toastAlert(response.message, 'danger');
                            }
                            //resultsDiv.innerHTML = JSON.stringify(response);
                            if (response.message) {
                                resultsDiv.innerHTML = '<p>' + response.message + '</p>';
                            }
                            if (response.results) {
                                resultsDiv.innerHTML += '<p><img src="' + response.results.result[0]['image'] + '" width="500">' + response.results.result[0]['i18n']['pt'] + '</p>';
                            }
                        })
                        .catch(error => {
                            //toastAlert('Upload failed: ' + error, 'danger');
                            console.error('Error:', error);
                        });
                    }, 'image/jpeg', 0.7);
                };
            }, false);

            if (file) {
                reader.readAsDataURL(file);
            }
        });

    }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/audits/scenex/submit.blade.php ENDPATH**/ ?>