<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $credit_card = str_replace(' ', '', $_POST['credit_card']); // Remove spaces from credit card number
    $expiration_date = $_POST['expiration_date'];

    // Sample items and prices (Replace with actual items and prices from your database or wherever you store them)
    $items = array(
        'item1' => 50.00,
        'item2' => 75.00,
        'item3' => 30.00
    );

    $selected_items = $_POST['selected_items']; // Assuming this is an array of selected item keys (e.g., ['item1', 'item3'])

    // Calculate total amount based on selected items
    $amount = 0;
    foreach ($selected_items as $item_key) {
        if (isset($items[$item_key])) {
            $amount += $items[$item_key];
        }
    }

    $vendor_id = 'VE001-99'; // Replace with your actual vendor ID

   // Validate Credit Card Number
    if (!preg_match('/^\d{16}$/', strval($credit_card))) {
        die("Error: Invalid credit card number. Please enter a 16-digit number without spaces or non-digit characters.");
    }


    // Validate Expiration Date
    if (!preg_match('/^(0[1-9]|1[0-2])\/(\d{4})$/', $expiration_date)) {
        die("Error: Invalid expiration date. Please enter the date in mm/yyyy format.");
    }

    // Prepare data for RESTful webservice
    $data = array(
        'vendor' => $vendor_id,
        'trans' => uniqid(), // Generate a unique transaction id
        'cc' => $credit_card,
        'name' => $name,
        'exp' => $expiration_date,
        'amount' => number_format($amount, 2)
    );

    $url = 'http://blitz.cs.niu.edu/CreditCard/';

    $options = array(
        'http' => array(
            'header' => array('Content-type: application/json', 'Accept: application/json'),
            'method' => 'POST',
            'content' => json_encode($data)
        )
    );

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    // Check response from RESTful webservice
    if (strpos($result, 'Error') === 0) {
        die($result); // Display error message
    } else {
        // Decode JSON response
        $response = json_decode($result, true);

        // Create authorization page
        echo "<!DOCTYPE html>
              <html lang='en'>
              <head>
                  <meta charset='UTF-8'>
                  <title>Credit Card Authorization</title>
              </head>
              <body>
                  <h2>Credit Card Authorization</h2>
                  <pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>
              </body>
              </html>";
    }
}
?>
