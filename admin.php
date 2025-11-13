<?php

require 'vendor/autoload.php';

$client = new Google_Client();
$client->setAuthConfig('credentials.json');
$client->addScope(Google_Service_Sheets::SPREADSHEETS);

$service = new Google_Service_Sheets($client);
$spreadsheetId = "1wAir3uS0tWopXW-XJcaZD3QCje4rlyjKm6Hhyl9B0_A";
$sheetName = "Chhi_Sport_Festival";


// --- 1. Get the last ID from the Google Sheet ---
$idColumnRange = $sheetName . '!A:A'; // Assuming IDs are in Column A
$response = $service->spreadsheets_values->get($spreadsheetId, $idColumnRange);
$values = $response->getValues();
$lastId = 0; // Default to 0 if the sheet is empty
if (!empty($values) && count($values) > 1) {
    // Get the last value from the array, which is the last row
    $lastRow = end($values);
    // Ensure the last row is not empty and get its value
    if (!empty($lastRow[0]) && is_numeric($lastRow[0])) {
        $lastId = (int)$lastRow[0];
    }
}

$conn = new mysqli("localhost", "root", "", "virtujfr_miltrf");

$result = $conn->query("SELECT * FROM e_inv_ack WHERE E_I_Id > ".$lastId." ORDER BY E_I_Id ASC");
// print_r($result);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Admin Panel - Registrations</title>
  <link rel="stylesheet" href="bootstrap.css">
</head>

<body class="container mt-4">
  <h2>Admin Panel - Sports Festival 2025</h2>
  <a href="sync1.php" class="btn btn-success mb-3">Sync to Google Sheets</a>
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>E_I_Id</th>
        <th>IRN</th>
        <th>Ack_No</th>
        <th>Ack_Date</th>
        <th>Doc_No</th>
        <th>Doc_Typ</th>
        <th>Doc_Date</th>
        <th>Inv_Value</th>
        <th>Recipient_GSTIN</th>
        <th>Status</th>
        <th>Signed_QR_Code</th>
        <th>If_Errs</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
          <td><?= $row['E_I_Id'] ?></td>
          <td><?= $row['IRN'] ?></td>
          <td><?= $row['Ack_No'] ?></td>
          <td><?= $row['Ack_Date'] ?></td>
          <td><?= $row['Doc_No'] ?></td>
          <td><?= $row['Doc_Typ'] ?></td>
          <td><?= $row['Doc_Date'] ?></td>
          <td><?= $row['Inv_Value'] ?></td>
          <td><?= $row['Recipient_GSTIN'] ?></td>
          <td><?= $row['Status'] ?></td>
          <td><?= $row['Signed_QR_Code'] ?></td>
          <td><?= $row['If_Errs'] ?></td>
        </tr>
      <?php

      } ?>

    </tbody>
  </table>
</body>

</html>
