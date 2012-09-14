<h1>{@jelix~crud.title.view@}</h1>

<h2>Order</h2>
<table class="jforms-table">
	<tbody>
		<tr>
			<th scope="row">Reference</th>
			<td>{$order->reference}</td>
		</tr>
		<tr>
			<th scope="row">Date order</th>
			<td>{$order->date_order}</td>
		</tr>
		<tr>
			<th scope="row">Payment</th>
			<td>{$order->payment}</td>
		</tr>
		<tr>
			<th scope="row">Delivery</th>
			<td>{$order->delivery}</td>
		</tr>
	</tbody>
</table>


<h2>Billing address</h2>
<p>
	{if $order->fact_company}{$order->fact_company}<br />{/if}
	{$order->fact_civility} {$order->fact_firstname} {$order->fact_lastname}<br />
	{$order->fact_address|nl2br}<br />
	{$order->fact_postal_code} {$order->fact_city}<br />
	{$order->fact_country}
</p>



<h2>Delivery address</h2>
<p>
	{if $order->delivery_company}{$order->delivery_company}<br />{/if}
	{$order->delivery_civility} {$order->delivery_firstname} {$order->delivery_lastname}<br />
	{$order->delivery_address|nl2br}<br />
	{$order->delivery_postal_code} {$order->delivery_city}<br />
	{$order->delivery_country}
</p>


<h2>Products</h2>
<table>
	<thead>
		<tr>
			<th>Designation</th>
			<th>Quantity</th>
			<th>Dutyfree price</th>
			<th>Unit price</th>
		</tr>
	</thead>
	<tbody>
		{foreach $order->items() as $item}
			<tr>
				<td class="col-name">{$item->name}</td>
				<td class="col-qtt">{$item->quantity}</td>
				<td class="col-dutyfree">{$item->price_dutyfree} €</td>
				<td class="col-price">{$item->price} €</td>
			</tr>
		{/foreach}
	</tbody>
</table>


<h2>Total</h2>
<table>
	<thead>
		<tr>
			<th>Total dutyfree</th>
			<th>Tax</h>
			<th>TOTAL</th>
		</tr>
	</thead>
	<tbody>
		<td>{$order->total('dutyfree')} €</td>
		<td>{$order->total('tax')} €</td>
		<td>{$order->total('price')} €</td>
	</tbody>
</table>

