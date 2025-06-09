<?php



class StandardClassesReplacementAutoloader
{
	
	static $arrReplaceableClasses = null; 
	
	public static function RegisterAutoloadFunction()
	{
		spl_autoload_register(array(__class__, 'SCRAutoloader'),false,true);
	}
	
	public static function PrepareReplaceableClassesArray()
	{
		if(self::$arrReplaceableClasses === null)
		{
			self::$arrReplaceableClasses = Array(
				strtolower("CAllCrmActivity") => "/modules/crm/classes/general/crm_activity.php",
            );
		}
				
	}
		
	public static function SCRAutoloader($strClassName)
	{
		self::PrepareReplaceableClassesArray();
		
		$strClassNameLC = strtolower($strClassName);
				
		foreach(self::$arrReplaceableClasses as $strCurrClassName => $strCFileName )
		{
			if(substr($strClassNameLC, -1 * strlen ($strCurrClassName)) === $strCurrClassName)
			{
				$strClassFileName = __DIR__ . $strCFileName;
			
				//error_log("-=(StandardClassesReplacementAutoloader[".print_r($strClassFileName,true)."])=-".PHP_EOL, 3, $_SERVER["DOCUMENT_ROOT"]."/wcomm.log");
				
				if (file_exists($strClassFileName))
				{
					//error_log("-=(StandardClassesReplacementAutoloader[1])=-".PHP_EOL, 3, $_SERVER["DOCUMENT_ROOT"]."/wcomm.log");
					
					require_once $strClassFileName;
					
					//error_log("-=(StandardClassesReplacementAutoloader[2])=-".PHP_EOL, 3, $_SERVER["DOCUMENT_ROOT"]."/wcomm.log");
				}
			}
		}
		
	}
	
}