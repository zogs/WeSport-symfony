<?php

namespace My\UtilsBundle\Utils;

use Doctrine\Common\Util\Debug as DoctrineDebug;

class Debug
{
	static function debug($var)
	{	
		ini_set('html_errors', 'On');

        if (extension_loaded('xdebug')) {
            ini_set('xdebug.var_display_max_depth', $maxDepth);
        }

        $debug = debug_backtrace();
		echo '<p>&nbsp;</p><p><a href="#" onclick="document.getElementById(\"backtrace\").style.display=\"block\"; return false;" ><strong>'.$debug[0]['file'].'</strong> Ligne .'.$debug[0]['line'].'</a></p>';
		

		echo '<ol id="backtrace" style="display:none">';
		foreach ($debug as $key => $value) {
			
			if($key>0 && isset($value['file'])){
			echo '<li>'.$value['file'].' ligne '.$value['line'].'</li>';
			}
		}
		echo '</ol>';

		$exported = DoctrineDebug::export($var,2);

		ob_start();
        var_dump($exported);
        $dump = ob_get_contents();
        ob_end_clean();

		echo '<pre>';
		echo strip_tags(html_entity_decode($dump));
		echo '</pre>';

		ini_set('html_errors', 'Off');


	}
}