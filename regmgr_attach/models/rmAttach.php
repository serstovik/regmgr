<?php
/** //** ----= CLASS rmAttach	=--------------------------------------------------------------------------------------\**//** \
 *
 * @package        Morweb
 * @subpackage
 * @category
 *
 * \**//** ---------------------------------------------------------------------= by SerStoVik @ Morad Media Inc. =----/** //**/
class rmAttach  extends vDBObject
{

	//	static public $statusNew	= 'new';

// ---- SETTINGS ----

	public	$Table			= 'regmgr_attachments'; 	// DB table to use.


	public	$id			= 0;				// DB id.
	public	$sn			= '';				// Application unique SN.

	public 	$files			= [];

	public	$modified		= '';				// |
	public	$created		= '';				// |- Creation and last modification dates.

	function __construct () {

		$this
			->setField('id')->Validations('isID')
			->setField('sn')->Validations('trim|isSN(A)')
			->setField('files')->SZ()
			->setField('modified')->Validations('')->DB(true, VDBO_DB_READ)->dbType('timestamp', false, true, true)
			->setField('created')->Validations('')->DB(true, VDBO_DB_READ)->dbType('timestamp', false, 'CURRENT_TIMESTAMP', false); //OBJECT $this

		; //Object $this

	}

	function uploadAndSaveDocument() {

		if( is_array($_FILES) and count($_FILES) > 1) {
			foreach($_FILES as $name => $file) {
				if($file['name'] && $file['type']){
					$fileName = $this->uploadImages($name);
					if ($fileName)
						$_POST['files'][]	= $fileName;
				} //IF
			}//FOREACH
		}//IF

		elseif(is_array($_FILES) and count($_FILES) == 1) {
			if ($_FILES['name'] && $_FILES['type'])
				$fileName = $this->uploadImages($_FILES['name']);
				if ($fileName)
					$_POST['files'][]	= $fileName;

		}//IF
		
		$this->sn	=  $_POST['sn'];

		// Making sure table is up to date
		$this->createTable()->updateTable();

		// And saving data into DB
		$this->fromArray($_POST)->toDB();

	}//FUNC uploadSingleFile
	
	function getFiles($sn){

		$sql	= "SELECT files FROM {$this->Table} WHERE sn = '{$sn}'";
		return mwDB()->query($sql)->asRow();

	}

	function uploadImages($name) {

		$UC = newUploadController()
			->MaxSize(20000)
			->RandomName(true)
			->Required(false)
			->Extensions('png')
			->fromField($name)
			->Destination(doPath(SITE_FILES . "/regmgr"))
			->GO(); //OBJECT $UC

		if($UC->isFile()) {
			return $UC->File->Base();
		}
		else {

			$msg = $UC->__messages['type_not_allowed'];
			$result = array($name, $msg);

			return $result;
		}//IF

	}//FUNC uploadImages
	
	function test(){
		__($this);
		__('this is just a  test function nothing else');
	}



} //CLASS rmAttach