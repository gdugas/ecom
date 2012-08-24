{meta_html jquery}

<table class="ecom-cart">
	<thead>
		<tr>
			<th>Nom du produit</th>
			<th>Prix</th>
			<th>Quantité</th>
			<th>Total</th>
			<th>Editer</th>
		</tr>
	</thead>
	<tbody>
		{foreach $cart as $item}
			<tr class="{cycle array('odd','even')}" oid="{$item->id}">
				<td class="col-name">{$item->name}</td>
				<td class="col-price">{$item->price}</td>
				<td class="col-qtt"><input type="text" name="qtt" value="{$item->quantity}"></td>
				<td class="col-total">{$item->price * $item->quantity}</td>
				<td class="col-edit">
					<a class="cart-button-edit" href="{jurl 'ecom~cart:update', array('id' => $item->id, 'redirect' => $currenturl)}"><span class="cart-label-edit cart-label">edit</span></a>
					<a class="cart-button-delete" href="{jurl 'ecom~cart:delete', array('id' => $item->id, 'redirect' => $currenturl)}"><span class="cart-label-delete cart-label">delete</span></a>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>

{literal}
<script type="text/javascript">
	$(document).ready(function () {

		$('.ecom-cart input[name=qtt]').each(function () {
			$(this).data('ovalue', $(this).val());
		});
		$('.ecom-cart').delegate('input[name=qtt]', 'change', function () {
			if ($(this).val() != $(this).data('ovalue')) {
				$(this).parent().parent().addClass('updated');
			} else {
				$(this).parent().parent().removeClass('updated');
			}
		});

		$('.ecom-cart').delegate('a.cart-button-edit', 'click', function () {
			var $jq = $(this).parent().parent(),
				oid = $jq.attr('oid'),
				url = $(this).attr('href');
			
			$jq.addClass('updated').find('input[name=qtt]').prop('disabled', true);
			$(this).attr('href', url + '&qtt=' + $jq.find('input[name=qtt]').val());
		});
		
		$('.ecom-cart').delegate('a.cart-button-delete', 'click', function () {
			return confirm('Sure ?');
		});

	});
</script>
{/literal}