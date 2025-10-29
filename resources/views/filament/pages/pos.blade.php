<x-filament-panels::page>

    <div class="flex flex-col md:flex-row gap-2">
        
        <div class="w-full md:w-1/3 md:order-2">
            <livewire:product-search />
        </div>
        <div class="w-full md:w-2/3 md:order-1">
            <div class="flex flex-col md:flex-row gap-4 pb-4">
                <div class="w-full md:w-1/2">
                    <livewire:barcode-scan />
                </div>
            </div>
            <livewire:cart />
        </div>
    </div>

    <livewire:error />

</x-filament-panels::page>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const barcodeInput = document.getElementById('barcode-input');
        const focusBarcode = () => barcodeInput?.focus();

        // Focus immediately
        focusBarcode();

        // Re-focus after Livewire updates or navigation
        document.addEventListener('livewire:update', focusBarcode);
        document.addEventListener('livewire:navigated', focusBarcode);

        // Re-focus when clicking anywhere outside inputs or modals
        document.addEventListener('click', (e) => {
            if (e.target.closest('input, textarea, [contenteditable], .filament-modal')) return;
            focusBarcode();
        });

        // Re-focus after pressing Enter (after addToCart runs)
        barcodeInput?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                setTimeout(focusBarcode, 150);
            }
        });
    });
</script>
@endpush
