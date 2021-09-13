<?php
	use sgpb\AdminHelper;
	$defaultData = ConfigDataHelper::defaultData();
?>
<div class="sgpb sgpb-wrapper video-popup-options sgpb-fblike-options">
	<div class="formItem">
		<span class="formItem__title formItem__title_marginBottom "><?php _e("URL", SG_POPUP_TEXT_DOMAIN)  ?>:</span>
		<input name="sgpb-fblike-like-url" id="sgpb-fblike-like-url" type="url" placeholder="http://" class="grayFormItem__input" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-fblike-like-url'))?>" required>
	</div>
	<div class="formItem">
		<span class="formItem__title"><?php _e('Layout', SG_POPUP_TEXT_DOMAIN)?>:</span>
		<?php echo AdminHelper::createSelectBox($defaultData['buttonsType'], esc_html($popupTypeObj->getOptionValue('sgpb-fblike-layout')), array('name' => 'sgpb-fblike-layout', 'class'=>'js-sg-select2', 'id'=>'sgpb-fblike-layout')); ?>
	</div>
	<div class="formItem">
		<span class="formItem__title"><?php _e('Don\'t show share button', SG_POPUP_TEXT_DOMAIN);?>:</span>
		<div class="sgpb-onOffSwitch">
			<input class="sgpb-onOffSwitch-checkbox" name="sgpb-fblike-dont-show-share-button" id="fblike-dont-show-share-button" type="checkbox" <?php echo $popupTypeObj->getOptionValue('sgpb-fblike-dont-show-share-button');?>>
			<label class="sgpb-onOffSwitch__label" for="fblike-dont-show-share-button">
				<span class="sgpb-onOffSwitch-inner"></span>
				<span class="sgpb-onOffSwitch-switch"></span>
			</label>
		</div>
	</div>
</div>

