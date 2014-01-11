
</div> <!-- End of container -->
  <!-- FOOTER -->
    <footer>
      <div class="container">

           <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer widgets') ) : ?>
         		<?php dynamic_sidebar('one'); ?>
           <?php endif; ?>

      </div>
    </footer>

    <div class="copyright">
    	<div class="container">
        	<p class="pull-right"><a href="#"><?php _e("Back to top", 'funding'); ?></a></p>
        	<p>© <?php if(of_get_option('year')!=""){echo of_get_option('year');}?>&nbsp;<?php if(of_get_option('copyright')!=""){ echo of_get_option('copyright');} ?>
        		&nbsp;
        	<a href="<?php if(of_get_option('privacy')!=""){echo of_get_option('privacy');}?>"><?php _e("Privacy", 'funding'); ?></a> ·
        	<a href="<?php if(of_get_option('terms')!=""){echo of_get_option('terms');}?>"><?php _e("Terms", 'funding'); ?></a></p>
        </div>
    </div>
<?php
$current_user= wp_get_current_user();
$level = $current_user->user_level;
if($level == 10){}else{
  if ( of_get_option('colfun') ){}else{?>
       <script>
           jQuery(document).ready(function($) {
               $('#project_funders').remove();
           });
       </script>
<?php }}?>
<script type="text/javascript">var switchTo5x=true;</script>
<script type="text/javascript" src="https://ws.sharethis.com/button/buttons.js"></script>
<script type="text/javascript">stLight.options({publisher: "ur-a91f8d30-251f-5433-c4d5-a5f68bb6664", doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>
<script>
    function cat_ajax_get(catID) {

        jQuery('#category-menu li').click(function(li) {
        jQuery('li').removeClass('current');
        jQuery(this).addClass('current');
        });

     jQuery("#category-post-content").hide();
    jQuery("#loading-animation").show();

    var ajaxurl ='<?php echo home_url(); ?>/wp-admin/admin-ajax.php';
       jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {"action": "load-filter", cat: catID },
        success: function(response) {
            jQuery("#category-post-content").html(response);
            jQuery("#loading-animation").hide();
               jQuery("#category-post-content").show();
            return false;
        }
    });
}
</script>
   <script>
   jQuery(document).ready(function($) {


    //  jQuery('ul.nav > li').each(function(){
    if (jQuery(this).find('ul').length > 0)
    {


        //item has children; do whatever you want
        $("ul.sub-menu").addClass('dropdown-menu').css("display","none");
          $("ul.children").addClass('dropdown-menu').css("display","none");


    }

//});
        $("ul.sub-menu").parent().addClass('parent');
        $(".menu-header ul.nav .parent").addClass('dropdown');

        $("ul.children").parent().addClass('parent');
        $(".menu ul").parent().addClass('dropdown');

        $('ul.children').hover(
           function(){
               $(this).parent().addClass('active');
           }, function(){
               $(this).parent().removeClass('active');
           }
        );

        $('ul.sub-menu').hover(
           function(){
               $(this).parent().addClass('active');
           }, function(){
                 $(this).parent().removeClass('active');
           }
        );



         //Add Hover effect to menus
jQuery('ul.nav li.parent').hover(function() {
  jQuery(this).find('.dropdown-menu').stop(true, true).delay(100).fadeIn();
}, function() {
  jQuery(this).find('.dropdown-menu').stop(true, true).delay(30).fadeOut();
});

      //Add Hover effect to menus
jQuery('.menu ul li.parent').hover(function() {
  jQuery(this).find('.dropdown-menu').stop(true, true).delay(100).fadeIn();
}, function() {
  jQuery(this).find('.dropdown-menu').stop(true, true).delay(30).fadeOut();
});



jQuery(function ($) {
    $('[rel=tooltip]').tooltip()
});

});
if(document.getElementById('click')){
document.getElementById('click').click();
}
</script>

