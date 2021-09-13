<?php
use sgpb\AdminHelper;
$defaultData = ConfigDataHelper::defaultData();

$deleteData = '';
if (get_option('sgpb-dont-delete-data')) {
	$deleteData = 'checked';
}

$enableDebugMode = '';
if (get_option('sgpb-enable-debug-mode')) {
	$enableDebugMode = 'checked';
}

$systemInfo = AdminHelper::getSystemInfoText();
$userSavedRoles = get_option('sgpb-user-roles');
?>

<div class="sgpb sgpb-wrapper">
	<div class="sgpb-generalSettings sgpb-display-flex sgpb-padding-30">
		<div class="sgpb-width-50 sgpb-padding-20">
			<p class="sgpb-header-h1 sgpb-margin-top-20 sgpb-margin-bottom-50"><?php _e('General Settings', SG_POPUP_TEXT_DOMAIN); ?></p>
			<form method="POST" action="<?php echo admin_url().'admin-post.php?action=sgpbSaveSettings'?>">
				<div class="formItem">
					<p class="subFormItem__title sgpb-flex-220"><?php _e('Enable DEBUG MODE', SG_POPUP_TEXT_DOMAIN)?>:</p>
					<div class="sgpb-onOffSwitch">
						<input type="checkbox" name="sgpb-enable-debug-mode" class="sgpb-onOffSwitch-checkbox" id="sgpb-enable-debug-mode" <?php echo $enableDebugMode; ?>>
						<label class="sgpb-onOffSwitch__label" for="sgpb-enable-debug-mode">
							<span class="sgpb-onOffSwitch-inner"></span>
							<span class="sgpb-onOffSwitch-switch"></span>
						</label>
					</div>
				</div>
				<div class="formItem">
					<span class="subFormItem__title sgpb-flex-220"><?php _e('Delete popup data', SG_POPUP_TEXT_DOMAIN)?>:</span>
					<div class="sgpb-onOffSwitch">
						<input type="checkbox" name="sgpb-dont-delete-data" class="sgpb-onOffSwitch-checkbox" id="sgpb-dont-delete-data" <?php echo $deleteData; ?>>
						<label class="sgpb-onOffSwitch__label" for="sgpb-dont-delete-data">
							<span class="sgpb-onOffSwitch-inner"></span>
							<span class="sgpb-onOffSwitch-switch"></span>
						</label>
					</div>
					<div class="question-mark">B</div>
					<div class="sgpb-info-wrapper">
						<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
							<?php _e('All the popup data will be deleted after removing the plugin if this option is checked.', SG_POPUP_TEXT_DOMAIN)?>
						</span>
					</div>
				</div>

				<div class="formItem">
					<span class="subFormItem__title sgpb-flex-220"><?php _e('Disable popup analytics', SG_POPUP_TEXT_DOMAIN)?>:</span>
					<div class="sgpb-onOffSwitch">
						<input type="checkbox" name="sgpb-disable-analytics-general" class="sgpb-onOffSwitch-checkbox" id="sgpb-disable-analytics-general"<?php echo (get_option('sgpb-disable-analytics-general')) ? ' checked' : ''; ?>>
						<label class="sgpb-onOffSwitch__label" for="sgpb-disable-analytics-general">
							<span class="sgpb-onOffSwitch-inner"></span>
							<span class="sgpb-onOffSwitch-switch"></span>
						</label>
					</div>
				</div>
				<div class="formItem">
					<div class="subFormItem__title sgpb-flex-220"><?php _e('User role to access the plugin', SG_POPUP_TEXT_DOMAIN)?></div>
					<?php echo AdminHelper::createSelectBox($defaultData['userRoles'], $userSavedRoles, array('name'=>'sgpb-user-roles[]', 'class' => 'js-sg-select2 js-select-ajax ', 'multiple'=> 'multiple', 'size'=> count($defaultData['userRoles'])));?>
					<div class="question-mark">B</div>
					<div class="sgpb-info-wrapper">
						<span class="infoSelectRepeat samefontStyle sgpb-info-text" style="display: none;">
							<?php _e('In spite of user roles the administrator always has access to the plugin.', SG_POPUP_TEXT_DOMAIN)?>
						</span>
					</div>
				</div>
				<input type="submit" class="saveCHangeButton sgpb-btn sgpb-btn-blue" value="<?php _e('Save Changes', SG_POPUP_TEXT_DOMAIN)?>" >
			</form>
		</div>
		<div class="sgpb-width-50 sgpb-padding-20 sgpb-shadow-black sgpb-border-radius-5px">
			<p class="sgpb-header-h1 sgpb-margin-top-20 sgpb-margin-bottom-50"><?php _e('Debug tools', SG_POPUP_TEXT_DOMAIN)?>:</p>
			<div class="formItem">
				<span class="formItem__title"><?php _e('System information', SG_POPUP_TEXT_DOMAIN)?>:</span>
			</div>
			<div class="formItem">
				<textarea onclick="this.select();" rows="20" class="formItem__textarea" readonly><?php echo $systemInfo ;?></textarea>
			</div>
			<input type="button" class="sgpb-download-system-info saveCHangeButton sgpb-btn sgpb-btn-blue" value="<?php _e('Download', SG_POPUP_TEXT_DOMAIN)?>">
		</div>
	</div>
</div>
