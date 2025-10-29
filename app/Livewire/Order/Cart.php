<?php

namespace App\Livewire\Order;

use App\Models\OrderItem;
use Livewire\Component;

class Cart extends Component
{
    public $cartItems = [];

    public $orderId;

    public $moneyReceived = 0;

    public $showConfirmModal = false;

    public $change = 0;

    private $currency_symbol;

    public function mount($orderId)
    {
        $this->orderId = $orderId;
        $this->cartItems = OrderItem::where('order_id', $orderId)->orderBy('id', 'DESC')->get();
        $this->currency_symbol = config('settings.currency_symbol');
    }

    public function render()
    {
        return view('livewire.order.cart', [
            'cartItems' => $this->cartItems,
            'currency_symbol' => $this->currency_symbol,
        ]);
    }

    public function calculateGrandTotal()
    {
        $total_price = 0;
        $total_tax = [];
        $grand_total = 0;

        foreach ($this->cartItems as $item) {
            $tax = $item->tax;
            $item_total = $item->price * $item->quantity;
            $gst_amount = ($item_total * $tax) / 100;
            $item_total_with_gst = $item_total + $gst_amount;
            $total_price += $item_total;
            $total_tax[$tax] = ($total_tax[$tax] ?? 0) + $gst_amount;
            $grand_total += $item_total_with_gst;
        }

        return $grand_total;
    }

    public function confirmCheckout()
    {
        $grand_total = $this->calculateGrandTotal();
        if ($this->moneyReceived <= 0) {
            session()->flash('error', 'Please enter the amount received.');

            return;
        }

        $this->change = $this->moneyReceived - $grand_total;
        if ($this->change < 0) {
            session()->flash('error', 'Insufficient payment amount.');

            return;
        }

        $this->showConfirmModal = true;
    }

    public function proceedCheckout()
    {
        $this->showConfirmModal = false;

        // Trigger print in browser via JS
        $this->dispatch('printOrder', url('print/'.$this->orderId));

    }
}
