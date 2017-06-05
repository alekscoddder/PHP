<?php

/*
Template Name: Browse Pro
*/

	global $wp;
	$ID = get_the_ID();
	$current_url = get_permalink( $ID );
	get_header();
	$is_page_builder_used = et_pb_is_pagebuilder_used(get_the_ID());
	$selector_categories = 0;
	$sub = 0;
	$sub_cat = 0;
	$sub_sub_cat = 0;
	$zip = '';
	$val1 = '';
	$description = '<div id="description_of_categories" style="display: none;">';
	// Checking whether the category is selected previously.
	if (isset($_GET['selector_categories'])){
		$selector_categories = $_GET['selector_categories'];
	}
	if (isset($_GET['selector_subcategories'])){
		$sub = $_GET['selector_subcategories'];
	}
	if (isset($_GET['sub_cat'])){
		$sub_cat = $_GET['sub_cat'];
	}
	if (isset($_GET['sub_sub_cat'])){
		$sub_sub_cat = $_GET['sub_sub_cat'];
	}
	if (isset($_GET['zip'])){
		$zip = $_GET['zip'];
	}
	if ($zip == '') {
		$urlzip = '';
	}
	else {
		$urlzip = '&zip='.$zip;
	}
    $variables = array (
		'current_url' => $current_url,
		'selector_categories' => $selector_categories,
		'selector_subcategories' => $sub,
		'sub_cat' => $sub_cat,
		'sub_sub_cat' => $sub_sub_cat,
		'zip' => $zip,
		'urlzip' => $urlzip,
    );
    echo '<script type="text/javascript">window.wp_data = '.json_encode($variables).';</script>';
	$categories = get_categories( 
		array(
			'hide_empty' => 0,
			'taxonomy' => 'professional_categories',
			'exclude' => '1',
			'parent'  => 0
		) 
	);
	// Create random name for function. This will avoid the coincidence with the name of the functions already available on the page.
	$function_name = 'function_'.substr(md5(rand()), 0, 15);
	echo '<script>';
	echo 'function '.$function_name.'(select){var sel =  select.options[select.selectedIndex].value;if (sel > 0){document.location = window.wp_data.current_url + "?selector_categories="+sel+window.wp_data.urlzip;}}';
	echo '</script>';
?>

