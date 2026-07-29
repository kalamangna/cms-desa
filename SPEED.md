Insights
========

## Improve image delivery — Est savings of 812 KiB

Reducing the download time of images can improve the perceived load time of the page and LCP. Learn more about optimizing image size

LCP | FCP | Unscored
--------------------

URL
Resource Size
Est Savings

tompobulu.desa.id 1st party
924.2 KiB 791.5 KiB

### Kegiatan Gotong Royong Warga

```html
<img src="https://tompobulu.desa.id/img/meta.png" class="w-full h-auto object-cover group-hover:scale-110 transition-transform dura…" alt="Kegiatan Gotong Royong Warga" loading="lazy" onerror="this.src='https://tompobulu.desa.id/img/meta.png'">
```

- Path: `/img/meta.png(tompobulu.desa.id)`
- Resource size: 204.8 KiB
- Est savings: 196.0 KiB
- Note: This image file is larger than it needs to be (1640x924) for its displayed dimensions (340x192). Use responsive images to reduce the image download size.
- Additional reported figure: 196.0 KiB

### Kerja Bakti

```html
<img src="https://tompobulu.desa.id/storage/posts/01KYNYJYXY117AZJ1WGBH6M3ED.jpg" class="w-full h-full object-cover group-hover:scale-105 transition-transform dura…" alt="Kerja Bakti" loading="lazy" onerror="this.onerror=null;this.src='https://tompobulu.desa.id/img/meta.png'">
```

- Path: `…posts/01KYNYJYX….jpg(tompobulu.desa.id)`
- Resource size: 234.1 KiB
- Est savings: 155.9 KiB
- Advice: Using a modern image format (WebP, AVIF) or increasing the image compression could improve this image's download size.
- Additional figure: 102.2 KiB
- Note: This image file is larger than it needs to be (1200x675) for its displayed dimensions (800x600). Use responsive images to reduce the image download size.
- Final additional figure: 95.4 KiB

### PENCANAGAN REKOR MURI

```html
<img src="https://tompobulu.desa.id/storage/posts/01KX2WK7J0FGHP9EKKT9V3KQHV.jpg" class="w-full h-full object-cover group-hover:scale-110 transition-transform dura…" alt="PENCANAGAN REKOR MURI " loading="lazy" onerror="this.onerror=null;this.src='https://tompobulu.desa.id/img/meta.png'">
```

- Path: `…posts/01KX2WK7J….jpg(tompobulu.desa.id)`
- Resource size: 126.8 KiB
- Est savings: 124.8 KiB
- Note: This image file is larger than it needs to be (900x900) for its displayed dimensions (128x96). Use responsive images to reduce the image download size.
- Additional reported figure: 124.8 KiB

### Foto ASRI S,.S.P

```html
<img src="https://tompobulu.desa.id/storage/officials/01KY971RB2Z3MG8XE0R3F9E55J.jpg" class="w-full h-full object-cover object-top" alt="Foto ASRI S,.S.P" width="384" height="480" loading="eager" fetchpriority="high" onerror="this.onerror=null;this.src='https://tompobulu.desa.id/img/meta.png'">
```

- Path: `…officials/01KY971RB….jpg(tompobulu.desa.id)`
- Resource size: 136.7 KiB
- Est savings: 122.9 KiB
- Note: This image file is larger than it needs to be (1197x1501) for its displayed dimensions (381x476). Use responsive images to reduce the image download size.
- Additional reported figure: 122.9 KiB

### Logo

```html
<img class="h-10 w-auto transition-all duration-300 h-11" :class="scrolled ? 'h-9' : 'h-11'" src="https://tompobulu.desa.id/img/sinjai.png" alt="Logo" width="44" height="44">
```

- Path: `/img/sinjai.png(tompobulu.desa.id)`
- Resource size: 109.3 KiB
- Est savings: 108.9 KiB
- Advice: Using a modern image format (WebP, AVIF) or increasing the image compression could improve this image's download size.
- Additional figure: 66.6 KiB
- Note: This image file is larger than it needs to be (512x512) for its displayed dimensions (44x44). Use responsive images to reduce the image download size.
- Additional reported figure: 108.4 KiB

### Informasi Pengumpulan Data

```html
<img src="https://tompobulu.desa.id/storage/settings/01KY9C4N9FD84PA3S7AS6S0BRV.jpeg" class="w-full h-auto object-contain max-h-[65vh] md:max-h-[70vh]" alt="Informasi Pengumpulan Data">
```

- Path: `…settings/01KY9C4N9….jpeg(tompobulu.desa.id)`
- Resource size: 112.7 KiB
- Est savings: 82.9 KiB
- Note: This image file is larger than it needs to be (984x1280) for its displayed dimensions (506x658). Use responsive images to reduce the image download size.
- Additional reported figure: 82.9 KiB

### YouTube video

