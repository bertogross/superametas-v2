<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.range-slider'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('build/libs/nouislider/nouislider.min.css')); ?>" rel="stylesheet">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Forms
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Range Slider
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Bootstrap Range</h4>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                            <label for="default-rangeslider" class="form-label text-muted">Show Code</label>
                            <input class="form-check-input code-switcher" type="checkbox" id="default-rangeslider">
                        </div>
                    </div>
                </div><!-- end card header -->

                <div class="card-body">
                    <div class="live-preview">
                        <div>
                            <div>
                                <h5 class="fs-13">Default Range</h5>
                                <p class="text-muted">Use <code>type="range"</code> attribute and
                                    <code>form-range</code> class to create a deafult range.</p>
                                <!-- Default Range -->
                                <input type="range" class="form-range" id="customRange1">
                            </div>

                            <div class="mt-4">
                                <h5 class="fs-13">Disabled</h5>
                                <p class="text-muted">Use <code>disabled</code> attribute on an input to give it a
                                    grayed out appearance and remove pointer events.</p>

                                <!-- Disabled Range -->
                                <input type="range" class="form-range" id="disabledRange" disabled>
                            </div>

                            <div class="mt-4">
                                <h5 class="fs-13">Min and Max</h5>
                                <p class="text-muted">Use <code>min</code> and <code>max</code> attribute with specified
                                    range input respectively.</p>

                                <!-- Min and Max -->
                                <input type="range" class="form-range" min="0" max="5" id="customRange2">
                            </div>

                            <div class="mt-4">
                                <h5 class="fs-13">Steps</h5>
                                <p class="text-muted">By default, range inputs “snap” to integer values. To change this,
                                    you can specify a step value. In the example below, we double the number of steps by
                                    using <code>step="0.5"</code> attribute.</p>

                                <!-- Steps -->
                                <input type="range" class="form-range" min="0" max="5" step="0.5" id="customRange3">
                            </div>
                        </div>
                    </div>


                    <div class="d-none code-view">
                        <pre class="language-markup">
<code>&lt;!-- Default Range --&gt;
&lt;input type=&quot;range&quot; class=&quot;form-range&quot; id=&quot;customRange1&quot;&gt;

&lt;!-- Disabled Range --&gt;
&lt;input type=&quot;range&quot; class=&quot;form-range&quot; id=&quot;disabledRange&quot; disabled&gt;

&lt;!-- Min and Max --&gt;
&lt;input type=&quot;range&quot; class=&quot;form-range&quot; min=&quot;0&quot; max=&quot;5&quot; id=&quot;customRange2&quot;&gt;

