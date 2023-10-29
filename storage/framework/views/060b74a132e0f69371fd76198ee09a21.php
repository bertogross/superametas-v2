<?php if(session('success')): ?>
    <!-- Success Alert -->
    <div id="success-alert" class="alert alert-success alert-dismissible alert-label-icon label-arrow fade show" role="alert">
        <i class="ri-check-double-line label-icon"></i><?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss=" alert" aria-label="Close"></button>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Check if the element exists
            var alert = document.getElementById('success-alert');
            if (alert) {
                // Set a timeout to hide the alert after 10 seconds
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 30000);
            }
        });
    </script>
<?php endif; ?>

<?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views/error/alert-success.blade.php ENDPATH**/ ?>