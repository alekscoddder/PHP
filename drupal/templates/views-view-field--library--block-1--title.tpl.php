<?php

/**
 * @file
 * This template is used to print a single field in a view.
 *
 * It is not actually used in default Views, as this is registered as a theme
 * function which has better performance. For single overrides, the template is
 * perfectly okay.
 *
 * Variables available:
 * - $view: The view object
 * - $field: The field handler object that can process the input
 * - $row: The raw SQL result that can be used
 * - $output: The processed output that will normally be used.
 *
 * When fetching output from the $row, this construct should be used:
 * $data = $row->{$field->field_alias}
 *
 * The above will guarantee that you'll always get the correct data,
 * regardless of any changes in the aliasing that might happen if
 * the view is modified.
 */
	global $language;
	// Display title with custom link.
?>
<div class="views-field views-field-title">
	<span class="field-content">
		<a href="/<?php print $language->language; ?>/user/<?php print arg(1); ?>/my_media_center/video/<?php print $row->file_managed_field_data_field_download2_fid; ?>"><?php print $output; ?></a>
	</span>
</div>
