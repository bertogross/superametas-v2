<h4 class="mb-4">Assinatura</h4>

<div class="row">
    <?php if($subscriptionId): ?>
        <?php
            try {
                $subscriptionItems = $stripe->subscriptionItems->all([
                    'subscription' => $subscriptionId,
                ]);
                $subscriptionItemId = $subscriptionId && isset($subscriptionItems->data[0]->id) ? $subscriptionItems->data[0]->id : '';
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        ?>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo e($error); ?></div>
        <?php endif; ?>
    <?php endif; ?>

    <?php
        $products = $stripe->products->all([
            'active' => true,
            'limit' => 100
        ]);
        $products = $products->data ?? [];
    ?>
    <?php if(!empty($products)): ?>
        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $productId = $product['id'] ?? '';
                $productName = $product['name'] ?? '';
                $productDescription = $product['description'] ?? '';

                //https://stripe.com/docs/api/prices/list
                $prices = $stripe->prices->all(
                    ['product' => $productId,
                    'active' => true,
                    'limit' => 100,
                    'expand' => ['data.product']
                ]);
                $prices = isset($prices->data) ? $prices->data : '';

                asort($prices);

            ?>
            <?php if( !empty($prices) && is_array($prices) ): ?>
                <?php $__currentLoopData = $prices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $price): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $PriceId = isset($price['id']) ? $price['id'] : '';

                        $nickname = $price['nickname'] ? $price['nickname'] : '';

                        $unitAmount = $price['unit_amount'] ? intval($price['unit_amount'])/100 : '';

                        $recurring = isset($price['recurring']) ? $price['recurring']->interval : '';

                        $intervalCount = isset($price['recurring']) ? $price['recurring']->interval_count : '';

                        $interval = isset($price['recurring']) ? $price['recurring']->interval : '';

                        $metadata = isset($price['product']) ? $price['product']->metadata : '';
                        $planType = isset($metadata->plan_type) ? trim($metadata->plan_type) : '';
                        switch ($planType) {
                            case 'annual':
                                $planTypeText = 'contrato 12 meses';
                                break;
                            case 'quarterly':
                                $planTypeText = 'contrato 3 meses';
                                break;
                            default:
                                $planTypeText = '';
                        }
                    ?>
                    <?php if(count($prices) == 1): ?>
                        <div class="col-sm-12 col-md-12 col-lg-6 col-xl-4 m-auto">
                            <div class="card pricing-box bg-black bg-opacity-10 ribbon-box right">
                                <div class="card-body p-4 m-2 <?php echo e(isset($currentPriceId) && $currentPriceId == $PriceId ? 'bg-light' : ''); ?>">

                                    <?php if(isset($currentPriceId) && $currentPriceId == $PriceId): ?>
                                        <div class="ribbon-two ribbon-two-theme"><span class="small">Vigente</span></div>
                                    <?php endif; ?>

                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h4 class="mb-1 fw-semibold text-center text-uppercase">
                                                <?php echo e($productName); ?>

                                            </h4>
                                        </div>
                                    </div>

                                    <div class="pt-4">
                                        <h1 class="text-center">
                                            <sup><small class="small">R$</small></sup>
                                            <span class="price-wrap-<?php echo e($PriceId); ?> text-theme" data-unit_amount="<?php echo e(numberFormat($unitAmount, 0)); ?>">
                                                <?php echo e(isset($currentPriceId) && $currentPriceId == $PriceId ? number_format((($unitAmount/$intervalCount) * $currentQuantity), 0, ',', '.') : number_format(($unitAmount/$intervalCount), 0, ',', '.')); ?>

                                            </span>
                                        </h1>
                                        <div class="form-text text-center text-white">
                                            <span class="small">Por Loja/mês: <?php echo e(brazilianRealFormat(($unitAmount/$intervalCount), 0)); ?></span>
                                            <?php echo e(!empty($planTypeText) ? '<span class="text-danger fs-13">*</span>' : ''); ?>

                                        </div>
                                    </div>

                                    <div class="mt-4 mb-4">
                                        <ul class="list-unstyled text-muted vstack gap-2 text-center">
                                            <li>
                                                Integração Inclusa
                                            </li>
                                            <li>
                                                Módulo de Metas
                                            </li>
                                            <li>
                                                Notificações
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="input-step full-width light <?php echo e(isset($currentPriceId) && $currentPriceId == $PriceId ? 'bg-soft-primary' : ''); ?>">
                                        <button type="button" class="minus btn-minus-plus" data-action="minus" data-target="<?php echo e($PriceId); ?>">-</button>
                                        <input class="quantity-<?php echo e($PriceId); ?>" type="text" placeholder="<?php echo e(isset($currentPriceId) && $currentPriceId == $PriceId && isset($currentQuantity) ? $currentQuantity.' Lojas' : 'Número de Lojas'); ?>" readonly autocomplete="off">
                                        <button type="button" class="plus btn-minus-plus" data-action="plus" data-target="<?php echo e($PriceId); ?>">+</button>
                                    </div>

                                    <div class="mt-4">
                                        <button data-product_id="<?php echo e($productId); ?>" data-price_id="<?php echo e($PriceId); ?>" data-recurring="<?php echo e($recurring); ?>" data-interval_count="<?php echo e($intervalCount); ?>" data-current-quantity="<?php echo e(isset($currentQuantity) ? $currentQuantity : 0); ?>" data-quantity="<?php echo e(isset($currentPriceId) && $currentPriceId == $PriceId && isset($currentQuantity) ? $currentQuantity : 0); ?>" class="btn btn-outline-theme w-100 waves-effect waves-light text-uppercase <?php echo e(isset($currentPriceId) && $currentPriceId == $PriceId ? 'btn-subscription-update' : 'btn-subscription'); ?>" data-current-price_id="<?php echo e(isset($currentPriceId) ? $currentPriceId : ''); ?>" data-subscription_item_id="<?php echo e($subscriptionItemId); ?>" disabled><?php echo e(isset($currentPriceId) && $currentPriceId == $PriceId ? 'Atualizar' : 'Contratar'); ?></button>

                                        <div class="form-text small text-center fs-11 text-white text-center mt-2">
                                            <?php echo e(!empty($productDescription) ? $productDescription : ''); ?>

                                            <?php echo e(!empty($planTypeText) ? '<div class="small"><span class="text-danger fs-13">*</span>'.$planTypeText.'</div>' : ''); ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php elseif( count($prices) > 1 && count($prices) < 4): ?>
                        <div class="col-sm-12 col-md-12 col-lg-6 col-xl-4">
                        <div class="card pricing-box bg-black bg-opacity-10 ribbon-box right">
                            <div class="card-body p-4 m-2 <?php echo e(isset($currentPriceId) && $currentPriceId == $PriceId ? 'bg-light' : ''); ?>">

                                <?php if(isset($currentPriceId) && $currentPriceId == $PriceId): ?>
                                    <div class="ribbon-two ribbon-two-theme"><span class="small">Vigente</span></div>
                                <?php endif; ?>

                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h4 class="mb-1 fw-semibold text-center text-uppercase">
                                            <?php echo e($productName); ?>

                                        </h4>
                                    </div>
                                </div>

                                <div class="pt-4">
                                    <h1 class="text-center">
                                        <sup><small class="small">R$</small></sup>
                                        <span class="price-wrap-<?php echo e($PriceId); ?> text-theme" data-unit_amount="<?php echo e(numberFormat($unitAmount, 0)); ?>">
                                            <?php echo e(isset($currentPriceId) && $currentPriceId == $PriceId ? number_format((($unitAmount/$intervalCount) * $currentQuantity), 0, ',', '.') : number_format(($unitAmount/$intervalCount), 0, ',', '.')); ?>

                                        </span>
                                    </h1>
                                    <div class="form-text text-center text-white">
                                        <span class="small">Por Loja/mês: <?php echo e(brazilianRealFormat(($unitAmount/$intervalCount), 0)); ?></span>
                                        <?php echo e(!empty($planTypeText) ? '<span class="text-danger fs-13">*</span>' : ''); ?>

                                    </div>
                                </div>

                                <div class="mt-4 mb-4">
                                    <ul class="list-unstyled text-muted vstack gap-2 text-center">
                                        <li>
                                            Integração Inclusa
                                        </li>
                                        <li>
                                            Módulo de Metas
                                        </li>
                                        <li>
                                            Notificações
                                        </li>
                                    </ul>
                                </div>

                                <div class="input-step full-width light <?php echo e(isset($currentPriceId) && $currentPriceId == $PriceId ? 'bg-soft-primary' : ''); ?>">
                                    <button type="button" class="minus btn-minus-plus" data-action="minus" data-target="<?php echo e($PriceId); ?>">-</button>
                                    <input class="quantity-<?php echo e($PriceId); ?>" type="text" placeholder="<?php echo e(isset($currentPriceId) && $currentPriceId == $PriceId && isset($currentQuantity) ? $currentQuantity.' Lojas' : 'Número de Lojas'); ?>" readonly autocomplete="off">
                                    <button type="button" class="plus btn-minus-plus" data-action="plus" data-target="<?php echo e($PriceId); ?>">+</button>
                                </div>

                                <div class="mt-4">
                                    <button data-product_id="<?php echo e($productId); ?>" data-price_id="<?php echo e($PriceId); ?>" data-recurring="<?php echo e($recurring); ?>" data-interval_count="<?php echo e($intervalCount); ?>" data-current-quantity="<?php echo e(isset($currentQuantity) ? $currentQuantity : 0); ?>" data-quantity="<?php echo e(isset($currentPriceId) && $currentPriceId == $PriceId && isset($currentQuantity) ? $currentQuantity : 0); ?>" class="btn btn-outline-theme w-100 waves-effect waves-light text-uppercase <?php echo e(isset($currentPriceId) && $currentPriceId == $PriceId ? 'btn-subscription-update' : 'btn-subscription'); ?>" data-current-price_id="<?php echo e(isset($currentPriceId) ? $currentPriceId : ''); ?>" data-subscription_item_id="<?php echo e($subscriptionItemId); ?>" disabled><?php echo e(isset($currentPriceId) && $currentPriceId == $PriceId ? 'Atualizar' : 'Contratar'); ?></button>
                                    <div class="form-text small text-center fs-11 text-white text-center mt-2">
                                        <?php echo e(!empty($productDescription) ? $productDescription : '&nbsp;'); ?>

                                        <?php echo e(!empty($planTypeText) ? '<div class="small"><span class="text-danger fs-13">*</span>'.$planTypeText.'</div>' : '<div>&nbsp;</div>'); ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                    <?php elseif(count($prices) > 3): ?>
                        <div class="col-lg-12">
                            <div class="card pricing-box bg-soft-primary bg-opacity-10 text-center ribbon-box ribbon-fill">

                                <?php if(isset($currentPriceId) && $currentPriceId == $PriceId): ?>
                                    <div class="ribbon bg-theme text-black"><span class="small">Vigente</span></div>
                                <?php endif; ?>
                                <div class="row g-0">
                                    <div class="col-lg-6">
                                        <div class="card-body h-100">
                                            <h4 class="mb-0 text-uppercase"><?php echo e($productName); ?></h4>

                                            <div class="py-4 pb-0">
                                                <h2>
                                                    <sup><small>R$</small></sup><span class="price-wrap-<?php echo e($PriceId); ?> text-theme" data-unit_amount="<?php echo e(numberFormat($unitAmount, 0)); ?>">
                                                        <?php echo e(isset($currentPriceId) && $currentPriceId == $PriceId ? numberFormat((($unitAmount/$intervalCount) * $currentQuantity), 0) : numberFormat(($unitAmount/$intervalCount), 0)); ?>

                                                    </span>
                                                </h2>
                                                <div class="form-text small text-center text-white fs-11">
                                                    Por Loja/mês: <?php echo e(brazilianRealFormat(($unitAmount/$intervalCount), 0)); ?>

                                                    <?php echo e(!empty($planTypeText) ? '<sup class="text-danger">*</sup>' : ''); ?>

                                                </div>
                                            </div>

                                            <div class="input-step full-width light mt-2">
                                                <button type="button" class="minus btn-minus-plus" data-action="minus" data-target="<?php echo e($PriceId); ?>">-</button>
                                                <input class="quantity-<?php echo e($PriceId); ?>" type="text" placeholder="<?php echo e(isset($currentPriceId) && $currentPriceId == $PriceId && isset($currentQuantity) ? $currentQuantity.' Lojas' : 'Número de Lojas'); ?>" readonly autocomplete="off">
                                                <button type="button" class="plus btn-minus-plus" data-action="plus" data-target="<?php echo e($PriceId); ?>">+</button>
                                            </div>

                                            <div class="text-center plan-btn mt-3">
                                                <button data-product_id="<?php echo e($productId); ?>" data-price_id="<?php echo e($PriceId); ?>" data-recurring="<?php echo e($recurring); ?>" data-interval_count="<?php echo e($intervalCount); ?>" data-current-quantity="<?php echo e(isset($currentQuantity) ? $currentQuantity : 0); ?>" data-quantity="<?php echo e(isset($currentPriceId) && $currentPriceId == $PriceId && isset($currentQuantity) ? $currentQuantity : 0); ?>" class="btn btn-outline-theme w-sm waves-effect waves-light text-uppercase <?php echo e(isset($currentPriceId) && $currentPriceId == $PriceId ? 'btn-subscription-update' : 'btn-subscription'); ?>" data-current-price_id="<?php echo e(isset($currentPriceId) ? $currentPriceId : ''); ?>" data-subscription_item_id="<?php echo e($subscriptionItemId); ?>" disabled><?php echo e(isset($currentPriceId) && $currentPriceId == $PriceId ? 'Atualizar' : 'Contratar'); ?></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="card-body border-start mt-4 mt-lg-0">
                                            <div class="card-header bg-light">
                                                <h5 class="fs-15 mb-0 text-uppercase">Recursos</h5>
                                            </div>
                                            <div class="card-body pb-0">
                                                <ul class="list-unstyled vstack gap-3 mb-0">
                                                    <li>
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0 text-theme me-1">
                                                                <i class="ri-checkbox-circle-fill fs-15 align-bottom"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                Integração Inclusa
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0 text-theme me-1">
                                                                <i class="ri-checkbox-circle-fill fs-15 align-bottom"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                Módulo de Metas
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0 text-theme me-1">
                                                                <i class="ri-checkbox-circle-fill fs-15 align-bottom"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                Notificações
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>

                                                <div class="form-text small text-center fs-11 text-white text-center mt-2">
                                                    <?php echo e(!empty($productDescription) ? $productDescription : '&nbsp;'); ?>

                                                    <?php echo e(!empty($planTypeText) ? '<div class="small"><span class="text-danger">*</span>'.$planTypeText.'</div>' : ''); ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">Ainda não há planos de assinatura disponíveis.</div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
        <div class="alert alert-warning">Ainda não há produtos disponíveis.</div>
    <?php endif; ?>
</div>

<div id="load-cart"></div>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views\settings\stripe\subscription.blade.php ENDPATH**/ ?>