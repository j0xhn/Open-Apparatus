<?php  /*
 * 	Template Name: Contact
 */
?>
<?php get_header(); ?>

<div class="row page-title">
  <div class="container">
    <h1><?php echo get_the_title(); ?></h1>
    <div class="breadcrumbs"><?php the_breadcrumb(); ?></div>
  </div>
</div>
<div class="container page cpage">
    <div class="row">
        <div class="span8">

          <?php
if(isset($_POST['submitted'])) {
	if(trim($_POST['contactName']) === '') {
		$nameError = __('Please enter your name.', 'funding');
		$hasError = true;
	} else {
		$name = trim($_POST['contactName']);
	}

	if(trim($_POST['email']) === '')  {
		$emailError = __('Please enter your email address.', 'funding');
		$hasError = true;
	} else if (!preg_match("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/i", trim($_POST['email']))) {
		$emailError = __('You entered an invalid email address.', 'funding');
		$hasError = true;
	} else {
		$email = trim($_POST['email']);
	}

	if(trim($_POST['comments']) === '') {
		$commentError = __('Please enter a message.', 'funding');
		$hasError = true;
	} else {
		if(function_exists('stripslashes')) {
			$comments = stripslashes(trim($_POST['comments']));
		} else {
			$comments = trim($_POST['comments']);
		}
	}

	if(!isset($hasError)) {
		$emailTo = of_get_option('contact_email');
		if (!isset($emailTo) || ($emailTo == '') ){
			$emailTo = of_get_option('contact_email');
		}
        $sub = $_POST['subject'];
		$subject = _('[PHP Snippets] From ','funding').$name;
		$body = "Name: $name \n\nEmail: $email \n\nSubject: $sub \n\nComments: $comments";

		wp_mail($emailTo, $subject, $body);
		$emailSent = true;
	}

} ?>
<?php get_header(); ?>
	<div class="contact">

			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				  <h3 class="title"><span><?php _e("Contact form", 'funding'); ?></span></h3>
					<div class="entry-content">
						<?php if(isset($emailSent) && $emailSent == true) { ?>
							<div class="thanks">
								<p><?php _e("Thanks, your email was sent successfully", 'funding'); ?>.</p>
							</div>
						<?php } else { ?>
							<?php the_content(); ?>
							<?php if(isset($hasError) || isset($captchaError)) { ?>
								<p class="error"><?php _e("Sorry, an error occured.", 'funding'); ?><p>
							<?php } ?>

						<form action="<?php the_permalink(); ?>" id="contactForm" method="post">
							<ul class="contactform controls">

							<li class="input-prepend">
								<span class="add-on"><i class="icon-user"></i></span>
								<input type="text" name="contactName" placeholder="Name*" id="contactName" value="<?php if(isset($_POST['contactName'])) echo $_POST['contactName'];?>" class="required requiredField" />
								<?php if($nameError != '') { ?>
									<span class="error"><?php $nameError;?></span>
								<?php } ?>
							</li>

							<li class="input-prepend">
							    <span class="add-on"><i class="icon-envelope"></i></span>
								<input type="text" placeholder="Email*" name="email" id="email" value="<?php if(isset($_POST['email']))  echo $_POST['email'];?>" class="required requiredField email" />
								<?php if($emailError != '') { ?>
									<span class="error"><?php $emailError;?></span>
								<?php } ?>
							</li>

							<li class="input-prepend">
                                <span class="add-on"><i class="icon-comment"></i></span>
                                <input type="text" placeholder="Subject" name="subject" id="subject" value="<?php if(isset($_POST['subject']))  echo $_POST['subject'];?>" class="subject" />

                            </li>


							<li class="input-prepend">
								<span class="add-on"><i class="icon-align-justify"></i></span>
								<textarea name="comments" placeholder="Your message*" id="commentsText" rows="20" cols="30" class="required requiredField"><?php if(isset($_POST['comments'])) { if(function_exists('stripslashes')) { echo stripslashes($_POST['comments']); } else { echo $_POST['comments']; } } ?></textarea>
								<?php if($commentError != '') { ?>
									<span class="error"><?php $commentError;?></span>
								<?php } ?>
							</li>

							<li>
								   <input type="submit" class="button-green button-small"  value="<?php echo __("Send email", 'funding'); ?>" />
							</li>
						</ul>
						<input type="hidden" name="submitted" id="submitted" value="true" />
					</form>
				<?php } ?>
				</div><!-- .entry-content -->

				<?php endwhile; endif; ?>
		</div><!-- #contact -->
        </div>
         <!-- /.span8 -->

        <div class="span4 ">


     <div class="contact">
        <h3 class="title"><span><?php if ( of_get_option('sidebar_title') ) { echo of_get_option('sidebar_title'); } ?></span></h3>
      <?php if ( of_get_option('sidebar_content') ) { echo of_get_option('sidebar_content'); } ?>
        <div class="gap" style="height: 20px;"></div>

        <h3 class="title"><span><?php _e("Social Network", 'funding'); ?></span></h3>
      <ul class="social-media">
                <?php if ( of_get_option('facebook') ) { ?><li><a target="_blank" class="facebook"href="<?php echo of_get_option('facebook_link'); ?>"><?php _e("facebook", 'funding'); ?></a></li><?php } ?>
                <?php if ( of_get_option('twitter') ) { ?><li><a target="_blank" class="twitter" href="<?php echo of_get_option('twitter_link'); ?>"><?php _e("twitter", 'funding'); ?></a></li><?php } ?>
                <?php if ( of_get_option('rss') ) { ?><li><a target="_blank" class="rss" href="<?php echo of_get_option('rss_link'); ?>"><?php _e("rss", 'funding'); ?></a></li><?php } ?>
                <?php if ( of_get_option('googleplus') ) { ?> <li><a target="_blank" class="google" href="<?php echo of_get_option('google_link'); ?>"><?php _e("google", 'funding'); ?></a></li><?php } ?>
                <?php if ( of_get_option('skype') ) { ?><li><a target="_blank" class="skype" href="skype:<?php echo of_get_option('skype_name'); ?>?add"><?php _e("skype", 'funding'); ?></a></li><?php } ?>
            </ul>
        <div class="clear"></div>
     </div>


    </div>
    <!-- /.span4 -->
    </div>
</div>


<?php get_footer(); ?>