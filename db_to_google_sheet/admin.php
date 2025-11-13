<?php

use function PHPSTORM_META\type;

require '../vendor/autoload.php';
include("connection.php");

$client = new Google_Client();
$client->setAuthConfig('credentials.json');
$client->addScope(Google_Service_Sheets::SPREADSHEETS);

$service = new Google_Service_Sheets($client);
$spreadsheetId = "1wAir3uS0tWopXW-XJcaZD3QCje4rlyjKm6Hhyl9B0_A";
$sheetName = "Chhi_Sport_Festival";


// --- 1. Get the last ID from the Google Sheet ---
$idColumnRange = $sheetName . '!A2:Z'; // Assuming IDs are in Column A
$response = $service->spreadsheets_values->get($spreadsheetId, $idColumnRange);
$values = $response->getValues();
// Default to 0 if the sheet is empty


$lastId = 0;
$sql = "SELECT E_I_Id FROM e_inv_ack ORDER BY E_I_Id DESC LIMIT 1";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
  $lastId =  (int)$row['E_I_Id'];
}

// echo $row

$filter_data = [];
if (!empty($values) && count($values) > 1) {
  foreach ($values as $row) {
    if ($lastId < (int)$row[0]) {
      $filter_data[] = $row;
    }  
  }
}
// print_r($filter_data);
// exit;


$result = $conn->query("SELECT * FROM e_inv_ack WHERE E_I_Id > " . $lastId . " ORDER BY E_I_Id ASC");
// print_r($result);
$result1 = $conn->query("SELECT * FROM e_inv_ack   ORDER BY E_I_Id DESC");

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Admin Panel - Registrations</title>
  <link rel="stylesheet" href="../bootstrap.css">
</head>

<body class="m-4 mt-4">
<div class="row">
<div class="col-sm-6">
   <h2>Data From Google Sheet - 2025</h2>
  <form action="savedata.php" method="POST">
    <input type="hidden" name="myData" value="<?php echo htmlspecialchars(json_encode($filter_data)); ?>">
    <button type="submit" class="btn btn-success mb-3">Sync to Database</button>
  </form>

  <table class="table table-bordered table-striped table-responsive" style="height: 600px;">
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
      <?php

      foreach ($filter_data as $row) { ?>
        <tr>
          <td><?= $row[0] ?></td>
          <td><?= $row[1] ?></td>
          <td><?= $row[2] ?></td>
          <td><?= $row[3] ?></td>
          <td><?php
              echo $row[4];


              ?></td>

          <td><?= $row[5] ?></td>
          <td><?= $row[6] ?></td>
          <td><?= $row[7] ?></td>
          <td><?= $row[8] ?></td>
          <td><?= $row[9] ?></td>
          <td><?= $row[10] ?></td>
          <td><?php if (isset($row[11])) {
                echo $row[11];
              } else {
                echo "null";
              } ?></td>
        </tr>
      <?php
      } ?>

    </tbody>
  </table>
</div>
<div class="col-sm-6">
  <h2>Data From Database   -  2025</h2>
  <a href="http://localhost:8080/khel_Mahotsav/sheet_sync/db_to_google_sheet/admin.php" class="btn btn-success mb-3">Refresh Data From Database</a>
  <table  class="table table-bordered table-striped table-responsive" style="height: 600px;">
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
      <?php while ($row = $result1->fetch_assoc()) { ?>
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
</div>
</div>

 
</body>

</html>