<?php $current_user= wp_get_current_user();
    $level = $current_user->user_level;
    if($level == 1){

    $idObj = get_category_by_slug('blog');
    $id = $idObj->term_id; ?>
<script>

jQuery(document).ready(function($) {

	var newmeta = $('#newmeta-submit');
	newmeta.removeClass('button').addClass('button-green button-small');
	var wpfooter =  $('#wpfooter');
    wpfooter.remove();
    var slugdiv =  $('#slugdiv');
    slugdiv.remove();
    var mymetabox2 = $('#my-meta-box2');
    mymetabox2.remove();
    var editslugbox = $('#edit-slug-box');
    editslugbox.remove();
    var category1 = $('#category-1');
    category1.remove();
    var category2 =  $('#category-<?php echo $id; ?>');
    category2.remove();
    var commentstatusdiv1 =   $('#commentstatusdiv .inside .meta-options label:last-child');
    commentstatusdiv1.remove();
    var commentstatusdiv2 = $('#commentstatusdiv .inside .meta-options br');
    commentstatusdiv2.remove();
    var postexcerpt1 =  $('#postexcerpt .inside p');
    postexcerpt1.remove();
    var postexcerpt2 = $('#postexcerpt h3 span');
    postexcerpt2.remove();
    var poststuff1 =  $('#poststuff');
    poststuff1.find('.closed').removeClass('closed');
    poststuff1.find('div.meta-box-sortables').removeClass('meta-box-sortables');
    var iconedit = $('#icon-edit');
    iconedit.remove();
    var wrap = $('.wrap h2');
    wrap.replaceWith('<div class="row page-title"><div class="container"><h1><?php global $wp_post_types; $obj = $wp_post_types['project'];print $obj->labels->add_new_item; ?></h1><div class="breadcrumbs"><?php the_breadcrumb(); ?></div></div></div>');
    var postexcerpt3 = $('#postexcerpt h3');
    postexcerpt3.prepend('<span>Summary</span>');
    var publishing = $('#publishing-action #publish');
    publishing.removeClass("button button-primary button-large");
    publishing.addClass("button-green button-medium");
    var projectrewards = $('#project_rewards #add-reward');
    projectrewards.removeClass("button-secondary");
    projectrewards.addClass("button-green button-small");
    var submitdiv = $('#submitdiv #save-post');
    submitdiv.removeClass("button");
    submitdiv.addClass("button-green button-small");
    var savereward = $('#reward-inputs #add-reward-save');
    savereward.removeClass("button-secondary");
    savereward.addClass("button-green button-small");
    poststuff1.find('.hide-if-js').removeClass("hide-if-js");
    var postcustom = $('#postcustom .inside p');
    postcustom.remove();
    var postcustomstuff = $('#postcustomstuff');
    postcustomstuff.prepend("<?php _e('<p><span>In this fields you will be able to add updates for your project. If you want to do so, you can edit the project and add more updates in the future.</span></p>','funding'); ?>");
    var metakeyinput = $('#metakeyinput');
    metakeyinput.css("display","none");
    var enternew = $('#enternew');
    enternew.remove();

    var $publish = $('#submitdiv');
    var $wysiwyg = $('#postdivrich');
    var $featured = $('#postimagediv');
    var $title = $('#titlediv');
    var $excerpt = $('#postexcerpt');
    var $rewards = $('#project_rewards');
    var $settings = $('#project_settings');
    var $sidebar = $('#side-sortables');
    var $top = $('.page-title');
    var $header = $('header');
    var $body = $('#wpbody');
    var $video = $('#my-meta-box');
    var $footer = $('footer');
    var $wpwrap = $('#wpwrap');
    var $copyright = $('.copyright');


    $title.prepend($publish);
    $wysiwyg.prepend($excerpt);
    $wysiwyg.prepend($featured);
    $sidebar.prepend($rewards);
    $sidebar.prepend($settings);
    $header.append($top);
    $video.insertAfter($featured);
    $footer.insertAfter($wpwrap);
    $copyright.insertAfter($footer);


});

</script>

<?php } ?>

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
<?php wp_enqueue_script("jquery"); ?>
<?php wp_footer(); ?>
</body></html>