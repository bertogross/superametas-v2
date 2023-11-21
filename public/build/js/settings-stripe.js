import {
    toastAlert
} from './helpers.js';

document.addEventListener('DOMContentLoaded', function() {

    // Update subscription (change subscription plan)
    document.addEventListener('click', function(event) {
        const clickedElement = event.target;

        if(clickedElement){
            // Create Session for Subscription
            if (clickedElement.classList.contains('.btn-subscription')) {
                event.preventDefault();
                this.blur();

                var priceId = this.getAttribute('data-price_id');
                var currentPriceId = this.getAttribute('data-current-price_id');
                var quantity = this.getAttribute('data-quantity');

                var params = {
                    'current_price_id': currentPriceId,
                    'price_id': priceId,
                    'quantity': quantity
                };

                fetch(stripeSubscriptionURL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(params)
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        toastAlert(data.message, 'danger');
                        return;
                    }

                    var checkoutURL = data.stripe.url;
                    Swal.fire({
                        confirmButtonClass: 'btn btn-outline-theme text-uppercase d-none',
                        buttonsStyling: false,
                        icon: '',
                        title: '',
                        html: '<img src="' + assetUrl + 'build/images/stripe/white-small.png" title="Stripe" width="100" class="mb-3"><br>Redirecionado para a página de pagamento...'
                    });

                    setTimeout(function() {
                        window.location.href = checkoutURL;
                    }, 2000);
                })
                .catch(error => {
                    var message = error.message || 'Não foi possível proceder com a solicitação.<br>Tente novamente mais tarde.';
                    toastAlert(message, 'danger');
                })
                .finally(() => {
                    //APP_loading();
                });
            }

            // Update subscription (change subscription plan)
            if (clickedElement.classList.contains('#btn-subscription-details')) {
                event.preventDefault();
                var btn = this;
                this.blur();

                var subscriptionId = this.getAttribute('data-subscription_id');
                var priceId = this.getAttribute('data-price_id');
                var quantity = this.getAttribute('data-quantity');

                var params = {
                    'subscription_id': subscriptionId,
                    'price_id': priceId,
                    'quantity': quantity
                };

                fetch(stripeSubscriptionDetailsURL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(params)
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        toastAlert(data.message, 'danger');
                        return;
                    }

                    var checkoutURL = data.stripe.url;
                    Swal.fire({
                        confirmButtonClass: 'btn btn-outline-theme text-uppercase d-none',
                        buttonsStyling: false,
                        icon: '',
                        title: '',
                        html: '<img src="' + assetUrl + 'build/images/stripe/white-small.png" title="Stripe" width="100" class="mb-3"><br>Redirecionado para a página de pagamento...'
                    });

                    setTimeout(function() {
                        window.location.href = checkoutURL;
                    }, 2000);
                })
                .catch(error => {
                    var message = error.message || 'Não foi possível proceder com a solicitação.<br>Tente novamente mais tarde.';
                    toastAlert(message, 'danger');
                })
                .finally(() => {
                    //APP_loading();
                });
            }

            // Remove from cart
            if (clickedElement.classList.contains('.btn-addon-cart-remove')) {
                event.preventDefault();
                var addon = this.getAttribute('data-addon');
                document.querySelector('.btn-addon-cart[data-addon="' + addon + '"]').click();
            }


            // START Addons Cart
            if (clickedElement.classList.contains('.btn-addon-cart')) {
                event.preventDefault();
                var cart = [];
                this.blur();

                if (this.classList.contains('selected')) {
                    if (this.classList.contains('option-label')) {
                        this.classList.remove('selected');
                        this.closest('.option').querySelector('input').checked = false;
                    } else {
                        this.classList.remove('selected');
                        this.innerHTML = 'Adicionar ao Carrinho';
                    }
                } else {
                    if (this.classList.contains('option-label')) {
                        document.querySelectorAll('input[name="storage"]').forEach(function(input) {
                            input.closest('.option').querySelector('.option-label').classList.remove('selected');
                        });

                        this.classList.add('selected');
                        this.closest('.option').querySelector('input').checked = true;
                    } else {
                        this.classList.add('selected');
                        this.innerHTML = '<i class="ri-check-line"></i> Adicionado';
                    }
                }

                document.querySelectorAll('.btn-addon-cart.selected').forEach(function(selectedBtn) {
                    cart.push(selectedBtn.getAttribute('data-addon'));
                });

                var jsonStr = JSON.stringify(cart);
                sessionStorage.setItem('app_addon_cart_IDs', jsonStr);

                var params = {
                    'cart': cart
                };

                fetch(stripeCartAddonURL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(params)
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('load-cart').innerHTML = data.html;
                })
                .finally(() => {
                    //APP_loading();
                });

                var cartValue = sessionStorage.getItem('app_addon_cart_IDs');
                var cartObj = JSON.parse(cartValue);
                if (cartValue && cartObj) {
                    cartObj.forEach(function(value) {
                        if (value) {
                            document.querySelector('.btn-addon-cart[data-addon="' + value + '"]').click();
                        }
                    });
                } else {
                    document.getElementById('load-cart').innerHTML = 'Novos recursos ainda não foram selecionados';
                }
            }
            // END Addons Cart



        }
    });


    document.addEventListener('click', function(event) {
        if (event.target.matches('.input-step .btn-minus-plus')) {
            event.preventDefault();

            var button = event.target;
            var step = 1;
            var target = button.getAttribute('data-target');
            var action = button.getAttribute('data-action');

            var subscriptionButton = document.querySelector('button[data-price_id="' + target + '"]');
            var currentQuantity = parseInt(subscriptionButton.getAttribute('data-quantity')) || 0;

            var newQuantity = action === 'minus' ? Math.max(currentQuantity - step, 0) : currentQuantity + step;

            if (newQuantity === 0) {
                subscriptionButton.setAttribute('disabled', true);
                subscriptionButton.classList.add('btn-outline-theme');
                subscriptionButton.classList.remove('btn-theme');
            } else {
                subscriptionButton.removeAttribute('disabled');
                subscriptionButton.classList.remove('btn-outline-theme');
                subscriptionButton.classList.add('btn-theme');
            }

            var text = newQuantity > step ? newQuantity + ' lojas' : '1 loja';
            var quantityInput = document.querySelector('input.quantity-' + target);
            quantityInput.value = text;
            quantityInput.setAttribute('data-quantity', newQuantity);

            var unitAmount = parseInt(document.querySelector('.price-wrap-' + target).getAttribute('data-unit_amount')) || 0;
            var totalPrice = newQuantity * unitAmount;
            document.querySelector('.price-wrap-' + target).textContent = totalPrice.toLocaleString('pt-BR');

            subscriptionButton.setAttribute('data-quantity', newQuantity);
        }
    });


});
