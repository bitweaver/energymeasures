EnergyMeasures = [
{foreach item=item from=$EnergyMeasures name=energymeasures}
{ldelim} 
	content_id: {$item.content_id},
	energymeasure_id: {$item.energymeasure_id},
	title: "{$item.title|escape}",	
	thumbnail_urls: {ldelim}small:"{$item.thumbnail_urls.small}",
							medium:"{$item.thumbnail_urls.medium}"{rdelim},
	mwh: {$item.mwh}
{rdelim}{if !$smarty.foreach.energymeasures.last},{/if}
{/foreach}
];
