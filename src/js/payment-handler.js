import { PAYPAL_CLIENT_ID, PAYPAL_CURRENCY, PAYPAL_INTENT, PAYMENT_CONFIG } from './paypal-config.js';

class PaymentHandler {
    constructor(cartTotal, cartItems) {
        this.cartTotal = cartTotal;
        this.cartItems = cartItems;
        this.supportedPaymentMethods = [];
        this.initializePaymentMethods();
    }

    async initializePaymentMethods() {
        // First check Payment Request API support
        if (window.PaymentRequest) {
            // Add standard card payment method
            const standardCardMethod = {
                supportedMethods: 'basic-card',
                data: PAYMENT_CONFIG.standardCard
            };

            try {
                const request = new PaymentRequest([standardCardMethod], this.getPaymentDetails());
                if (await request.canMakePayment()) {
                    this.supportedPaymentMethods.push('standard-card');
                }
            } catch (error) {
                console.error('Standard card payment support check failed:', error);
            }

            // Check Google Pay support
            if (await this.isGooglePaySupported()) {
                this.supportedPaymentMethods.push('googlepay');
            }
            
            // Check Apple Pay support
            if (await this.isApplePaySupported()) {
                this.supportedPaymentMethods.push('applepay');
            }
        }

        // Check PayPal support
        if (window.paypal) {
            this.supportedPaymentMethods.push('paypal');
        }

        this.updatePaymentUI();
    }

    async isGooglePaySupported() {
        const googlePayMethod = {
            supportedMethods: 'https://google.com/pay',
            data: {
                environment: PAYMENT_CONFIG.googlePay.environment,
                apiVersion: PAYMENT_CONFIG.googlePay.apiVersion,
                apiVersionMinor: PAYMENT_CONFIG.googlePay.apiVersionMinor,
                merchantInfo: {
                    merchantId: PAYMENT_CONFIG.googlePay.merchantId
                },
                allowedPaymentMethods: [{
                    type: 'CARD',
                    parameters: {
                        allowedAuthMethods: ['PAN_ONLY', 'CRYPTOGRAM_3DS'],
                        allowedCardNetworks: PAYMENT_CONFIG.standardCard.supportedNetworks.map(network => 
                            network.toUpperCase()
                        )
                    }
                }]
            }
        };

        try {
            const request = new PaymentRequest([googlePayMethod], this.getPaymentDetails());
            return await request.canMakePayment();
        } catch (error) {
            console.error('Google Pay support check failed:', error);
            return false;
        }
    }

    async isApplePaySupported() {
        const applePayMethod = {
            supportedMethods: 'https://apple.com/apple-pay',
            data: PAYMENT_CONFIG.applePay
        };

        try {
            const request = new PaymentRequest([applePayMethod], this.getPaymentDetails());
            return await request.canMakePayment();
        } catch (error) {
            console.error('Apple Pay support check failed:', error);
            return false;
        }
    }

    getPaymentDetails() {
        return {
            total: {
                label: 'Total',
                amount: {
                    currency: 'EUR',
                    value: this.cartTotal.toString()
                }
            },
            displayItems: this.cartItems.map(item => ({
                label: item.name,
                amount: {
                    currency: 'EUR',
                    value: (item.price * item.quantity).toString()
                }
            }))
        };
    }

    updatePaymentUI() {
        const paymentContainer = document.getElementById('payment-methods-container');
        paymentContainer.innerHTML = '';

        // Add payment buttons based on support
        this.supportedPaymentMethods.forEach(method => {
            const button = this.createPaymentButton(method);
            paymentContainer.appendChild(button);
        });

        // Always add fallback payment form
        const fallbackForm = this.createFallbackForm();
        paymentContainer.appendChild(fallbackForm);
    }

    createPaymentButton(method) {
        const button = document.createElement('button');
        button.className = `payment-button ${method}-button`;
        
        switch (method) {
            case 'standard-card':
                button.innerHTML = 'Mit Karte bezahlen';
                button.onclick = () => this.handleStandardCardPayment();
                break;
            case 'googlepay':
                button.innerHTML = 'Mit Google Pay bezahlen';
                button.onclick = () => this.handleGooglePay();
                break;
            case 'applepay':
                button.innerHTML = 'Mit Apple Pay bezahlen';
                button.onclick = () => this.handleApplePay();
                break;
            case 'paypal':
                // PayPal button will be rendered by PayPal SDK
                return this.createPayPalButton();
        }

        return button;
    }

