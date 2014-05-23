{* $Header$ *}
{strip}
<div class="floaticon">{bithelp}</div>

<div class="admin energymeasures">
	{if $smarty.request.preview}
		<h2>Preview {$gContent->mInfo.title|escape}</h2>
		<div class="preview">
			{include file="bitpackage:energymeasures/display_energymeasures.tpl" page=$gContent->mInfo.energymeasure_id}
		</div>
	{/if}

	<div class="header">
		<h1>
			{if $gContent->mInfo.energymeasure_id}
				{tr}Edit {$gContent->mInfo.title|escape}{/tr}
			{else}
				{tr}Create New EnergyMeasure{/tr}
			{/if}
		</h1>
	</div>

	<div class="body">
		{form enctype="multipart/form-data" id="editenergymeasuresform"}
			{jstabs}
				{jstab title="Edit"}
					{legend legend="EnergyMeasure"}
						<input type="hidden" name="energymeasure_id" value="{$gContent->mInfo.energymeasure_id}" />
						{formfeedback warning=$errors.store}

						<div class="control-group">
							{formfeedback warning=$errors.title}
							{formlabel label="Title" for="title"}
							{forminput}
								<input type="text" size="50" name="title" id="title" value="{$gContent->mInfo.title|escape}" />
							{/forminput}
						</div>

						<div class="control-group">
							{formfeedback warning=$errors.type}
							{formlabel label="Type" for="type"}
							{forminput}
								<select name="type" id="type">
									<option value="Conservation" {if $gContent->getField('type') eq 'Conservation'}selected='selected'{/if}>Conservation</option> 
									<option value="Production" {if $gContent->getField('type') eq 'Production'}selected='selected'{/if}>Production</option>
								</select>
							{/forminput}
						</div>

						<div class="control-group">
							{formfeedback warning=$errors.mwh}
							{formlabel label="MwH Value" for="mwh"}
							{forminput}
								<input type="text" size="50" name="mwh" id="mwh" value="{$gContent->mInfo.mwh}" />
							{/forminput}
						</div>

						{textarea name="edit" edit=$gContent->mInfo.data}

						{include file="bitpackage:liberty/edit_services_inc.tpl" serviceFile="content_edit_mini_tpl"}

						{if $gBitUser->hasPermission('p_liberty_attach_attachments') }
							{legend legend="Attachments"}
								{include file="bitpackage:liberty/edit_storage.tpl"}
							{/legend}
						{/if}

						<div class="control-group submit">
							<input type="submit" class="btn btn-default" name="preview" value="{tr}Preview{/tr}" />
							<input type="submit" class="btn btn-default" name="save_energymeasures" value="{tr}Save{/tr}" />
						</div>
					{/legend}
				{/jstab}

				{* any service edit template tabs *}
				{include file="bitpackage:liberty/edit_services_inc.tpl" serviceFile="content_edit_tab_tpl"}
			{/jstabs}
		{/form}
	</div><!-- end .body -->
</div><!-- end .energymeasures -->

{/strip}
