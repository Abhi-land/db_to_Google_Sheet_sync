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

// --- 2. Fetch only NEW rows from the database ---
$conn = new mysqli("localhost", "root", "", "virtujfr_miltrf");
// Use a prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM e_inv_ack WHERE E_I_Id > ? ORDER BY E_I_Id ASC");
$stmt->bind_param("i", $lastId);
$stmt->execute();
$result = $stmt->get_result();

// --- 3. Prepare new data for appending ---
$newData = [];
while ($row = $result->fetch_assoc()) {
    $newData[] = array_values($row);
}

// --- 4. Append the new data if there is any ---
if (!empty($newData)) {
    $rangeForAppend = $sheetName; // Just the sheet name is needed for appending
    $body = new Google_Service_Sheets_ValueRange(['values' => $newData]);
    $params = ['valueInputOption' => 'RAW'];

    // Use the "append" method instead of "update"
    $service->spreadsheets_values->append($spreadsheetId, $rangeForAppend, $body, $params);

    echo "✅ " . count($newData) . " new rows synced to Google Sheets!";
} else {
    echo "👍 Sheet is already up-to-date. No new data.";
}
// ******* Start Update Json file ****
$idColumnRange = $sheetName . '!B:Z'; // Assuming IDs are in Column A
$response = $service->spreadsheets_values->get($spreadsheetId, $idColumnRange);
$values = $response->getValues();
// print_r($values);

$jsonFilePath = __DIR__ . '\JSON\data.php';
// echo $jsonFilePath;
$jsonData = json_encode($values, JSON_PRETTY_PRINT);
file_put_contents($jsonFilePath, $jsonData);

// ******* End Update Json file ****

$stmt->close();
$conn->close();
