<?php include "../inc/dbinfo.inc"; ?>
<html>
<head>
  <title>Manage Bookings</title>
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
  <h1>Manage Bookings</h1>
  <a href="admin.php">Back to Dashboard</a>

  <?php
  // Connect to MySQL and select the database
  $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

  if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

  $database = mysqli_select_db($connection, DB_DATABASE);

  // Handle form submissions
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (isset($_POST['delete_booking'])) {
          // Deleting a booking
          $booking_id = htmlentities($_POST['booking_id']);
          $query = "DELETE FROM Booking WHERE bookingId=$booking_id";
          if (mysqli_query($connection, $query)) {
              echo "<p>Booking ID '$booking_id' deleted successfully!</p>";
          } else {
              echo "<p class='error'>Error deleting booking: " . mysqli_error($connection) . "</p>";
          }
      }
  }

  // Fetch bookings from the database
  $query = "SELECT * FROM Booking";
  $result = mysqli_query($connection, $query);
  ?>

  <h2>Existing Bookings</h2>

  <!-- Display existing bookings -->
  <table>
    <tr>
      <th>Booking ID</th>
      <th>Device ID</th>
      <th>User Name</th>
      <th>Start Time</th>
      <th>End Time</th>
      <th>Actions</th>
    </tr>
    <?php if (mysqli_num_rows($result) > 0): ?>
      <?php while($row = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?php echo $row['bookingId']; ?></td>
          <td><?php echo $row['deviceId']; ?></td>
          <td><?php echo $row['userName']; ?></td>
          <td><?php echo $row['startDate']; ?></td>
          <td><?php echo $row['endDate']; ?></td>
          <td>
            <!-- Delete form -->
            <form action="manage_bookings.php" method="POST" style="display:inline;">
              <input type="hidden" name="booking_id" value="<?php echo $row['bookingId']; ?>">
              <input type="submit" name="delete_booking" value="Delete" onclick="return confirm('Are you sure you want to delete this booking?');">
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr>
        <td colspan="6">No bookings found.</td>
      </tr>
    <?php endif; ?>
  </table>

</body>
</html>

<?php mysqli_close($connection); ?>
