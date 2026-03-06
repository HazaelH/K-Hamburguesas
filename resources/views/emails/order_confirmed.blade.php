<x-mail::message>
# {{ __('client/messages.order_email_greeting', ['name' => $order->cliente_nombre]) }}

{{ __('client/messages.order_email_received') }}

<x-mail::panel>
**{{ __('client/messages.order_email_code') }}:** {{ $order->codigo_entrega }}  
**{{ __('client/messages.order_email_address') }}:** {{ $order->direccion }}
</x-mail::panel>

### {{ __('client/messages.order_email_details') }}

<x-mail::table>
| {{ __('client/messages.order_email_item') }} | {{ __('client/messages.order_email_qty') }} | {{ __('client/messages.order_email_price') }} |
|:---|:---:|---:|
@foreach($order->items as $item)
| {{ $item->product->nombre_traducido ?? $item->product->nombre }} | {{ $item->cantidad }} | ${{ number_format($item->subtotal, 2) }} |
@endforeach
</x-mail::table>

**{{ __('client/checkout.subtotal') }}:** ${{ number_format($order->total - ($order->datos_entrega['costo_envio_cobrado'] ?? 0), 2) }}  
**{{ __('client/checkout.delivery_fee') }}:** ${{ number_format($order->datos_entrega['costo_envio_cobrado'] ?? 0, 2) }}  
**{{ __('client/checkout.total_to_pay') }}:** ${{ number_format($order->total, 2) }}

<x-mail::button :url="route('ticket', $order->id)" color="success">
{{ __('client/messages.order_email_track') }}
</x-mail::button>

{{ __('profile/messages.email_thanks') }}<br>
{{ __('profile/messages.email_team', ['app' => config('app.name')]) }}
</x-mail::message>