**edit_orders.php**

<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get order ID from URL
$id = $_GET['id'];

// Fetch existing order details
$order = json_decode(file_get_contents('../backend/orders.php?id=' . $id), true);

// Check if order exists
if (empty($order)) {
    echo 'Order not found';
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <div class="max-w-md mx-auto p-4 bg-gray-200 rounded-md shadow-md">
        <h2 class="text-lg font-bold text-orange-500 mb-4">Edit Order</h2>
        <form id="edit-order-form">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Name:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" type="text" value="<?= $order['name'] ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" type="email" value="<?= $order['email'] ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="order_date">Order Date:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="order_date" type="date" value="<?= $order['order_date'] ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="total">Total:</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="total" type="number" value="<?= $order['total'] ?>">
            </div>
            <button class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded" type="submit">Update Order</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#edit-order-form').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'PUT',
                    url: '../backend/orders.php',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            window.location.href = 'list_orders.php';
                        } else {
                            alert('Error updating order');
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>


**orders.php (backend)**

<?php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(array('error' => 'User not logged in'));
    exit;
}

// Get order ID from URL
$id = $_GET['id'];

// Fetch existing order details
$order = json_decode(file_get_contents('orders.json'), true);
$order = $order[$id];

// Update order details
if (isset($_POST['name'])) {
    $order['name'] = $_POST['name'];
    $order['email'] = $_POST['email'];
    $order['order_date'] = $_POST['order_date'];
    $order['total'] = $_POST['total'];
    file_put_contents('orders.json', json_encode($order));
    echo json_encode(array('success' => true));
} else {
    echo json_encode($order);
}


**orders.json**
json
{
    "1": {
        "name": "John Doe",
        "email": "john@example.com",
        "order_date": "2022-01-01",
        "total": 100.00
    },
    "2": {
        "name": "Jane Doe",
        "email": "jane@example.com",
        "order_date": "2022-01-15",
        "total": 200.00
    }
}