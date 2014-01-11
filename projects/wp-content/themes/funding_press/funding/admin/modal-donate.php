<div id="f-modal-donate">
	<h3><?php _e('Error!', 'funding') ?></h3>
	<div class="content">
		<?php print wpautop(file_get_contents(dirname(__FILE__).'/modal-donate-text.txt')) ?>
	</div>
	<div class="donate">
		<a href="#" class="close"><?php _e("Ok", 'funding'); ?></a>
	</div>
</div>

<div id="f-modal-donate-overlay">

</div>

<div id="f-modal-donate2">
    <h3><?php _e('Congratulations!', 'funding') ?></h3>
    <div class="content">
        <?php print wpautop(file_get_contents(dirname(__FILE__).'/modal-donate-text2.txt')) ?>
    </div>
    <div class="donate">
        <a href="#" class="close"><?php _e("Ok", 'funding'); ?></a>
    </div>
</div>

<div id="f-modal-donate-overlay2">

</div>