&lt;!-- Steps --&gt;
&lt;input type=&quot;range&quot; class=&quot;form-range&quot; min=&quot;0&quot; max=&quot;5&quot; step=&quot;0.5&quot; id=&quot;customRange3&quot;&gt;</code></pre>
                    </div>

                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div> <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Nouislider</h4>
                </div><!-- end card header -->

                <div class="card-body">

                    <p class="text-muted mb-4">noUiSlider is a lightweight JavaScript range slider</p>

                    <div class="live-preview">
                        <div>
                            <div class="mb-3">
                                <div class="row align-items-center">
                                    <div class="col-lg-3">
                                        <h5 class="fs-13">Basic Example</h5>
                                    </div><!-- end col -->
                                    <div class="col-lg-9">
                                        <div data-rangeslider></div>
                                    </div><!-- end col -->
                                </div><!-- end row -->
                            </div>

                            <div class="border border-dashed"></div>

                            <div class="my-4">
                                <div class="row align-items-center">
                                    <div class="col-lg-3">
                                        <h5 class="fs-13">Multi elements range handle</h5>
                                    </div><!-- end col -->
                                    <div class="col-lg-9">
                                        <div data-multielement></div>
                                    </div><!-- end col -->
                                </div><!-- end row -->
                            </div>

                            <div class="border border-dashed"></div>

                            <div class="my-4">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <h5 class="fs-13">Colorpicker</h5>
                                    </div><!-- end col -->
                                    <div class="col-lg-9">
                                        <div class="sliders" id="red"></div>
                                        <div class="sliders" id="green"></div>
                                        <div class="sliders" id="blue"></div>

                                        <div id="result"></div>
                                    </div><!-- end col -->
                                </div><!-- end row -->
                            </div>

                            <div class="border border-dashed"></div>

                            <div class="my-4">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <h5 class="fs-13">Using HTML5 Input Elements</h5>
                                    </div><!-- end col -->
                                    <div class="col-lg-9">
                                        <div class="d-inline-flex gap-2 mb-3">
                                            <select id="input-select"
                                                class="form-select form-select-sm w-xs shadow-none"></select>
                                            <input type="number" class="form-control form-control-sm w-xs shadow-none"
                                                min="-20" max="40" step="1" id="input-number">
                                        </div>
                                        <div id="html5"></div>

                                    </div><!-- end col -->
                                </div><!-- end row -->
                            </div>

                            <div class="border border-dashed"></div>

                            <div class="my-4">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <h5 class="fs-13">Non linear slider</h5>
                                    </div><!-- end col -->
                                    <div class="col-lg-9">
                                        <div id="nonlinear"></div>
                                        <div class="d-flex justify-content-between">
                                            <div class="example-val" id="lower-value"></div>
                                            <div class="example-val" id="upper-value"></div>
                                        </div>
                                    </div><!-- end col -->
                                </div><!-- end row -->
                            </div>

                            <div class="border border-dashed"></div>

                            <div class="my-4">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <h5 class="fs-13">Locking sliders together</h5>
                                    </div><!-- end col -->
                                    <div class="col-lg-9">
                                        <div class="slider" id="slider1"></div>
                                        <span class="example-val mt-2" id="slider1-span"></span>

                                        <div class="slider" id="slider2"></div>
                                        <span class="example-val mt-2" id="slider2-span"></span>

                                        <button id="lockbutton" class="btn btn-primary"
                                            style="float: right; margin: 20px 0 0;">Lock</button>
                                    </div><!-- end col -->
                                </div><!-- end row -->
                            </div>

                            <div class="border border-dashed"></div>

                            <div class="my-4">
                                <div class="row align-items-center">
                                    <div class="col-lg-3">
                                        <h5 class="fs-13">Merging overlapping tooltips</h5>
                                    </div><!-- end col -->
                                    <div class="col-lg-9">
                                        <div class="slider" id="slider-merging-tooltips"></div>
                                    </div><!-- end col -->
                                </div><!-- end row -->
                            </div>

                            <div class="border border-dashed"></div>

                            <div class="my-4">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <h5 class="fs-13">Only showing tooltips when sliding handle</h5>
                                    </div><!-- end col -->
                                    <div class="col-lg-9">
                                        <div class="slider" id="slider-hide"></div>
                                    </div><!-- end col -->
                                </div><!-- end row-->
                            </div>

                            <div class="border border-dashed"></div>

                            <div class="mt-4 mb-5">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <h5 class="fs-13">Moving the slider by clicking pips</h5>
                                    </div><!-- end col -->
                                    <div class="col-lg-9">
                                        <div class="slider" id="slider-pips"></div>
                                    </div><!-- end col -->
                                </div><!-- end row-->
                            </div>

                            <div class="border border-dashed"></div>

                            <div class="my-4">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <h5 class="fs-13">Colored Connect Elements</h5>
                                    </div><!-- end col -->
                                    <div class="col-lg-9">
                                        <div class="slider" id="slider-color"></div>
                                    </div><!-- end col -->
                                </div><!-- end row -->
                            </div>

                            <div class="border border-dashed"></div>

                            <div class="my-4">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <h5 class="fs-13">Creating a toggle</h5>
                                    </div><!-- end col -->
                                    <div class="col-lg-9">
                                        <div id="slider-toggle"></div>
                                    </div><!-- end col -->
                                </div><!-- end row -->
                            </div>

                            <div class="border border-dashed"></div>

                            <div class="my-4">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <h5 class="fs-13">Soft limits</h5>
                                    </div><!-- end col -->
                                    <div class="col-lg-9">
                                        <div id="soft"></div>
                                    </div><!-- end col -->
                                </div><!-- end row -->
                            </div>
                        </div>
                    </div>

                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Color Scheme</h4>
                </div><!-- end card header -->
                <div class="card-body">

                    <div class="row">
                        <div class="col-xl-4 col-lg-6">
                            <div>
                                <h5 class="fs-13">Primary</h5>
                                <p class="text-muted mb-4">Use <code>data-slider-color="primary"</code> attribute to set
                                    primary color scheme.</p>
                                <div data-rangeslider data-slider-color="primary"></div>
                            </div>
                        </div>
                        <!-- end col -->
                        <div class="col-xl-4 col-lg-6">
                            <div class="mt-4 mt-lg-0">
                                <h5 class="fs-13">Secondary</h5>
                                <p class="text-muted mb-4">Use <code>data-slider-color="secondary"</code> attribute to set
                                    secondary color scheme.</p>
                                <div data-rangeslider data-slider-color="secondary"></div>
                            </div>
                        </div>
                        <!-- end col -->
                        <div class="col-xl-4 col-lg-6">
                            <div class="mt-4 mt-xl-0">
                                <h5 class="fs-13">Success</h5>
                                <p class="text-muted mb-4">Use <code>data-slider-color="success"</code> attribute to set
                                    success color scheme.</p>
                                <div data-rangeslider data-slider-color="success"></div>
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->

                    <div class="row mt-4">
                        <div class="col-xl-4 col-lg-6">
                            <div class="mt-4">
                                <h5 class="fs-13">Info</h5>
                                <p class="text-muted mb-4">Use <code>data-slider-color="info"</code> attribute to set info
                                    color scheme.</p>
                                <div data-rangeslider data-slider-color="info"></div>
                            </div>
                        </div>
                        <!-- end col -->
                        <div class="col-xl-4 col-lg-6">
                            <div class="mt-4">
                                <h5 class="fs-13">Warning</h5>
                                <p class="text-muted mb-4">Use <code>data-slider-color="warning"</code> attribute to set
                                    warning color scheme.</p>
                                <div data-rangeslider data-slider-color="warning"></div>
                            </div>
                        </div>
                        <!-- end col -->
                        <div class="col-xl-4 col-lg-6">
                            <div class="mt-4">
                                <h5 class="fs-13">Danger</h5>
                                <p class="text-muted mb-4">Use <code>data-slider-color="danger"</code> attribute to set
                                    danger color scheme.</p>
                                <div data-rangeslider data-slider-color="danger"></div>
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->

                    <div class="row mt-4">
                        <div class="col-xl-4 col-lg-6">
                            <div class="mt-4">
                                <h5 class="fs-13">Dark</h5>
                                <p class="text-muted mb-4">Use <code>data-slider-color="dark"</code> attribute to set dark
                                    color scheme.</p>
                                <div data-rangeslider data-slider-color="dark"></div>
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Sizes</h4>
                </div><!-- end card header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-4 col-lg-6">
                            <div>
                                <h5 class="fs-13">Large</h5>
                                <p class="text-muted mb-4">Use <code>data-slider-size="lg"</code> attribute to set large
                                    size rangeslider.</p>
                                <div data-rangeslider data-slider-size="lg"></div>
                            </div>
                        </div>
                        <!-- end col -->
                        <div class="col-xl-4 col-lg-6">
                            <div class="mt-4 mt-lg-0">
                                <h5 class="fs-13">Medium</h5>
                                <p class="text-muted mb-4">Use <code>data-slider-size="md"</code> attribute to set medium
                                    size data-rangeslider.</p>
                                <div data-rangeslider data-slider-size="md"></div>
                            </div>
                        </div>
                        <!-- end col -->

                        <div class="col-xl-4 col-lg-6">
                            <div class="mt-4 mt-xl-0">
                                <h5 class="fs-13">Small</h5>
                                <p class="text-muted mb-4">Use <code>data-slider-size="sm"</code> attribute to set small
                                    size rangeslider.</p>
                                <div data-rangeslider data-slider-size="sm"></div>
                            </div>
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                </div>
                <!-- end card body -->
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Styling</h4>
                </div><!-- end card header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div>
                                <h5 class="fs-13">Line Rangeslider</h5>
                                <p class="text-muted mb-4">Use <code>data-slider-style="line"</code> attribute to set line
                                    rangeslider.</p>
                                <div data-rangeslider data-slider-style="line"></div>
                            </div>
                        </div>
                        <!-- end col -->
                        <div class="col-lg-6">
                            <div class="mt-4 mt-lg-0">
                                <h5 class="fs-13">Circle Bordered Rangeslider</h5>
                                <p class="text-muted mb-4">Use <code>data-slider-style="border"</code> attribute to set
                                    circle bordered rangeslider.</p>
                                <div data-rangeslider data-slider-style="border"></div>
                            </div>
                        </div>
                        <!-- end col -->

                        <div class="row mt-4">
                            <div class="col-lg-6">
                                <div class="mt-4">
                                    <h5 class="fs-13">Square Rangeslider</h5>
                                    <p class="text-muted mb-4">Use <code>data-slider-style="square"</code> attribute to set
                                        square shape rangeslider.</p>
                                    <div data-rangeslider data-slider-style="square"></div>
                                </div>
                            </div>
                            <!-- end col -->
                        </div>
                        <!-- end row -->
                    </div>
                    <!-- end card body -->
                </div>
                <!-- end card -->
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/libs/nouislider/nouislider.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/wnumb/wnumb.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/js/pages/range-sliders.init.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/js/app.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\www\superametas\application\development.superametas.com\public_html\resources\views\_many_other_templates\forms-range-sliders.blade.php ENDPATH**/ ?>