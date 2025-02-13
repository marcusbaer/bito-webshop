# Bito Webshop

This example webshop has been generated step by step with the help of Bito AI.

## Timelog

It took me

- 1h to create a basic webshop in a Docker environment with example products being able to be added to cart

## Prompts

Here are all the prompts used to create and enhance this webshop:

1. "Render list of cart items in checkout"
   - Added cart items display to checkout page
   - Implemented cart query and calculations
   - Added styling for cart items section

2. "Replace payment section in checkout by implementation of Payment Request API to used with the cart data"
   - Implemented modern Payment Request API
   - Created payment processing system
   - Added fallback for unsupported browsers
   - Integrated order management

3. "Add delete option for cart items in cart"
   - Added delete functionality with confirmation
   - Implemented real-time cart updates
   - Added visual feedback and animations

4. "Implement consistent header to navigate in the webshop between list of products, product detail page and cart. checkout should have a different header to only navigate to cart page"
   - Created two header variants (main shop and checkout)
   - Implemented responsive navigation
   - Added dynamic cart count
   - Created consistent styling across pages

5. "Add all future prompts and the prompts I have used already here in wingman to the existing readme file in a new section called 'Prompts'"
   - Created this Prompts section
   - Documented all previous prompts and their implementations
   - Set up structure for future prompt documentation

6. "Fix links in header navigation as they are set to main pages in `src` folder. But this subfolder is not used"
   - Updated header.php navigation links
   - Updated checkout-header.php navigation links
   - Removed incorrect /src/ references

7. "Add a new example product into the shop with a price of only 0.01 €"
   - Added test product with minimal price
   - Set up product details and availability
   - Ensured proper price formatting

8. "When I am clicking on 'Jetzt bezahlen', I get the following error: 'Payment failed: The payment method 'basic-card' is not supported.'"
   - Implemented multiple payment methods (Google Pay, Apple Pay)
   - Added better error handling
   - Updated payment flow
   - Added merchant ID placeholders

9. "I'd like you to implement all of your suggested options, a fallback payment form, specific error messages and PayPal as an additional payment method"
   - Added comprehensive payment solution
   - Implemented PayPal integration
   - Created fallback credit card form
   - Added method-specific error handling

10. "footer.php is missing. What is it for?"
    - Created responsive footer.php
    - Added three main sections (About Us, Legal, Contact)
    - Implemented consistent styling
    - Added dynamic year in copyright

11. "But the include seems to be broken. it tries to include `includes/footer.php` but it is on root level"
    - Moved footer.php to correct location
    - Updated all page includes
    - Fixed path references

12. "Regarding payment, I don't see anymore the Payment Request API integrated. I only see Google Pay and the alternative payment method"
    - Restored Payment Request API as primary payment method
    - Maintained Google Pay and Apple Pay integration
    - Kept PayPal and fallback form
    - Updated error handling

13. "I get 'ignoring session_start ...' error"
    - Created centralized session handling
    - Updated files to use session include
    - Prevented duplicate session starts

14. "My recently added example product needs to be added to the sample product data in the setup script as well"
    - Added test product (0.01€) to setup data
    - Maintained existing sample products
    - Ensured consistent product structure