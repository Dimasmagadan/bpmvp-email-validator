(function($){
	"use strict";
	$(document).ready(function(){
		var app = {
			elMsg: $('#bpmvp-message'),
			elList: $('#bpmvp-list'),
			show: function(){
				$(this.elMsg).show();
			},
			hide: function(){
				$(this.elMsg).hide();
			},
			empty: function(){
				$(this.elMsg).html('');

				return this.hide();
			},
			setMessage: function(text){
				$(this.elMsg).html(text);

				return this.show();
			},
			append: function(mail,status){
				$(this.elList).append( _.template( bpmvp.tpl, {mail:mail, status: status} ) );
			},
			request: function(mail){
				if(!$('body').hasClass('bpmvp-loading')){
					var self = this;

					$('body').addClass('bpmvp-loading');

					$.ajax({
						url: 'http://api.email-validator.net/api/verify',
						type: 'POST',
						cache: false,
						crossDomain: true,
						data: {
							EmailAddress: mail,
							APIKey: bpmvp.key,
							scope: 'wpplugin',
						},
						dataType: 'json',
						success: function (data) {
							$('body').removeClass('bpmvp-loading');
							self.append( mail, data.status );
						},
						error: function(){
							$('body').removeClass('bpmvp-loading');
							self.setMessage(bpmvp[801]);
						}
					});
				}
			}
		};

		$('#bpmvp-button-validate').on('click',function(){
			if($('#bpmvp-mail').val().length){
				app.hide();
				app.request( $('#bpmvp-mail').val() );
			} else {
				app.setMessage(bpmvp[800]);
			}

			return false;
		});
	});
}(jQuery));