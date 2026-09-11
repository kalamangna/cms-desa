{{-- Honeypot & Time-gate Protection for Public Forms --}}
<div style="position: absolute; left: -9999px; top: -9999px; opacity: 0; pointer-events: none; width: 0; height: 0; overflow: hidden;" aria-hidden="true" tabindex="-1">
    <label for="_hp_website">Jangan isi kolom ini</label>
    <input type="text" name="_hp_website" id="_hp_website" tabindex="-1" autocomplete="off" value="">
</div>
<input type="hidden" name="_form_time" value="{{ encrypt(time()) }}">
