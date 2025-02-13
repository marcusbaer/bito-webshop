function deleteCartItem(productId) {
    if (!confirm('Möchten Sie diesen Artikel wirklich aus dem Warenkorb entfernen?')) {
        return;
    }

    fetch('delete_cart_item.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the cart item element
            const itemElement = document.querySelector(`[data-product-id="${productId}"]`);
            itemElement.remove();

            // Update cart total if it exists
            const totalElement = document.getElementById('cart-total');
            if (totalElement) {
                totalElement.textContent = `€${data.total}`;
            }

            // Show success message
            showMessage('Artikel wurde aus dem Warenkorb entfernt', 'success');
        } else {
            showMessage(data.error || 'Fehler beim Entfernen des Artikels', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Fehler beim Entfernen des Artikels', 'error');
    });
}

function showMessage(message, type) {
    const messageDiv = document.getElementById('message');
    messageDiv.textContent = message;
    messageDiv.className = `message ${type}`;
    messageDiv.style.display = 'block';

    // Hide message after 3 seconds
    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 3000);
}