<?php

/**
 * This is the default template for rendering a funding page.
 *
 * The user chooses the amount they want to fund and the reward they'd like.
 */

?>

<?php get_header(); the_post(); ?>
<div class="row page-title">
  <div class="container">
    <h1><?php _e("Thank you for helping out!", 'funding'); ?></h1>
    <div class="breadcrumbs"><?php the_breadcrumb(); ?></div>
  </div>
</div>
<div class="container blog">
	<div id="primary">
		<div id="content" role="main">

			<?php if(!empty($message)) : ?>
				<div id="form-error-message"><?php print $message ?></div>
			<?php endif; ?>

			<form id="funding-form" method="post" action="<?php print add_query_arg(array('step' => 2), get_post_permalink()) ?>">
				<div class="span7">
								<h3 style="margin-top:0px;"><?php _e('How much would you like to contribute?', 'funding'); ?></h3>
				<ul id="project-rewards-list">
					<li>
						<span><?php print $project_currency_sign ?></span>
						<input type="text" name="amount" id="field-amount" value="<?php esc_attr_e(@$_REQUEST['amount']) ?>" />
						<div class="clear"></div>
					</li>
				</ul>

				<h3><?php _e('Choose Your Reward', 'funding'); ?></h3>
				<ul id="project-rewards-list" class="perks-wrapper">
					<?php foreach($rewards as $reward) : ?>
						<?php
							$reward_funding_amount = get_post_meta($reward->ID, 'funding_amount', true);
							$reward_available = get_post_meta($reward->ID, 'available', true);
							$funders = get_posts(array(
								'numberposts'     => -1,
								'post_type' => 'funder',
								'post_parent' => $reward->ID,
								'post_status' => 'publish'
							));
						?>

						<?php if(empty($reward_available) || count($funders) < $reward_available) : ?>
							<li class="perk">
								<label for="<?php print 'reward-'.$reward->ID ?>">
									<input type="radio" name="chosen_reward" value="<?php print $reward->ID ?>" id="<?php print 'reward-'.$reward->ID ?>" <?php checked($reward->ID, @$_REQUEST['chosen_reward']) ?> />
									<div class="funding-perk-content">
										<h5><?php print $reward->post_title ?></h5>
										<?php if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') { ?>
                                           <div class="min-amount"><strong><?php printf(__('Pledge %s%s or more', 'funding'), $project_currency_sign, number_format($reward_funding_amount, 2));?></strong></div>
                                        <?php } else { ?>
                                           <div class="min-amount"><strong><?php printf(__('Pledge %s%s or more', 'funding'), $project_currency_sign, money_format('%.2n', $reward_funding_amount));?></strong></div>
                                        <?php } ?>
										<p><?php print $reward->post_content ?></p>
									</div>
									<div class="clear"></div>
								</label>
							</li>
						<?php endif; ?>

					<?php endforeach ?>
				</ul>

				<h3><?php _e('Who Are You?', 'funding'); ?></h3>
				<dl>
					<lh><label for="field-name"><?php _e('Your Name', 'funding') ?></label></lh>
					<dt><input type="text" name="name" id="field-name" value="<?php esc_attr_e(@$_REQUEST['name']) ?>" /></dt>

					<lh><label for="field-email"><?php _e('Your Email', 'funding') ?></label></lh>
					<dt><input type="text" name="email" id="field-email" value="<?php esc_attr_e(@$_REQUEST['email']) ?>" /></dt>

				</dl>
				</div>
				<div class="span4">

					<h3 style="margin-top:0px; margin-bottom:15px;"><?php _e('You are helping fund:', 'funding'); ?></h3>

					<div class="project-card span3">
                    <?php if(!has_post_thumbnail()){ ?>
                     <div class="project-thumb-wrapper"><a href="<?php the_permalink(); ?>"><img src="<?php echo get_template_directory_uri().'/img/default-image.jpg'?>" /></a></div>
                    <?php }else{ ?>
                        <div class="project-thumb-wrapper"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('projects');  ?></a></div>
                    <?php } ?>
                       <h5 class="bbcard_name"><a href="<?php the_permalink(); ?>"><?php $title = get_the_title(); echo mb_substr($title, 0,20); if(strlen($title) > 23){echo '...';}?></a></h5>

            <p> <?php
                $excerpt = get_the_excerpt();
                echo mb_substr($excerpt, 0,80);echo '...';
             ?></p>

            <?php
                global $post;
                global $f_currency_signs;
                $project_settings = (array) get_post_meta($post->ID, 'settings', true);
                $project_expired = strtotime($project_settings['date']) < time();
                $project_currency_sign = $f_currency_signs[$project_settings['currency']];
                $target= $project_settings['target'];

                $rewards = get_children(array(
                'post_parent' => $post->ID,
                'post_type' => 'reward',

                'order' => 'ASC',
                'orderby' => 'meta_value_num',
                'meta_key' => 'funding_amount',
            ));

            $funders = array();
            $funded_amount = 0;
            $chosen_reward = null;

            foreach($rewards as $this_reward){
                $these_funders = get_children(array(
                    'post_parent' => $this_reward->ID,
                    'post_type' => 'funder',
                    'post_status' => 'publish'
                ));
                foreach($these_funders as $this_funder){
                    $funding_amount = get_post_meta($this_funder->ID, 'funding_amount', true);
                    $funders[] = $this_funder;
                    $funded_amount += $funding_amount;
                }
            }?>
            <?php if($funded_amount == $target or $funded_amount > $target){ ?>
                <div class="project-successful">
                    <strong><?php _e('Successful!', 'funding'); ?></strong>
                </div>
            <?php }elseif($project_expired){ ?>

                        <div class="project-unsuccessful">
                            <strong><?php _e('Unsuccessful!', 'funding'); ?></strong>
                        </div>

            <?php } ?>
            <div class="progress progress-striped active bar-green"><div style="width: <?php printf(__('%u%', 'funding'), round($funded_amount/$target*100), $project_currency_sign, round($target)) ?>%" class="bar"></div></div>

            <ul class="project-stats">
                <li class="first funded">
                     <strong><?php printf(__('%u%%', 'funding'), round($funded_amount/$target*100), $project_currency_sign, number_format(round((int)$target), 0, '.', ',')) ?></strong><?php _e('funded', 'funding'); ?>
                </li>
                <li class="pledged">
                    <strong>
                        <?php print $project_currency_sign; print number_format(round((int)$target), 0, '.', ',');?></strong><?php _e('target', 'funding'); ?>
                </li>
                <li data-end_time="2013-02-24T08:41:18Z" class="last ksr_page_timer">
                    <?php

                    if(!$project_expired) : ?>
                        <strong><?php print F_Controller::timesince(time(), strtotime($project_settings['date']), 1, ''); ?></strong>
                        <?php _e('days to go', 'funding'); ?>

                    <?php endif; ?>
                </li>
            </ul>
            <div class="clear"></div>
            </div>

			       <div class="notice">
					<h6 class="important"><span class="highlight"><?php _e('Important', 'funding'); ?></span></h6>
                  <?php  if (of_get_option('important_text')!=""){ ?>
                    <?php echo of_get_option('important_text'); ?>
                    <?php } ?>
					</div>

			       <a href="#" style="float:left;" onclick="javascript:window.open('https://www.paypal.com/cgi-bin/webscr?cmd=xpt/Marketing/popup/OLCWhatIsPayPal-outside','olcwhatispaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=400, height=350');"><img  src="<?php bloginfo('template_directory'); ?>/img/paypal_payment.jpg" border="0" alt="Solution Graphics"></a>

				<div class="clear"></div>
				</div>
				<div class="clear"></div>
				<h3 class="funding-comments-title"><?php _e('Comment', 'funding'); ?></h3>
				<p><?php _e('If you selected a reward that involve received an item, please write the address in the box bellow. You can also use it if you want to tell something to the creator of the project.', 'funding'); ?></p>
				<ul id="funding-comments">
					<li>
						<textarea name="message"><?php esc_attr_e(@$_REQUEST['message']) ?></textarea>
					</li>
				</ul>





				<div class="submit">
					<input type="submit" class="button-green button-medium button-contribute" value="<?php _e('Commit To Funding', 'funding') ?>" />
				</div>

				<div id="funding-information">
					<?php include(dirname(__FILE__).'/info.php') ?>
				</div>


			</form>

		</div><!-- #content -->
	</div><!-- #primary -->
</div>
<?php get_footer() ?>