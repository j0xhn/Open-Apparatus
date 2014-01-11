<?php

add_filter("manage_edit-project_columns", "fundit_project_columns");
/**
 * Custom columns for the project post type
 * @param array() $columns
 */
function fundit_project_columns($columns){
	return array(
		"cb" => "<input type=\"checkbox\" />",
		"title" => __("Project Title", 'funding'),
		"status" => __("Project status", 'funding'),
		"funding-progress" => __("Progress", 'funding'),
		"funding-time" => __("Time Remaining", 'funding'),
		"author" => __("Creator", 'funding'),
		"comments" => '<img src="'.get_bloginfo('url').'/wp-admin/images/comment-grey-bubble.png" alt="Comments" />',
		'date' => __('Date', 'funding'),
	);
}

add_filter("manage_edit-reward_columns", "fundit_reward_columns");
/**
 * Custom columns for the reward post type
 * @param array() $columns
 */
function fundit_reward_columns($columns){
	return array(
		"cb" => "<input type=\"checkbox\" />",
		"title" => __("Reward Title", 'funding'),
		'reward-project' => __('Project', 'funding'),
		'reward-contribution' => __('Min Contribution', 'funding'),
		'reward-available' => __('Available', 'funding'),
		"author" => __("Author", 'funding'),
		'comments' => '<img src="'.get_bloginfo('url').'/wp-admin/images/comment-grey-bubble.png" alt="Comments" />',
		'date' => __('Date', 'funding'),

	);
}

add_filter("manage_edit-funder_columns", "fundit_funder_columns");
/**
 * Custom columns for the funder post type
 * @param array() $columns
 */
function fundit_funder_columns($columns){
	return array(
		"cb" => "<input type=\"checkbox\" />",
		"funder-name" => __("Name", 'funding'),
		"funder-amount" => __("Amount", 'funding'),
		"funder-reward" => __("Reward", 'funding'),
		"funder-project" => __("Project", 'funding'),
		"funder-email" => __("Email", 'funding'),
		'funder-status' => __('Status', 'funding'),
	);
}

add_action("manage_posts_custom_column", "fundit_custom_columns");
/**
 * Custom column display
 * @param string $column The name of the column
 */
function fundit_custom_columns($column, $post_id){
	global $post;

	switch($column){
		case 'funding-progress':

                global $f_currency_signs;
                $project_settings = (array) get_post_meta($post_id, 'settings', true);
                $project_expired = strtotime($project_settings['date']) < time();

		if ( empty( $project_expired ) )
				echo __( 'Unknown' , 'funding');

			/* If there is a duration, append 'minutes' to the text string. */
			else
				printf( __( '%s minutes', 'funding' ), $project_expired );

			break;
			break;

		case 'funding-time':

			break;

		// Stuff for the rewards
		case 'reward-project':
			$reward = new Fundit_Model_Reward($post);
			$project = $reward->get_project();
			?><a href="<?php print admin_url('post.php?action=edit&post='.$project->ID) ?>"><?php print $project->post_title ?></a><?php
			break;
		case 'reward-contribution':
			$reward = new Fundit_Model_Reward($post);
			$project = $reward->get_project();

			if(empty($project->contribution)){
				print 'No minimum'; break;
			}
			print $project->get_currency_sign().$project->contribution;
			break;
		case 'reward-available':
			$reward = new Fundit_Model_Reward($post);
			$funders = count($reward->get_funders());
			if($reward->available == 0) {
				print 'Unlimited';
			}
			else{
				print ($reward->available - $funders).' of '.$reward->available;
			}
			print ' <span style="color:#888">('.$funders.' '.($funders == 1 ? 'funder' : 'funders').')</span>';
			break;

		// Stuff for the funders
		case 'funder-name':
			$funder = new Fundit_Model_Funder($post);
			?><strong><a href="mailto:<?php print $funder->email ?>"><?php print $funder->post_title ?></a></strong><?php
			break;
		case 'funder-reward':
			$funder = new Fundit_Model_Funder($post);
			$reward = $funder->get_reward();
			?><a href="<?php print admin_url('post.php?action=edit&post='.$reward->ID) ?>"><?php print $reward->post_title ?></a><?php
			break;
		case 'funder-project':
			$funder = new Fundit_Model_Funder($post);
			$project = $funder->get_project();
			?><a href="<?php print admin_url('post.php?action=edit&post='.$project->ID) ?>"><?php print $project->post_title ?></a><?php
			break;
		case 'funder-amount':
			$funder = new Fundit_Model_Funder($post);
			print $funder->get_currency_sign().$funder->amount;
			break;
		case 'funder-email':
			$funder = new Fundit_Model_Funder($post);
			?><a href="mailto:<?php print $funder->email ?>"><?php print $funder->email ?></a><?php
			break;
		case 'funder-status':
			$funder = new Fundit_Model_Funder($post);
			if($funder->fund_status == 'cancelled'){
				print 'Cancelled';
			}
			else{
				if($funder->post_status == 'draft') print '<strong>'.__('Awaiting Confirmation', 'funding').'</strong>';
				elseif($funder->post_status == 'publish'){
					if($funder->fund_status == 'funded') print 'Funded';
					else print 'Approved';
				}
			}

			?> &nbsp; <a href="<?php print FUNDIT_PLUGIN_URL_ROOT.'/admin/refresh-funder.php?funder_id='.$funder->ID.'&return='.esc_attr(add_query_arg(null,null)) ?>"><?php _e("Refresh", 'funding'); ?></a><?php

			if($_GET['funder_updated'] == $funder->ID){
				?><div id="updated" class="updated"><p><?php _e("Funder", 'funding'); ?> "<?php print $funder->post_title ?>" <?php _e("status updated.", 'funding'); ?></p></div><?php
			}

			break;
	}
}