<?php

$postdata = file_get_contents("php://input");


$campaigns = [
    [
        "campaignname" => "Test_Banner_13th-31st_march_Developer",
        "advertiser" => "TestGP",
        "code" => "118965F12BE33FB7E",
        "appid" => "20240313103027",
        "tld" => "https://adplaytechnology.com/",
        "portalname" => "",
        "creative_type" => "1",
        "creative_id" => 167629,
        "day_capping" => 0,
        "dimension" => "320x480",
        "attribute" => "rich-media",
        "url" => "https://adplaytechnology.com/",
        "billing_id" => "123456789",
        "price" => 2.5,
        "bidtype" => "CPM",
        "image_url" => "https://s3-ap-southeast-1.amazonaws.com/elasticbeanstalk-ap-southeast-1-5410920200615/CampaignFile/20240117030213/D300x250/e63324c6f222208f1dc66d3e2daaaf06.png",
        "htmltag" => "",
        "from_hour" => "0",
        "to_hour" => "23",
        "hs_os" => "Android,iOS,Desktop",
        "operator" => "Banglalink,GrameenPhone,Robi,Teletalk,Airtel,Wi-Fi",
        "device_make" => "No Filter",
        "country" => "BGD",
        "city" => "",
        "lat" => "",
        "lng" => "",
        "app_name" => null,
        "user_list_id" => "0",
        "adplay_logo" => 1,
        "vast_video_duration" => null,
        "logo_placement" => 1,
        "hs_model" => null,
        "is_rewarded_inventory" => 0,
        "pixel_tag" => null,
        "dmp_campaign_audience" => 0,
        "platform" => null,
        "open_publisher" => 1,
        "audience_targeting" => 0,
        "native_title" => null,
        "native_type" => null,
        "native_data_value" => null,
        "native_data_cta" => null,
        "native_data_rating" => null,
        "native_data_price" => null,
        "native_img_icon" => null
    ]
];


$bidRequest = json_decode($postdata, true);
if (!$bidRequest) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid bid request JSON"]);
    exit;
}


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


$selectedCampaign = null;

foreach ($campaigns as $campaign) {
    [$campaignWidth, $campaignHeight] = explode('x', $campaign['dimension']);

    $matchedDimension = false;
    foreach ($requestedFormats as $format) {
        if ((int)$campaignWidth === $format['w'] && (int)$campaignHeight === $format['h']) {
            $matchedDimension = true;
            break;
        }
    }
    if (!$matchedDimension) continue;

    $campaignOSList = array_map('strtolower', explode(',', $campaign['hs_os']));
    if (!in_array($deviceOS, $campaignOSList)) continue;

  
    if (strtolower($campaign['country']) !== $deviceCountry) continue;

  
    if ($campaign['price'] < $requestedFloor) continue;

  
    if (!$selectedCampaign || $campaign['price'] > $selectedCampaign['price']) {
        $selectedCampaign = $campaign;
    }
}


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


header('Content-Type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);
?>