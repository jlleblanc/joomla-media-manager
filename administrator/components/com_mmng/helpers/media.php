<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

class MediaHelper
{
	/** @var array Hard-coded file extension to MIME type map, to serve as a fallback */ 
	protected static $mimeMap = array( 
		"ai"	=> "application/postscript",
		"avi"	=> "video/x-msvideo",
		"bmp"	=> "image/bmp",
		"djv"	=> "image/vnd.djvu",
		"djvu"	=> "image/vnd.djvu",
		"dvi"	=> "application/x-dvi",
		"doc"	=> "application/msword",
		"eps"	=> "application/postscript",
		"gif"	=> "image/gif",
		"ico"	=> "image/x-icon",
		"ief"	=> "image/ief",
		"jpe"	=> "image/jpeg",
		"jpeg"	=> "image/jpeg",
		"jpg"	=> "image/jpeg",
		"mov"	=> "video/quicktime",
		"mpg"	=> "video/mpeg",
		"mpe"	=> "video/mpeg",
		"mpeg"	=> "video/mpeg",
		"mxu"	=> "video/vnd.mpegurl",
		"odc"	=> "application/vnd.oasis.opendocument.chart",
		"odf"	=> "application/vnd.oasis.opendocument.formula",
		"odg"	=> "application/vnd.oasis.opendocument.graphics",
		"odi"	=> "application/vnd.oasis.opendocument.image",
		"odp"	=> "application/vnd.oasis.opendocument.presentation",
		"ods"	=> "application/vnd.oasis.opendocument.spreadsheet",
		"odt"	=> "application/vnd.oasis.opendocument.text",
		"pbm"	=> "image/x-portable-bitmap",
		"pdf"	=> "application/pdf",
		"pgm"	=> "image/x-portable-graymap",
		"png"	=> "image/png",
		"pnm"	=> "image/x-portable-anymap",
		"ppm"	=> "image/x-portable-pixmap",
		"ppt"	=> "application/vnd.ms-powerpoint",
		"ps"	=> "application/postscript",
		"qt"	=> "video/quicktime",
		"ras"	=> "image/x-cmu-raster",
		"rgb"	=> "image/x-rgb",
		"swf"	=> "application/x-shockwave-flash",
		"tif"	=> "image/tif",
		"tiff"	=> "image/tiff",
		"vsd"	=> "application/x-visio",
		"wbmp"	=> "image/vnd.wap.wbmp",	
		"xbm"	=> "image/x-xbitmap",
		"xcf"	=> "image/x-xcf",
		"xls"	=> "application/vnd.ms-excel",
		"xpm"	=> "image/x-xpixmap",
		"xwd"	=> "image/x-windowdump",
		"zip"	=> "application/zip"
	);
		
	/**
	 * Detects the MIME type of a given file
	 * @param string $fileName The full path to the name whose MIME type you want to find out
	 * @return string The MIME type, e.g. image/jpeg
	 */
	static function getMimeType( $fileName )
	{
		$mime = null;
		
		// Try fileinfo first
		if( function_exists('finfo_open') )
		{
			$finfo = finfo_open(FILEINFO_MIME);
			if( $finfo !== false )
			{
				$mime = finfo_file($finfo, $fileName);
				finfo_close($finfo);
			}
		}
		
		// Fallback to mime_content_type() if finfo didn't work
		if( is_null($mime) && function_exists('mime_content_type') )
		{
			$mime = mime_content_type($fileName);
		}
		
		// Final fallback, detection based on extension
		if( is_null($mime) )
		{
			$extension = self::getTypeIcon(getTypeIcon);
			if(array_key_exists($extension, self::$mimeMap))
			{
				$mime = self::$mimeMap[$extension];
			}
			else
			{
				$mime = "application/octet-stream";
			}
		}
		
		return $mime;
	}
	
	/**
	 * Checks if the file is an image, based on its extension. Compatibility w/ Joomla! 1.6.
	 * @param string The filename
	 * @return boolean
	 */
	static function isImage( $fileName )
	{
		$extension = self::getTypeIcon(getTypeIcon);
		return in_array(
			$extension,
			array(
				"ai", "bmp", "djv", "djvu", "dvi", "eps", "gif", "ico", "ief", "jpe", "jpeg",
				"jpg", "odg", "odi", "pbm", "pgm", "png", "pnm", "ppm", "ps", "ras", "rgb",
				"tif", "tiff", "wbmp", "xbm", "xcf", "xpm", "xwd"
			)
		);		
	}
	
