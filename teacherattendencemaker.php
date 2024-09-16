<?php
// Define the filename for the attendance data
$filename = 'attendance.json';

// List of teachers (can be expanded as needed)
$teachers = [
    'John Doe',
    'Jane Smith',
    'Emily Johnson',
    'Michael Brown',
    'Sarah Davis'
];

// Function to read the attendance data from the file
function readAttendance() {
    global $filename;
    if (!file_exists($filename)) {
        return [];
    }
    $data = file_get_contents($filename);
    return json_decode($data, true) ?? [];
}

// Function to write the attendance data to the file
function writeAttendance($attendance) {
    global $filename;
    file_put_contents($filename, json_encode($attendance, JSON_PRETTY_PRINT));
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attendance = readAttendance();

    if (isset($_POST['mark_attendance'])) {
        $name = $_POST['teacher'];
        if (!empty($name)) {
            $attendance[$name] = date('Y-m-d H:i:s');
            writeAttendance($attendance);
        }
    } elseif (isset($_POST['reset_attendance'])) {
        writeAttendance([]);
        $attendance = [];
    }
}

// Read the current attendance data
$attendance = readAttendance();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management System</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f5f5;
            color: #333;
        }
        .container {
            width: 90%;
            max-width: 900px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1 {
            text-align: center;
            color: #007bff;
        }
        form {
            margin-bottom: 20px;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        select, button {
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        select {
            width: calc(100% - 22px);
            margin-right: 10px;
        }
        button {
            color: #fff;
            background-color: #007bff;
            cursor: pointer;
            transition: background-color 0.3s;
            border: none;
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
            background-color: #007bff;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .no-records {
            text-align: center;
            color: #888;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Attendance Management System</h1>
        
        <!-- Form to mark attendance -->
        <form method="post">
            <label for="teacher">Select Teacher:</label>
            <select id="teacher" name="teacher" required>
                <option value="">-- Select a Teacher --</option>
                <?php foreach ($teachers as $teacher): ?>
                    <option value="<?php echo htmlspecialchars($teacher); ?>">
                        <?php echo htmlspecialchars($teacher); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="mark_attendance">Mark Attendance</button>
        </form>

        <!-- Form to reset attendance data -->
        <form method="post">
            <button type="submit" name="reset_attendance">Reset Attendance</button>
        </form>

        <!-- Display attendance data -->
        <h2>Attendance List</h2>
        <table>
            <thead>
                <tr>
                    <th>Teacher</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($attendance)): ?>
                    <?php foreach ($attendance as $teacher => $timestamp): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($teacher); ?></td>
                            <td><?php echo htmlspecialchars($timestamp); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2" class="no-records">No attendance records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
<!-- Add this section in your index.php, below the table -->
<h2>View Specific Teacher's Attendance</h2>
<form method="get" action="view_teacher_attendance.php">
    <label for="teacher_select">Select Teacher:</label>
    <select id="teacher_select" name="teacher" required>
        <option value="">-- Select a Teacher --</option>
        <?php foreach ($teachers as $teacher): ?>
            <option value="<?php echo htmlspecialchars($teacher); ?>">
                <?php echo htmlspecialchars($teacher); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">View Attendance</button>
</form>

    </div>
</body>
</html>
