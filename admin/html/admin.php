<html>
<head>
  <title>Admin Dashboard</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      padding: 50px;
    }
    h1 {
      margin-bottom: 40px;
    }
    .dashboard-button {
      display: inline-block;
      padding: 15px 30px;
      margin: 20px;
      font-size: 18px;
      color: white;
      background-color: #007BFF;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      text-decoration: none;
    }
    .dashboard-button:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
  <h1>Admin Dashboard</h1>
  <p>Welcome to the admin panel. Select an option below to manage the database.</p>

  <a href="manage_devices.php" class="dashboard-button">Manage Devices</a>
  <a href="manage_bookings.php" class="dashboard-button">Manage Bookings</a>

</body>
</html>
