{strip}
	<a class="dropdown-toggle" data-toggle="dropdown" href="#"> {tr}{$packageMenuTitle}{/tr} <b class="caret"></b></a>
<ul class="{$packageMenuClass}">
		{if $gBitUser->hasPermission( 'p_energymeasures_view')}
			<li><a class="item" href="{$smarty.const.ENERGYMEASURES_PKG_URL}list.php">{tr}List{/tr}</a></li>
		{/if}
		{if $gBitUser->hasPermission( 'p_energymeasures_create' )}
			<li><a class="item" href="{$smarty.const.ENERGYMEASURES_PKG_URL}edit.php">{tr}Create{/tr}</a></li>
		{/if}
		{if $gBitUser->hasPermission( 'p_energymeasures_admin' )}
			<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=energymeasures">{tr}Admin{/tr}</a></li>
		{/if}
		{if $gBitUser->hasPermission( 'p_energymeasures_view')}
			<li><a class="item" href="{$smarty.const.GAMES_PKG_URL}index.php?game=switch">{tr}Play Game{/tr}</a></li>
		{/if}
	</ul>
{/strip}
