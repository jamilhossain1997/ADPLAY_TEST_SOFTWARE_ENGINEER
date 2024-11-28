<?php
// Simulated POST data for testing (you can remove this and use live data from php://input)
$postdata = json_encode([
    "id" => "myB92gUhMdC5DUxndq3yAg",
    "imp" => [
        [
            "id" => "1",
            "banner" => [
                "format" => [
                    ["w" => 776, "h" => 393],
                    ["w" => 667, "h" => 375],
                    ["w" => 320, "h" => 480]
                ]
            ],
            "bidfloor" => 2,
            "bidfloorcur" => "USD",
        ]
    ],
    "device" => [
        "geo" => ["country" => "BGD"],
        "os" => "android"
    ]
]);

// Campaign Array
$campaigns = [
    [
        "campaignname" => "Test_Banner_13th-31st_march_Developer",
        "advertiser" => "TestGP",
        "creative_id" => "167629",
        "dimension" => "776x393",
        "price" => 2.5,
        "hs_os" => "android,ios",
        "country" => "BGD",
        "url" => "https://adplaytechnology.com/",
        "image_url" => "https://example.com/banner.jpg"
    ]
];

// Decode Bid Request
$bidRequest = json_decode($postdata, true);
if (!$bidRequest) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid bid request JSON"]);
    exit;
}

// Extract bid request details
$imp = $bidRequest['imp'][0] ?? null;
$device = $bidRequest['device'] ?? null;

if (!$imp || !$device) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid bid request: Missing impression or device information"]);
    exit;
}

$requestedFormats = $imp['banner']['format'] ?? [];
$requestedFloor = $imp['bidfloor'] ?? 0;
$deviceOS = strtolower($device['os'] ?? '');
$deviceCountry = strtolower($device['geo']['country'] ?? '');

// Select a suitable campaign
$selectedCampaign = null;

foreach ($campaigns as $campaign) {
    [$campaignWidth, $campaignHeight] = explode('x', $campaign['dimension']);

    // Check if any format matches the campaign's dimension
    $matchedDimension = false;
    foreach ($requestedFormats as $format) {
        if ((int)$campaignWidth === $format['w'] && (int)$campaignHeight === $format['h']) {
            $matchedDimension = true;
            break;
        }
    }
    if (!$matchedDimension) continue;

    // Check OS compatibility
    $campaignOSList = array_map('strtolower', explode(',', $campaign['hs_os']));
    if (!in_array($deviceOS, $campaignOSList)) continue;

    // Check country compatibility
    if (strtolower($campaign['country']) !== $deviceCountry) continue;

    // Check bid floor compatibility
    if ($campaign['price'] < $requestedFloor) continue;

    // Select the campaign with the highest price
    if (!$selectedCampaign || $campaign['price'] > $selectedCampaign['price']) {
        $selectedCampaign = $campaign;
    }
}

// Generate response
if ($selectedCampaign) {
    $response = [
        "campaign_name" => $selectedCampaign['campaignname'],
        "advertiser" => $selectedCampaign['advertiser'],
        "bid_price" => $selectedCampaign['price'],
        "ad_id" => uniqid(),
        "creative_id" => $selectedCampaign['creative_id'],
        "creative_type" => "banner",
        "image_url" => $selectedCampaign['image_url'],
        "landing_page_url" => $selectedCampaign['url'],
        "adomain" => [$selectedCampaign['url']]
    ];
} else {
    $response = ["error" => "No suitable campaign found"];
}

// Output JSON response
header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);
?>