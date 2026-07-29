<?php
// Session check
session_start();
if (!isset($_SESSION['authenticated'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>منصة تسويق إلكتروني</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="h-screen bg-gray-200">
    <header class="bg-orange-500 text-white p-4 text-center">
        <h1 class="text-3xl font-bold">منصة تسويق إلكتروني</h1>
    </header>
    <main class="container mx-auto p-4 pt-6 md:p-6 lg:p-12 xl:p-24">
        <div class="flex justify-between mb-4">
            <h2 class="text-2xl font-bold">مرحبا <?php echo $_SESSION['username']; ?></h2>
            <a href="logout.php" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">
                تسجيل الخروج
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded shadow-md p-4 glassmorphism">
                <h3 class="text-lg font-bold mb-2">إجمالي المنتجات</h3>
                <p id="total-products" class="text-3xl font-bold"></p>
            </div>
            <div class="bg-white rounded shadow-md p-4 glassmorphism">
                <h3 class="text-lg font-bold mb-2">إجمالي الطلبات</h3>
                <p id="total-orders" class="text-3xl font-bold"></p>
            </div>
            <div class="bg-white rounded shadow-md p-4 glassmorphism">
                <h3 class="text-lg font-bold mb-2">إجمالي المدفوعات</h3>
                <p id="total-payments" class="text-3xl font-bold"></p>
            </div>
            <div class="bg-white rounded shadow-md p-4 glassmorphism">
                <h3 class="text-lg font-bold mb-2">إجمالي المبيعات</h3>
                <p id="total-sales" class="text-3xl font-bold"></p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
            <a href="products.php" class="bg-white rounded shadow-md p-4 glassmorphism">
                <h3 class="text-lg font-bold mb-2">إدارة المنتجات</h3>
                <p class="text-gray-600">إضافة، تعديل، حذف المنتجات</p>
            </a>
            <a href="orders.php" class="bg-white rounded shadow-md p-4 glassmorphism">
                <h3 class="text-lg font-bold mb-2">إدارة الطلبات</h3>
                <p class="text-gray-600">مشاهدة، تعديل، حذف الطلبات</p>
            </a>
            <a href="payments.php" class="bg-white rounded shadow-md p-4 glassmorphism">
                <h3 class="text-lg font-bold mb-2">إدارة المدفوعات</h3>
                <p class="text-gray-600">مشاهدة، تعديل، حذف المدفوعات</p>
            </a>
        </div>
    </main>

    <script>
        // Fetch stats dynamically via Javascript API calls from the backend files
        fetch('api/stats.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('total-products').innerText = data.totalProducts;
                document.getElementById('total-orders').innerText = data.totalOrders;
                document.getElementById('total-payments').innerText = data.totalPayments;
                document.getElementById('total-sales').innerText = data.totalSales;
            });
    </script>
</body>
</html>