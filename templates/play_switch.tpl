{strip}
<div class="display energymeasures game switch">
	<div class="bgcontainer">
	{* the pre-load view *}
	<div class="preload" style="display:none">
		<div class="body">
			<div class="header">
				<img class="logo" src="{$smarty.const.THEMES_STYLE_URL}images/gglogo_lrg.png" alt="Gotham Gazette's"/><br />
				<img src="{$smarty.const.THEMES_STYLE_URL}images/switchlogo_lrg.png" alt="{$game.title}"/><br />
			</div><!-- end .header -->
			<div class="content">	
				<div class="lcol floatleft">
					<p>
						New York City used XXX megawatt hours of energy (MwH) during 2008. Based on current estimates of growth, we'll need XXX MwH by 2030. SWITCH lets you play with ways to fill that gap.
					</p>
					<a class="btn play">PLAY the GAME</a>
				</div><!--  end .lcol -->
				<div class="rcol floatleft">
					<div class="summary">
						{$game.summary}
					</div>
				</div><!--  end .rcol -->
			</div><!--  end .content -->
			<div class="clear"></div>
		</div><!--  end .body -->
	</div><!-- end .preload -->

	{* the play view *}
	<div class="play" {*style="display:none"*}>
		<div class="header">
			<div class="floatleft">
				<h2>Gotham Gazette’s</h2>
				{*
				<img class="logo" src="{$smarty.const.THEMES_STYLE_URL}images/gglogo_sm.png" alt="Gotham Gazette's"/><br />
				*}
				<img src="{$smarty.const.THEMES_STYLE_URL}images/switchlogo_sm.png" alt="{$game.title}"/><br />
			</div>
			<div class="summary floatleft">
				{$game.summary}
			</div>
			{*
			<div class="description">
				{$game.description}
			</div>
			*}
		</div><!-- end .header -->

		<div class="body clear">
			<div class="content">	
				{* suggestion box overlaps game *}
				<div id="thankyoudialog" style="display:none">{* thankyou from saving score lands here *}</div>
				<div id="suggestdialog" style="display:none">{* dialog is ajaxed into place, see suggestion:edit_suggestion.tpl *}</div>
				<div id="gamecontainer">

					<div class="instructions">
						{$game.instructions}
					</div>

					<div id="game">
						{* matchesdialog popup overlaps gameboard and scoreboard *}
						<div id="matchesdialog" style="display:none">
							{* dialog is ajaxed into place, see display_energymeasure_dialog.tpl *}
						</div>

						<div class="lcol floatleft">
							<div id="gameboard"></div>

							<div class="options">
								<p><strong>{tr}Suggest A Measure:{/tr}</strong> {tr}Do you have an electric idea that should be part of the game? <a class="suggest">Send us your ideas!</a>{/tr}</p>
							</div>
							<div class="postgame actions" style="display:none">
								<a class="btn savescore">Save Score</a>
								<a class="btn playagain">Play Again</a>
							</div>
						</div><!--  end .lcol -->

						<div class="rcol">
							<div id="savescoredialog" style="display:none">{* dialog is ajaxed into place, see edit_switch_score.tpl *}</div>

							<div class="description">{$game.description}</div>

							<div id="scoreboard" style="display:none">
								<ul class="tabbar">
									<li class="selectedtab"><a>Accepted</a></li>
									<li class="rejectedtab"><a>Rejected</a></li>
								</ul>
								<div class="selected">
									<table>
										<tbody>
										</tbody>
									</table>
								</div>
								<div class="rejected" style="display:none">
									<table>
										<tbody>
										</tbody>
									</table>
								</div>
								<div class="actions">
									<a class="endgame"><img class="floatleft" src="{$smarty.const.ENERGYMEASURES_PKG_URL}images/switch_icon.png"><span>{tr}FLIP The SWITCH{/tr}</span></a>
									<a class="playagain">{tr}Reshuffle{/tr}</a>
								</div>
								<div class="score" style="display:none">
									<div class="total">Your Total:<span class="value floatright">{* user's score *}</span></div>
									<div class="goal">NYC 2030 Goal:<span class="value floatright">{$gBitSystem->getConfig('energymeasures_switch_goal', 20000000)|number_format}</span></div>
								</div>
							</div>
						</div><!--  end .rcol -->
					</div><!-- end #game -->
				</div><!-- end #gamecontainer -->
			</div> <!-- end .content -->
		</div> <!-- end .body -->
	</div><!-- end .play -->
	{* footer always visible *}
	<div class="footer clear">
		<p>
			{tr}<strong>Learn More</strong> about New York City's electricity needs and policy options at <a href="http://www.gothamgazette.com">www.gothamgazette.com</a>{/tr}
		</p>
		<span class="copyright">Copyright &copy; 2009 Citizens Union Foundation</span>
	</div> <!-- end .footer -->
	</div> <!-- end .bgcontainer -->
</div> <!-- end .display -->

<script type="text/javascript">
	{include_js file=`$smarty.const.UTIL_PKG_PATH`javascript/bootstrap.js}

	var Switch;

	loadScript("{$gBitThemes->mStyles.joined_javascript}", function(){ldelim}
		/*initialization code*/
		SwitchOpts = {ldelim}
			cards: EnergyMeasures,
			cardCount: 18,
			cardValueKey: "mwh",
			matchesDialogId: "matchesdialog",
			scoreBoardId: "scoreboard",
			api:true,
			onEndGame:function(){ldelim}
				var goal = {$gBitSystem->getConfig('energymeasures_switch_goal', 20000000)};
				var score = this.getScore();
				var percent = Math.round( score/goal * 10 ) * 10;
				$('#gameboard').empty().css( {ldelim}backgroundImage: "url('{$smarty.const.BIT_ROOT_URL}energymeasures/images/"+percent+".jpg')" {rdelim} );
			{rdelim},
			onStartOver:function(){ldelim}
				$('#gameboard').css( {ldelim}backgroundImage: "url('{$smarty.const.BIT_ROOT_URL}energymeasures/images/gameboard_bg.jpg')" {rdelim} );
			{rdelim}
		{rdelim};

		Switch = $('#gameboard').memory( SwitchOpts ); 

		{if $gBitSystem->isPackageActive('suggestion')}
			Suggestion = $('.suggest').suggestion( {ldelim}api:true{rdelim} );
			Suggestion.onStoreSuccess = function(){ldelim}
				/* bind the play btn */
				$(conf.dialogbox).find('.playbtn').click( Switch.startOver );
			{rdelim};
			Suggestion.onEditSuggestion = function(){ldelim}
				$('#gamecontainer').hide();
			{rdelim};
			Suggestion.onCancelEditSuggestion = function(){ldelim}
				$('#gamecontainer').show();
			{rdelim};
		{/if}

	{rdelim});
</script>
{/strip}
