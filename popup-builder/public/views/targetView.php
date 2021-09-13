<?php
namespace sgpb;

$targetData = $popupTypeObj->getOptionValue('sgpb-target');
$popupTargetData = ConditionBuilder::createTargetConditionBuilder($targetData);
$type = (!empty($_GET['sgpb_type'])) ? sanitize_text_field($_GET['sgpb_type']) : $popupTypeObj->getOptionValue('sgpb-type');
?>

<div class="popup-conditions-wrapper popup-conditions-target" data-condition-type="target">
	<?php
	$creator = new ConditionCreator($popupTargetData);
	echo $creator->render();
	?>
</div>

<input type="hidden" name="sgpb-type" value="<?php echo esc_html($type); ?>">
<input id="sgpb-is-preview" type="hidden" name="sgpb-is-preview" value="0" autocomplete="off">
<input id="sgpb-is-active" type="hidden" name="sgpb-is-active" value="<?php echo $popupTypeObj->getOptionValue('sgpb-is-active'); ?>" autocomplete="off">
