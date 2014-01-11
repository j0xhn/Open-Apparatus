jQuery(function($){
	// Set up the target date picker
	$( "input[name=f_target_date]" ).datepicker();

	var editing = false;
	var deleted = [];
	var newId = 0;

	$('input#add-reward').click(function(){
		editing = false;
		$('#add-reward').slideUp();
		$('#reward-inputs').slideDown().find('input, textarea').not('input[type=button]').val('');
		return false;
	});

	$('#add-reward-cancel').click(function(){
		$('#add-reward').slideDown();
		$('#reward-inputs').slideUp();
		return false;
	});

	// Clicking on the delete button
	$('#add-reward-delete').click(function(){
		if(editing == false) return false;

		if(confirm("Are you sure you want to delete this reward? It'll delete all its funders too.")){
			deleted.push(editing);
			delete rewards[editing];

			$('#rewards-deleted-field').val($.toJSON(deleted));
			$('#rewards-field').val($.toJSON(rewards));
			$('#add-reward').slideDown();
			$('#reward-inputs').slideUp();

			$('#reward-' + editing).slideUp('normal', function(){
				$(this).remove();
			});
		}
		return false;
	});

	$('#add-reward-save').click(function(){
		var form = $('#reward-inputs');
		if(editing == false){
			var newReward = {
				'title' : form.find('input[name=reward_title]').val(),
				'description' : form.find('textarea[name=reward_description]').val(),
				'amount' : parseInt(form.find('input[name=reward_amount]').val()),
				'available' : parseInt(form.find('input[name=reward_available]').val())
			};
			if(isNaN(newReward.amount) || !isFinite(newReward.amount)) newReward.amount = 0;
			if(isNaN(newReward.available) || !isFinite(newReward.available)) newReward.available = 0;

			rewards['new-'+newId] = newReward;

			var available = newReward.available;
			if(available == 0) available = 'Unlimited';

			// Create a new reward
			var reward = $('<li class="reward" />')
				.data('new-id', newId)
				.attr('id', 'reward-new-'+newId)
				.append( $('<strong />').html(newReward.title) )
				.append( $('<span class="availability" />').html(available + ' available @ '+newReward.amount+' each') )
				.append( $('<p />').html(newReward.description) )
				.hide()
				.appendTo('#current-rewards')
				.slideDown();

			newId++;
		}
		else{
			// Edit an existing reward
			var newReward = {
				'id' : editing,
				'title' : form.find('input[name=reward_title]').val(),
				'description' : form.find('textarea[name=reward_description]').val(),
				'amount' : form.find('input[name=reward_amount]').val(),
				'available' : form.find('input[name=reward_available]').val()
			};

			var available = newReward.available;
			if(available == 0) available = 'Unlimited';

			var reward = $('li#reward-'+editing);
			reward.find('strong').html(newReward.title);
			reward.find('.availability').html(available + ' available @ '+newReward.amount+' each');
			reward.find('p').html(newReward.description);

			rewards[editing] = newReward;
		}

		$('#add-reward').slideDown();
		$('#reward-inputs').slideUp().find('input, textarea').not('input[type=button]').val('');
		editing = false;

		// Update the current rewards
		$('#rewards-field').val($.toJSON(rewards));

		return false;
	});

	// Editing one of the rewards
	$('#current-rewards strong').live('click', function(){
		var $$ = $(this);
		var c = $$.parent();
		if(c.data('new-id') != undefined){
			// This is a new reward
			editing = 'new-'+c.data('new-id');
		}
		else{
			editing = c.attr('id').substring(7);
		}
		var reward = rewards[editing];
		console.log(reward);

		// Populate the form
		var form = $('#reward-inputs');
		form.find('input[name=reward_title]').val(reward.title);
		form.find('textarea[name=reward_description]').val(reward.description);
		form.find('input[name=reward_amount]').val(reward.amount);
		form.find('input[name=reward_available]').val(reward.available);

		$('#add-reward').slideUp();
		$('#reward-inputs').slideDown();
	});


	//////////////////////////////////////////////////////////////////////////////////////////
	// Funding Collection
	//////////////////////////////////////////////////////////////////////////////////////////

	$('#collect-funding').click(function(){
		if(confirm('Are you sure you want to collect funding?')){
			var total = $('#project-funders .funder').length;
			var done = 0;
			var totalCollected = 0;

			var displayDonationModal = function(){
				$('#f-modal-donate, #f-modal-donate-overlay').fadeIn();
				$('#f-modal-donate input[name=currency_code]').val(Funding.currency);

				$('#f-modal-donate .close').click(function(){
					$('#f-modal-donate, #f-modal-donate-overlay').fadeOut();
					return false;
				});

				$('#f-modal-donate .paypal').click(function(){
					$('#donate-form').submit();
				});
			}

			var displayDonationModal2 = function(){
				$('#f-modal-donate2, #f-modal-donate-overlay2').fadeIn();
				$('#f-modal-donate2 input[name=currency_code]').val(Funding.currency);

				$('#f-modal-donate2 .close').click(function(){
					$('#f-modal-donate2, #f-modal-donate-overlay2').fadeOut();
					return false;
				});

				$('#f-modal-donate2 .paypal').click(function(){
					$('#donate-form').submit();
				});
			}

			if($('#project-funders .funder').length == 0) {
				displayDonationModal();
			}

			$('#project-funders .funder').each(function(){
				var $$ = $(this);
				$$.find('.loader').css('opacity', 0.2).show().fadeIn();
				$.getJSON(
					Funding.site_url,{
						'fa' : 'charge_funder',
						'funder_id' : $$.attr('data-funder-id'),
						'project_id' : $$.attr('data-project-id'),
						'_wpnonce' : Funding.charge_nonce
				},
					function(data, textStatus){

						$$.find('.loader').fadeOut();
						if(data.status == 'success'){
							totalCollected += Number(data.amount);
							$$.append($('<div class="icon charged">').fadeIn());
						}
						else if(data.status == 'fail'){
							alert (JSON.stringify(data));
							var icon = $('<a class="icon charged_error" href="#" />')
								.fadeIn()
								.click(function(){
									alert(data.message);
									return false;
								});
							$$.append(icon);
						}

						done++;
						if(done == total){

						}

					}

				); window.setTimeout('location.reload()', 3000);
			});

		}
		return false;

	});


});