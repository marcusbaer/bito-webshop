// Payment Request API implementation
class WebshopPayment {
    constructor(cartItems, total) {
        this.cartItems = cartItems;
        this.total = total;
        this.supportedPaymentMethods = [
            {
                supportedMethods: "https://google.com/pay",
                data: {
                    environment: "TEST",
                    apiVersion: 2,
                    apiVersionMinor: 0,
                    merchantInfo: {
                        merchantId: "BCR2DN4TQPBGYYK6",  // Test merchant ID
                        merchantName: "Bito Webshop"
                    },
                    allowedPaymentMethods: [{
                        type: "CARD",
                        parameters: {
                            allowedAuthMethods: ["PAN_ONLY", "CRYPTOGRAM_3DS"],
                            allowedCardNetworks: ["VISA", "MASTERCARD"]
                        }
                    }]
                }
            },
            {
                supportedMethods: "https://apple.com/apple-pay",
                data: {
                    version: 3,
                    merchantIdentifier: "merchant.com.bito.webshop",
                    merchantCapabilities: ["supports3DS"],
                    supportedNetworks: ["visa", "masterCard"],
                    countryCode: "DE"
                }
            }
        ];
    }

    createPaymentDetails() {
        return {
            total: {
                label: 'Gesamtsumme',
                amount: {
                    currency: 'EUR',
                    value: this.total.toFixed(2)
                }
            },
            displayItems: this.cartItems.map(item => ({
                label: `${item.name} (${item.quantity}x)`,
                amount: {
                    currency: 'EUR',
                    value: (item.sale_price * item.quantity).toFixed(2)
                }
            }))
        };
    }

    createPaymentOptions() {
        return {
            requestPayerName: true,
            requestPayerEmail: true,
            requestShipping: true,
            requestPayerPhone: true,
            shippingType: 'delivery'
        };
    }

    async initializePayment() {
        if (!window.PaymentRequest) {
            throw new Error('Payment Request API is not supported by your browser');
        }

        const request = new PaymentRequest(
            this.supportedPaymentMethods,
            this.createPaymentDetails(),
            this.createPaymentOptions()
        );

        // Check if payment can be made
        try {
            const canMakePayment = await request.canMakePayment();
            if (!canMakePayment) {
                throw new Error('No supported payment methods available');
            }
        } catch (error) {
            console.error('canMakePayment error:', error);
            this.handlePaymentError(error);
            return;
        }

        request.addEventListener('shippingaddresschange', event => {
            event.updateWith(this.createPaymentDetails());
        });

        try {
            const paymentResponse = await request.show();
            // Process the payment
            const result = await this.processPayment(paymentResponse);
            if (result.success) {
                await paymentResponse.complete('success');
                window.location.href = 'payment-success.php?order_id=' + result.orderId;
            } else {
                await paymentResponse.complete('fail');
                throw new Error('Payment processing failed');
            }
        } catch (error) {
            console.error('Payment error:', error);
            this.handlePaymentError(error);
        }
    }

    async processPayment(paymentResponse) {
        // Send payment data to the server
        const response = await fetch('process-payment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                paymentMethod: paymentResponse.methodName,
                paymentDetails: paymentResponse.details,
                shippingAddress: paymentResponse.shippingAddress,
                payerName: paymentResponse.payerName,
                payerEmail: paymentResponse.payerEmail,
                payerPhone: paymentResponse.payerPhone,
                total: this.total
            })
        });

        return await response.json();
    }

    handlePaymentError(error) {
        const errorMessage = document.getElementById('payment-error');
        if (errorMessage) {
            errorMessage.textContent = 'Payment failed: ' + error.message;
            errorMessage.style.display = 'block';
        }
    }
}