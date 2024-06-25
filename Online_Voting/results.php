<?php
session_start();
include 'db_Conn.php';

$query = "
SELECT c.name, COUNT(*) as vote_count
FROM results r
JOIN candidates c ON r.candidate_id = c.id
GROUP BY r.candidate_id
";

$result = mysqli_query($conn, $query);

$candidate_votes = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $candidate_votes[] = $row;
    }
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Results</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
        body{
            background-color:#ffe6f2;
        }
        table
        {
            background-color: #b3b3ff;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Election Results</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Candidate Name</th>
                <th>Vote Count</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($candidate_votes as $vote) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($vote['name']); ?></td>
                    <td><?php echo htmlspecialchars($vote['vote_count']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https
