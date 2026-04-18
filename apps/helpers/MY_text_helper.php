<?php if (!defined('BASEPATH')) exit('No direct script access allowed.');

/**
 * CodeIgniter Text Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/helpers/text_helper.html
 */

// ------------------------------------------------------------------------

function nl2p($str)
{
  return str_replace('<p></p>', '', '<p>'
        . nl2br(preg_replace('#([\r\n]\s*?[\r\n]){2,}#', '</p>$0<p>', $str))
        . '</p>');
}

// ------------------------------------------------------------------------

/**
 * Returns a string with all spaces converted to underscores (by default), accented
 * characters converted to non-accented characters, and non word characters removed.
 *
 * @param string $string the string you want to slug
 * @param string $replacement will replace keys in map
 * @return string
 * @access public
 */
function convert_accented_characters($string, $replacement = '-')
{
  $string = strtolower($string);
  
  $foreign_characters = array(
  '/ä|æ|ǽ/' => 'ae',
  '/ö|œ/' => 'oe',
  '/ü/' => 'ue',
  '/Ä/' => 'Ae',
  '/Ü/' => 'Ue',
  '/Ö/' => 'Oe',
  '/À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ/' => 'A',
  '/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª/' => 'a',
  '/Ç|Ć|Ĉ|Ċ|Č/' => 'C',
  '/ç|ć|ĉ|ċ|č/' => 'c',
  '/Ð|Ď|Đ/' => 'D',
  '/ð|ď|đ/' => 'd',
  '/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě/' => 'E',
  '/è|é|ê|ë|ē|ĕ|ė|ę|ě/' => 'e',
  '/Ĝ|Ğ|Ġ|Ģ/' => 'G',
  '/ĝ|ğ|ġ|ģ/' => 'g',
  '/Ĥ|Ħ/' => 'H',
  '/ĥ|ħ/' => 'h',
  '/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ/' => 'I',
  '/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı/' => 'i',
  '/Ĵ/' => 'J',
  '/ĵ/' => 'j',
  '/Ķ/' => 'K',
  '/ķ/' => 'k',
  '/Ĺ|Ļ|Ľ|Ŀ|Ł/' => 'L',
  '/ĺ|ļ|ľ|ŀ|ł/' => 'l',
  '/Ñ|Ń|Ņ|Ň/' => 'N',
  '/ñ|ń|ņ|ň|ŉ/' => 'n',
  '/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ/' => 'O',
  '/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º/' => 'o',
  '/Ŕ|Ŗ|Ř/' => 'R',
  '/ŕ|ŗ|ř/' => 'r',
  '/Ś|Ŝ|Ş|Š/' => 'S',
  '/ś|ŝ|ş|š|ſ/' => 's',
  '/Ţ|Ť|Ŧ/' => 'T',
  '/ţ|ť|ŧ/' => 't',
  '/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ/' => 'U',
  '/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ/' => 'u',
  '/Ý|Ÿ|Ŷ/' => 'Y',
  '/ý|ÿ|ŷ/' => 'y',
  '/Ŵ/' => 'W',
  '/ŵ/' => 'w',
  '/Ź|Ż|Ž/' => 'Z',
  '/ź|ż|ž/' => 'z',
  '/Æ|Ǽ/' => 'AE',
  '/ß/'=> 'ss',
  '/Ĳ/' => 'IJ',
  '/ĳ/' => 'ij',
  '/Œ/' => 'OE',
  '/ƒ/' => 'f'
  );
  
  if (is_array($replacement))
  {
    $map = $replacement;
    $replacement = '_';
  }
  
  $quotedReplacement = preg_quote($replacement, '/');
  
  $merge = array(
                '/[^\s\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}]/mu' => ' ',
                '/\\s+/' => $replacement,
                sprintf('/^[%s]+|[%s]+$/', $quotedReplacement, $quotedReplacement) => '',
                );
  
  $map = $foreign_characters + $merge;
  return preg_replace(array_keys($map), array_values($map), $string);
}

// ------------------------------------------------------------------------

function escape_tags($string)
{
	return str_replace(array('{', '}'), array('&#123;', '&#125;'), $string);
}

// ------------------------------------------------------------------------

/**
 * Proccess data JMR1
 *
 * Minifying final HTML output
 *
 * @access	public
 * @param	string	The HTML output
 * @return	string  The HTML without white spaces or the input text if its is too big to your SO proccess.
 * @author	Alan Moore, ridgerunner <http://stackoverflow.com/q/5312349>
 * @author	Marcos Coelho <marcos@marcoscoelho.com>
 */

// Set PCRE recursion limit to sane value = STACKSIZE / 500 (256KB stack. Win32 Apache or  8MB stack. *nix)
ini_set('pcre.recursion_limit', (strtolower(substr(PHP_OS, 0, 3)) === 'win' ? '524' : '16777'));

function process_data_jmr1($text = '')
{
    $re = '%                                # Collapse whitespace everywhere but in blacklisted elements.
        (?>                                 # Match all whitespans other than single space.
          [^\S]\s*                          # Either one [\t\r\n\f\v] and zero or more ws,
          |\s{2,}                           # or two or more consecutive-any-whitespace.
        )				                    # Note: The remaining regex consumes no text at all...
        (?=                                 # Ensure we are not in a blacklist tag.
          [^<]*+                            # Either zero or more non-"<" {normal*}
          (?:                               # Begin {(special normal*)*} construct
            <                               # or a < starting a non-blacklist tag.
            (?!/?(?:textarea|pre|script)\b)
            [^<]*+                          # more non-"<" {normal*}
          )*+                               # Finish "unrolling-the-loop"
          (?:                               # Begin alternation group.
            <                               # Either a blacklist start tag.
            (?>textarea|pre|script)\b
            |\z                             # or end of file.
          )                                 # End alternation group.
        )                                   # If we made it here, we are not in a blacklist tag.
        %Six';

    if (($data = preg_replace($re, ' ', $text)) === NULL)
	{
		log_message('error', 'PCRE Error! Output of the page "' . uri_string() . '" is too big.');

		return $text;
	}

    return $data;
}

if ( ! function_exists('escape_chars'))
{
  function escape_chars($str,$esc_chars='"')
  {
	  
	  return htmlentities($str ?? '');
  }
} 

if (!function_exists('pad_number')) {
  function pad_number($number, $pad_num = 6, $replace = '0') {
    return str_pad($number, $pad_num, $replace, STR_PAD_LEFT);
  }
}