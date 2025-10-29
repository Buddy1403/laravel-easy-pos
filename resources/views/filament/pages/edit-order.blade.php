<x-filament-panels::page>
    <div
        class="flex flex-col md:flex-row gap-2"
        x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('filament-print'))]"
        x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('filament-print-js'))]"


    >
        <div class="w-full md:w-2/3 md:order-1">
            {{-- Removed barcode-scan --}}
            <livewire:order.cart :order-id="$this->record->id" />
        </div>
    </div>

    <livewire:error />
</x-filament-panels::page>

