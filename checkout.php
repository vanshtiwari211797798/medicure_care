<?php
session_start();
ob_start();
$email = isset($_SESSION['user']) ? $_SESSION['user'] : '';
$price = isset($_GET['price']) ? $_GET['price'] : '';
// echo $price;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Checkout Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-2xl shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Fill Your Details</h2>
        <!-- MODAL -->
        <div id="deliveryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white p-6 rounded-xl w-80 text-center shadow-lg">
                <h3 class="text-xl font-semibold mb-4">Choose Delivery Type</h3>

                <div class="flex flex-col gap-4">
                    <button onclick="submitForm('Cash on Delivery')"
                        class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Cash on Delivery</button>
                    <button onclick="submitForm('Online Payment')"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Online Payment</button>
                </div>

                <button onclick="closeModal()" class="mt-6 text-sm text-gray-500 hover:text-gray-700">Cancel</button>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700">Name</label>
            <input type="text" id="name" name="name" required
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700">Phone</label>
            <input type="tel" id="phone" name="phone" required pattern="[0-9]{10}"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700">Email</label>
            <input type="email" id="email" value="<?= $email ?>" readonly name="email" required
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700">Pincode</label>
            <input type="text" id="pincode" name="pincode" required pattern="[0-9]{6}"
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700">Address</label>
            <textarea id="address" name="address" rows="3" placeholder="Full address" required
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
        </div>

        <button type="button" onclick="payOut()"
            class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg text-lg font-semibold">Submit</button>
    </div>


    <!-- script code -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    <script>
        const payOut = () => {
            let name = document.getElementById("name").value.trim();
            let phone = document.getElementById("phone").value.trim();
            let email = document.getElementById("email").value.trim();
            let address = document.getElementById("address").value.trim();
            let pincode = document.getElementById("pincode").value.trim();

            if (!name || !phone || !email || !address || !pincode) {
                alert("Please fill all fields");
            } else {
                // Show modal
                document.getElementById("deliveryModal").classList.remove("hidden");
                document.getElementById("deliveryModal").classList.add("flex");
            }
        };

        const closeModal = () => {
            document.getElementById("deliveryModal").classList.add("hidden");
            document.getElementById("deliveryModal").classList.remove("flex");
        };

        const submitForm = (deliveryType) => {
            if (deliveryType === "Cash on Delivery") {
                let name = document.getElementById("name").value.trim();
                let phone = document.getElementById("phone").value.trim();
                let email = document.getElementById("email").value.trim();
                let address = document.getElementById("address").value.trim();
                let pincode = document.getElementById("pincode").value.trim();
                let delivery_charge = '<?= $_GET['delivery_charge'] ?>';

                fetch("insert_data_2.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            name: name,
                            phone: phone,
                            email: email,
                            address: address,
                            pincode: pincode,
                            delivery_charge: delivery_charge
                        })
                    })
                    .then(response => response.text())
                    .then(data => {
                        if (data.trim() === "Order saved") {
                            alert("🎉 Order Success!");
                            // Optional: Redirect to success page
                            window.location.href = "myorders.php";
                        } else {
                            alert("⚠️ " + data);
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                    });

            } else if (deliveryType === "Online Payment") {

                let name = document.getElementById("name").value.trim();
                let phone = document.getElementById("phone").value.trim();
                let email = document.getElementById("email").value.trim();
                let address = document.getElementById("address").value.trim();
                let pincode = document.getElementById("pincode").value.trim();
                let delivery_charge = '<?= $_GET['delivery_charge'] ?>';
                console.log("charge is", delivery_charge);

                // console.log(name,email,contact,address);

                const amount = <?= $price ?> * 100; // Amount in paisa
                // alert(amount);
                // console.log(name,phone,email,address,pincode);
                // alert(name);

                var options = {
                    "key": "rzp_live_pGkJ9YKFp0XEnE",
                    "amount": amount,
                    "currency": "INR",
                    "name": "Medicure",
                    "description": "Order Payment",
                    "handler": function(response) {
                        // Send AJAX to insert order
                        fetch("insert_order.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                razorpay_payment_id: response.razorpay_payment_id,
                                name: name,
                                email: email,
                                phone: phone,
                                address: address,
                                pincode: pincode,
                                delivery_charge: delivery_charge
                            })
                        }).then(res => res.text()).then(data => {
                            alert("Order placed successfully!");
                            // window.location.href = "myorders.php";
                        });
                    },
                    "prefill": {
                        "name": name,
                        "email": email,
                        "contact": phone
                    },
                    "theme": {
                        "color": "#3399cc"
                    }
                };
                var rzp = new Razorpay(options);
                rzp.open();

            } else {
                alert("wrong selected");
            }

            closeModal();
        };
    </script>


    <!-- <script src="https://checkout.razorpay.com/v1/checkout.js"></script> -->


</body>

</html>