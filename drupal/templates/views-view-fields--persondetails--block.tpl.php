<?php

/**
 * @file
 * Default simple view template to all the fields as a row.
 *
 * - $view: The view in use.
 * - $fields: an array of $field objects. Each one contains:
 *   - $field->content: The output of the field.
 *   - $field->raw: The raw data for the field, if it exists. This is NOT output safe.
 *   - $field->class: The safe class id to use.
 *   - $field->handler: The Views field handler object controlling this field. Do not use
 *     var_export to dump this object, as it can't handle the recursion.
 *   - $field->inline: Whether or not the field should be inline.
 *   - $field->inline_html: either div or span based on the above flag.
 *   - $field->wrapper_prefix: A complete wrapper containing the inline_html to use.
 *   - $field->wrapper_suffix: The closing tag for the wrapper.
 *   - $field->separator: an optional separator that may appear before a field.
 *   - $field->label: The wrap label text to use.
 *   - $field->label_html: The full HTML of the label to use including
 *     configured element type.
 * - $row: The raw result object from the query, with all data it fetched.
 *
 * @ingroup views_templates
 */
 
	// Get the translation of some words from the custom module. 
	$overview_linc_url = 'people';
 	$more_linc_title = get_cst()['more_linc_title'];
	$less_linc_title = get_cst()['less_linc_title'];
	$find_all_linc_title = get_cst()['find_all_linc_title'];

	$link_to_person = "/people";
	if(!empty($row->_field_data['nid']['entity']->field_person_key)){
		$link_to_person = l($find_all_linc_title, 'taxonomy/term/' . $row->_field_data['nid']['entity']->field_person_key['und'][0]["tid"], array('attributes'=>array('class'=>'link_to_peron'))) ;
		$link_title = l($row->node_title, 'taxonomy/term/' . $row->_field_data['nid']['entity']->field_person_key['und'][0]["tid"]) ;
	}
?>

<div class="block_people_in_video">
	<div class="block_people_in_video_panel_wrapper">
		<?php // Displays the person picture field. ?>
		<div class="block_people_in_video_left_panel">
			<div class="block_people_in_video_left_panel">
				<?php print $row->field_field_person_image[0]["rendered"]; ?>
			</div>	
		</div>	
		<div class="block_people_in_video_right_panel">
			<div class="block_people_in_video_top">
				<?php // Displays the field title, date and references to the person. ?>
				<div class="block_people_in_video_title">
					<?php print $row->node_title; ?>
				</div>	
				<?php if($row->field_field_dates[0]["rendered"]) : ?>
					<div class="block_people_in_video_dates">
						<?php print $row->field_field_dates[0]["rendered"]; ?>
					</div>	
				<?php endif; ?>
				<?php if(!empty($row->_field_data['nid']['entity']->field_person_key)) : ?>
					<div class="block_people_in_video_link">
						<?php  print $link_to_person ; ?>
					</div>	
				<?php endif; ?>
			</div>	
			<div class="block_people_in_video_body more_less_field">
				<?php // Displays a field with a description of the person. ?>
				<?php print $row->field_body[0]["rendered"]; ?>
				<div class="more_less_button_block" onclick="jQuery(this).parent().toggleClass('more_description').toggleClass('less_description');">
					<span class="mlb_more_button"><?php print $more_linc_title; ?></span>
					<span class="mlb_less_button"><?php print $less_linc_title; ?></span>
				</div>
			</div>	
		</div>	
	</div>	
</div>	
	
