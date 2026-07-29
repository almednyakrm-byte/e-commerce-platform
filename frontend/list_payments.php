**list_payments.php**

<?php
// Session validation
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
        }
        .orange-500 {
            color: #ff9900;
        }
        .gray-200 {
            color: #e5e5ea;
        }
    </style>
</head>
<body class="bg-gray-200">
    <div class="container mx-auto p-4 mt-4">
        <div class="flex justify-between items-center mb-4">
            <a href="index.php" class="text-orange-500 hover:text-orange-700">Back to Dashboard</a>
            <div class="flex items-center">
                <span class="text-gray-700 mr-2">Welcome, <?= $_SESSION['username'] ?></span>
                <button class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='logout.php'">Logout</button>
            </div>
        </div>
        <div class="bg-white p-4 rounded shadow-md">
            <h2 class="text-lg font-bold mb-2">Payments</h2>
            <button class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded" onclick="location.href='create_payments.php'">Add New Item</button>
            <div class="flex justify-between items-center mt-4">
                <input type="search" id="search" class="w-full p-2 text-gray-700 border border-gray-300 rounded" placeholder="Search...">
                <button class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded" onclick="searchPayments()">Search</button>
            </div>
            <table class="w-full mt-4">
                <thead>
                    <tr>
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">Date</th>
                        <th class="px-4 py-2">Amount</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody id="payments-table">
                    <?php
                    // Fetch payments data from backend
                    $response = file_get_contents('../backend/payments.php');
                    $payments = json_decode($response, true);
                    foreach ($payments as $payment) {
                        ?>
                        <tr>
                            <td class="px-4 py-2"><?= $payment['id'] ?></td>
                            <td class="px-4 py-2"><?= $payment['date'] ?></td>
                            <td class="px-4 py-2"><?= $payment['amount'] ?></td>
                            <td class="px-4 py-2">
                                <a href="edit_payments.php?id=<?= $payment['id'] ?>" class="text-orange-500 hover:text-orange-700">Edit</a>
                                <button class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded" onclick="deletePayment(<?= $payment['id'] ?>)">Delete</button>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function searchPayments() {
            const searchInput = document.getElementById('search');
            const searchQuery = searchInput.value.trim();
            if (searchQuery) {
                fetch('../backend/payments.php?search=' + searchQuery)
                    .then(response => response.json())
                    .then(data => {
                        const paymentsTable = document.getElementById('payments-table');
                        paymentsTable.innerHTML = '';
                        data.forEach(payment => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="px-4 py-2">${payment.id}</td>
                                <td class="px-4 py-2">${payment.date}</td>
                                <td class="px-4 py-2">${payment.amount}</td>
                                <td class="px-4 py-2">
                                    <a href="edit_payments.php?id=${payment.id}" class="text-orange-500 hover:text-orange-700">Edit</a>
                                    <button class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded" onclick="deletePayment(${payment.id})">Delete</button>
                                </td>
                            `;
                            paymentsTable.appendChild(row);
                        });
                    });
            } else {
                fetch('../backend/payments.php')
                    .then(response => response.json())
                    .then(data => {
                        const paymentsTable = document.getElementById('payments-table');
                        paymentsTable.innerHTML = '';
                        data.forEach(payment => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="px-4 py-2">${payment.id}</td>
                                <td class="px-4 py-2">${payment.date}</td>
                                <td class="px-4 py-2">${payment.amount}</td>
                                <td class="px-4 py-2">
                                    <a href="edit_payments.php?id=${payment.id}" class="text-orange-500 hover:text-orange-700">Edit</a>
                                    <button class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded" onclick="deletePayment(${payment.id})">Delete</button>
                                </td>
                            `;
                            paymentsTable.appendChild(row);
                        });
                    });
            }
        }

        function deletePayment(id) {
            if (confirm('Are you sure you want to delete this payment?')) {
                fetch('../backend/payments.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Payment deleted successfully!');
                        window.location.reload();
                    } else {
                        alert('Error deleting payment!');
                    }
                })
                .catch(error => console.error(error));
            }
        }
    </script>
</body>
</html>

This code includes a premium Tailwind UI design with a specific color palette matching the theme. It also includes session validation, a search bar, and AJAX calls to fetch and delete payments data from the backend.