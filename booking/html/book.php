<?php include "../inc/dbinfo.inc"; ?>
<html>
<head>
  <title>Device Booking System</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <style>
    .confirmation {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      padding: 20px;
      background-color: white;
      border: 1px solid #ccc;
      box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
      z-index: 1000;
    }
    .overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
    }
    .error {
      color: red;
      font-weight: bold;
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script>
    function confirmBooking(deviceId, deviceName, deviceType, startDate, endDate) {
        const userName = document.getElementById('userName').value.trim();
        const startInput = document.getElementById('startDate').value.trim();
        const endInput = document.getElementById('endDate').value.trim();
        const errorMessage = document.getElementById('error-message');

        // Clear previous error message
        errorMessage.innerHTML = '';

        // Check if fields are filled
        if (userName === '' || startInput === '' || endInput === '') {
            errorMessage.innerHTML = 'Please fill in all required fields: Your Name, Start Date, and End Date.';
            return;
        }

        const confirmationBox = document.getElementById('confirmation');
        const confirmationText = `
            <h3>Confirm Booking</h3>
            <p><strong>Your Name:</strong> ${userName}</p>
            <p><strong>Device ID:</strong> ${deviceId}</p>
            <p><strong>Device Name:</strong> ${deviceName}</p>
            <p><strong>Device Type:</strong> ${deviceType}</p>
            <p><strong>Start Date:</strong> ${startDate}</p>
            <p><strong>End Date:</strong> ${endDate}</p>
            <p>Are you sure you want to book this device?</p>
            <form action="add_booking.php" method="POST">
                <input type="hidden" name="userName" value="${userName}" />
                <input type="hidden" name="deviceId" value="${deviceId}" />
                <input type="hidden" name="deviceName" value="${deviceName}" />
                <input type="hidden" name="deviceType" value="${deviceType}" />
                <input type="hidden" name="startDate" value="${startDate}" />
                <input type="hidden" name="endDate" value="${endDate}" />
                <input type="submit" value="Confirm Booking" />
                <button type="button" onclick="closeConfirmation()">Cancel</button>
            </form>
        `;
        
        confirmationBox.innerHTML = confirmationText;
        confirmationBox.style.display = 'block';
        document.getElementById('overlay').style.display = 'block';
    }

    function closeConfirmation() {
        document.getElementById('confirmation').style.display = 'none';
        document.getElementById('overlay').style.display = 'none';
    }
    
    function clearFilters() {
      document.getElementsByName('DEVICE_NAME')[0].value = '';
      document.getElementById('startDate').value = '';
      document.getElementById('endDate').value = '';
      document.getElementsByName('DEVICE_TYPE')[0].selectedIndex = 0;
      document.getElementById('userName').value = '';
    }

    // Set the minimum date for the date inputs
    function setMinDate() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('startDate').setAttribute('min', today);
        document.getElementById('endDate').setAttribute('min', today);
    }
  </script>
</head>
<body onload="setMinDate();">
<h1>Device Booking System</h1>

<?php
  /* Connect to MySQL and select the database */
  $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

  if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

  $database = mysqli_select_db($connection, DB_DATABASE);

  /* Capture form inputs */
  $user_name = htmlentities($_POST['USER_NAME'] ?? '');
  $device_name = htmlentities($_POST['DEVICE_NAME'] ?? '');
  $start_date = htmlentities($_POST['START_DATE'] ?? date('Y-m-d'));
  $end_date = htmlentities($_POST['END_DATE'] ?? '');
  $device_type = htmlentities($_POST['DEVICE_TYPE'] ?? '');

  /* Base query to select all devices */
  $query = "SELECT d.deviceId, d.deviceName, d.type, d.available FROM Device d";

  /* Conditions to filter the query if any inputs are provided */
  $conditions = [];

  //condition to filter out things flagged as unavailable.
  $conditions[] = "d.available = 0";

  if (!empty($device_name)) {
      $conditions[] = "d.deviceName LIKE '%$device_name%'";
  }

  if (!empty($start_date) && !empty($end_date)) {
      $conditions[] = "d.deviceId NOT IN (
          SELECT b.deviceId 
          FROM Booking b 
          WHERE ('$start_date' <= b.endDate AND '$end_date' >= b.startDate)
      )";
  }

  if (!empty($device_type)) {
      $conditions[] = "d.type = '$device_type'";
  }

  if (!empty($conditions)) {
      $query .= " WHERE " . implode(' AND ', $conditions);
  }

  /* Execute the query to get the devices */
  $result = mysqli_query($connection, $query);

  if(!$result) {
      echo "<p>Error retrieving devices: " . mysqli_error($connection) . "</p>";
  }

  // Query to get distinct device types
  $typeQuery = "SELECT DISTINCT type FROM Device";
  $typeResult = mysqli_query($connection, $typeQuery);
  $deviceTypes = [];

  while ($row = mysqli_fetch_assoc($typeResult)) {
      $deviceTypes[] = $row['type'];
  }
?>

<!-- Input form -->
<form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
  <table border="0">
    <tr>
      <td>Your Name:</td>
      <td><input type="text" id="userName" name="USER_NAME" value="<?php echo $user_name; ?>" required /></td>
    </tr>
    <tr>
      <td>Device Name:</td>
      <td><input type="text" name="DEVICE_NAME" value="<?php echo $device_name; ?>" /></td>
    </tr>
    <tr>
      <td>Start Date:</td>
      <td><input type="date" id="startDate" name="START_DATE" value="<?php echo $start_date; ?>" required /></td>
    </tr>
    <tr>
      <td>End Date:</td>
      <td><input type="date" id="endDate" name="END_DATE" value="<?php echo $end_date; ?>" required /></td>
    </tr>
    <tr>
      <td>Device Type:</td>
      <td>
        <select name="DEVICE_TYPE">
          <option value="">-- Select Device Type --</option>
          <?php foreach ($deviceTypes as $type): ?>
            <option value="<?php echo $type; ?>" <?php echo (isset($device_type) && $device_type == $type) ? 'selected' : ''; ?>>
              <?php echo $type; ?>
            </option>
          <?php endforeach; ?>
        </select>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <input type="submit" value="Find Available Devices" />
        <input type="button" value="Clear Filters" onclick="clearFilters()" />
      </td>
    </tr>
  </table>
</form>

<div id="error-message" class="error"></div>

<?php if ($result && mysqli_num_rows($result) > 0): ?>
  <!-- Display list of available devices -->
  <h2>Available Devices</h2>
  <table border="1" cellpadding="2" cellspacing="2">
    <tr>
      <th>Device ID</th>
      <th>Name</th>
      <th>Type</th>
      <th>Action</th>
    </tr>
    
    <?php while($row = mysqli_fetch_assoc($result)): ?>
      <tr>
        <td><?php echo $row['deviceId']; ?></td>
        <td><?php echo $row['deviceName']; ?></td>
        <td><?php echo $row['type']; ?></td>
        <td>
          <button onclick="confirmBooking('<?php echo $row['deviceId']; ?>', '<?php echo $row['deviceName']; ?>', '<?php echo $row['type']; ?>', '<?php echo $start_date; ?>', '<?php echo $end_date; ?>')">Book</button>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
<?php else: ?>
  <p>No available devices found.</p>
<?php endif; ?>

<!-- Confirmation Box -->
<div id="overlay" class="overlay" onclick="closeConfirmation()"></div>
<div id="confirmation" class="confirmation"></div>

</body>
</html>
