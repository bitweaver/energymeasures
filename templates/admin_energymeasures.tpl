{strip}
	{jstabs}
		{jstab title="Game Settings"}
			{form}
				{legend legend="Display Values"}
					{include file="bitpackage:games/admin_game_settings_inc.tpl" game=$game}
					<div class="control-group">
						{formlabel label="Switch Game NYC 2030 Goal Value" for="energymeasures_switch_goal"}
						{forminput}
							<input type="text" size="50" name="energymeasures_switch_goal" value="{$gBitSystem->getConfig('energymeasures_switch_goal', 20000000)}" />
						{/forminput}
					</div>
					<input type="hidden" name="page" value="{$page}" />
					<div class="control-group submit">
						<input type="submit" class="btn" name="game_settings" value="{tr}Save Settings{/tr}" />
					</div>
				{/legend}
			{/form}
		{/jstab}

		{jstab title="EnergyMeasures Settings"}
			{form}
				{legend legend="Javascript Cache"}
					<input type="hidden" name="page" value="{$page}" />
					<input type="submit" class="btn" name="energymeasures_refresh_js" value="{tr}Refresh Cached Javascript{/tr}" />
					{formhelp note="Published energy measures are cached to a javascript file for game play. You can refresh the cache by click the button above."}
				{/legend}
			{/form}
		{/jstab}

		{jstab title="List Settings"}
			{form}
				{legend legend="List Settings"}
					<input type="hidden" name="page" value="{$page}" />
					{foreach from=$formEnergyMeasuresLists key=item item=output}
						<div class="control-group">
							{formlabel label=$output.label for=$item}
							{forminput}
								{html_checkboxes name="$item" values="y" checked=$gBitSystem->getConfig($item) labels=false id=$item}
								{formhelp note=$output.note page=$output.page}
							{/forminput}
						</div>
					{/foreach}
				{/legend}
				<div class="control-group submit">
					<input type="submit" class="btn" name="energymeasures_settings" value="{tr}Change preferences{/tr}" />
				</div>
			{/form}
		{/jstab}

	{/jstabs}
{/strip}
