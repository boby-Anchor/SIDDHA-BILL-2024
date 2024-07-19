<?php
session_start();
if (!isset($_SESSION['store_id'])) {
    header("location:login.php");
    exit();
} else {
    include('config/db.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Collection Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        form {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>

<body>
    <h1>Data Collection Form</h1>
    <form id="dataForm" method="POST">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        <label for="age">Age:</label>
        <input type="number" id="age" name="age" required>
        <button type="submit">Submit</button>
    </form>
    <button id="loadData">Load Data</button>
    <h2>Collected Data</h2>
    <table id="dataTable">
        <thead>
            <tr>
                <th>Name</th>
                <th>Age</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data from the database will be loaded here -->
        </tbody>
    </table>

    <script>
        document.getElementById('dataForm').addEventListener('submit', function(event) {
            event.preventDefault();
            var formData = new FormData(this);
            fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(html => {
                    document.querySelector('tbody').innerHTML += html.match(/<tbody>([\s\S]*?)<\/tbody>/)[1];
                    this.reset();
                });
        });

        document.getElementById('loadData').addEventListener('click', function() {
            fetch('?action=loadData')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.querySelector('tbody');
                    tbody.innerHTML = '';
                    data.forEach(row => {
                        tbody.innerHTML += `<tr><td>${c1}</td><td>${c2}</td></tr>`;
                    });
                });
        });
    </script>
</body>

</html>

<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_GET['action'])) {
    $name = htmlspecialchars($_POST['name']);
    $age = htmlspecialchars($_POST['age']);

    $stmt = $conn->prepare("INSERT INTO test (c1, c2) VALUES (?, ?)");
    $stmt->bind_param("si", $name, $age);
    $stmt->execute();

    echo "<tr><td>$name</td><td>$age</td></tr>";
    $stmt->close();
}

if (isset($_GET['action']) && $_GET['action'] == 'loadData') {
    $sql = "SELECT c1, c2 FROM test";
    $result = $conn->query($sql);
    $data = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    echo json_encode($data);
    exit;
}

$conn->close();
?>