Accessibility
=============

These checks highlight opportunities to improve the accessibility of your web app. Automatic detection can only detect a subset of issues and does not guarantee the accessibility of your web app, so manual testing is also encouraged.

ARIA
----

Elements with `role="dialog"` or `role="alertdialog"` do not have accessible names.

ARIA dialog elements without accessible names may prevent screen reader users from discerning the purpose of these elements. Learn how to make ARIA dialog elements more accessible.

Failing Elements
---------------

- **INFOGRAFIS DESA TOMPO BULU Informasi Pengumpulan Data Tutup**

```html
<div x-data="{ isOpen: false, activeSlide: 0, popups: JSON.parse('[{\u0022image\u0022:…' ) }" x-show="isOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @keydown.escape.window="closePopup()" @click="closePopup()" class="fixed inset-0 z-[9999] bg-slate-900/80 backdrop-blur-md flex items-center …" role="dialog" aria-modal="true" style="">
```

These are opportunities to improve the usage of ARIA in your application which may enhance the experience for users of assistive technology, like a screen reader.

Contrast
--------

Background and foreground colors do not have a sufficient contrast ratio.

Low-contrast text is difficult or impossible for many users to read. Learn how to provide sufficient color contrast.

Failing Elements
---------------

- **INFOGRAFIS DESA TOMPO BULU**
- `<span class="text-[10px] font-black uppercase tracking-wider text-emerald-600">`
- `INFOGRAFIS DESA TOMPO BULU Informasi Pengumpulan Data Tutup`
- `<div class="p-5 text-center bg-white border-t border-slate-100 flex items-center justi…">`

These are opportunities to improve the legibility of your content.
