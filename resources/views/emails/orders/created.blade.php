<h1>Nouvelle commande {{ $order->order_number }}</h1>

<p><strong>Œuvre :</strong> {{ $order->artwork->title }}</p>
<p><strong>Montant :</strong> {{ $order->amount }} €</p>
<p><strong>Client :</strong> {{ $order->customer_name }} ({{ $order->customer_email }})</p>
<p><strong>Adresse de livraison :</strong></p>
<address>
    {{ $order->shipping_address['address_line_1'] }}<br>
    {{ $order->shipping_address['postal_code'] }} {{ $order->shipping_address['admin_area_2'] }}<br>
    {{ $order->shipping_address['country_code'] }}
</address>