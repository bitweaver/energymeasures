(function($){
	Memory = function(el,conf){
		// protected
		var self = this;

		var cardGrid = el;

		var cardsRef = {};
		
		var selected = [];

		var rejected = [];

		var score;

		var currUpCard;

		var matchCardId;

		function random(){
			return 0.5 - Math.random();
		}

		// fast loop
		// @arr array
		function doWhile( arr, fn ){
			var count = Math.ceil(arr.length / 8);
			var test = arr.length % 8;
			var n = 0;
			do {
				switch (test) {
					case 0: fn( arr[n++] );
					case 7: fn( arr[n++] );
					case 6: fn( arr[n++] );
					case 5: fn( arr[n++] );
					case 4: fn( arr[n++] );
					case 3: fn( arr[n++] );
					case 2: fn( arr[n++] );
					case 1: fn( arr[n++] );
				}
				test = 0;
			} while (--count > 0);
		}

        // public
        $.extend(self, {
			// card click action handler
			onCardClick: function( $el ){
				// did user click an up card if so ignore all this
				if( !$el.hasClass( 'upcard' ) ){
					// is there zero,one,or more up cards
					var upcards = cardGrid.find('.upcard');
					var count = upcards.length;
					console.log( count );
					switch( count ){
						case 0:
							// nothing to check continue
							break;
						case 1:
							// is this a match
							$upcard = $(upcards[0]);
							if( $el.data('cardId') == $upcard.data('cardId') ){
							console.log(  $el.data('cardId')  );
							console.log( $upcard.data('cardId') );
								// yes -  display match option
								matchCardId = $el.data('cardId');
								self.alertMatchDialog( $el.data('cardId') );
							}else{
							console.log(  $el.data('cardId')  );
							console.log( $upcard.data('cardId') );
							console.log( 'settimeout' );
								// no - set timer to hide
								setTimeout( function(){
									console.log('flipback');
									self.flipCard($el);
									self.flipCard($upcard);
								}, 1500);
							}
							break;
						default: 
							// if two up cards we just ignore the click action
							return;
					}
					// display the clicked card
					var cardData = cardsRef[ $el.data('cardId') ];
					$el.css( {backgroundImage: "url( '"+cardData.thumbnail_urls.medium +"' )"} );
					$el.addClass( 'upcard' );
				}
			},

			flipCard: function($el){
				$el.css( {backgroundImage:""} );
				$el.toggleClass( 'upcard' );
			},

			alertMatchDialog: function( cardId ){
				// ajax dialog box content
				var data = cardsRef[ cardId ];
				if( data.html == undefined ){
					// request data
					var path = BitSystem.urls.energymeasures+'index.php?';
					var req = {content_id:data.content_id};
					var fn = function(rslt, textStatus){
						cardsRef[cardId].html = rslt;
						self.alertMatchDialog( cardId );
					}
					$.get( path, req, fn, 'html' );
				}else{
					var el = $('#'+conf.matchesDialogId).html( data.html ).show();
					// configure the match action btns
					el.find('.select').click( self.selectMatch );
					el.find('.reject').click( self.rejectMatch );

				}
			},

			// select
            selectMatch: function() {
				selected.push( matchCardId );
				var el = $('#'+conf.scoreBoardId);
				el.find('.selected').find('tbody').append( self.makeMatchRowHtml( matchCardId ) );
				self.showSelected();
				self.clearMatch();
            },

			// reject
            rejectMatch: function() {
				rejected.push( matchCardId );
				var el = $('#'+conf.scoreBoardId);
				el.find('.rejected').find('tbody').append( self.makeMatchRowHtml( matchCardId ) );
				self.showRejected();
				self.clearMatch();
            },

			makeMatchRowHtml: function( cardId ){
				var data = cardsRef[cardId];
				return $('<tr class="matchrow"><td class="icon"><img width="30px" height="30px" src="'+data.thumbnail_urls.small+'" /></td><td>'+data.title+'</td><td class="value hidden">'+self.number_format(data[conf.cardValueKey])+'</td></tr>');
			},

			showSelected: function(){
				$('.description').hide();
				var el = $('#'+conf.scoreBoardId).show();
				el.find('.selected').show();
				el.find('.rejected').hide();
				var el2 = $('.tabbar');
				el2.find('.selectedtab').addClass( 'active' );
				el2.find('.rejectedtab').removeClass( 'active' );
			},

			showRejected: function(){
				$('.description').hide();
				var el = $('#'+conf.scoreBoardId).show();
				el.find('.rejected').show();
				el.find('.selected').hide();
				var el2 = $('.tabbar');
				el2.find('.rejectedtab').addClass( 'active' );
				el2.find('.selectedtab').removeClass( 'active' );
			},

			clearMatch: function(){
				$('#'+conf.matchesDialogId).hide();
				$('.upcard').each( function( i ){ 
						var el = $(this);
						el.unbind( 'click' );
						el.css( 'cursor','default' );
						self.flipCard( el ); 
					});
				matchCardId = null;
			},

			// calculate score
            calcScore: function() {
				score = 0;
				// loop over select list
				doWhile( selected, self.addCardValueToScore ); 
            },

			// getScore
            getScore: function() {
				return score;
            },

			// select a random set of cards from the total set
            randomSelectCards: function() {
				var cardset = conf.cards.slice(0); 
				cardset.sort( random );
				return cardset.slice( 0, conf.cardCount );
            },

			// generate a grid of cards
			dealCard: function(card){
				var el = $('<div class="card"></div>')
				.css( {width: conf.cardW, 
						height: conf.cardH,
						backgroundRepeat: "no-repeat"
						} )
				.data( 'cardId', card.content_id )
				.click( function(){ self.onCardClick( $(this) ); });
				cardGrid.append(el); 
			},

			// get points value of card and add it to the score
			addCardValueToScore: function(cardId){
				score += self.getCardValue(cardsRef[cardId]);
			},

			// get the score value for a card
			getCardValue: function(card){
				if( card[conf.cardValueKey] != undefined ){
					return card[conf.cardValueKey];
				}
				return 0;
			},

			// gets the form to store a score
			editScore: function(){
				// request data
				var path = BitSystem.urls.energymeasures+'edit_score.php?';
				var req = {game:'switch'};
				var fn = function(rslt, textStatus){
					var el = $('#savescoredialog');
					el.html(rslt);
					el.find( 'input[name="save_score"]' ).click( function(){ self.storeScore( $('#edit_score' ) ) } );
					el.find( 'input[name="cancel_save_score"]' ).click( self.cancelEditScore );
					el.show();
					// hide stuff
					$('.description').hide();
					$('#'+conf.scoreBoardId).hide();
				}
				$.get( path, req, fn, 'html' );
			},

			storeScore: function($f){
				var path = BitSystem.urls.energymeasures+'edit_score.php?';
				path += $f.serialize() + "&game=switch&save_switch_score=y&score="+self.getScore()+"&selected="+selected.toString()+"&rejected="+rejected.toString();
				var fn = function(rslt, textStatus){
					// there could be an error on the page
					var el = $('#savescoredialog');
					if( $(rslt).find('input').length > 0 ){
						el.html(rslt);
						el.find( 'input[name="save_score"]' ).click( function(){ self.storeScore( $('#edit_score' ) ) });
						el.find( 'input[name="cancel_save_score"]' ).click( self.cancelEditScore );
					}else{
						el.hide();
						$('#thankyoudialog').html(rslt).show().find('.playagain').click( self.startOver ); 
					}
				}
				$.post( path, null, fn, 'html' );
			},

			cancelEditScore: function(){
				$('#savescoredialog').hide();
				$('#'+conf.scoreBoardId).show();
			},

			endGame: function(){
				if( selected.length > 0 ){
					var el = $('#'+conf.scoreBoardId);
					// display the values in each row
					el.find('.value').css( 'visibility', 'visible' );
					// calc the score and display
					self.calcScore();
					el.find('.score').show();
					el.find('.score').find('.value').html( self.number_format(self.getScore()) );
					// disable all remaining cards
					$('.card').unbind( 'click' ).css( 'cursor','default' );
					// this is a bit of a mess due to runaway layout requests
					// display postgame btns
					$('.postgame').show();
					// hide other options
					$('.options').hide();
					// hide scoreboard actions
					el.find('.actions').hide();
				}else{
					alert( 'You must select at least one match, please try again.' );
				}
			},

			startOver: function( isInit ){
				selected = [];
				rejected = [];
				score = 0;
				currUpCard = null;
				matchCardId = null;

				// remove any matches from scoreboard from previous play
				$('.matchrow').remove();

				// clear the calculated score
				// and hide various buttons and dialog boxes
				var el = $('#'+conf.scoreBoardId);
				if( !isInit ){
					el.show();
					self.showSelected();
				}
				el.find('.score').find('.value').empty();
				el.find('.actions').show();
				$('.postgame').hide();
				$('.score').hide();
				$('.options').show();
				$('#savescoredialog').hide();
				$('#thankyoudialog').hide();

				// clear the card grid
				cardGrid.empty();

				// generate a card set
				var cardset = self.randomSelectCards(); 
				// duplicate selected cards to make pairs
				cardset = cardset.concat(cardset);
				// randomize the order of all the cards
				cardset.sort( random );
				// deal the cards	
				doWhile( cardset, self.dealCard );
			},

			// this should move to an external utility some day 
			// thanks to http://phpjs.org/functions/number_format:481
			// note it has a bug for floats longer than 3 places
			number_format: function(number, decimals, dec_point, thousands_sep) {
				// Formats a number with grouped thousands
				var n = number, prec = decimals;
			 
				var toFixedFix = function (n,prec) {
					var k = Math.pow(10,prec);
					return (Math.round(n*k)/k).toString();
				};
			 
				n = !isFinite(+n) ? 0 : +n;
				prec = !isFinite(+prec) ? 0 : Math.abs(prec);
				var sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep;
				var dec = (typeof dec_point === 'undefined') ? '.' : dec_point;
			 
				var s = (prec > 0) ? toFixedFix(n, prec) : toFixedFix(Math.round(n), prec); //fix for IE parseFloat(0.55).toFixed(0) = 0;
			 
				var abs = toFixedFix(Math.abs(n), prec);
				var _, i;
			 
				if (abs >= 1000) {
					_ = abs.split(/\D/);
					i = _[0].length % 3 || 3;
			 
					_[0] = s.slice(0,i + (n < 0)) +
						  _[0].slice(i).replace(/(\d{3})/g, sep+'$1');
					s = _.join(dec);
				} else {
					s = s.replace('.', dec);
				}
			 
				var decPos = s.indexOf(dec);
				if (prec >= 1 && decPos !== -1 && (s.length-decPos-1) < prec) {
					s += new Array(prec-(s.length-decPos-1)).join(0)+'0';
				}
				else if (prec >= 1 && decPos === -1) {
					s += dec+new Array(prec).join(0)+'0';
				}
				return s;
			}
        });
        // end extend

		function init(){
			// generate quick pointers to each card for fast lookup
			var n = conf.cards.length-1;
			while( n>-1 ){
				cardsRef[conf.cards[n].content_id] = conf.cards[n];
				n--;
			}

			// config game btns
			$('.endgame').click( self.endGame );
			$('.playagain').click( self.startOver );
			$('.savescore').click( self.editScore );
			$('.selectedtab').click( self.showSelected );
			$('.rejectedtab').click( self.showRejected );

			// deal the cards - need to do this before we can calc the card grid size needed
			self.startOver( true );

			// set card grid size
			if( conf.layout = 'auto' ){
				var cardel = $('.card').eq(0);
				var rawRowCount = Math.sqrt( conf.cardCount * 2 );
				cardGrid.css( 'width', (Math.ceil(rawRowCount) * cardel.outerWidth(true)) );
				cardGrid.css( 'height', (Math.floor(rawRowCount) * cardel.outerHeight(true)) );
			}

			return self;
		}
		
		init();
	}

	$.fn.memory = function(conf){
		var el = this.eq(typeof conf == 'number' ? conf : 0).data("memory");
		if (el) { return el; }

		// card = { img_url, points, id }; 

		var opts = {
			// game params
			cards:[], 				// required in conf
			cardCount:18,			// unique cards to play, default makes a grid of 36
			cardH:54,				// card hight in pixels
			cardW:54,				// card width in pixels
			cardValueKey:'points',  // data sources may want to use something other than 'points' as a value 
			layout:'auto',

			// ui and layout identifiers
			matchesDialogId:'matchesdialog',
			scoreBoardId:'scoreboard',

			// external object access
			api:true
		};

        // set options - merge options passed in (conf) with defaults (opts)
        $.extend(opts, conf);

        this.each( function(){
            el = new Memory( $(this), opts );
			$(this).data("memory", el);
        });

		return opts.api ? el: this;
	}
})(jQuery);
