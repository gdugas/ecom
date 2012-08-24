{formdatafull $form}
<a href="{jurl 'ecom~account:edit'}">{@jelix~crud.link.edit.record@}</a>

<hr />

<h2>Adresses de facturation</h2>
<table>
	<thead>
		<tr>
			<th>Label</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		{foreach $account_billings as $billing}
			<tr>
				<td>{$billing->label}</td>
				<td>
					<a href="{jurl 'ecom~account_address:view', array('id'=>$billing->id)}">view</a>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>

&bull; <a href="{jurl 'ecom~account_address:create'}">add billing adress</a>
