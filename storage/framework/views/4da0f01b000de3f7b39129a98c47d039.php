<h4 class="mb-4">Faturamento</h4>

<?php if($customerId): ?>
    <?php
        try {
            //https://stripe.com/docs/api/invoices/list#list_invoices
            $invoices = $stripe->invoices->all(['customer' => $customerId]);
        } catch (Exception $e) {
            //echo $e->getError()->message;
        }

        try {
            //https://stripe.com/docs/api/invoices/upcoming
            $upcoming = $stripe->invoices->upcoming([
                'customer' => $customerId,
                //'subscription' => $subscriptionId
            ]);
        } catch (Exception $e) {
            $upcoming = '';
            //echo $e->getError()->message;
        }
    ?>

    <?php if(isset($invoices) && is_object($invoices) || is_object($upcoming)): ?>
        <table class="table table-hover table-bordered table-striped table-compact">
            <thead class="table-light">
                <th class="d-none text-uppercase">ID</th>
                <th class="text-uppercase">Decrição</th>
                <th class="text-uppercase">Vencimento</th>
                <th class="text-center text-uppercase">Status</th>
                <th class="text-uppercase">Período de Atividade</th>
                <th></th>
                <th></th>
            </thead>
            <tbody>
                <?php if(!empty($upcoming) && isset($upcoming) && is_object($upcoming) && count($upcoming->lines->data) > 1): ?>

                    <?php
                        $next_payment_attempt = !empty($upcoming['next_payment_attempt']) ? date('d/m/Y', $upcoming['next_payment_attempt']) : '';

                        $period_start = !empty($upcoming['period_start']) ? date('d/m/Y', $upcoming['period_start']) : '';
                        $period_end = !empty($upcoming['period_end']) ? date('d/m/Y', $upcoming['period_end']) : '';

                        $total = '';
                        $total = $upcoming['total'] > 0 ? ($upcoming['total']/100) : 0;
                        $total = $total > 0 ? brazilianRealFormat($total, 2) : '';

                        $status = !empty($upcoming['status']) ? $upcoming['status'] : '';

                        $btn_result = '<button id="btn-modal-upcoming" class="btn btn-sm btn-outline-light text-uppercase" type="button" title="Visualizar Detalhes"><i class="ri-error-warning-line me-1 float-start"></i>Detalhes</button>';
                    ?>

                    <?php if( !empty($status) && $status == 'draft' ): ?>
                        <tr data-listing="upcoming">
                            <td class="d-none align-middle" data-label="ID">
                                #<?php echo e(isset($invoice['receipt_number']) ? $invoice['receipt_number'] : ''); ?>

                            </td>
                            <td class="align-middle" data-label="Descrição">
                                -
                            </td>
                            <td class="align-middle" data-label="Faturamento">
                                <?php echo e($next_payment_attempt); ?>

                            </td>
                            <td class="align-middle text-center" data-label="Status">
                                <span class="badge text-uppercase fs-11px p-1 d-inline-flex align-items-center border small border-warning text-warning" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="Faturamento agendado" data-stripe-case="paid"><i class="ri-checkbox-blank-circle-fill me-1"></i>Agendado</span>
                            </td>
                            <td class="align-middle" data-label="Período">
                                <?php echo e($period_start); ?> <span class="text-theme">&#8646;</span> <?php echo e($period_end); ?>

                            </td>
                            <td class="align-middle text-end" data-label="Total">
                                <?php echo e(!empty($total) ? $total : '-'); ?>

                            </td>
                            <td class="align-middle text-end" data-label="Actions">
                                <?php echo e($btn_result); ?>

                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endif; ?>

                <?php $__currentLoopData = $invoices->autoPagingIterator(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $period_start = !empty($invoice['lines']['data'][0]['period']['end']) ? date('d/m/Y', $invoice['lines']['data'][0]['period']['start']) : '';
                        $period_end = !empty($invoice['lines']['data'][0]['period']['end']) ? date('d/m/Y', $invoice['lines']['data'][0]['period']['end']) : '';

                        $invoice_subscription = !empty($invoice['lines']['data'][0]['subscription']) ? $invoice['lines']['data'][0]['subscription'] : '';

                        $post_payment_credit_notes_amount = $invoice['post_payment_credit_notes_amount'] > 0 ? $invoice['post_payment_credit_notes_amount'] : '';

                        $total = '';
                        $total = $invoice['total'] > 0 ? ($invoice['total']/100) : 0;
                        $total = $total > 0 ? brazilianRealFormat($total, 2) : '';

                        $adjusted_invoice_total = $post_payment_credit_notes_amount ? brazilianRealFormat(($invoice['total'] - $post_payment_credit_notes_amount)/100, 2) : '';

                        $status_label = '-';
                        $btn_result = '-';
                        $status = !empty($invoice['status']) ? $invoice['status'] : '';

                        /**
                         * Check if refunded
                         * https://stripe.com/docs/api/payment_intents/retrieve
                         */
                        $payment_intent = $invoice->payment_intent;
                        if( !empty($payment_intent) ){
                            try {
                                $paymentIntents = $stripe->paymentIntents->retrieve(
                                    $payment_intent
                                );
                                $refunded = isset($paymentIntents->charges->data[0]->refunded) ? $paymentIntents->charges->data[0]->refunded : false;
                                $status = $refunded == true ? 'refunded' : $status;
                            }catch (Exception $e){
                                //echo $e->getError()->message;
                            }
                        }
                        $linethrough = $status == 'refunded' ? 'text-decoration-line-through fw-normal text-primary' : '';

                        //https://stripe.com/docs/invoicing/overview
                        switch ($status) {
                            case 'draft':
                                $status_label = '<span class="badge bg-transparent text-uppercase fs-11px p-1 d-inline-flex align-items-center border small border-info text-info" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="Período contábil ainda não foi encerrado" data-stripe-case="'.$status.'">Rascunho</span>';
                                break;
                            case 'refunded':
                                $status_label = '<span class="badge bg-transparent text-uppercase fs-11px p-1 d-inline-flex align-items-center border small border-primary text-primary" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="Valor Reembolsado" data-stripe-case="'.$status.'"><i class="ri-checkbox-blank-circle-fill me-1"></i>Reembolsado</span>';

                                $btn_result = !empty($total) ? '<a class="btn btn-sm btn-outline-theme text-uppercase" href="'.$invoice['hosted_invoice_url'].'" target="_blank" title="Visualizar Recibo"><i class="ri-file-paper-line float-start me-1"></i>Recibo</a>' : '-';
                                break;
                            case 'open':
                                $status_label = '<span class="badge bg-transparent text-uppercase fs-11px p-1 d-inline-flex align-items-center border small border-warning text-warning" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="Aguardando pagamento" data-stripe-case="'.$status.'"><i class="ri-checkbox-blank-circle-fill me-1"></i>Processando</span>';
                                break;
                            case 'past_due':
                            case 'unpaid':
                                $status_label = '<span class="badge bg-transparent text-uppercase fs-11px p-1 d-inline-flex align-items-center border small border-warning text-warning" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="Não foi possível debitar o valor. Por favor, atualize o método de pagamento." data-stripe-case="'.$status.'"><i class="ri-checkbox-blank-circle-fill align-middle blink"></i>Requer Atenção</span>';

                                $btn_result = !empty($total) ? '<a class="btn btn-sm btn-outline-warning btn-invoice-regularize" href="'.$invoice['hosted_invoice_url'].'" title="Pagar">Pagar</a>' : '-';
                                break;
                            case 'paid':
                                $status_label = '<span class="badge bg-transparent text-uppercase fs-11px p-1 d-inline-flex align-items-center border small border-theme text-theme" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="Esta fatura foi paga" data-stripe-case="'.$status.'"><i class="ri-checkbox-blank-circle-fill me-1"></i>Pago</span>';

                                $btn_result = !empty($total) ? '<a class="btn btn-sm btn-outline-theme text-uppercase" href="'.$invoice['hosted_invoice_url'].'" target="_blank" title="Visualizar Recibo"><i class="ri-file-paper-line float-start me-1"></i>Recibo</a>' : '-';
                                break;
                            case 'void':
                                $status_label = '<span class="badge bg-transparent text-uppercase fs-11px p-1 d-inline-flex align-items-center border small border-danger text-danger" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="Este erro será corrigido" data-stripe-case="'.$status.'"><i class="ri-checkbox-blank-circle-fill me-1"></i>Erro</span>';
                                break;
                            case 'uncollectible':
                                $status_label = '<span class="badge bg-transparent text-uppercase fs-11px p-1 d-inline-flex align-items-center border small border-danger text-danger btn-invoice-uncollectible" data-bs-html="true" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-placement="top" data-bs-content="O débito no cartão não foi efetuado pois a assinatura está/estava suspensa" data-stripe-case="'.$status.'"><i class="ri-checkbox-blank-circle-fill me-1"></i>Não Debitado</span>';
                                break;
                            default:
                                $status_label = '-';
                                $btn_result = '-';
                        }
                    ?>

                    <?php if(!empty($status) && $status != 'draft' && !empty($total)): ?>
                        <tr data-listing="invoive">
                            <td class="d-none align-middle" data-label="ID">
                                #<?php echo e($invoice['number'] ?? ''); ?>

                            </td>
                            <td class="align-middle" data-label="Descrição">
                                <?php echo e($invoice['lines']['data'][0]['description']); ?>

                                <br><code title="Invoice Subscription ID"><?php echo e($invoice_subscription); ?></code>
                            </td>
                            <td class="align-middle" data-label="Faturamento">
                                <?php echo e(!empty($total) && !empty($invoice['status_transitions']['paid_at']) ? date('d/m/Y', $invoice['status_transitions']['paid_at']) : '-'); ?>

                            </td>
                            <td class="align-middle text-center" data-label="Status">
                                <?php echo !empty($total) ? $status_label : '-'; ?>

                            </td>
                            <td class="align-middle" data-label="Período">
                                <?php echo e($period_start); ?> <span class="text-theme">&#8646;</span> <?php echo e($period_end); ?>

                            </td>
                            <td class="align-middle text-end" data-label="Total">
                                <?php if($adjusted_invoice_total): ?>
                                    <?php echo !empty($adjusted_invoice_total) ? '<strong>'.$adjusted_invoice_total.'</strong>' : '-'; ?>

                                    <?php echo !empty($total) ? '<br><strong class="text-decoration-line-through small text-muted">'.$total.'</strong>' : '-'; ?>

                                <?php else: ?>
                                    <?php echo !empty($total) ? '<strong class="'.$linethrough.'">'.$total.'</strong>' : '-'; ?>

                                <?php endif; ?>
                            </td>
                            <td class="align-middle text-end" data-label="Actions">
                                <?php echo $btn_result; ?>

                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    <?php else: ?>
        Não há dados Stripe
    <?php endif; ?>
<?php else: ?>
    Não há dados de Cliente ID na Stripe
<?php endif; ?>
<?php /**PATH D:\www\superametas\applicationV2\development.superametas.com\public_html\resources\views/settings/stripe/invoices.blade.php ENDPATH**/ ?>