- Resource size: 35.0 KiB
- Est savings: 20.9 KiB

#### Profil Desa Tompo Bulu

```html
<img src="https://img.youtube.com/vi/0ilbn0cOVe0/hqdefault.jpg" class="w-full h-auto object-cover group-hover:scale-110 transition-transform dura…" alt="Profil Desa Tompo Bulu" loading="lazy" onerror="this.src='https://tompobulu.desa.id/img/meta.png'">
```

- Path: `…0ilbn0cOVe0/hqdefault.jpg(img.youtube.com)`
- Resource size: 35.0 KiB
- Est savings: 20.9 KiB
- Advice: Using a modern image format (WebP, AVIF) or increasing the image compression could improve this image's download size.
- Additional reported figure: 6.9 KiB
- Note: This image file is larger than it needs to be (480x360) for its displayed dimensions (340x255). Use responsive images to reduce the image download size.
- Additional reported figure: 17.4 KiB

## Font display — Est savings of 190 ms

Consider setting font-display to swap or optional to ensure text is consistently visible. swap can be further optimized to mitigate layout shifts with font metric overrides.

FCP | Unscored
------------

URL
Est Savings

Cloudflare CDN cdn
- `…webfonts/fa-brands-400.woff2(cdnjs.cloudflare.com)` — 190 ms
- `…webfonts/fa-regular-400.woff2(cdnjs.cloudflare.com)` — 170 ms
- `…webfonts/fa-solid-900.woff2(cdnjs.cloudflare.com)` — 50 ms

## Use efficient cache lifetimes — Est savings of 53 KiB

A long cache lifetime can speed up repeat visits to your page. Learn more about caching.

LCP | FCP | Unscored
--------------------

Request | Cache TTL | Transfer Size
----------------------------------

- YouTube video — 36 KiB — `…0ilbn0cOVe0/hqdefault.jpg(img.youtube.com)` — 2h — 36 KiB
- JSDelivr CDN cdn — 243 KiB — `/npm/apexcharts(cdn.jsdelivr.net)` — 7d — 225 KiB
- JSDelivr CDN cd — 18 KiB — `…dist/cdn.min.js(cdn.jsdelivr.net)` — 7d — 18 KiB
- userway.org — 7 KiB — `/widget.js(cdn.userway.org)` — 1h — 2 KiB
- userway.org — 5 KiB — `…2026-07-07-10-43-48/widget_base.css?v=178…(cdn.userway.org)` — 10d — 5 KiB

## Legacy JavaScript — Est savings of 10 KiB

Polyfills and transforms enable older browsers to use new JavaScript features. However, many aren't necessary for modern browsers. Consider modifying your JavaScript build process to not transpile Baseline features, unless you know you must support older browsers. Learn why most sites can deploy ES6+ code without transpiling

LCP | FCP | Unscored
--------------------

URL
Wasted bytes

- userway.org — 10.5 KiB
  - `…2026-07-07-10-43-48/widget_app_base_178….js(cdn.userway.org)` — 10.5 KiB
  - `…2026-07-07-10-43-48/widget_app_base_178….js:1:2805(cdn.userway.org)`
  - `Array.prototype.find`
  - `…2026-07-07-10-43-48/widget_app_base_178….js:1:2694(cdn.userway.org)`
  - `Math.imul`

## Forced reflow

A forced reflow occurs when JavaScript queries geometric properties (such as offsetWidth) after styles have been invalidated by a change to the DOM state. This can result in poor performance. Learn more about forced reflows and possible mitigations.

Unscored

Top function call | Total reflow time
-----------------------------------

- `https://tompobulu.desa.id:164:56` — 8 ms

Source | Total reflow time
---------------------------

- `[unattributed]` — 44 ms
- `/npm/apexcharts:5:386758(cdn.jsdelivr.net)` — 1 ms
- `/npm/apexcharts:5:8689(cdn.jsdelivr.net)` — 2 ms
- `/npm/apexcharts:5:220800(cdn.jsdelivr.net)` — 0 ms
- `/npm/apexcharts:5:479249(cdn.jsdelivr.net)` — 2 ms
- `/npm/apexcharts:5:9073(cdn.jsdelivr.net)` — 0 ms
- `/npm/apexcharts:5:335017(cdn.jsdelivr.net)` — 4 ms

## LCP request discovery

Optimize LCP by making the LCP image discoverable from the HTML immediately, and avoiding lazy-loading

LCP | Unscored
------------

- LCP resources should not use loading=lazy
- fetchpriority=high should be applied
- Request is discoverable in initial document

### Informasi Pengumpulan Data

```html
<img src="https://tompobulu.desa.id/storage/settings/01KY9C4N9FD84PA3S7AS6S0BRV.jpeg" class="w-full h-auto object-contain max-h-[65vh] md:max-h-[70vh]" alt="Informasi Pengumpulan Data">
```
