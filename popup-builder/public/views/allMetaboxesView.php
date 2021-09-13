<?php
namespace sgpb;
$metaboxes = apply_filters('sgpbAdditionalMetaboxes', array());
?>

<div class="sgpb sgpb-options">
	<?php foreach ( $metaboxes as $key => $metabox ) {
		if ( $key == 'allMetaboxesView' ) {
			continue;
		}
	?>
	<div class="sgpb-options-menu"
	     id="<?php echo $key; ?>">
		<h3 class="sgpb-options-menu-header"><?php echo $metabox['displayName']; ?></h3>
		<span class="sgpb-options-menu-header__sub"><?php  echo $metabox['short_description']; ?></span>
	</div>

	<div class="sgpb-options-content">
		<div id="options-<?php echo $key; ?>" class="sgpb-metabox sgpb-metabox-options ">
			<p class="sgpb-header-h1 sgpb-margin-top-20 sgpb-margin-bottom-50"><?php echo $metabox['displayName']; ?></p>
			<?php require_once( $metabox['filePath'] ); ?>
		</div>
	</div>
	<?php }; ?>
</div>
<script type="text/javascript">
	let hash = window.location.hash.replace(/^#/,'');
	if (hash) {
		jQuery('#'+hash).addClass('sgpb-options-menu-active');
	} else {
		jQuery('.sgpb-options-menu').first().addClass('sgpb-options-menu-active')
	}
	jQuery(document).ready(function () {
		setTimeout(function () {
			let minHeightShouldBe = jQuery('.sgpb-options-menu-active').next().height();
			jQuery('#allMetaboxesView').css('min-height', parseInt(minHeightShouldBe+100)+'px');
		});
		jQuery('.sgpb-options-content, .sgpb-options-menu').click(function(){
			setTimeout(function(){
				let minHeightShouldBe = jQuery('.sgpb-options-menu-active').next().height();
				jQuery('#allMetaboxesView').css('min-height', parseInt(minHeightShouldBe+100)+'px');
			}, 500);
		});
		jQuery('.sgpb-options-menu').click(function () {
			if (jQuery(this).hasClass('sgpb-options-menu-active')) {
				return;
			}
			const findActive = jQuery('.sgpb-options-menu-active');
			findActive.removeClass('sgpb-options-menu-active');
			jQuery(this).addClass('sgpb-options-menu-active');
			jQuery([document.documentElement, document.body]).animate({
				scrollTop: jQuery('#allMetaboxesView').offset().top
			}, 500);
			location.hash = jQuery(this).attr('id');
		});
	});
</script>
