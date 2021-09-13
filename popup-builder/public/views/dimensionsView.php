<?php
	use sgpb\AdminHelper;
	use sgpb\MultipleChoiceButton;
	$defaultData = ConfigDataHelper::defaultData();
	$removedOptions = $popupTypeObj->getRemoveOptions();
	$multipleChoiceButton = new MultipleChoiceButton($defaultData['popupDimensions'], $popupTypeObj->getOptionValue('sgpb-popup-dimension-mode'));
	$subOptionClass = ' sgpb-sub-option';
	if (!empty($removedOptions['sgpb-popup-dimension-mode'])) {
		$subOptionClass = '';
	}
?>
<div class="sgpb sgpb-wrapper dimensions ">
	<?php echo (!empty($removedOptions['sgpb-popup-dimension-mode'])) ? '' : $multipleChoiceButton; ?>
	<div class="sg-hide sg-full-width" id="responsive-dimension-wrapper">
		<div class="subFormItem<?php echo $subOptionClass; ?>">
			<span class="subFormItem__title" for="max-height"><?php _e('Size', SG_POPUP_TEXT_DOMAIN)  ?>:</span>
			<?php echo AdminHelper::createSelectBox($defaultData['responsiveDimensions'], esc_html($popupTypeObj->getOptionValue('sgpb-responsive-dimension-measure')), array('name' => 'sgpb-responsive-dimension-measure', 'class'=>'js-sg-select2 sgpb-responsive-mode-change-js')); ?>
		</div>
	</div>
	<div class="<?php echo (!empty($removedOptions['sgpb-popup-dimension-mode'])) ? '' : 'sg-hide '; ?>sg-full-width formItem" id="custom-dimension-wrapper">
		<div class="subFormItem<?php echo $subOptionClass; ?>">
			<span class="subFormItem__title"><?php _e('Width', SG_POPUP_TEXT_DOMAIN); ?>:</span>
			<input type="text" id="width" class="subFormItem__input" name="sgpb-width" placeholder="<?php _e('Ex: 100, 100px or 100%', SG_POPUP_TEXT_DOMAIN)?>" pattern = "\d+(([px]+|%)|)" title="<?php _e('It must be number  + px or %', SG_POPUP_TEXT_DOMAIN)  ?>" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-width')) ?>">
		</div>
		<div class="subFormItem<?php echo $subOptionClass; ?>">
			<span class="subFormItem__title"><?php _e('Height', SG_POPUP_TEXT_DOMAIN); ?>:</span>
			<input type="text" id="height" class="subFormItem__input" name="sgpb-height" placeholder="<?php _e('Ex: 100, 100px or 100%', SG_POPUP_TEXT_DOMAIN)?>" pattern = "\d+(([px]+|%)|)" title="<?php _e('It must be number  + px or %', SG_POPUP_TEXT_DOMAIN)  ?>" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-height')) ?>">
		</div>
	</div>
	<div class="formItem bottom">
		<div class="minWidth sgpb-display-inline-flex sgpb-align-item-center sgpb-margin-right-20">
			<span class="formItem__title formItem__title_equals"><?php _e('Max width', SG_POPUP_TEXT_DOMAIN); ?>:</span>
			<input type="text" id="max-width" class="subFormItem__input" name="sgpb-max-width" placeholder="<?php _e('Ex: 100, 100px or 100%', SG_POPUP_TEXT_DOMAIN)?>" pattern = "\d+(([px]+|%)|)" title="<?php _e('It must be number  + px or %', SG_POPUP_TEXT_DOMAIN)  ?>" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-max-width')) ?>">
		</div>
		<div class="maxHeight sgpb-display-inline-flex sgpb-align-item-center">
			<span class="formItem__title formItem__title_equals"><?php _e('Max height', SG_POPUP_TEXT_DOMAIN); ?>:</span>
			<input type="text" id="max-height" class="subFormItem__input" name="sgpb-max-height" placeholder="<?php _e('Ex: 100, 100px or 100%', SG_POPUP_TEXT_DOMAIN)?>" pattern = "\d+(([px]+|%)|)" title="<?php _e('It must be number  + px or %', SG_POPUP_TEXT_DOMAIN)  ?>" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-max-height')) ?>">
		</div>
	</div>
	<div class="formItem">
		<div class="minWidth sgpb-display-inline-flex sgpb-align-item-center sgpb-margin-right-20">
			<span class="formItem__title formItem__title_equals"><?php _e('Min width', SG_POPUP_TEXT_DOMAIN); ?>:</span>
			<input type="text" id="min-width" class="subFormItem__input" name="sgpb-min-width" placeholder="<?php _e('Ex: 100, 100px or 100%', SG_POPUP_TEXT_DOMAIN)?>" pattern = "\d+(([px]+|%)|)" title="<?php _e('It must be number  + px or %', SG_POPUP_TEXT_DOMAIN)  ?>" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-min-width')) ?>">
		</div>
		<div class="maxHeight sgpb-display-inline-flex sgpb-align-item-center">
			<span class="formItem__title formItem__title_equals"><?php _e('Min height', SG_POPUP_TEXT_DOMAIN); ?>:</span>
			<input type="text" id="min-height" class="subFormItem__input" name="sgpb-min-height" placeholder="<?php _e('Ex: 100, 100px or 100%', SG_POPUP_TEXT_DOMAIN)?>" pattern = "\d+(([px]+|%)|)" title="<?php _e('It must be number  + px or %', SG_POPUP_TEXT_DOMAIN)  ?>" value="<?php echo esc_html($popupTypeObj->getOptionValue('sgpb-min-height')) ?>">
		</div>
	</div>
</div>