    async handleStandardCardPayment() {
        try {
            const standardCardMethod = {
                supportedMethods: 'basic-card',
                data: PAYMENT_CONFIG.standardCard
            };

            const request = new PaymentRequest([standardCardMethod], this.getPaymentDetails());
            const response = await request.show();
            
            // Process the payment
            await this.handlePaymentSuccess(response, 'standard-card');
            
            // Complete the payment
            await response.complete('success');
        } catch (error) {
            this.handlePaymentError(error, 'standard-card');
            if (error.name !== 'AbortError') {
                // If not user cancelled, try to complete with failure
                try {
                    await response?.complete('fail');
                } catch (completeError) {
                    console.error('Error completing failed payment:', completeError);
                }
            }
        }
    }

    createPayPalButton() {
        const container = document.createElement('div');
        container.id = 'paypal-button-container';

        paypal.Buttons({
            createOrder: (data, actions) => {
                return actions.order.create({
                    intent: PAYPAL_INTENT,
                    purchase_units: [{
                        amount: {
                            currency_code: PAYPAL_CURRENCY,
                            value: this.cartTotal.toString(),
                            breakdown: {
                                item_total: {
                                    currency_code: PAYPAL_CURRENCY,
                                    value: this.cartTotal.toString()
                                }
                            }
                        },
                        items: this.cartItems.map(item => ({
                            name: item.name,
                            unit_amount: {
                                currency_code: PAYPAL_CURRENCY,
                                value: item.price.toString()
                            },
                            quantity: item.quantity.toString()
                        }))
                    }]
                });
            },
            onApprove: async (data, actions) => {
                try {
                    const order = await actions.order.capture();
                    await this.handlePaymentSuccess(order, 'paypal');
                } catch (error) {
                    this.handlePaymentError(error, 'PayPal');
                }
            }
        }).render(container);

        return container;
    }

    createFallbackForm() {
        const form = document.createElement('form');
        form.id = 'fallback-payment-form';
        form.className = 'payment-form';
        form.innerHTML = `
            <h3>Alternative Zahlungsmethode</h3>
            <div class="form-group">
                <label for="card-number">Kartennummer</label>
                <input type="text" id="card-number" required pattern="[0-9]{16}" placeholder="1234 5678 9012 3456">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="expiry">Gültig bis</label>
                    <input type="text" id="expiry" required pattern="(0[1-9]|1[0-2])/[0-9]{2}" placeholder="MM/YY">
                </div>
                <div class="form-group">
                    <label for="cvv">CVV</label>
                    <input type="text" id="cvv" required pattern="[0-9]{3,4}" placeholder="123">
                </div>
            </div>
            <div class="form-group">
                <label for="card-holder">Karteninhaber</label>
                <input type="text" id="card-holder" required placeholder="Max Mustermann">
            </div>
            <button type="submit" class="submit-button">Mit Karte bezahlen</button>
        `;

        form.onsubmit = (e) => this.handleFallbackFormSubmit(e);
        return form;
    }

    async handleGooglePay() {
        try {
            const googlePayMethod = {
                supportedMethods: 'https://google.com/pay',
                data: {
                    environment: PAYMENT_CONFIG.googlePay.environment,
                    apiVersion: PAYMENT_CONFIG.googlePay.apiVersion,
                    apiVersionMinor: PAYMENT_CONFIG.googlePay.apiVersionMinor,
                    merchantInfo: {
                        merchantId: PAYMENT_CONFIG.googlePay.merchantId
                    },
                    allowedPaymentMethods: [{
                        type: 'CARD',
                        parameters: {
                            allowedAuthMethods: ['PAN_ONLY', 'CRYPTOGRAM_3DS'],
                            allowedCardNetworks: PAYMENT_CONFIG.standardCard.supportedNetworks.map(network => 
                                network.toUpperCase()
                            )
                        }
                    }]
                }
            };

            const request = new PaymentRequest([googlePayMethod], this.getPaymentDetails());
            const response = await request.show();
            await this.handlePaymentSuccess(response, 'googlepay');
            await response.complete('success');
        } catch (error) {
            this.handlePaymentError(error, 'Google Pay');
            if (error.name !== 'AbortError' && response) {
                try {
                    await response.complete('fail');
                } catch (completeError) {
                    console.error('Error completing failed payment:', completeError);
                }
            }
        }
    }

