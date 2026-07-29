**create_orders.php**

<?php
// Include session validation
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Include header and navigation
include 'header.php';
?>

<!-- Page Content -->
<div class="container mx-auto p-4 pt-6 md:p-6 lg:p-12 xl:p-12 2xl:p-12">
    <div class="bg-white rounded-lg shadow-md p-4 md:p-6 lg:p-8 xl:p-8 2xl:p-8">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Create Order</h2>
        <form id="create-order-form">
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2" for="customer_name">Customer Name</label>
                <input class="block w-full px-4 py-2 text-sm text-gray-700 bg-gray-200 rounded-lg focus:outline-none focus:ring-orange-500 focus:border-orange-500" type="text" id="customer_name" name="customer_name" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2" for="order_date">Order Date</label>
                <input class="block w-full px-4 py-2 text-sm text-gray-700 bg-gray-200 rounded-lg focus:outline-none focus:ring-orange-500 focus:border-orange-500" type="date" id="order_date" name="order_date" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2" for="total_amount">Total Amount</label>
                <input class="block w-full px-4 py-2 text-sm text-gray-700 bg-gray-200 rounded-lg focus:outline-none focus:ring-orange-500 focus:border-orange-500" type="number" id="total_amount" name="total_amount" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-2" for="status">Status</label>
                <select class="block w-full px-4 py-2 text-sm text-gray-700 bg-gray-200 rounded-lg focus:outline-none focus:ring-orange-500 focus:border-orange-500" id="status" name="status" required>
                    <option value="">Select Status</option>
                    <option value="pending">Pending</option>
                    <option value="shipped">Shipped</option>
                    <option value="delivered">Delivered</option>
                </select>
            </div>
            <button class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded-lg" type="submit">Create Order</button>
        </form>
    </div>
</div>

<!-- Include footer -->
<?php include 'footer.php'; ?>

<script>
    $(document).ready(function() {
        $('#create-order-form').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: '../backend/orders.php',
                data: formData,
                success: function(response) {
                    if (response == 'success') {
                        window.location.href = 'list_orders.php';
                    } else {
                        alert('Error creating order');
                    }
                }
            });
        });
    });
</script>


**orders.php (backend)**

<?php
// Include database connection
include 'db.php';

// Check if form data is submitted
if (isset($_POST['customer_name']) && isset($_POST['order_date']) && isset($_POST['total_amount']) && isset($_POST['status'])) {
    // Insert data into orders table
    $customer_name = $_POST['customer_name'];
    $order_date = $_POST['order_date'];
    $total_amount = $_POST['total_amount'];
    $status = $_POST['status'];

    $query = "INSERT INTO orders (customer_name, order_date, total_amount, status) VALUES ('$customer_name', '$order_date', '$total_amount', '$status')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo 'success';
    } else {
        echo 'Error creating order';
    }
}
?>