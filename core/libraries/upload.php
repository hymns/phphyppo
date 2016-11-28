<?php
/**
 * Upload
 *
 * library for file uploading
 * 
 * @package			phpHyppo
 * @subpackage		Application Library
 * @author			Muhammad Hamizi Jaminan
 */
class Upload
{
	/**
	 * Location to upload file
	 */
	public $uploadPath = '';

	/**
	 * Uploaded file name
	 */
	public $uploadedFile = '';

	/**
	 * Type of file to accepted uploading
	 */
	public $fileType = array(
			            'jpg' => 'image/jpeg',
			            'png' => 'image/png',
			            'gif' => 'image/gif',
			            'csv' => 'text/csv',
			            'pdf' => 'application/pdf'
		        	);

	/**
	 * Maximum file size
	 */
	public $maxSize = 1000;

	/**
	 * Error
	 */
	public $error = array();

	/**
	 * execute uploading
	 * 
	 * @access public
	 * @param $field field name
	 * @param $path upload path, leave blank to use global setting
	 */
	public function execute($field, $path = null)
	{
		if ( ! is_null($path) )
			$this->uploadPath = $path;

		try 
		{		   
		    if ( ! isset($_FILES[$field]['error']) || is_array($_FILES[$field]['error']) ) 
		    {
		        throw new RuntimeException('Invalid parameters.');
		    }

		    switch ( $_FILES[$field]['error'] ) 
		    {
				case UPLOAD_ERR_OK:
		            break;
		        case UPLOAD_ERR_NO_FILE:
		            throw new RuntimeException('No file sent.');
		        case UPLOAD_ERR_INI_SIZE:
		        case UPLOAD_ERR_FORM_SIZE:
		            throw new RuntimeException('Exceeded filesize limit.');
		        default:
		            throw new RuntimeException('Unknown errors.');
		    }

		    if ( $_FILES[$field]['size'] > $this->maxSize ) 
		    {
		        throw new RuntimeException('Exceeded filesize limit.');
		    }

		    $finfo = new finfo(FILEINFO_MIME_TYPE);
		    if ( false === $ext = array_search( $finfo->file($_FILES[$field]['tmp_name']), $this->fileType, true) ) 
		    {
		    	throw new RuntimeException('Invalid file format.');
		    }

		    $this->uploadedFile = sprintf($this->uploadPath . '/%s.%s', sha1_file($_FILES[$field]['tmp_name']), $ext);
		    if ( ! move_uploaded_file($_FILES[$field]['tmp_name'], $this->uploadedFile) ) 
		    {
		        throw new RuntimeException('Failed to move uploaded file.');
		    }

		    return true;
		} 
		catch (RuntimeException $e) 
		{			
		    $this->error = $e->getMessage();
		    $this->uploadedFile = '';
		    return false;
		}		
	}
}