<?php 

namespace My\UtilsBundle\Utils;

class Object {

	/**
	 * return an key/value array of difference
	 *
	 *@param object $original the original object
	 *@param object $changed the changed object
	 *
	 *@return array(
	 *		'property_name'=>array('original'=>value1,'change'=>value2),
	 *		'property_name2=>...')
	 */
	static public function getChanges($original,$changed)
	{
		if(get_class($original) !== get_class($changed)) throw new \Exception('Class object must be identical ('.get_class($original).' != '.get_class($changed).')');
		if($original instanceof \stdClass) return self::getChangesFromStdClass($original,$changed);
			
		$reflector = new \ReflectionClass($changed);
		$properties = $reflector->getProperties();

		$changes = array();
		foreach ($properties as $property) {			
			$property->setAccessible(true);		

			if($property->getValue($original) != $property->getValue($changed)){				
				$changes[$property->getName()] = array(
					'before' => $property->getValue($original),
					'after' => $property->getValue($changed),
					);
			}			
		}
		return $changes;
	}

	/**
	 * return an key/value array of difference
	 *
	 *@param object $original the original stdobject
	 *@param object $changed the changed stdobject
	 *
	 *@return array(
	 *		'property_name'=>array('original'=>value1,'change'=>value2),
	 *		'property_name2=>...')
	 */
	static public function getChangesFromStdClass($original,$changed)
	{		
		if(!$original instanceof \stdClass) throw new \Exception("Must be a stdClass", 1);

		$changes = array();
		$properties = get_object_vars($original);

		foreach ($properties as $property => $value) {
			if($value != $changed->$property) {
				$changes[$property] = array(
					'before' => $value,
					'after' => $changed->$property,
					);
			}
		}
		return $changes;
		
	}
}