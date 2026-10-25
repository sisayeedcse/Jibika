# JIBIKA — REST API Guide (v1)

## 1. Overview & Conventions

The JIBIKA v1 API provides standardized REST endpoints for inspecting productive asset categories, live funding pools, and user-scoped asset requests.

- **Base URL:** `/api/v1/`
- **Format:** JSON (`Content-Type: application/json`, `Accept: application/json`)
- **Pagination:** Offset/limit pagination with `meta` and `links` envelopes (`?page=1`).
- **Date Format:** ISO 8601 (`YYYY-MM-DDTHH:MM:SS.sssZ`).

---

## 2. Endpoints Reference

### 2.1 Asset Categories (Public)

#### `GET /api/v1/categories`
Returns a paginated list of supported productive asset categories.

**Response `200 OK`:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Industrial Sewing Machine",
      "name_bn": "শিল্প সেলাই মেশিন",
      "estimated_cost_bdt": 35000.0,
      "description": "Heavy-duty automatic needle feed machine for tailors and apparel artisans.",
      "created_at": "2026-10-22T08:00:00.000000Z"
    }
  ],
  "links": { "first": "...", "last": "...", "prev": null, "next": null },
  "meta": { "current_page": 1, "from": 1, "last_page": 1, "path": "...", "per_page": 20, "to": 1, "total": 1 }
}
```

#### `GET /api/v1/categories/{id}`
Returns details for a specific asset category.

---

### 2.2 Funding Opportunities (Public)

#### `GET /api/v1/funding-pools`
Lists open crowd-funding pools currently accepting simulated pledges.

**Query Parameters:**
- `status` *(optional)*: `open` (default), `completed`, `cancelled`.

**Response `200 OK`:**
```json
{
  "data": [
    {
      "id": 1,
      "request_id": 12,
      "target_amount_bdt": 35000.0,
      "current_amount_bdt": 15000.0,
      "progress_percent": 42.9,
      "status": "open",
      "is_simulated_mode": true,
      "disclaimer": "Simulated funding mode only. Not a real financial transaction.",
      "created_at": "2026-10-22T09:30:00.000000Z"
    }
  ]
}
```

#### `GET /api/v1/funding-pools/{id}`
Returns real-time progress for a specific funding pool.

---

### 2.3 Asset Requests (Authenticated)

#### `GET /api/v1/session/requests`
Returns asset requests scoped to the authenticated caller:
- **Workers:** Returns only requests owned by the authenticated worker.
- **Administrators:** Returns all asset requests across the platform.

**Response `200 OK`:**
```json
{
  "data": [
    {
      "id": 12,
      "worker_id": 4,
      "status": "funding_open",
      "requested_amount_bdt": 35000.0,
      "reason": "Expanding tailoring workshop to take on uniform contracts.",
      "livelihood_justification": "Generates BDT 14,000 additional monthly income.",
      "category": { "id": 1, "name": "Industrial Sewing Machine" },
      "verification": { "id": 8, "status": "verified" },
      "assessment": {
        "id": 5,
        "suitability_category": "HIGH",
        "numerical_score": 85.0,
        "method_version": "v1.0-rules",
        "is_ai_generated": false,
        "provider_name": "NOT AN LLM RESPONSE"
      },
      "funding_pool_id": 1,
      "procurement_order_id": null,
      "created_at": "2026-10-22T08:30:00.000000Z",
      "updated_at": "2026-10-22T09:30:00.000000Z"
    }
  ]
}
```

#### `GET /api/v1/session/requests/{id}`
Returns complete lifecycle details for a specific asset request.  
*Returns `403 Forbidden` if a worker attempts to access another worker's request.*

---

## 3. Error Responses

All API errors return RFC 7807 compliant JSON envelopes:

```json
{
  "message": "Unauthorized access to asset request."
}
```
| Status Code | Reason |
| :--- | :--- |
| **`401 Unauthorized`** | Missing or invalid authentication credentials. |
| **`403 Forbidden`** | Insufficient role permissions or cross-user resource violation. |
| **`404 Not Found`** | Requested resource ID does not exist. |
| **`422 Unprocessable Entity`** | Validation failure with detailed field errors in `errors` dictionary. |