    async handleApplePay() {
        try {
            const applePayMethod = {
                supportedMethods: 'https://apple.com/apple-pay',
                data: PAYMENT_CONFIG.applePay
            };

            const request = new PaymentRequest([applePayMethod], this.getPaymentDetails());
            const response = await request.show();
            await this.handlePaymentSuccess(response, 'applepay');
            await response.complete('success');
        } catch (error) {
            this.handlePaymentError(error, 'Apple Pay');
            if (error.name !== 'AbortError' && response) {
                try {
                    await response.complete('fail');
                } catch (completeError) {
                    console.error('Error completing failed payment:', completeError);
                }
            }
        }
    }

    async handleFallbackFormSubmit(event) {
        event.preventDefault();
        const form = event.target;
        
        try {
            // Here you would normally send the card data to your payment processor
            // For this example, we'll simulate a successful payment
            await this.handlePaymentSuccess({
                id: 'FALLBACK-' + Date.now(),
                method: 'card'
            }, 'card');
        } catch (error) {
            this.handlePaymentError(error, 'Kreditkarte');
        }
    }

    async handlePaymentSuccess(paymentResult, method) {
        try {
            // Send payment result to your server
            const response = await fetch('/process-payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    paymentResult,
                    method,
                    cartTotal: this.cartTotal,
                    cartItems: this.cartItems
                })
            });

            if (!response.ok) {
                throw new Error('Server processing failed');
            }

            const result = await response.json();
            
            if (result.success) {
                window.location.href = '/payment-success.php?order=' + result.orderId;
            } else {
                throw new Error(result.message || 'Payment processing failed');
            }
        } catch (error) {
            this.handlePaymentError(error, method);
        }
    }

    handlePaymentError(error, method) {
        const errorMessages = {
            'standard-card': {
                'AbortError': 'Kartenzahlung wurde abgebrochen.',
                'NotSupportedError': 'Diese Zahlungsmethode wird nicht unterstützt.',
                'default': 'Bei der Kartenzahlung ist ein Fehler aufgetreten.'
            },
            'googlepay': {
                'AbortError': 'Google Pay Zahlung wurde abgebrochen.',
                'NotSupportedError': 'Google Pay wird auf diesem Gerät nicht unterstützt.',
                'default': 'Bei der Google Pay Zahlung ist ein Fehler aufgetreten.'
            },
            'applepay': {
                'AbortError': 'Apple Pay Zahlung wurde abgebrochen.',
                'NotSupportedError': 'Apple Pay wird auf diesem Gerät nicht unterstützt.',
                'default': 'Bei der Apple Pay Zahlung ist ein Fehler aufgetreten.'
            },
            'paypal': {
                'default': 'Bei der PayPal Zahlung ist ein Fehler aufgetreten.'
            },
            'card': {
                'default': 'Bei der Kartenzahlung ist ein Fehler aufgetreten.'
            }
        };

        const methodErrors = errorMessages[method] || { default: 'Bei der Zahlung ist ein Fehler aufgetreten.' };
        const errorMessage = methodErrors[error.name] || methodErrors.default;

        // Show error message to user
        const errorContainer = document.getElementById('payment-error-container');
        errorContainer.textContent = `${errorMessage} (${error.message})`;
        errorContainer.style.display = 'block';

        // Log error for debugging
        console.error(`Payment error (${method}):`, error);
    }
}

// Initialize payment handler when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Get cart data from the page
    const cartTotal = parseFloat(document.getElementById('cart-total').dataset.total);
    const cartItems = Array.from(document.querySelectorAll('.cart-item')).map(item => ({
        name: item.dataset.name,
        price: parseFloat(item.dataset.price),
        quantity: parseInt(item.dataset.quantity)
    }));

    // Initialize payment handler
    const paymentHandler = new PaymentHandler(cartTotal, cartItems);
});