{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='nav' serviceHash=$gContent->mInfo}
<div class="display energymeasures">
	<div class="floaticon">
		{if $print_page ne 'y'}
			{if $gContent->hasUpdatePermission()}
				<a title="{tr}Edit this energymeasures{/tr}" href="{$smarty.const.ENERGYMEASURES_PKG_URL}edit.php?energymeasure_id={$gContent->mInfo.energymeasure_id}">{biticon ipackage="icons" iname="accessories-text-editor" iexplain="Edit EnergyMeasures"}</a>
			{/if}
			{if $gContent->hasExpungePermission()}
				<a title="{tr}Remove this energymeasures{/tr}" href="{$smarty.const.ENERGYMEASURES_PKG_URL}remove_energymeasures.php?energymeasure_id={$gContent->mInfo.energymeasure_id}">{biticon ipackage="icons" iname="edit-delete" iexplain="Remove EnergyMeasures"}</a>
			{/if}
		{/if}<!-- end print_page -->
	</div><!-- end .floaticon -->

	<div class="header">
		<h1>{$gContent->mInfo.title|escape}</h1>
		<div class="date">
			{tr}Created by{/tr}: {displayname user=$gContent->mInfo.creator_user user_id=$gContent->mInfo.creator_user_id real_name=$gContent->mInfo.creator_real_name}, {$gContent->mInfo.created|bit_long_datetime}<br/>
			{tr}Last modification by{/tr}: {displayname user=$gContent->mInfo.modifier_user user_id=$gContent->mInfo.modifier_user_id real_name=$gContent->mInfo.modifier_real_name}, {$gContent->mInfo.last_modified|bit_long_datetime}
		</div>
		<h3>Type: {tr}{$gContent->mInfo.type}{/tr}</h3>
		<h3>MwH: {tr}{$gContent->mInfo.mwh|number_format}{/tr}</h3>
		<img class="icon" src="{$gContent->mInfo.thumbnail_urls.small}" />
		<img class="icon" src="{$gContent->mInfo.thumbnail_urls.avatar}" />
		<img class="icon" src="{$gContent->mInfo.thumbnail_urls.icon}" />
	</div><!-- end .header -->

	<div class="body">
		<div class="content">
			{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='body' serviceHash=$gContent->mInfo}
			{$gContent->mInfo.parsed_data}
		</div><!-- end .content -->
	</div><!-- end .body -->
</div><!-- end .energymeasures -->
{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='view' serviceHash=$gContent->mInfo}
