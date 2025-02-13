// PayPal Configuration
export const PAYPAL_CLIENT_ID = 'AZQt9GIKFIvyYGSCu_yBDxVggNIJGDVF-xGH6rRorQEZG8fPvRGSDiamYatIxrm1Cu9CFxYkMZtoSqKK';
export const PAYPAL_CURRENCY = 'EUR';
export const PAYPAL_INTENT = 'capture';

// Payment Method Configuration
export const PAYMENT_CONFIG = {
    // Standard Card Payment
    standardCard: {
        supportedNetworks: ['visa', 'mastercard'],
        supportedTypes: ['credit', 'debit']
    },
    
    // Google Pay
    googlePay: {
        environment: 'TEST',
        merchantId: 'BCR2DN4TQPBGYYK6', // Replace with your merchant ID
        apiVersion: 2,
        apiVersionMinor: 0
    },
    
    // Apple Pay
    applePay: {
        version: 3,
        merchantIdentifier: 'merchant.com.bito.webshop', // Replace with your merchant identifier
        merchantCapabilities: ['supports3DS'],
        supportedNetworks: ['masterCard', 'visa'],
        countryCode: 'DE'
    }
};