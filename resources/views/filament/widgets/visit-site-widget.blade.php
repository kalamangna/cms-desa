<x-filament-widgets::widget>
    <x-filament::section>
        <x-filament::button
            href="/"
            tag="a"
            target="_blank"
            icon="heroicon-m-arrow-top-right-on-square"
            color="primary"
            size="lg"
            class="w-full shadow-sm"
        >
            Kunjungi Website Desa {{ $site_settings['village_name'] ?? '' }}
        </x-filament::button>
    </x-filament::section>
</x-filament-widgets::widget>
