<?php include "../inc/dbinfo.inc"; ?>
<html>
<head>
  <title>Manage Devices</title>
  <style>
    table {
      border-collapse: collapse;
      width: 100%;
    }
    table, th, td {
      border: 1px solid black;
    }
    th, td {
      padding: 10px;
      text-align: left;
    }
    .error {
      color: red;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <h1>Manage Devices</h1>
  <a href="admin.php">Back to Dashboard</a>

  <?php
  // Connect to MySQL and select the database
  $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

  if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

  $database = mysqli_select_db($connection, DB_DATABASE);

  // Handle form submissions for adding, updating, and deleting devices
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (isset($_POST['add_device'])) {
          $device_name = htmlentities($_POST['device_name']);
          $device_type = htmlentities($_POST['device_type']);
          $query = "INSERT INTO Device (deviceName, type, bookingStatus) VALUES ('$device_name', '$device_type', 'available')";
          if (mysqli_query($connection, $query)) {
              echo "<p>Device '$device_name' added successfully!</p>";
          } else {
              echo "<p class='error'>Error adding device: " . mysqli_error($connection) . "</p>";
          }
      } elseif (isset($_POST['update_device'])) {
          $device_id = htmlentities($_POST['device_id']);
          $device_name = htmlentities($_POST['device_name']);
          $device_type = htmlentities($_POST['device_type']);
          $booking_status = htmlentities($_POST['booking_status']);
          $query = "UPDATE Device SET deviceName='$device_name', type='$device_type', bookingStatus='$booking_status' WHERE deviceId=$device_id";
          if (mysqli_query($connection, $query)) {
              echo "<p>Device ID '$device_id' updated successfully!</p>";
          } else {
              echo "<p class='error'>Error updating device: " . mysqli_error($connection) . "</p>";
          }
      } elseif (isset($_POST['delete_device'])) {
          $device_id = htmlentities($_POST['device_id']);
          $query = "DELETE FROM Device WHERE deviceId=$device_id";
          if (mysqli_query($connection, $query)) {
              echo "<p>Device ID '$device_id' deleted successfully!</p>";
          } else {
              echo "<p class='error'>Error deleting device: " . mysqli_error($connection) . "</p>";
          }
      }
  }

  // Fetch devices from the database
  $query = "SELECT * FROM Device";
  $result = mysqli_query($connection, $query);
  ?>

  <!-- Add Device Form -->
  <h2>Add New Device</h2>
  <form action="manage_devices.php" method="POST">
    <label for="device_name">Device Name:</label>
    <input type="text" name="device_name" required>
    <label for="device_type">Device Type:</label>
    <input type="text" name="device_type" required>
    <input type="submit" name="add_device" value="Add Device">
  </form>

  <h2>Existing Devices</h2>

  <!-- Display existing devices -->
  <table>
    <tr>
      <th>Device ID</th>
      <th>Name</th>
      <th>Type</th>
      <th>Status</th>
      <th>Actions</th>
    </tr>
    <?php if (mysqli_num_rows($result) > 0): ?>
      <?php while($row = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?php echo $row['deviceId']; ?></td>
          <td><?php echo $row['deviceName']; ?></td>
          <td><?php echo $row['type']; ?></td>
          <td><?php echo $row['bookingStatus']; ?></td>
          <td>
            <!-- Edit form -->
            <form action="manage_devices.php" method="POST" style="display:inline;">
              <input type="hidden" name="device_id" value="<?php echo $row['deviceId']; ?>">
              <input type="text" name="device_name" value="<?php echo $row['name']; ?>" required>
              <input type="text" name="device_type" value="<?php echo $row['type']; ?>" required>
              <select name="booking_status" required>
                <option value="available" <?php if ($row['bookingStatus'] == 0) echo 'selected'; ?>>Available</option>
                <option value="booked" <?php if ($row['bookingStatus'] == 1) echo 'selected'; ?>>Booked</option>
              </select>
              <input type="submit" name="update_device" value="Update">
            </form>
            <!-- Delete form -->
            <form action="manage_devices.php" method="POST" style="display:inline;">
              <input type="hidden" name="device_id" value="<?php echo $row['deviceId']; ?>">
              <input type="submit" name="delete_device" value="Delete" onclick="return confirm('Are you sure you want to delete this device?');">
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr>
        <td colspan="5">No devices found.</td>
      </tr>
    <?php endif; ?>
  </table>

</body>
</html>

<?php mysqli_close($connection); ?>
