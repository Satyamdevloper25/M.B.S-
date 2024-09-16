<?php
// Define the filename for the attendance data
$filename = 'attendance.json';

// Function to read the attendance data from the file
function readAttendance() {
    global $filename;
    if (!file_exists($filename)) {
        return [];
    }
    $data = file_get_contents($filename);
    return json_decode($data, true) ?? [];
}

// Get teacher's name from query parameter
$teacher = isset($_GET['teacher']) ? trim($_GET['teacher']) : '';

// Read the attendance data
$attendance = readAttendance();

// Filter attendance for the specific teacher
$teacherAttendance = [];
if ($teacher && array_key_exists($teacher, $attendance)) {
    $teacherAttendance[$teacher] = $attendance[$teacher];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance for <?php echo htmlspecialchars($teacher); ?></title>
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
        <h1>Attendance for <?php echo htmlspecialchars($teacher); ?></h1>

        <!-- Display attendance data for the specific teacher -->
        <h2>Attendance List</h2>
        <table>
            <thead>
                <tr>
                    <th>Teacher</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($teacherAttendance)): ?>
                    <?php foreach ($teacherAttendance as $name => $timestamp): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($name); ?></td>
                            <td><?php echo htmlspecialchars($timestamp); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2" class="no-records">No attendance records found for <?php echo htmlspecialchars($teacher); ?>.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
