<?php
// Database connection
include("connection.php");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (isset($_POST['myData'])) {

    $jsonString = $_POST['myData'];
    $receivedArray = json_decode($jsonString, true);
    // print_r($receivedArray);
    foreach ($receivedArray as $row) {
        if (!isset($row[11])) {
            $row[11] = "null";
        }

        $originalDate = trim($row[6]);
        $dateObject = DateTime::createFromFormat('d/m/Y', $originalDate);
        $newDate = $dateObject->format('Y-m-d');
        $row[6] = $newDate;

        $sql = "INSERT INTO e_inv_ack 
(IRN, Ack_No, Ack_Date, Doc_No, Doc_Typ, Doc_Date, Inv_Value, Recipient_GSTIN, Status, Signed_QR_Code, If_Errs)
VALUES 
('" . $row[1] . "','" . $row[2] . "','" . $row[3] . "','" . $row[4] . "','" . $row[5] . "','" . $row[6] . "','" . $row[7] . "','" . $row[8] . "','" . $row[9] . "','" . $row[10] . "','" . $row[11] . "')";
        $conn->query($sql);
    }
}

$conn->close();
?>
<script>
    window.location.href = "http://localhost:8080/khel_Mahotsav/sheet_sync/db_to_google_sheet/admin.php";
</script>