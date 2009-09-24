<h1 class="highlight">It's a match!</h1>
<div class="dialog">
	<h1>
		<img class="icon" src="{$gContent->mInfo.thumbnail_urls.small}" />&nbsp;
		{$gContent->mInfo.title|escape}
	</h1>
	<h2>{tr}You'll {if $gContent->mInfo.type eq 'Conservation'}conserve{else}generate{/if} {$gContent->mInfo.mwh|number_format} megawatt hours per year.{/tr}</h2>
	<div class="body">{$gContent->mInfo.parsed_data}</div>
	<h3>{tr}So, is this right for New York?{/tr}</h3>
</div>
<div class="actions">
	<a class="btn select">Right</a>
	<a class="btn reject">Wrong</a>
</div>
