<?php
// Define filenames for salary and receipt data
$salariesFilename = 'salaries.json';
$receiptsFilename = 'receipts.json';

// Function to read data from a JSON file
function readData($filename) {
    if (!file_exists($filename)) {
        return [];
    }
    $data = file_get_contents($filename);
    return json_decode($data, true) ?? [];
}

// Function to write data to a JSON file
function writeData($filename, $data) {
    file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
}

// Initialize error variable
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $salaries = readData($salariesFilename);
    $receipts = readData($receiptsFilename);

    if (isset($_POST['add_employee'])) {
        $name = trim($_POST['name']);
        $salary = trim($_POST['salary']);
        if (!empty($name) && is_numeric($salary)) {
            $salaries[$name] = ['salary' => $salary, 'status' => 'Unpaid'];
            writeData($salariesFilename, $salaries);
        }
    } elseif (isset($_POST['update_salary'])) {
        $name = trim($_POST['update_name']);
        $new_salary = trim($_POST['new_salary']);
        $status = trim($_POST['status']);
        if (!empty($name) && is_numeric($new_salary) && isset($salaries[$name])) {
            $salaries[$name] = ['salary' => $new_salary, 'status' => $status];
            writeData($salariesFilename, $salaries);
        }
    } elseif (isset($_POST['generate_receipt'])) {
        $name = trim($_POST['receipt_name']);
        $amount = trim($_POST['receipt_amount']);
        if (!empty($name) && is_numeric($amount) && isset($salaries[$name]) && is_array($salaries[$name])) {
            $receiptId = uniqid('receipt_', true);
            $receipts[$receiptId] = [
                'name' => $name,
                'amount' => $amount,
                'date' => date('Y-m-d H:i:s'),
                'school' => 'M.B.S International School',
                'issued_by' => 'Satyam'
            ];

            // Update salary status to Paid if not already Paid
            if ($salaries[$name]['status'] !== 'Paid') {
                $salaries[$name]['status'] = 'Paid';
            }

            writeData($receiptsFilename, $receipts);
            writeData($salariesFilename, $salaries);
        } else {
            $error = 'Invalid employee data or employee not found.';
        }
    }
}

// Read current data
$salaries = readData($salariesFilename);
$receipts = readData($receiptsFilename);

// Get unpaid employees for receipt generation
$unpaidEmployees = array_filter($salaries, function($salary) {
    return $salary['status'] === 'Unpaid';
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Management System</title>
    <style>
        /* Global Styles */
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #e9ecef;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h1, h2 {
            text-align: center;
            color: #007BFF;
        }
        form {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background-color: #f9f9f9;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"], input[type="number"], select {
            width: calc(100% - 22px);
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #007BFF;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        .no-records {
            text-align: center;
            font-style: italic;
            color: #666;
        }
        .error {
            color: red;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Salary Management System</h1>

        <!-- Display error message if exists -->
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <!-- Form to add a new employee -->
        <form method="post">
            <h2>Add Employee</h2>
            <label for="name">Employee Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="salary">Salary:</label>
            <input type="number" id="salary" name="salary" step="0.01" required>
            <button type="submit" name="add_employee">Add Employee</button>
        </form>

        <!-- Form to update salary -->
        <form method="post">
            <h2>Update Salary</h2>
            <label for="update_name">Employee Name:</label>
            <input type="text" id="update_name" name="update_name" required>
            <label for="new_salary">New Salary:</label>
            <input type="number" id="new_salary" name="new_salary" step="0.01" required>
            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="Paid">Paid</option>
                <option value="Unpaid">Unpaid</option>
            </select>
            <button type="submit" name="update_salary">Update Salary</button>
        </form>

        <!-- Form to generate a receipt -->
        <form method="post">
            <h2>Generate Receipt</h2>
            <label for="receipt_name">Employee Name:</label>
            <select id="receipt_name" name="receipt_name" required>
                <option value="">Select Employee</option>
                <?php foreach ($unpaidEmployees as $name => $details): ?>
                    <option value="<?php echo htmlspecialchars($name); ?>"><?php echo htmlspecialchars($name); ?></option>
                <?php endforeach; ?>
            </select>
            <label for="receipt_amount">Amount:</label>
            <input type="number" id="receipt_amount" name="receipt_amount" step="0.01" required>
            <button type="submit" name="generate_receipt">Generate Receipt</button>
        </form>

        <!-- Display salary data -->
        <h2>Employee Salaries</h2>
        <?php if (!empty($salaries)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <th>Salary</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($salaries as $name => $salary): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($name); ?></td>
                            <td><?php echo htmlspecialchars($salary['salary']); ?></td>
                            <td><?php echo htmlspecialchars($salary['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-records">No salary records found.</p>
        <?php endif; ?>

        <!-- Display receipt data -->
        <h2>Receipts</h2>
        <?php if (!empty($receipts)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Receipt ID</th>
                        <th>Employee Name</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Issued By</th>
                        <th>School</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($receipts as $id => $receipt): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($id); ?></td>
                            <td><?php echo htmlspecialchars($receipt['name']); ?></td>
                            <td><?php echo htmlspecialchars($receipt['amount']); ?></td>
                            <td><?php echo htmlspecialchars($receipt['date']); ?></td>
                            <td><?php echo htmlspecialchars($receipt['issued_by']); ?></td>
                            <td><?php echo htmlspecialchars($receipt['school']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-records">No receipt records found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