<div id="main-content">
	<div id="browseproheader" class="et_pb_section pageheader et_pb_section_0 et_pb_with_background et_section_regular et_pb_section_first">
		<div class="et_pb_row et_pb_row_0 et_pb_row_1">
			<div class="et_pb_column et_pb_column_1 et_pb_column_3">
				<form role="search" action="<?php echo site_url('/'); ?>" method="get" id="searchform" class="prosearchform">
					<select onchange="<?php echo $function_name; ?>(this)" class="filters-select selector_categories">
					<?php
					// Creating options for drop-down select.
						if ($selector_categories == 0) {
							echo '<option value="" selected>FILTER BY ISSUE</option>';
						}
						else {
							echo '<option value="">FILTER BY ISSUE</option>';
						}
						foreach($categories as $profession) {
							$sel = '';
							if ($selector_categories == $profession->term_id) {
								$sel .=  ' selected';
							}
							echo '<option value="'.$profession->term_id.'"'.$sel.'>'.strtoupper($profession->name).'</option>';
							$description .= '<div class="description_id_'.$profession->term_id.'">'.$profession->description.'</div>';
						}
					?>
					</select>
					<?php if ($selector_categories>0) : ?>
					<?php // category
						$categories = get_categories( 
							array(
								'hide_empty' => 0,
								'taxonomy' => 'professional_categories',
								'exclude' => '1',
								'parent'  => $selector_categories
							) 
						);
						// Create random name for function. This will avoid the coincidence with the name of the functions already available on the page.
						$function_name = 'function_'.substr(md5(rand()), 0, 15);
						echo '<script> function '.$function_name.'(select){var subsel =  select.options[select.selectedIndex].value; if (subsel > 0){document.location = window.wp_data.current_url + "?selector_categories='.$selector_categories.'&selector_subcategories="+subsel+window.wp_data.urlzip;}}</script>';
					?>	
					<select onchange="<?php echo $function_name; ?>(this)" class="filters-select selector_subcategories">
					<?php
						// Creating options for drop-down select.
						if ($sub == 0) {
							echo '<option value="" selected>CATEGORIES</option>';
						}
						else {
							echo '<option value="">CATEGORIES</option>';
						}
						foreach($categories as $tag) {
							$sel = '';
							if ($sub == $tag->term_id) {
								$sel .=  ' selected';
							}
							echo '<option value="'.$tag->term_id.'"'.$sel.'>'.strtoupper($tag->name).'</option>';
							$description .= '<div class="description_id_'.$tag->term_id.'">'.$tag->description.'</div>';
						}
					?>
					</select>
					<?php endif; ?>	
					
					<?php // sub-category 
					if ($sub>0) {
						
						$categories = get_categories( 
							array(
								'hide_empty' => 0,
								'taxonomy' => 'professional_categories',
								'exclude' => '1',
								'parent'  => $sub
							) 
						);
						
						if (!empty($categories)){
							// Create random name for function. This will avoid the coincidence with the name of the functions already available on the page.
							$function_name = 'function_'.substr(md5(rand()), 0, 15);
							echo '<script> function '.$function_name.'(select){
								var sub_cat =  select.options[select.selectedIndex].value;
								if (sub_cat > 0){
									document.location = window.wp_data.current_url + "?selector_categories='.$selector_categories.'&selector_subcategories='.$sub.'&sub_cat="+sub_cat+window.wp_data.urlzip;
								}
							}</script>';
							echo '<select onchange="'.$function_name.'(this)" class="filters-select sub_cat">';
							// Creating options for drop-down select.
							if ($sub_cat == 0) {
								echo '<option value="" selected>SUB-CATEGORIES</option>';
							}
							else {
								echo '<option value="">SUB-CATEGORIES</option>';
							}
							foreach($categories as $tag) {
								$sel = '';
								if ($sub_cat == $tag->term_id) {
									$sel .=  ' selected';
								}
								echo '<option value="'.$tag->term_id.'"'.$sel.'>'.strtoupper($tag->name).'</option>';
								$description .= '<div class="description_id_'.$tag->term_id.'">'.$tag->description.'</div>';
							}
							echo '</select>';
						}
					} ?>	
					
					<?php // sub-sub-category 
					if ($sub_cat>0) {
						
						$categories = get_categories( 
							array(
								'hide_empty' => 0,
								'taxonomy' => 'professional_categories',
								'exclude' => '1',
								'parent'  => $sub_cat
							) 
						);
						
						if (!empty($categories)){
							// Create random name for function. This will avoid the coincidence with the name of the functions already available on the page.
							$function_name = 'function_'.substr(md5(rand()), 0, 15);
							echo '<script> function '.$function_name.'(select){
								var sub_sub_cat =  select.options[select.selectedIndex].value;
								if (sub_sub_cat > 0){
									document.location = window.wp_data.current_url + "?selector_categories='.$selector_categories.'&selector_subcategories='.$sub.'&sub_cat='.$sub_cat.'&sub_sub_cat="+sub_sub_cat+window.wp_data.urlzip;
								}
							}</script>';
							echo '<select onchange="'.$function_name.'(this)" class="filters-select sub_sub_cat">';
							// Creating options for drop-down select.
							if ($sub_sub_cat == 0) {
								echo '<option value="" selected>SUB-SUB-CATEGORIES</option>';
							}
							else {
								echo '<option value="">SUB-SUB-CATEGORIES</option>';
							}
							foreach($categories as $tag) {
								$sel = '';
								if ($sub_sub_cat == $tag->term_id) {
									$sel .=  ' selected';
								}
								echo '<option value="'.$tag->term_id.'"'.$sel.'>'.strtoupper($tag->name).'</option>';
								$description .= '<div class="description_id_'.$tag->term_id.'">'.$tag->description.'</div>';
							}
							echo '</select>';
						}
					} ?>	
						
					
					<?php if ($zip !=''){$val1 = ' value="'.$zip.'"';} ?>	
				    <input class="prosearchtxt" type="text" name="s" placeholder="LOCATED IN (ZIP CODE)"<?php echo $val1;?>/>
				    <input type="hidden" name="post_type" value="professionals" /> <!-- // hidden 'products' value -->
					<?php
						// Create random name for function. This will avoid the coincidence with the name of the functions already available on the page.
						$function_name = 'function_'.substr(md5(rand()), 0, 15);
						echo '<script> 
						function '.$function_name.'(){
							var zipval =  document.getElementsByClassName("prosearchtxt")[0].value;
							if (zipval != ""){
								document.location = window.wp_data.current_url + "?selector_categories='.$selector_categories.'&selector_subcategories='.$sub.'&sub_cat='.$sub_cat.'&zip="+zipval;
							}
							else{
								document.location = window.wp_data.current_url + "?selector_categories='.$selector_categories.'&selector_subcategories='.$sub.'&sub_cat='.$sub_cat.'";
							}
						}
						</script>';
					?>	
				    <input class="prosearchbtn" onclick="<?php echo $function_name; ?>()" type="button" alt="Search" value="Search" />
			  	</form>
			</div>
		</div> <!-- .et_pb_row -->
	</div>
	<?php	
		if ($selector_categories>0) {
			if ($sub > 0) {
				if ($sub_cat > 0) {
					if ($sub_sub_cat > 0) {
						$cat = $sub_sub_cat;
					}
					else {
						$cat = $sub_cat;
					}
				}
				else {
					$cat = $sub;
				}
			}
			else {
				$cat = $selector_categories;
			}
		}	
		if(($selector_categories == 0) && ($sub == 0) && ($sub_cat == 0) && ($sub_sub_cat == 0)){
			if ($zip ==''){
				$query_args = array( 
					'posts_per_page' => 5,
					'post_type' => 'professional',
					'orderby' => 'date',
					'order' => 'DESC',
					'paged' => get_query_var('paged'),
				);
				$val1 = ' value="'.$zip.'"';
			}
			else {
				$query_args = array( 
					'posts_per_page' => 5,
					'post_type' => 'professional',
					'orderby' => 'date',
					'order' => 'DESC',
					'paged' => get_query_var('paged'),
					'meta_key'		=> 'address',
					'meta_value'	=> $zip,
					'meta_compare' 	=> 'LIKE',
				);
				
			}
			  // create a new instance of WP_Query
			  $the_query = new WP_Query( $query_args );
		?>
		<div class="proinfowrapper et_pb_section et_pb_section_1 et_section_regular">
		<?php if ( $the_query->have_posts() ) : while ( $the_query->have_posts() ) : $the_query->the_post(); // run the loop ?>
			<div class="proinfocontainer et_pb_row et_pb_row_2">
				<div class="et_pb_column et_pb_column_2_3 et_pb_column_2">
					<div class="et_pb_column et_pb_column_1_4 et_pb_column_0">
						<a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_post_thumbnail('pro-thumb', array('class' => 'alignleft imgcircle')); ?></a>
					</div>
					<div class="et_pb_column et_pb_column_3_4 et_pb_column_1">	
						<h2><a class="protitle bluegreentext" href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php echo the_title(); ?></a></h2>
						<h3 class="proprofession"><?php the_field( 'professional_title' ); ?></h3>
						<div class="excerpt"><?php echo limitwords( get_the_excerpt(), 40, '...' ); ?></div>
					</div>
				</div>
				<div class="et_pb_column et_pb_column_1_3 et_pb_column_3 probtnaddress">
					<h4 class="proaddress"><?php the_field( 'address' ); ?></h4>
					<a href="<?php the_permalink(); ?>" class="et_pb_more_button et_pb_button et_pb_button_one probutton">VIEW PROFILE AND SCHEDULE</a>
				</div>
			</div>
		<?php wp_reset_query(); ?>
		<?php endwhile; ?>
		<?php endif; ?>	
		</div>
		<div class="et_pb_section  et_pb_section_1 et_section_regular pronavcontainer">
			<div class=" et_pb_row et_pb_row_2">
				<?php if ($the_query->max_num_pages > 1) { // check if the max number of pages is greater than 1  ?>
					<?php wp_pagenavi(array('query' => $the_query )); ?>
				<?php } ?>
			</div>
		</div>
	<?php } else if (($selector_categories > 0) && ($sub < 0)) { ?>
		<div class="custom-blank"><h2 class="titletwo centertext bluegreentext">Please select a sub-category</h2></div>	
	<?php } ?>
	<?php if ($selector_categories>0): ?>
	<div class="entry-content">
	<?php
		$tax=array();
		$tax = array(
				array(
					'taxonomy' => 'professional_categories',
					'field'    => 'term_id',
					'terms'    => $cat,
				),
			);
		if ($zip ==''){
			$args = array( 
				'posts_per_page' => 5,
				'tax_query' => $tax,
				'orderby' => 'date',
				'order' => 'ASC',
				'paged' => get_query_var('paged'),
			);
		}
		else {
			$args = array( 
				'posts_per_page' => 5,
				'tax_query' => $tax,
				'orderby' => 'date',
				'order' => 'ASC',
				'paged' => get_query_var('paged'),
				'meta_key'		=> 'address',
				'meta_value'	=> $zip,
				'meta_compare' 	=> 'LIKE',
			);
		}
		$the_query = new WP_Query($args);
	?>
		<div class="proinfowrapper et_pb_section et_pb_section_1 et_section_regular">
			<?php if ( $the_query->have_posts() ) : while ( $the_query->have_posts() ) : $the_query->the_post(); // run the loop ?>
				<div class="proinfocontainer et_pb_row et_pb_row_2">
					<div class="et_pb_column et_pb_column_2_3 et_pb_column_2">
						<div class="et_pb_column et_pb_column_1_4 et_pb_column_0">
							<a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_post_thumbnail('pro-thumb', array('class' => 'alignleft imgcircle')); ?></a>
						</div>

						<div class="et_pb_column et_pb_column_3_4 et_pb_column_1">	
							<h2><a class="protitle bluegreentext" href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php echo the_title(); ?></a></h2>
							<h3 class="proprofession"><?php the_field( 'professional_title' );?></h3>
							<div class="excerpt"><?php echo limitwords( get_the_excerpt(), 40, '...' ); ?></div>
						</div>
					</div>
					<div class="et_pb_column et_pb_column_1_3 et_pb_column_3 probtnaddress">
						<h4 class="proaddress"><?php the_field( 'address' ); ?></h4>
						<a href="<?php the_permalink(); ?>" class="et_pb_more_button et_pb_button et_pb_button_one probutton">VIEW PROFILE AND SCHEDULE</a>
					</div>
				</div>
			<?php wp_reset_query(); ?>
	 		<?php endwhile; ?>
			<?php endif; ?>	
			</div>
		<div class="et_pb_section  et_pb_section_1 et_section_regular pronavcontainer">
			<div class=" et_pb_row et_pb_row_2">
				<?php if ($the_query->max_num_pages > 1) { // check if the max number of pages is greater than 1  ?>
					<?php wp_pagenavi(array('query' => $the_query )); ?>
				<?php } ?>
			</div>
		</div>	
	</div>
	<?php endif; ?>	
</div>
<?php $description .= '</div>'; ?>
<?php echo $description; ?>
<script>
	jQuery( document ).ready(function() {
		jQuery("select.filters-select").ikSelect()
		var m = document.getElementsByClassName('ik_select_option');
		for (i=0 ; i < m.length ; i++){
			n = m[i].getAttribute('data-value');
			if (n != '') {
				jQuery('.ik_select_option[data-value="'+n+'"]').append('<div class="select_item"><div class="select_item_icon">i</div><div class="select_item_description">' + jQuery('.description_id_'+n)[0].innerHTML + '</div></div>');
			}
		}
	});
</script>

<?php get_footer(); ?>