	/**
	 * Returns the file extension. Compatibility w/ Joomla! 1.6.
	 * @param string The filename
	 * @return boolean
	 */
	function getTypeIcon( $fileName )
	{
		$nameParts = explode('.', $fileName);
		return strtolower( array_pop($nameParts) );
	}
	
	/**
	 * Checks if the file can be uploaded.
	 *
	 * @param array File information
	 * @param string An error message to be returned
	 * @return boolean
	 */
	function canUpload( $file, &$err )
	{
		$params = &JComponentHelper::getParams( 'com_mmng' );

		if(empty($file['name'])) {
			$err = 'Please input a file for upload';
			return false;
		}

		jimport('joomla.filesystem.file');
		if ($file['name'] !== JFile::makesafe($file['name'])) {
			$err = 'WARNFILENAME';
			return false;
		}

		$format = strtolower(JFile::getExt($file['name']));

		$allowable = explode( ',', $params->get( 'upload_extensions' ));
		$ignored = explode(',', $params->get( 'ignore_extensions' ));
		if (!in_array($format, $allowable) && !in_array($format,$ignored))
		{
			$err = 'WARNFILETYPE';
			return false;
		}

		$maxSize = (int) $params->get( 'upload_maxsize', 0 );
		if ($maxSize > 0 && (int) $file['size'] > $maxSize)
		{
			$err = 'WARNFILETOOLARGE';
			return false;
		}

		$user = JFactory::getUser();
		$imginfo = null;
		if($params->get('restrict_uploads',1) ) {
			$images = explode( ',', $params->get( 'image_extensions' ));
			if(in_array($format, $images)) { // if its an image run it through getimagesize
				if(($imginfo = getimagesize($file['tmp_name'])) === FALSE) {
					$err = 'WARNINVALIDIMG';
					return false;
				}
			} else if(!in_array($format, $ignored)) {
				// if its not an image...and we're not ignoring it
				$allowed_mime = explode(',', $params->get('upload_mime'));
				$illegal_mime = explode(',', $params->get('upload_mime_illegal'));
				if($params->get('check_mime',1)) {
					$type = self::getMimeType($file['tmp_name']);
					if(strlen($type) && !in_array($type, $allowed_mime) && in_array($type, $illegal_mime)) {
						$err = 'WARNINVALIDMIME';
						return false;
					}
				} else if(!$user->authorize( 'login', 'administrator' )) {
					$err = 'WARNNOTADMIN';
					return false;
				}
			}
		}

		$xss_check =  JFile::read($file['tmp_name'],false,256);
		$html_tags = array('abbr','acronym','address','applet','area','audioscope','base','basefont','bdo','bgsound','big','blackface','blink','blockquote','body','bq','br','button','caption','center','cite','code','col','colgroup','comment','custom','dd','del','dfn','dir','div','dl','dt','em','embed','fieldset','fn','font','form','frame','frameset','h1','h2','h3','h4','h5','h6','head','hr','html','iframe','ilayer','img','input','ins','isindex','keygen','kbd','label','layer','legend','li','limittext','link','listing','map','marquee','menu','meta','multicol','nobr','noembed','noframes','noscript','nosmartquotes','object','ol','optgroup','option','param','plaintext','pre','rt','ruby','s','samp','script','select','server','shadow','sidebar','small','spacer','span','strike','strong','style','sub','sup','table','tbody','td','textarea','tfoot','th','thead','title','tr','tt','ul','var','wbr','xml','xmp','!DOCTYPE', '!--');
		foreach($html_tags as $tag) {
			// A tag is '<tagname ', so we need to add < and a space or '<tagname>'
			if(stristr($xss_check, '<'.$tag.' ') || stristr($xss_check, '<'.$tag.'>')) {
				$err = 'WARNIEXSS';
				return false;
			}
		}
		return true;
	}	
	
}