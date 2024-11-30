<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Home - ZDFinance</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .header {
            background-color: #007bff;
            color: white;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header h1, .header h3 {
            margin: 0;
        }
        .btn-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            margin-top: 30px;
        }
        .btn-container a {
            width: 200px;
        }
        .chart-container {
            margin-top: 50px;
        }
        canvas {
            max-width: 100%;
            height: 400px;
            margin: auto;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <h1>ZDFinance</h1>
        <h3>Admin WebPage (God mode)</h3>
    </div>

    <!-- Main Content -->
    <div class="container text-center">
        <!-- Buttons -->
        <div class="btn-container">
            <a href="read_clients.php" class="btn btn-primary">Display Users</a>
            <a href="read_accounts.php" class="btn btn-primary">Display Accounts</a>
            <a href="read_cards.php" class="btn btn-primary">Display Cards</a>
            <a href="read_transactions_ext.php" class="btn btn-primary">Display External Transactions</a>
            <a href="read_transactions_int.php" class="btn btn-primary">Display Internal Transactions</a>
        </div>

        <!-- Charts Section -->
        <div class="chart-container">
            <h4>Gender Distribution of Users</h4>
            <canvas id="genderChart"></canvas>
        </div>
        <div class="chart-container">
            <h4>Account Status</h4>
            <canvas id="accountChart"></canvas>
        </div>
        <div class="chart-container">
            <h4>Transaction Types</h4>
            <canvas id="transactionChart"></canvas>
        </div>
    </div>

    <!-- JavaScript to Fetch Data and Render Charts -->
    <script>
        // Function to create a chart
        function createChart(ctx, type, data, options) {
            new Chart(ctx, {
                type: type,
                data: data,
                options: options
            });
        }

        // Fetch and render Gender Chart
        fetch('fetch_gender_stats.php')
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('genderChart').getContext('2d');
                createChart(ctx, 'pie', {
                    labels: data.labels,
                    datasets: [{
                        label: 'Gender Distribution',
                        data: data.values,
                        backgroundColor: ['#007bff', '#ff6384']
                    }]
                });
            });

        // Fetch and render Account Status Chart
        fetch('fetch_account_stats.php')
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('accountChart').getContext('2d');
                createChart(ctx, 'doughnut', {
                    labels: data.labels,
                    datasets: [{
                        label: 'Account Status',
                        data: data.values,
                        backgroundColor: ['#28a745', '#dc3545']
                    }]
                });
            });

        // Fetch and render Transaction Types Chart
        fetch('fetch_transaction_stats.php')
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('transactionChart').getContext('2d');
                createChart(ctx, 'bar', {
                    labels: data.labels,
                    datasets: [{
                        label: 'Transaction Types',
                        data: data.values,
                        backgroundColor: ['#ffc107', '#17a2b8']
                    }]
                });
            });
    </script>
</body>
</html>
