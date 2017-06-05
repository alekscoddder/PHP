<?php
/*
Plugin Name: Selector Categories
Description: Step selector categories
Version: 1.0
Author: Aleksandr Gritsaj
*/

// Function create HTML with categories in drop-down box.
function selector_categories1($atts) {
	// Get a list of categories from the database.
	$categories = get_categories( 
		array(
			'hide_empty' => 0,
			'taxonomy' => 'category',
			'exclude' => '1',
			'parent'  => 0
		) 
	);
	// Create random name for function. This will avoid the coincidence with the name of the functions already available on the page.
	$function_name = 'function_'.substr(md5(rand()), 0, 15); 
	// Create HTML with categories in drop-down box.
	$temp = '<div class="selector_categories">';
	$temp .= '<script>';
	$temp .= 'function '.$function_name.'(select){var sel =  select.options[select.selectedIndex].value; if (sel > 0){document.location = "/selector-page/?selector_categories="+sel;}}';
	$temp .= '</script>';
	$temp .= '<div id="selector_categories_1">';
	$temp .= '<div class="selector_categories_1_title"><span class="selector_categories_1_title_text">SELECT A PRO</span><span class="selector_categories_1_title_icon"></span></div>';
	$temp .= '<div class="selector_categories_1_select">';
	$temp .= '<select onchange="'.$function_name.'(this)">';
	$temp .=  '<option value="0" selected>SELECT A PRO</option>';
	foreach ( $categories as $category ) {
		$temp .=  '<option value="'.$category->term_id.'">'.$category->name.'</option>';
	}
	$temp .= '</select>';
	$temp .= '</div>';
	$temp .= '</div>';
	$temp .= '</div>';
	// Returns the resulting HTML.
	return $temp;
}

// Add shortcode filter for "[selected_categories_1]".
add_shortcode('selector_categories_1', 'selector_categories1');

