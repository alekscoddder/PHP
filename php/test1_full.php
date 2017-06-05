<?php
// Predefined array of exceptions.
class Pattern {
	# These patterns would be normally counted as two syllables but SHOULD be one syllable. May be incomplete; do not modify.
	public $subtract_syllable_patterns = Array(
		"cia(l|$)",
		"tia",
		"cius",
		"cious",
		"[^aeiou]giu",
		"[aeiouy][^aeiouy]ion",
		"iou",
		"sia$",
		"eous$",
		"[oa]gue$",
		".[^aeiuoycgltdb]{2,}ed$",
		".ely$",
		"^jua",
		"uai",
		"eau",
		"[aeiouy](b|c|ch|d|dg|f|g|gh|gn|k|l|ll|lv|m|mm|n|nc|ng|nn|p|r|rc|rn|rs|rv|s|sc|sk|sl|squ|ss|st|t|th|v|y|z)e$",
		"[aeiouy](b|c|ch|dg|f|g|gh|gn|k|l|lch|ll|lv|m|mm|n|nc|ng|nch|nn|p|r|rc|rn|rs|rv|s|sc|sk|sl|squ|ss|th|v|y|z)ed$",
		"[aeiouy](b|ch|d|f|gh|gn|k|l|lch|ll|lv|m|mm|n|nch|nn|p|r|rn|rs|rv|s|sc|sk|sl|squ|ss|st|t|th|v|y)es$",
		"^busi$"
	);
	# These patterns might be counted as one syllable according to $subtract_syllable_patterns
	# and the base rules but SHOULD be two syllables. May be incomplete; do not modify.
	public $add_syllable_patterns = Array(
		"([^s]|^)ia",
		"iu",
		"io",
		"eo($|[b-df-hj-np-tv-z])",
		"ii",
		"[ou]a$",
		"[aeiouym]bl$",
		"[aeiou]{3}",
		"[aeiou]y[aeiou]",
		"^mc",
		"ism$",
		"asm$",
		"thm$",
		"([^aeiouy])\1l$",
		"[^l]lien",
		"^coa[dglx].",
		"[^gq]ua[^auieo]",
		"dnt$",
		"uity$",
		"[^aeiouy]ie(r|st|t)$",
		"eings?$",
		"[aeiouy]sh?e[rsd]$",
		"iell",
		"dea$",
		"real",
		"[^aeiou]y[ae]",
		"gean$",
		"riet",
		"dien",
		"uen"
	);
	# Single syllable prefixes and suffixes. May be incomplete; do not modify.
	public $prefix_and_suffix_patterns = Array(
		"^un",
		"^fore",
		"^ware",
		"^none?",
		"^out",
		"^post",
		"^sub",
		"^pre",
		"^pro",
		"^dis",
		"^side",
		"ly$",
		"less$",
		"some$",
		"ful$",
		"ers?$",
		"ness$",
		"cians?$",
		"ments?$",
		"ettes?$",
		"villes?$",
		"ships?$",
		"sides?$",
		"ports?$",
		"shires?$",
		"tion(ed)?$"
	);
	# Specific common exceptions that don't follow the rule set below are handled individually.
	# The correct syllable count is the value. May be incomplete; do not modify.
	public $problem_words = Array(
		'abalone' => 4,
		'abare' => 3,
		'abed' => 2,
		'abruzzese' => 4,
		'abbruzzese' => 4,
		'aborigine' => 5,
		'acreage' => 3,
		'adame' => 3,
		'adieu' => 2,
		'adobe' => 3,
		'anemone' => 4,
		'apache' => 3,
		'aphrodite' => 4,
		'apostrophe' => 4,
		'ariadne' => 4,
		'cafe' => 2,
		'calliope' => 4,
		'catastrophe' => 4,
		'chile' => 2,
		'chloe' => 2,
		'circe' => 2,
		'coyote' => 3,
		'epitome' => 4,
		'forever' => 3,
		'gethsemane' => 4,
		'guacamole' => 4,
		'hyperbole' => 4,
		'jesse' => 2,
		'jukebox' => 2,
		'karate' => 3,
		'machete' => 3,
		'maybe' => 2,
		'people' => 2,
		'recipe' => 3,
		'sesame' => 3,
		'shoreline' => 2,
		'simile' => 3,
		'syncope' => 3,
		'tamale' => 3,
		'yosemite' => 4,
		'daphne' => 2,
		'eurydice' => 4,
		'euterpe' => 3,
		'hermione' => 4,
		'penelope' => 4,
		'persephone' => 4,
		'phoebe' => 2,
		'zoe' => 2
	);
}

class Readability{
    function ease_score($writing_sample){
		$pattern = new Pattern();
		$syllables = 0;
		$count = 0;
        // Calculate $sentences 
		$tmp1 = preg_replace('/[\.\!\:\;]/', ' ', $writing_sample, -1 , $count);
		$tmp1 = preg_replace('/\,/', '', $tmp1, -1);
		$sentences = $count;
        // Calculate words 
		$words = str_word_count($writing_sample);
        # Calculate score 
		// Processed by an array of exceptions for words.
		foreach($pattern->{'problem_words'} as $key=>$item){
			$tmp1 = preg_replace('/'.$key.'/i', '', $tmp1, -1 , $count);
			$syllables = $syllables + $count * $item;
		}
		$tmp1 = preg_replace('/\s+/', ' ', $tmp1, -1);
		$tmp2 = explode(" ", $tmp1);
		foreach($tmp2 as $word){
			// Processed by an array of exceptions for prefix and suffix.
			foreach($pattern->{'prefix_and_suffix_patterns'} as $item){
				$word = preg_replace('/'.$item.'/i', '', $word, -1 , $count);
				$syllables = $syllables + $count;
			}
			// Processed by an array of exceptions for syllable patterns.
			foreach($pattern->{'add_syllable_patterns'} as $item){
				$word = preg_replace('/'.$item.'/i', '', $word, -1 , $count);
				$syllables = $syllables + $count*2;
			}
			// Processed by an array of exceptions for subtract syllable patterns.
			foreach($pattern->{'subtract_syllable_patterns'} as $item){
				$word = preg_replace('/'.$item.'/i', '', $word, -1 , $count);
				$syllables = $syllables + $count;
			}
			$word = preg_replace('/[aeiouy]{1,2}/i', '', $word, -1 , $count);
			// Calculate syllables 
			$syllables = $syllables + $count;
		}
		// Calculate ASL 
		$asl = $words/$sentences;
		// Calculate ASW 
		$asw = $syllables/$words;
		$answer = 206.835 - (1.015 * $asl) - (84.6 * $asw);
        # Return of 0.0 to 100.0
        // return 9999;
        return round($answer, 1);
    }
}
// Testing calculating
$readability = new Readability();
$text = 'Heavy metals are generally defined as metals with relatively high densities, atomic weights, or atomic numbers. The criteria used, and whether metalloids are included, vary depending on the author and context. In metallurgy, for example, a heavy metal may be defined on the basis of density, whereas in physics the distinguishing criterion might be atomic number, while a chemist would likely be more concerned with chemical behavior. More specific definitions have been published, but none of these have been widely accepted. The definitions surveyed in this article encompass up to 96 out of the 118 chemical elements; only mercury, lead and bismuth meet all of them.';
echo $readability->ease_score($text);
echo "<br />";

$pattern = new Pattern();
echo "Printing size of the first of four pattern arrays: ";
echo sizeof($pattern->{'subtract_syllable_patterns'});

# What PHP version is this?
echo "<br />";
echo 'Current PHP version: ' . phpversion();

?>







