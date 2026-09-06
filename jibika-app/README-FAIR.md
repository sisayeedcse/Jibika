# JIBIKA — Innovation Fair 2026 Walkthrough

Welcome to the JIBIKA prototype for the Bangladesh Innovation Fair 2026. This is a fully functional, mobile-first MVP that simulates the end-to-end lifecycle of an asset financing request.

## How to Demo (The "Rahim" Scenario)

To demonstrate the full power of JIBIKA to the judges, follow this exact sequence:

1. **Start at the Landing Page (`/`)**
   * Demonstrate the **Language Toggle** (top right, switches between English and Bangla instantly).
   * Demonstrate the **Theme Toggle** (Light/Dark mode).
   * Click **"I am a Worker"**.

2. **Worker Flow (`/worker`)**
   * The dashboard shows Rahim has no active requests.
   * Click **"Request an Asset"**.
   * Walk through the 3-step wizard (Progressive disclosure, low-literacy friendly).
   * Submit the request for an "Industrial Sewing Machine" for ৳35,000.
   * The dashboard now shows the request is in the **Requested** state.

3. **Community Verification (`/`) -> click "I am a Community Verifier"**
   * See Rahim's pending request.
   * Review his story and asset need.
   * Click **"Approve"**.
   * *Behind the scenes: The system transitions the status to VERIFIED and automatically runs the mock AI Assessment, transitioning it to AI_ASSESSED and opening the FUNDING pool.*

4. **Contributor Flow (`/`) -> click "I want to Contribute"**
   * See Rahim's opportunity card. Notice the **"Smart Match"** badge indicating AI recommendation.
   * Click the card to view details (`/contributor/opportunity/[id]`).
   * Show the AI Explanation box (Transparency/Trust).
   * Enter an amount (e.g., 35000) and click **"Fund this Asset"**.
   * Confirm the funding.
   * *Behind the scenes: The pool hits 100% and transitions the request to FUNDED.*

5. **Admin / Procurement (`/`) -> click "Admin Dashboard"**
   * See 1 asset in the "Procurement Queue (Funded)".
   * Click **"Assign Vendor"** (Status -> PROCURED).
   * See the asset move to the "Delivery Queue".
   * Click **"Mark Delivered"** (Status -> DELIVERED & MONITORING).

6. **Worker Feedback (`/worker`)**
   * Go back to Rahim's dashboard.
   * Show the **Lifecycle Tracking** timeline, which now contains every timestamped event from Request to Delivery, proving the transparency of the system.

## What is Real vs. Simulated

When speaking to judges, be transparent about the prototype boundaries:

### What is REAL (Production-Ready Architecture)
* **The State Machine:** The entire lifecycle (Requested -> Verified -> AI Assessed -> Funding -> Procured -> Delivered) is driven by a real, strict state machine using Zustand. It prevents invalid transitions.
* **The UI/UX:** Mobile-first responsive design, proper color contrast ratios (WCAG AA), progressive disclosure flows, and touch-optimized targets.
* **i18n (Internationalization):** The English/Bangla language switching is real and architected using a Context API pattern, exactly as it would be in production.
* **Theming:** Full dark/light mode persistence.

### What is SIMULATED (Mocked for the Demo)
* **The Database:** Data is held in memory (Zustand store) seeded from `data/demoData.ts`. A page reload resets the state. In production, this maps to a PostgreSQL/MongoDB database.
* **The Payment Gateway:** Funding currently just updates a number in the state store. In production, this would integrate with SSLCommerz, bKash, or a bank API.
* **The AI Assessment:** The logic that matches Rahim to the Sewing Machine is hardcoded in `store/jibikaStore.ts` (`runAIAssessment`). In production, this would call a Python microservice hosting the ML model.

## Tech Stack
* Next.js 14 (App Router)
* TypeScript
* Tailwind CSS (v4)
* Zustand (State Management)
* Lucide React (Icons)
