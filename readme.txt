=== WPIntegrations Form Connector for Contact Form 7 and RD Station ===
Contributors: wpintegrations
Tags: rd station, contact form 7, leads, forms, conversions
Requires at least: 6.2
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Send Contact Form 7 submissions to RD Station Marketing as conversion events, with field mapping and lead attribution.

== Description ==

**WPIntegrations Form Connector** sends every Contact Form 7 submission to RD Station Marketing as a conversion event — automatically, and with the lead source attribution intact.

= Why this plugin =

* **Attribution preserved** — the visitor's RD Station tracking cookies (`rdtrk`, `__trf.src`) are forwarded with each conversion, so your funnel and traffic source reports stay accurate, exactly like a native RD Station form.
* **Field mapping UI** — pick one of your Contact Form 7 forms and map its fields to RD Station fields in a modern admin interface, custom fields from your own account included. Your forms and their fields are listed for you, so nothing has to be typed.
* **Doesn't hold up your form** — on hosts running PHP-FPM or LiteSpeed (most of them), delivery happens after the visitor already has their confirmation, so a slow RD Station doesn't slow your form down.
* **Leads arrive even when email does not** — the submission is sent to RD Station whether or not Contact Form 7 managed to send its notification email.
* **LGPD ready** — map your form's consent checkbox and the plugin sends the corresponding legal basis with each conversion.
* **Full delivery log** — see every request, response and failure. Nothing is silent.
* **Secure by design** — your OAuth tokens are stored encrypted, every admin endpoint requires administrator capability, and nothing is ever sent anywhere except RD Station's API.

= What this free version includes =

* Unlimited integrations, each sending a Contact Form 7 form to RD Station Marketing
* Unlimited mapped fields per integration
* Your own conversion identifier per integration
* Your forms, their fields and your account's real custom fields offered in the mapping screen
* LGPD consent mapping and RD Station attribution cookies forwarded with every conversion
* A delivery log with the full request and response for every send

= What the Pro version adds =

* **RD Station CRM as a destination** — send submissions straight into your CRM as contacts, matched on email so a returning visitor updates the record they already have, with companies and deals alongside
* Tags, funnel stage, lead owner and the opportunity flag
* A delivery queue that retries failed sends automatically

[Learn more about the Pro version](https://wpintegrations.net/plugins/connect-contact-form-7-to-rd-station/)

= Source code and third-party libraries =

The complete, human-readable source of this plugin is public at [github.com/wpintegrations/wpintegrations-connector-for-cf7-rd-station](https://github.com/wpintegrations/wpintegrations-connector-for-cf7-rd-station).

The admin screens are a React app. The compiled bundle in `build/assets/` is generated with Vite from the source in `resources/js/`, which ships with the plugin together with `package.json` and `vite.config.mjs`. To rebuild it, install Node.js 18 or later and pnpm, then run `pnpm install` and `pnpm run build` in the plugin folder.

The bundle includes these open-source libraries:

* React and React DOM 18.3.1 (MIT) — [github.com/facebook/react](https://github.com/facebook/react)
* Ant Design 5.29.3 (MIT) — [github.com/ant-design/ant-design](https://github.com/ant-design/ant-design)
* Ant Design Icons 5.6.1 (MIT) — [github.com/ant-design/ant-design-icons](https://github.com/ant-design/ant-design-icons)

This plugin is built by WPIntegrations and is not affiliated with, endorsed by, or sponsored by Contact Form 7, its author, or RD Station. "Contact Form 7" and "RD Station" are the trademarks of their respective owners, used here only to describe what the plugin connects.

= Requirements =

* Contact Form 7
* An app registered in the RD Station App Store, with the RD Station Marketing permissions

== External services ==

This plugin connects to the **RD Station APIs** (operated by RD Station, https://www.rdstation.com/). Nothing is sent until you create an integration, and then only when the Contact Form 7 form it listens to is submitted.

**RD Station Marketing** — delivers your submissions as conversion events.

* **What is sent:** the form fields you explicitly map (such as name, email, phone), the conversion identifier, and — if present — the visitor's RD Station tracking values for lead attribution: the visitor ID from the `rdtrk` cookie and the traffic source from the `__trf.src` cookie. If you map them, it also sends the optional values the mapping screen offers: the page URL, the submission time, the logged-in user's name and email, the site title and URL, and any fixed text you enter.
* **What is read:** your account's contact fields, so the mapping screen can offer your real custom fields instead of asking you to type identifiers.
* **Connection test:** the Test connection button sends one conversion with the identifier `wpi-connection-test` and no contact data.
* **Where:** `https://api.rd.services/platform/events` and `https://api.rd.services/platform/contacts/fields`, authenticated with the OAuth token from your own RD Station app.

**RD Station authentication** — used only while connecting an account and when an access token is renewed. Your app's Client ID and Client Secret, the authorization code and the refresh token are sent to `https://api.rd.services/auth/dialog` and `https://api.rd.services/auth/token`.

No data is sent to WPIntegrations or any other third party. RD Station terms of use: https://legal.rdstation.com/en/rdstation-mp-services-agreement/ — privacy notice: https://legal.rdstation.com/en/privacy-policy/

== Installation ==

1. Install and activate the plugin.
2. Create an app in the RD Station App Store with the RD Station Marketing permissions, register the callback URL shown on the plugin's Settings tab, then paste the app's Client ID and Client Secret there and connect.
3. Open the **Integrations** tab and create an integration: pick your contact form, then map the fields and save.

== Frequently Asked Questions ==

= Do I need anything besides Contact Form 7? =

No other form plugin. You need Contact Form 7, an RD Station Marketing account, and your own app registered in the RD Station App Store.

= Which Contact Form 7 fields can I map? =

Any field that has a name — text, email, phone, textarea, menus, checkboxes, radio buttons, acceptance and so on. File uploads, quizzes and CAPTCHAs are not sent. A field with several values, such as a checkbox group, is sent as a comma-separated list.

= Is the lead sent if Contact Form 7 cannot send its email? =

Yes. Delivery to RD Station does not depend on the notification email. A submission Contact Form 7 rejects — a validation error, spam, or a missing acceptance — is not sent.

= Which RD Station plans are supported? =

Any RD Station Marketing plan with API access. You only need to register your own app in the App Store and give it the Marketing permissions.

= Can it send submissions to RD Station CRM instead? =

No. This plugin delivers conversion events to RD Station Marketing. Sending to RD Station CRM is part of the separate Pro plugin.

= Which fields do I have to map? =

The same one RD Station itself marks as required on its own forms: an **email**. Any custom field your account marks mandatory is added to that list automatically. Everything else is optional.

= What happens if RD Station is temporarily unavailable? =

The submission is not delivered, and the failure is recorded in the Logs tab with the full response.

= Where is the source code of the admin screens? =

In `resources/js/` inside the plugin, and publicly at [github.com/wpintegrations/wpintegrations-connector-for-cf7-rd-station](https://github.com/wpintegrations/wpintegrations-connector-for-cf7-rd-station). The compiled files in `build/` are generated from it with Vite: run `pnpm install` and then `pnpm run build` in the plugin folder. See "Source code and third-party libraries" above for the bundled libraries.

= Where do I see errors? =

The **Logs** tab shows every delivery with the full request and response.

== Changelog ==

= 1.0.0 =
* Initial release: Contact Form 7 → RD Station Marketing conversion events, field mapping with your forms' fields and your account's custom fields, LGPD consent mapping, RD Station attribution cookies, a delivery log, and encrypted credential storage.
