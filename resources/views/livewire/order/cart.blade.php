<div class="">
    {{-- Error Message --}}
    @if (session()->has('error'))
        <p class="text-red-500 mb-2">{{ session('error') }}</p>
    @endif

    {{-- CART TABLE --}}
    <div class="overflow-x-auto md:overflow-x-none">
        <table class="min-w-[600px] min-w-full border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-2 py-1 border border-gray-400 text-left w-3/5 dark:text-gray-800">Item</th>
                    <th class="px-2 py-1 border border-gray-400 text-center w-1/6 dark:text-gray-800">Rate</th>
                    <th class="px-2 py-1 border border-gray-400 text-center w-1/6 dark:text-gray-800">Tax(%)</th>
                    <th class="px-2 py-1 border border-gray-400 text-center w-1/6 dark:text-gray-800">Quantity</th>
                    <th class="px-2 py-1 border border-gray-400 text-center w-1/6 dark:text-gray-800">Total</th>
                </tr>
            </thead>

            <tbody>
                @if (!is_countable($cartItems) || count($cartItems) < 1)
                    <tr class="min-h-32"><td class="p-4">Add Items.</td></tr>
                @else
                    @php 
                        $total_price = 0;
                        $total_tax = [];
                        $grand_total = 0;
                    @endphp
                
                    @foreach ($cartItems as $item) 
                        @php 
                            $tax = $item->tax;
                            $item_total = $item->price * $item->quantity;
                            $gst_amount = ($item_total * $tax) / 100;
                            $item_total_with_gst = $item_total + $gst_amount;
                            $total_price += $item_total;
                            $total_tax[$tax] = ($total_tax[$tax] ?? 0) + $gst_amount;
                            $grand_total += $item_total_with_gst;
                            $item->item_total_with_gst = $item_total_with_gst;
                        @endphp
                        <livewire:order.cart-item 
                            :cartItem="$item" 
                            :currency_symbol="$currency_symbol" 
                            :order-id="$orderId" 
                            :key="$item->id" 
                        />
                    @endforeach

                    {{-- Subtotal --}}
                    <tr class="border-gray-400 border">
                        <td colspan="3" class="px-4 py-2 border-r text-right font-semibold">Subtotal</td>
                        <td colspan="2" class="px-4 py-2 text-center font-semibold">
                            {{ $currency_symbol }}{{ number_format($total_price, 2) }}
                        </td>
                    </tr>

                    {{-- Tax --}}
                    @foreach ($total_tax as $rate => $amount)
                        <tr class="border-gray-400 border">
                            <td colspan="3" class="px-4 py-2 border-r text-right font-semibold">VAT/GST @ {{ $rate }}%</td>
                            <td colspan="2" class="px-4 py-2 text-center font-semibold">
                                {{ $currency_symbol }}{{ number_format($amount, 2) }}
                            </td>
                        </tr>
                    @endforeach

                    {{-- Money Received Input --}}
                    <tr class="border-gray-400 border bg-gray-50">
                        <td colspan="3" class="px-4 py-2 border-r text-right font-semibold align-middle">Money Received</td>
                        <td colspan="2" class="px-4 py-2 text-center">
                            <input 
                                wire:model="moneyReceived"
                                type="number"
                                step="0.01"
                                placeholder="Enter amount"
                                class="w-3/4 px-2 py-1 border border-gray-300 rounded-md text-right focus:ring focus:ring-blue-200"
                            />
                        </td>
                    </tr>

                    {{-- Grand Total --}}
                    <tr class="bg-gray-100 border-gray-400 border">
                        <td colspan="3" class="px-4 py-2 border-r text-right font-bold">Grand Total</td>
                        <td colspan="2" class="px-4 py-2 text-center font-bold">
                            {{ $currency_symbol }}{{ number_format($grand_total, 2) }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    {{-- Checkout Button --}}
    <button 
        wire:click="confirmCheckout"
        class="bg-green-500 rounded text-white px-4 py-2 mt-3 hover:bg-green-600 transition"
    >
        Checkout
    </button>

    {{-- CONFIRM MODAL --}}
    @if($showConfirmModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-96">
                <h2 class="text-lg font-bold mb-4 text-gray-800">Payment Summary</h2>

                <p class="mb-2 text-gray-700">
                    <strong>Grand Total:</strong> {{ $currency_symbol }}{{ number_format($this->calculateGrandTotal(), 2) }}
                </p>
                <p class="mb-2 text-gray-700">
                    <strong>Money Received:</strong> {{ $currency_symbol }}{{ number_format($moneyReceived, 2) }}
                </p>
                <p class="mb-4 text-gray-700">
                    <strong>Change:</strong> {{ $currency_symbol }}{{ number_format($change, 2) }}
                </p>

                <div class="flex justify-end space-x-2">
                    <button wire:click="$set('showConfirmModal', false)" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                        Cancel
                    </button>
                    <button wire:click="proceedCheckout" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('printOrder', (url) => {
        printJS({
            printable: url,
            type: 'pdf',
            onPrintDialogClose: () => {
                // Redirect to orders page after print dialog closes
                window.location.href = '/admin/pos'; // adjust to your actual route
            }
        });
    });
});
</script>


