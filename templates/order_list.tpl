<h1>{@jelix~crud.title.list@}</h1>

<table class="records-list">
<thead>
<tr>
    <th>Reference</th>
    <th>Delivery</th>
    <th>Payment</th>
    <th>Status</th>
    <th>&nbsp;</th>
</tr>
</thead>
<tbody>
{foreach $list as $record}
<tr class="{cycle array('odd','even')}">
	<td>{$record->reference}</td>
	<td>{$record->delivery}</td>
	<td>{$record->payment}</td>
	<td>{$record->status}</td>
    <td>
        <a href="{jurl 'ecom~order:view',array('id'=>$record->id)}">{@jelix~crud.link.view.record@}</a>
    </td>
</tr>
{/foreach}
</tbody>
</table>
