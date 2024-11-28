
# Real-Time Bidding (RTB) Banner Campaign Response Script

This PHP script handles bid requests and generates appropriate banner campaign responses for Real-Time Bidding (RTB) scenarios. It parses incoming bid request JSON, matches campaigns based on specific criteria, and returns the most suitable campaign as a JSON response.

---

## Features
- **Bid Request Parsing**: Validates and processes bid requests in JSON format.
- **Campaign Matching**: Matches campaigns based on:
  - Banner dimensions
  - Operating system compatibility
  - Geographical targeting
  - Bid floor price
- **Response Generation**: Returns a JSON response compliant with RTB standards, including bid price, ad ID, creative ID, and other details.
- **Error Handling**: Provides clear error messages for invalid requests or no matching campaigns.

---

## Requirements
- PHP 7.4 or higher
- Web server (e.g., Apache, Nginx) to host the script

---

## Setup

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/your-repo/rtb-banner-script.git
   cd rtb-banner-script
   ```

2. **Configure the Campaign Array**:
   Update the `$campaigns` array in the script with your campaign details, such as dimensions, bid price, operating system, and country targeting.

3. **Host the Script**:
   Place the script on your web server and ensure it is accessible via a public or local URL (e.g., `http://localhost/rtb-script.php`).

---

## Usage

### Sending a Bid Request

Use the following JSON format to send a bid request to the script:

#### Bid Request JSON
```json
{
    "id": "myB92gUhMdC5DUxndq3yAg",
    "imp": [
        {
            "id": "1",
            "banner": {
                "format": [
                    {"w": 776, "h": 393},
                    {"w": 667, "h": 375},
                    {"w": 320, "h": 480}
                ]
            },
            "bidfloor": 2,
            "bidfloorcur": "USD"
        }
    ],
    "device": {
        "geo": {"country": "BGD"},
        "os": "android"
    }
}
```

#### Sample cURL Command
Send the bid request using cURL:
```bash
curl -X POST \
-H "Content-Type: application/json" \
-d '{
    "id": "myB92gUhMdC5DUxndq3yAg",
    "imp": [
        {
            "id": "1",
            "banner": {
                "format": [
                    {"w": 776, "h": 393},
                    {"w": 667, "h": 375},
                    {"w": 320, "h": 480}
                ]
            },
            "bidfloor": 2,
            "bidfloorcur": "USD"
        }
    ],
    "device": {
        "geo": {"country": "BGD"},
        "os": "android"
    }
}' \
http://yourserver.com/rtb-script.php
```

---

## Response

### Success Response
If a matching campaign is found, the script returns:
```json
{
    "campaign_name": "Test_Banner_13th-31st_march_Developer",
    "advertiser": "TestGP",
    "bid_price": 2.5,
    "ad_id": "645aefb6b5c7c",
    "creative_id": "167629",
    "creative_type": "banner",
    "image_url": "https://example.com/banner.jpg",
    "landing_page_url": "https://adplaytechnology.com/",
    "adomain": ["https://adplaytechnology.com/"]
}
```

### Error Response
If no matching campaign is found, the script returns:
```json
{
    "error": "No suitable campaign found"
}
```

---

## License
This project is licensed under the MIT License. See the LICENSE file for details.
