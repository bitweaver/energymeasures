{* $Header$ *}
{strip}
<div class="floaticon">{bithelp}</div>

<div class="listing energymeasures">
	<div class="header">
		<h1>{tr}EnergyMeasures{/tr}</h1>
	</div>

	<div class="body">
		{form id="checkform"}
			<input type="hidden" name="offset" value="{$control.offset|escape}" />
			<input type="hidden" name="sort_mode" value="{$control.sort_mode|escape}" />

			<table class="table data">
				<tr>
					{if $gBitSystem->isFeatureActive( 'energymeasures_list_energymeasure_id' ) eq 'y'}
						<th>{smartlink ititle="EnergyMeasures Id" isort=energymeasure_id offset=$control.offset iorder=desc idefault=1}</th>
					{/if}

					{if $gBitSystem->isFeatureActive( 'energymeasures_list_title' ) eq 'y'}
						<th>{smartlink ititle="Title" isort=title offset=$control.offset}</th>
					{/if}

					{if $gBitSystem->isFeatureActive( 'energymeasures_list_description' ) eq 'y'}
						<th>{smartlink ititle="Description" isort=description offset=$control.offset}</th>
					{/if}

					{if $gBitSystem->isFeatureActive( 'energymeasures_list_data' ) eq 'y'}
						<th>{smartlink ititle="Text" isort=data offset=$control.offset}</th>
					{/if}

					<th>{tr}Actions{/tr}</th>
				</tr>

				{foreach item=energymeasures from=$energymeasuresList}
					<tr class="{cycle values="even,odd"}">
						{if $gBitSystem->isFeatureActive( 'energymeasures_list_energymeasure_id' )}
							<td><a href="{$smarty.const.ENERGYMEASURES_PKG_URL}index.php?energymeasure_id={$energymeasures.energymeasure_id|escape:"url"}" title="{$energymeasures.energymeasure_id}">{$energymeasures.energymeasure_id}</a></td>
						{/if}

						{if $gBitSystem->isFeatureActive( 'energymeasures_list_title' )}
							<td><a href="{$smarty.const.ENERGYMEASURES_PKG_URL}index.php?energymeasure_id={$energymeasures.energymeasure_id|escape:"url"}" title="{$energymeasures.energymeasure_id}">{$energymeasures.title|escape}</a></td>
						{/if}

						{if $gBitSystem->isFeatureActive( 'energymeasures_list_description' )}
							<td>{$energymeasures.description|escape}</td>
						{/if}

						{if $gBitSystem->isFeatureActive( 'energymeasures_list_data' )}
							<td>
								<img src="{$energymeasures.thumbnail_urls.medium}" class="floatleft">
								<strong>Type: {$energymeasures.type}</strong><br />
								<strong>MwH: {$energymeasures.mwh}</strong><br />
								{$energymeasures.data|escape}
							</td>
						{/if}

						<td class="actionicon">
						{if $gBitUser->hasPermission( 'p_energymeasures_update' )}
							{smartlink ititle="Edit" ifile="edit.php" booticon="icon-edit" energymeasure_id=$energymeasures.energymeasure_id}
						{/if}
						{if $gBitUser->hasPermission( 'p_energymeasures_expunge' )}
							<input type="checkbox" name="checked[]" title="{$energymeasures.title|escape}" value="{$energymeasures.energymeasure_id}" />
						{/if}
						</td>
					</tr>
				{foreachelse}
					<tr class="norecords"><td colspan="16">
						{tr}No records found{/tr}
					</td></tr>
				{/foreach}
			</table>

			{if $gBitUser->hasPermission( 'p_energymeasures_expunge' )}
				<div style="text-align:right;">
					<script type="text/javascript">/* <![CDATA[ check / uncheck all */
						document.write("<label for=\"switcher\">{tr}Select All{/tr}</label> ");
						document.write("<input name=\"switcher\" id=\"switcher\" type=\"checkbox\" onclick=\"switchCheckboxes(this.form.id,'checked[]','switcher')\" /><br />");
					/* ]]> */</script>

					<select name="submit_mult" onchange="this.form.submit();">
						<option value="" selected="selected">{tr}with checked{/tr}:</option>
						<option value="remove_energymeasures_data">{tr}remove{/tr}</option>
					</select>

					<noscript><div><input type="submit" class="btn btn-default" value="{tr}Submit{/tr}" /></div></noscript>
				</div>
			{/if}
		{/form}

		{pagination}
	</div><!-- end .body -->
</div><!-- end .admin -->
{/strip}
