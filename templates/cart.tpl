
<table class="ecom-cart">
	<thead>
		<tr>
			<th>Article</th>
			<th>Nom du produit</th>
			<th>Prix</th>
			<th>Quantité</th>
			<th>Total</th>
		</tr>
	</thead>
	<tbody>
		{foreach $cart as $item}
			<tr>
				<td></td>
				<td>{$item->name}</td>
				<td>{$item->price}</td>
				<td>{$item->quantity}</td>
				<td>{$item->price * $item->quantity}</td>
			</tr>
		{/foreach}
	</tbody>
</table>