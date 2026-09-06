# JIBIKA — Innovation Fair 2026
## Production-Ready MVP Specification & Implementation Guideline

**Official Title:** AI-Powered FinTech for Inclusive Livelihood & Productive Asset Financing
**Tagline:** From Small Capital to Productive Livelihood
**Focus:** FinTech + Artificial Intelligence + Digital Trust

### 1. First Principle
JIBIKA is an AI-enhanced FinTech lifecycle platform that connects verified livelihood needs with small-scale capital for productive assets, using community verification, transparent funding workflows, intelligent matching, procurement tracking, and asset lifecycle monitoring.
*It is a realistic, interactive demonstration using controlled/mock data, NOT a live production banking platform.*

### 2. The Core Philosophy (5 Layers)
1. **Asset-first:** Capital connects to a productive asset.
2. **Trust-first:** Community/Somitte verification provides human trust.
3. **Transparency-first:** Lifecycle is visible.
4. **AI-assisted:** AI improves decisions, doesn't replace humans.
5. **Outcome-oriented:** System follows the asset beyond funding.

### 3. The Central Object: `AssetRequest`
Everything revolves around the `AssetRequest`. It travels through the complete lifecycle:
`REQUESTED` → `VERIFIED` → `AI ASSESSED` → `FUNDING` → `FUNDED` → `PROCURED` → `DELIVERED` → `MONITORING`

### 4. MVP Scope
*   **Mandatory (Tier 1):** Worker profiles/requests, Community verification, AI suitability/recommendation/matching, Contributor simulated funding, FinTech funding pool, Procurement tracking, Monitoring/Transparency dashboards.
*   **DO NOT BUILD for Fair:** Real payment gateways, real banking integration, blockchain/crypto, complex auth, live DBs.

### 5. Role Architecture (5 Roles)
*   **WORKER:** Profile, request asset, view status/lifecycle.
*   **COMMUNITY / SOMITTE:** Review & approve pending verifications.
*   **CONTRIBUTOR:** View matched opportunities, simulate funding, portfolio.
*   **VENDOR:** (Procurement) Fulfill verified assets.
*   **ADMIN:** Manage system flow, assign vendors, oversee deliveries.

### 6. AI Intelligence Layer
*   **AIAssessment:** Evaluates suitability (High/Med/Low) based on occupation, experience, location, cost, demand.
*   **Asset Optimization:** Compares requested asset against alternatives (e.g., Industrial Sewing Machine vs. Embroidery Machine).
*   **Smart Matching:** Content/rule-based matching of opportunities to contributors (No complex collaborative filtering yet).
*   **Explanation:** Clear "Why?" for every recommendation (AI safety/transparency).
*   **Feedback Loop:** Post-delivery monitoring data informs future AI accuracy.

### 7. Tech Stack & State Management
*   **Frontend:** Next.js + TypeScript
*   **State:** Zustand (recommended) or React Context
*   **Mock Data Layer:** `/data/workers.ts`, `/data/assets.ts`, etc., feeding into the Zustand store (`/store/jibikaStore.ts`) to simulate DB interactions locally.

### 8. Single Golden Demo Scenario (Rahim the Tailor)
1. **Worker:** Rahim registers & requests an Industrial Sewing Machine.
2. **Community:** Somitte verifies his request.
3. **AI:** Evaluates request, recommends the machine, generates explanation.
4. **Contributor:** Sees opportunity via smart match, adds simulated funding (reaches 100%).
5. **Admin/Vendor:** Assigns vendor, procures, and delivers asset.
6. **Monitoring:** Asset status becomes ACTIVE, livelihood outcome data begins feeding back into AI.

### 9. Development Phases
1. **FinTech Foundation:** Worker, asset, request, verification, funding pool, dashboards.
2. **Lifecycle:** Contribution, vendor, procurement, delivery, tracking.
3. **AI:** Mock dataset, suitability, recommendation, matching, explanation.
4. **Analytics:** Funding analytics, lifecycle dashboard, outcome metrics.
5. **Fair Polish:** Offline testing, edge cases, transitions.
