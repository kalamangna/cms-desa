SPEED Report
============

Key metrics
-----------
- FCP: 0.8 s
- LCP: 1.6 s
- TBT: 10 ms
- CLS: 0.021
- SI: 1.7 s

Notes
-----
- Performance values are estimated and may vary.
- Captured at Jul 29, 2026, 7:31 PM GMT+8.
- Emulated Desktop with Lighthouse 13.4.1.
- Single page session, initial page load, custom throttling.
- HeadlessChromium 149.0.7827.155.

Opportunities
-------------

1. Improve image delivery (est. savings: 812 KiB)
   - Use responsive images and modern formats (WebP / AVIF) where possible.
   - Avoid serving images larger than their displayed dimensions.

   Images to optimize:
   - `/img/meta.png` (tompobulu.desa.id): 924.2 KiB, savings 791.5 KiB. Displayed at 340x192.
   - `/storage/posts/01KYNYJYXY117AZJ1WGBH6M3ED.jpg`: 234.1 KiB, savings 155.9 KiB. Displayed at 800x600.
   - `/storage/posts/01KX2WK7J0FGHP9EKKT9V3KQHV.jpg`: 126.8 KiB, savings 124.8 KiB. Displayed at 128x96.
   - `/storage/officials/01KY971RB2Z3MG8XE0R3F9E55J.jpg`: 136.7 KiB, savings 122.9 KiB. Displayed at 381x476.
   - `/img/sinjai.png`: 109.3 KiB, savings 108.9 KiB. Displayed at 44x44.
   - `/storage/settings/01KY9C4N9FD84PA3S7AS6S0BRV.jpeg`: 112.7 KiB, savings 82.9 KiB. Displayed at 506x658.
   - YouTube thumbnail `img.youtube.com/vi/0ilbn0cOVe0/hqdefault.jpg`: 35.0 KiB, savings 20.9 KiB. Displayed at 340x255.

2. Optimize font loading (est. savings: 290 ms)
   - Add `font-display: swap` or `optional` for webfont files.
   - Affected fonts:
     - `fa-solid-900.woff2`
     - `fa-brands-400.woff2`
     - `fa-regular-400.woff2`

3. Reduce render-blocking requests (est. savings: 40 ms)
   - Defer or inline critical CSS where appropriate.
   - Blocking asset:
     - `/assets/app-CDmH8tG7.css` (14.4 KiB, 160 ms)

4. Address forced reflow
   - Avoid reading layout properties after mutating the DOM.
   - Top source: `https://tompobulu.desa.id:155:56`
   - Additional reflow sources from `apexcharts`.

5. Improve LCP discovery
   - LCP breakdown:
     - Time to first byte: 20 ms
     - Resource load delay: 360 ms
     - Resource load duration: 500 ms
     - Element render delay: 1,700 ms
   - Ensure the main LCP image is discoverable in the initial HTML.
   - Do not lazy-load the LCP image and use `fetchpriority="high"`.

6. Simplify network dependency tree
   - Maximum critical path latency: 414 ms.
   - Critical chain examples:
     - Initial navigation: `https://tompobulu.desa.id` – 405 ms, 16.68 KiB.
     - `/npm/apexcharts` (cdn.jsdelivr.net) – 414 ms, 225.47 KiB.

7. Preconnect hints
   - Already present:
     - `https://fonts.googleapis.com/`
     - `https://fonts.gstatic.com/`

Screenshots and audits are available in the original Lighthouse report.
