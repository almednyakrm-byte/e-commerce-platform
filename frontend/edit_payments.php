**edit_payments.php**

<?php
// Session validation
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get payment ID from URL
$id = $_GET['id'];

// Fetch existing record details via GET
$data = json_decode(file_get_contents('../backend/payments.php?id=' . $id), true);

// Check if record exists
if (empty($data)) {
    echo 'Payment not found';
    exit;
}

// Set page title and mod slug
$page_title = 'Edit Payment';
$mod_slug = 'payments';

// Include header and navigation
include 'header.php';
?>

<div class="container mx-auto p-4 pt-6 md:p-6 lg:p-12 xl:p-12 2xl:p-12">
    <h1 class="text-3xl font-bold text-orange-500 mb-4"><?= $page_title ?></h1>
    <form id="edit-payment-form" class="bg-gray-200 p-4 rounded shadow-md">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 2xl:grid-cols-2">
            <div>
                <label for="payment_date" class="block text-gray-700 text-sm font-bold mb-2">Payment Date:</label>
                <input type="date" id="payment_date" name="payment_date" class="block w-full p-2 text-gray-700 bg-gray-200 rounded" value="<?= $data['payment_date'] ?>">
            </div>
            <div>
                <label for="amount" class="block text-gray-700 text-sm font-bold mb-2">Amount:</label>
                <input type="number" id="amount" name="amount" class="block w-full p-2 text-gray-700 bg-gray-200 rounded" value="<?= $data['amount'] ?>">
            </div>
            <div>
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description:</label>
                <textarea id="description" name="description" class="block w-full p-2 text-gray-700 bg-gray-200 rounded h-20" rows="4"><?= $data['description'] ?></textarea>
            </div>
        </div>
        <button type="submit" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">Update Payment</button>
    </form>
</div>

<script>
    // Fetch existing record details via GET
    fetch('../backend/payments.php?id=' + <?= $id ?>)
        .then(response => response.json())
        .then(data => {
            document.getElementById('payment_date').value = data.payment_date;
            document.getElementById('amount').value = data.amount;
            document.getElementById('description').value = data.description;
        })
        .catch(error => console.error(error));

    // Submit form via AJAX PUT request
    document.getElementById('edit-payment-form').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        fetch('../backend/payments.php', {
            method: 'PUT',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'list_<?= $mod_slug ?>.php';
                } else {
                    console.error(data.error);
                }
            })
            .catch(error => console.error(error));
    });
</script>

<?php
// Include footer
include 'footer.php';
?>


**backend/payments.php**

<?php
// Check if record exists
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $record = get_payment($id);
    if ($record) {
        echo json_encode($record);
    } else {
        echo json_encode(array('error' => 'Payment not found'));
    }
} elseif (isset($_POST['id'])) {
    // Update payment record
    $id = $_POST['id'];
    $payment_date = $_POST['payment_date'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];
    update_payment($id, $payment_date, $amount, $description);
    echo json_encode(array('success' => true));
} else {
    echo json_encode(array('error' => 'Invalid request'));
}

// Helper functions
function get_payment($id) {
    // Retrieve payment record from database
    // ...
}

function update_payment($id, $payment_date, $amount, $description) {
    // Update payment record in database
    // ...
}
?>