<script>
document.addEventListener('DOMContentLoaded', function() {
    // Coupon application
    document.getElementById('apply_coupon').addEventListener('click', function() {
        var couponCode = document.getElementById('coupon_code').value.trim();
        var productId = document.querySelector('input[name="product_id"]').value;
        var quantity = document.getElementById('quantity').value;
        var basePrice = parseFloat(document.getElementById('base_price').value);
        
        if (couponCode === "") {
            showCouponMessage("Please enter a coupon code.", "red");
            return;
        }

        // Send AJAX request to check the coupon code
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'apply_coupon.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                if (xhr.status == 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.valid) {
                            showCouponMessage("Coupon applied successfully! " + 
                                response.discount + 
                                (response.discount_type == 'percentage' ? '%' : '₹') + 
                                " discount", "green");
                            
                            // Update discount amount
                            document.getElementById('discount_amount').value = response.discount_amount;
                            
                            // Recalculate total
                            updateTotalPrice();
                        } else {
                            showCouponMessage(response.message || "Invalid coupon code", "red");
                        }
                    } catch(e) {
                        showCouponMessage("Error processing coupon", "red");
                        console.error(e);
                    }
                } else {
                    showCouponMessage("Error processing request", "red");
                }
            }
        };
        xhr.send('coupon_code=' + encodeURIComponent(couponCode) + 
                '&product_id=' + productId + 
                '&quantity=' + quantity +
                '&base_price=' + basePrice);
    });

    // Remove coupon
    document.getElementById('remove_coupon')?.addEventListener('click', function() {
        document.getElementById('coupon_code').value = '';
        document.getElementById('discount_amount').value = '0';
        document.getElementById('coupon_message').style.display = 'none';
        updateTotalPrice();
        // You might want to add an AJAX call here to remove the coupon server-side
    });

    function showCouponMessage(message, color) {
        var msgElement = document.getElementById('coupon_message');
        msgElement.textContent = message;
        msgElement.style.color = color;
        msgElement.style.display = 'block';
    }
});

// Update your updateTotalPrice function to include discount
function updateTotalPrice() {
    var quantity = parseInt(document.getElementById('quantity').value);
    var basePrice = parseFloat(document.getElementById('base_price').value);
    var deliveryCharge = parseFloat(document.getElementById('delivery_charge').value);
    var discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;
    
    var subtotal = (basePrice * quantity) - discountAmount;
    if (subtotal < 0) subtotal = 0;
    
    var total = subtotal + deliveryCharge;
    
    document.getElementById('subtotal').textContent = '₹' + subtotal.toFixed(2);
    document.getElementById('total-amount').textContent = '₹' + total.toFixed(2);
    document.getElementById('total_amount').value = total.toFixed(2);
    document.getElementById('pay-amount').textContent = '₹' + total.toFixed(2);
}
</script>