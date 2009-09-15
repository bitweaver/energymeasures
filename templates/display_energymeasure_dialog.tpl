<h1>It's a Match</h1>
<div class="dialog">
	<h1>
		<img class="icon" src="{$gContent->mInfo.thumbnail_urls.icon}" />&nbsp;
		{$gContent->mInfo.title|escape}
	</h1>
	<h2>{tr}You'll {if $gContent->mInfo.type eq 'Conservation'}conserve{else}generate{/if} {$gContent->mInfo.mwh|number_format} Megawatts per year{/tr}</h2>
	{$gContent->mInfo.parsed_data}
	<h3>{tr}So is {$gContent->mInfo.title|escape} right for New York?{/tr}</h3>
</div>
<div class="actions">
	<a class="btn select">Accept</a>
	<a class="btn reject">Reject</a>
</div>
