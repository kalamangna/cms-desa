# Accessibility

These checks highlight opportunities to improve the accessibility of your web app. Automatic detection can only detect a subset of issues and does not guarantee the accessibility of your web app, so manual testing is also encouraged.

## Contrast

Background and foreground colors do not have a sufficient contrast ratio.

Low-contrast text is difficult or impossible for many users to read. Learn how to provide sufficient color contrast.

## Failing Elements

### Dashboard Statistik

- `<a href="/statistik" class="group inline-flex items-center justify-center gap-3 bg-emerald-600 hover:b…">`
- `BERITA UTAMA`
- `<span class="bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest…">`
- Links rely on color to be distinguishable.
- Low-contrast text is difficult or impossible for many users to read. Link text that is discernible improves the experience for users with low vision. Learn how to make links distinguishable.

### KALAMANGNA

- `<a href="https://github.com/kalamangna" target="_blank" class="text-emerald-400 hover:text-emerald-300 transition">`
- `DIKEMBANGKAN OLEH KALAMANGNA • V1.26.25`
- `<p class="text-slate-400 text-[10px] font-bold uppercase tracking-[0.15em]">`
