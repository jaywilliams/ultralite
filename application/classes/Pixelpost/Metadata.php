<?php
/**
 * This class allows you to read the metadata from JPG images.
 * You can read either the XMP, EXIF or ITPC data embedded in the images.
 *
 * @author Dennis Mooibroek (Pixelpost Development Team)
 * @version 0.1
 * @since Version 2.0 (Alpha 1)
 * @package pixelpost
 * @subpackage metadata
 **/

class Pixelpost_Metadata
{
	/**
	 * Path to image directory
	 *
	 * @var string
	 */
	private $path = __IMAGE_PATH;
	
	/**
	 * Keyword delimiter for the IPTC data
	 *
	 * @var string
	 */
	private $IPTC_keyword_delimiter = " ";

	/**
	 * Temporary EXIF array
	 * Holds the temporary EXIF array for the readEXIF file (used while collecting
	 * the EXIF data)
	 *
	 * @var array
	 */
	private $tmpEXIFdata = array();
	
	/**
	 * EIXF Alignment
	 * Used to hold the alignment of the EXIF
	 *
	 * @var array
	 */
	private $EXIFalign;
	
	private static $instance;
	
	private function __construct()
	{
		// do nothing here, just make sure we cannot initiate the object
	}		  
	
	public static function & getInstance()
	{
		if ( empty( self::$instance ) ) 
		{
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	* function readMetadata (string $image)
	*
	* Reads the combined XMP, EXIF and IPTC data from the image and 
	* returns it as a merged array
	*
	* @since Version 2.0 (Alpha 1)
	* @param string (NOT INCLUDING PATH)
	* @return array containing XMP, EXIF and IPTC data
	*/
	public static function readMETA($image)
    {
    	$self = self::getInstance();
		$METAdata = array();
		$XMPdata  = array();
		$EXIFdata = array();
		$IPTCdata = array();
		/**
		 * All functions should at least supply an empty array when
		 * no data is found
		 */
		$XMPdata  = $self->readXMP($image);
		$EXIFdata = $self->readEXIF($image);
		$IPTCdata = $self->readIPTC($image);

		/**
		 * Order of priority in processing: XMP, EXIF, IPTC
		 * Find unique entries only and remove the empty entries
		 */
		$METAdata = array_filter( array_unique( array_merge( $XMPdata , $EXIFdata , $IPTCdata ) ) );

		/**
		 * Return result array
		 */
		return $METAdata;
	}

	/**
	* function readXMP (string $image)
	*
	* Read the XMP data from the image
	* 
	* @since Version 2.0 (Alpha 1)
	* @param string (NOT INCLUDING PATH)
	* @return array containing XMP data
	*/
	public static function readXMP($image)
	{
		$self = self::getInstance();
		$XMPdata = array();
		
		/**
		 * Read a file into the output_buffer and assign it to $source
		 */
		ob_start();
		readfile($self->path . '/' . $image);
		$source = ob_get_contents();
		ob_end_clean();

		/**
		 * Define the start and endtag of the XMP meta block in the image
		 */
		$xmpdata_start = strpos($source, "<x:xmpmeta");
		$xmpdata_end   = strpos($source, "</x:xmpmeta>");
		$xmplenght     = $xmpdata_end - $xmpdata_start;

		/**
		 * Extract the XMP meta block
		 */
		$xmpdata = substr($source, $xmpdata_start, $xmplenght + 12);

		/**
		 * Use a regex to read out the data
		 */
		$result = preg_match_all('/(<?(photoshop|crs|tiff|exif|aux)):(\w+)?(>|=")(.*?)(<\/\2:\3>|")/',
			$xmpdata, $tags, PREG_SET_ORDER);
		if ($result == true) 
		{
			foreach ($tags as $tag) 
			{
				/**
				 * Add each value into the associative array $XMPdata
	 			 */
				$XMPdata[$tag[3]] = $tag[5];
			}
		}
		
		/**
		 * Return result array
		 */
		return $XMPdata;
	}


	/**
	 * function readIPTC (string $image)
	 *
	 * Read the IPTC data from the image
	 *
	 * @since Version 2.0 (Alpha 1)
	 * @param string $image (NOT INCLUDING PATH)
	 * @return array containing IPTC data
	 */
	public static function readIPTC($image)
	{
		$self = self::getInstance();
		$IPTCdata = array();
		$size = getimagesize($self->path . '/' . $image, $info);

		/**
		 * Check if there is indeed an data block
		 */
		if (is_array($info)) 
		{
			/**
			 * Try to parse the IPCT data block
			 */
		    if (array_key_exists('APP13',$info))
		    {
				$iptc = iptcparse($info["APP13"]);
				/**
				 * If $iptc is an array there was data found.
				 * Please note there can be many things stored into
				 * the IPTC data. The biggest problem with it is that
				 * it can be in a different language, depending on the
				 * language of the image. There is no way to get a clean
				 * array containing English array keys. Therefore the only
				 * approach is to fill the array with specific IPTC blocks
				 * @link http://www.sno.phy.queensu.ca/~phil/exiftool/TagNames/IPTC.html
				 */
				if (is_array($iptc)) 
				{
					if(array_key_exists("2#005", $iptc))
						$IPTCdata['ObjectName'] = $iptc["2#005"][0];
					if(array_key_exists("2#010", $iptc))
						$IPTCdata['Urgency'] = $iptc["2#010"][0];
					if(array_key_exists("2#015", $iptc))
						$IPTCdata['Category'] = $iptc["2#015"][0];
					/**
					 * Note that sometimes SupplementalCategories contans multiple entries
					 */
				 	if(array_key_exists("2#020", $iptc))
				 	{
						$IPTCdata['SupplementalCategories'] = $iptc["2#020"][0];
						/**
					 	* There can be multiple keywords stored, so we need to create a single
					 	* string containing these keywords seperated by IPTC_keyword_delimiter
					 	*/
						$keywordcount = count($iptc["2#025"]);
						$keywords = '';
						for ($i = 0; $i < $keywordcount; $i++)
							$keywords .= $iptc["2#025"][$i] . $self->IPTC_keyword_delimiter;
						$IPTCdata['Keywords'] = $keywords;
					}
					if(array_key_exists("2#040", $iptc))
						$IPTCdata['SpecialInstructions'] = $iptc["2#040"][0];
					if(array_key_exists("2#055", $iptc))
						$IPTCdata['DateCreated'] = $iptc["2#055"][0];
					if(array_key_exists("2#080", $iptc))
						$IPTCdata['By-line'] = $iptc["2#080"][0];
					if(array_key_exists("2#085", $iptc))
						$IPTCdata['By-lineTitle'] = $iptc["2#085"][0];
					if(array_key_exists("2#090", $iptc))
						$IPTCdata['City'] = $iptc["2#090"][0];
					if(array_key_exists("2#095", $iptc))
						$IPTCdata['Province-State'] = $iptc["2#095"][0];
					if(array_key_exists("2#101", $iptc))
						$IPTCdata['Country-PrimaryLocationName'] = $iptc["2#101"][0];
					if(array_key_exists("2#103", $iptc))
						$IPTCdata['OriginalTransmissionReference'] = $iptc["2#103"][0];
					if(array_key_exists("2#105", $iptc))
						$IPTCdata['Headline'] = $iptc["2#105"][0];
					if(array_key_exists("2#110", $iptc))
						$IPTCdata['Credit'] = $iptc["2#110"][0];
					if(array_key_exists("2#115", $iptc))
						$IPTCdata['Source'] = $iptc["2#115"][0];
					if(array_key_exists("2#116", $iptc))
						$IPTCdata['CopyrightNotice'] = $iptc["2#116"][0];
					if(array_key_exists("2#118", $iptc))
						$IPTCdata['Contact'] = $iptc["2#118"][0];
					if(array_key_exists("2#120", $iptc))
						$IPTCdata['Caption-Abstract'] = $iptc["2#120"][0];
				}
				$IPTCdata = array_filter($IPTCdata);
				$size = $info = $iptc = null;
				unset($size, $info, $iptc);
			}
		}

		/**
		 * Return result array
		 */
		return $IPTCdata;
	}

	/**
	 * Opens the JPEG file and attempts to find the EXIF data
	 *
	 * @since Version 2.0 (Alpha 1)
	 * @param string $image (NOT INCLUDING PATH)
	 * @return array containing EXIF data
	 */
	public static function readEXIF($image)
	{
		/**
		 * Based upon Exif reader v 1.3 by Richard James Kendall
		 * Modified by Dennis Mooibroek
		 *  + Added support for global variables (register_globals is off)
		 *	+ Added support for GPS readouts.
		 */
		$self = self::getInstance();
		$EXIFdata = array();
		$fp = fopen($self->path . '/' . $image, "rb");
		$a = $self->fgetord($fp);
		
		if ($a != 255 || $self->fgetord($fp) != 216) 
			return false;
		
		$ef = false;
		
		while (!feof($fp)) 
		{
			$section_length = 0;
			$section_marker = 0;
			$lh = 0;
			$ll = 0;
			for ($i = 0; $i < 7; $i++) 
			{
				$section_marker = $self->fgetord($fp);
				if ($section_marker != 255) 
					break;
				if ($i >= 6) 
					return false;
			}
			if ($section_marker == 255) 
				return false;
			$lh = $self->fgetord($fp);
			$ll = $self->fgetord($fp);
			$section_length = ($lh << 8) | $ll;
			$data = chr($lh) . chr($ll);
			$t_data = fread($fp, $section_length - 2);
			$data .= $t_data;
			switch ($section_marker) 
			{
				case 225:
					$ef = true;
					$self->extractEXIFData(substr($data, 2), $section_length);
					$EXIFdata = $self->tmpEXIFdata;
					fclose($fp);
					/**
					 * We are done, break from the switch and the while loop.
		 			 */	
					break 2;
			}
		}
		/**
		 * Return result array
		 */
		return $EXIFdata;
	}


	/**
	 * Get one byte from the file at handle $fp and converts it to a number
	 *
	 * @since Version 2.0 (Alpha 1)
	 *
	 * @param string $fp
	 *
	 * @return float
	 *
	 */
	protected function fgetord($fp)
	{
		return ord(fgetc($fp));
	}

	/**
	 * Takes $data and pads it from the left so strlen($data) == $shouldbe
	 *
	 * @since Version 2.0 (Alpha 1)
	 *
	 * @param string $data
	 * @param string $shouldbe
	 * @param string $put
	 *
	 * @return string
	 *
	 */
	protected function pad($data, $shouldbe, $put)
	{
		if (strlen($data) == $shouldbe) 
		{
			return $data;
		} 
		else 
		{
			$padding = "";
			for ($i = strlen($data); $i < $shouldbe; $i++) 
			{
				$padding .= $put;
			}
			return $padding . $data;
		}
	}

	/**
	 * Converts a number from intel (little endian) to motorola (big endian format)
	 *
	 * @since Version 2.0 (Alpha 1)
	 *
	 * @param string $number
	 *
	 * @return string
	 *
	 */
	protected function ii2mm($intel)
	{
		$mm = "";
		for ($i = 0; $i <= strlen($intel); $i += 2) 
		{
			$mm .= substr($intel, (strlen($intel) - $i), 2);
		}
		return $mm;
	}

	/**
	 * Gets a number from the EXIF data and converts if to the correct representation
	 *
	 * @since Version 2.0 (Alpha 1)
	 *
	 * @param string $data
	 * @param integer $start
	 * @param integer $length
	 * @param integer $align
	 *
	 * @return float
	 *
	 */
	protected function getnumber($data, $start, $length, $align)
	{
		$self = self::getInstance();
		$a = bin2hex(substr($data, $start, $length));
		if (!$align) 
		{
			$a = $self->ii2mm($a);
		}
		return hexdec($a);
	}

	/**
	 * Gets a rational number (num, denom) from the EXIF data and produces a decimal
	 *
	 * @since Version 2.0 (Alpha 1)
	 *
	 * @param string $data
	 * @param integer $align
	 * @param string $type
	 *
	 * @return string
	 *
	 */
	protected function getrational($data, $align, $type)
	{
		$self = self::getInstance();
		$a = bin2hex($data);
		if (!$align) 
			$a = $self->ii2mm($a);
		if ($align == 1) 
		{
			$n = hexdec(substr($a, 0, 8));
			$d = hexdec(substr($a, 8, 8));
		} 
		else 
		{
			$d = hexdec(substr($a, 0, 8));
			$n = hexdec(substr($a, 8, 8));
		}
		if ($type == "S" && $n > 2147483647) 
			$n = $n - 4294967296;
		if ($n == 0) 
			return 0;
		if ($d != 0) 
		{
			return ($n / $d);
		} 
		else 
		{
			return $n . "/" . $d;
		}
	}


	/**
	 * Reads the EXIF header and if it is intact it calls readEXIFDir to get the data
	 *
	 * @since Version 2.0 (Alpha 1)
	 *
	 * @param string $date
	 * @param integer $length
	 *
	 * @return array containing EXIF data
	 *
	 */
	protected function extractEXIFData($data, $length)
	{
		$self = self::getInstance();
		if (substr($data, 0, 4) == "Exif") 
		{
			if (substr($data, 6, 2) == "II")
			{
				/**
				 * Intel byte order
				 */
				$self->EXIFalign = 0;
			} 
			else 
			{
				if (substr($data, 6, 2) == "MM")
				{
					/**
					 * Motorola byte order
					 */
					$self->EXIFalign = 1;
				} 
				else 
				{
					/**
					 * Unknown byte order, can't process further
					 */
					return false;
				}
			}
			$a = $self->getnumber($data, 8, 2, $self->EXIFalign);
			if ($a != 0x2a) 
				return false;

			$first_offset = $self->getnumber($data, 10, 4, $self->EXIFalign);
			if ($first_offset < 8 || $first_offset > 16) 
				return false;

			$self->readEXIFDir(substr($data, 14), 8, $length - 6, $self->EXIFalign);
			return true;
		} 
		else 
		{
			return false;
		}
	}

	/**
	 * Takes an EXIF tag id and returns the string name of that tag
	 *
	 * @since Version 2.0 (Alpha 1)
	 * @param decimal $dec
	 * @return string
	 */
	protected function tagid2name($dec)
	{
		$tag_arr['0x0100'] = 'ImageWidth';
		$tag_arr['0x0101'] = 'ImageLength';
		$tag_arr['0x0102'] = 'BitsPerSample';
		$tag_arr['0x0103'] = 'Compression';
		$tag_arr['0x0106'] = 'PhotometricInterpretation';
		$tag_arr['0x0112'] = 'Orientation';
		$tag_arr['0x0115'] = 'SamplesPerPixel';
		$tag_arr['0x011c'] = 'PlanarConfiguration';
		$tag_arr['0x0212'] = 'YCbCrSubSampling';
		$tag_arr['0x0213'] = 'YCbCrPositioning';
		$tag_arr['0x011a'] = 'XResolution';
		$tag_arr['0x011b'] = 'YResolution';
		$tag_arr['0x0128'] = 'ResolutionUnit';
		$tag_arr['0x0111'] = 'StripOffsets';
		$tag_arr['0x0116'] = 'RowsPerStrip';
		$tag_arr['0x0117'] = 'StripByteCounts';
		$tag_arr['0x0201'] = 'JPEGInterchangeFormat';
		$tag_arr['0x0202'] = 'JPEGInterchangeFormatLength';
		$tag_arr['0x012d'] = 'TransferFunction';
		$tag_arr['0x013e'] = 'WhitePoint';
		$tag_arr['0x013f'] = 'PrimaryChromaticities';
		$tag_arr['0x0211'] = 'YCbCrCoefficients';
		$tag_arr['0x0214'] = 'ReferenceBlackWhite';
		$tag_arr['0x0132'] = 'DateTime';
		$tag_arr['0x010e'] = 'ImageDescription';
		$tag_arr['0x010f'] = 'Make';
		$tag_arr['0x0110'] = 'Model';
		$tag_arr['0x0131'] = 'Software';
		$tag_arr['0x013b'] = 'Artist';
		$tag_arr['0x8298'] = 'Copyright';
		$tag_arr['0x9000'] = 'ExifVersion';
		$tag_arr['0xa000'] = 'FlashpixVersion';
		$tag_arr['0xa001'] = 'ColorSpace';
		$tag_arr['0x9101'] = 'ComponentsConfiguration';
		$tag_arr['0x9102'] = 'CompressedBitsPerPixel';
		$tag_arr['0xa002'] = 'PixelXDimension';
		$tag_arr['0xa003'] = 'PixelYDimension';
		$tag_arr['0x927c'] = 'MakerNote';
		$tag_arr['0x9286'] = 'UserComment';
		$tag_arr['0x9003'] = 'DateTimeOriginal';
		$tag_arr['0x9004'] = 'DateTimeDigitized';
		$tag_arr['0x9290'] = 'SubSecTime';
		$tag_arr['0x9291'] = 'SubSecTimeOriginal';
		$tag_arr['0x9292'] = 'SubSecTimeDigitized';
		$tag_arr['0xa420'] = 'ImageUniqueID';
		$tag_arr['0x829a'] = 'ExposureTime';
		$tag_arr['0x829d'] = 'FNumber';
		$tag_arr['0x8822'] = 'ExposureProgram';
		$tag_arr['0x8824'] = 'SpectralSensitivity';
		$tag_arr['0x8827'] = 'ISOSpeedRatings';
		$tag_arr['0x8828'] = 'OECF';
		$tag_arr['0x9201'] = 'ShutterSpeedValue';
		$tag_arr['0x9202'] = 'ApertureValue';
		$tag_arr['0x9203'] = 'BrightnessValue';
		$tag_arr['0x9204'] = 'ExposureBiasValue';
		$tag_arr['0x9205'] = 'MaxApertureValue';
		$tag_arr['0x9206'] = 'SubjectDistance';
		$tag_arr['0x9207'] = 'MeteringMode';
		$tag_arr['0x9208'] = 'LightSource';
		$tag_arr['0x9209'] = 'Flash';
		$tag_arr['0x920a'] = 'FocalLength';
		$tag_arr['0x9214'] = 'SubjectArea';
		$tag_arr['0xa20b'] = 'FlashEnergy';
		$tag_arr['0xa20c'] = 'SpatialFrequencyResponse';
		$tag_arr['0xa20e'] = 'FocalPlaneXResolution';
		$tag_arr['0xa20f'] = 'FocalPlaneYResolution';
		$tag_arr['0xa210'] = 'FocalPlaneResolutionUnit';
		$tag_arr['0xa214'] = 'SubjectLocation';
		$tag_arr['0xa215'] = 'ExposureIndex';
		$tag_arr['0xa217'] = 'SensingMethod';
		$tag_arr['0xa300'] = 'FileSource';
		$tag_arr['0xa301'] = 'SceneType';
		$tag_arr['0xa302'] = 'CFAPattern';
		$tag_arr['0xa401'] = 'CustomRendered';
		$tag_arr['0xa402'] = 'ExposureMode';
		$tag_arr['0xa403'] = 'WhiteBalance';
		$tag_arr['0xa404'] = 'DigitalZoomRatio';
		$tag_arr['0xa405'] = 'FocalLengthIn35mmFilm';
		$tag_arr['0xa406'] = 'SceneCaptureType';
		$tag_arr['0xa407'] = 'GainControl';
		$tag_arr['0xa408'] = 'Contrast';
		$tag_arr['0xa409'] = 'Saturation';
		$tag_arr['0xa40a'] = 'Sharpness';
		$tag_arr['0xa40b'] = 'DeviceSettingDescription';
		$tag_arr['0xa40c'] = 'SubjectDistanceRange';
		$tag_arr['0x00fe'] = 'ImageType';
		$tag_arr['0x0106'] = 'PhotometicInterpret';
		$tag_arr['0x9213'] = 'ImageHistory';
		$tag_arr['0xa004'] = 'RelatedSoundFile';
		$tag_arr['0x0000'] = 'GPSVersionID';
		$tag_arr['0x0001'] = 'GPSLatitudeRef';
		$tag_arr['0x0002'] = 'GPSLatitude';
		$tag_arr['0x0003'] = 'GPSLongitudeRef';
		$tag_arr['0x0004'] = 'GPSLongitude';
		$tag_arr['0x0005'] = 'GPSAltitudeRef';
		$tag_arr['0x0006'] = 'GPSAltitude';
		$tag_arr['0x0007'] = 'GPSTimeStamp';
		$tag_arr['0x0008'] = 'GPSSatellites';
		$tag_arr['0x0009'] = 'GPSStatus';
		$tag_arr['0x000a'] = 'GPSMeasureMode';
		$tag_arr['0x000b'] = 'GPSDOP';
		$tag_arr['0x000c'] = 'GPSSpeedRef';
		$tag_arr['0x000d'] = 'GPSSpeed';
		$tag_arr['0x000e'] = 'GPSTrackRef';
		$tag_arr['0x000f'] = 'GPSTrack';
		$tag_arr['0x0010'] = 'GPSImgDirectionRef';
		$tag_arr['0x0011'] = 'GPSImgDirection';
		$tag_arr['0x0012'] = 'GPSMapDatum';
		$tag_arr['0x0013'] = 'GPSDestLatitudeRef';
		$tag_arr['0x0014'] = 'GPSDestLatitude';
		$tag_arr['0x0015'] = 'GPSDestLongitudeRef';
		$tag_arr['0x0016'] = 'GPSDestLongitude';
		$tag_arr['0x0017'] = 'GPSDestBearingRef';
		$tag_arr['0x0018'] = 'GPSDestBearing';
		$tag_arr['0x0019'] = 'GPSDestDistanceRef';
		$tag_arr['0x001a'] = 'GPSDestDistance';
		$tag_arr['0x001b'] = 'GPSProcessingMethod';
		$tag_arr['0x001c'] = 'GPSAreaInformation';
		$tag_arr['0x001d'] = 'GPSDateStamp';
		$tag_arr['0x001e'] = 'GPSDifferential';
		$hex = '0x' . str_pad(dechex($dec), 4, "0", STR_PAD_LEFT);
		if (array_key_exists($hex, $tag_arr)) 
		{
			return $tag_arr[$hex];
		} 
		else 
		{
			return false;
		}
	}

	/**
	 * Takes a (U/S)(SHORT/LONG) checks if an enumeration for this value exists and if it does returns the enumerated value for $tvalue
	 *
	 * @since Version 2.0 (Alpha 1)
	 *
	 * @param string $tname
	 * @param integer $tvalue
	 *
	 * @return string
	 *
	 */
	protected function enumvalue($tname, $tvalue)
	{
		// data for EXIF enumeations
		$Orientation = array("", "Normal (0 deg)", "Mirrored", "Upsidedown",
			"Upsidedown & Mirrored", "", "", "");
		$ResUnit = array("", "inches", "inches", "cm", "mm", "um");
		$YCbCrPos = array("", "Centre of Pixel Array", "Datum Points");
		$ExpProg = array("", "Manual", "Program", "Apeture Priority", "Shutter Priority",
			"Program Creative", "Program Action", "Portrait", "Landscape");
		$LightSource = array("Unknown", "Daylight", "Fluorescent",
			"Tungsten (incandescent)", "Flash", "Fine Weather", "Cloudy Weather", "Share",
			"Daylight Fluorescent", "Day White Fluorescent", "Cool White Fluorescent",
			"White Fluorescent", "Standard Light A", "Standard Light B", "Standard Light C",
			"D55", "D65", "D75", "D50", "ISO Studio Tungsten");
		$MeterMode = array("Unknown", "Average", "Centre Weighted", "Spot", "Multi-Spot",
			"Pattern", "Partial");
		$RenderingProcess = array("Normal Process", "Custom Process");
		$ExposureMode = array("Auto", "Manual", "Auto Bracket");
		$WhiteBalance = array("Auto", "Manual");
		$SceneCaptureType = array("Standard", "Landscape", "Portrait", "Night Scene");
		$GainControl = array("None", "Low Gain Up", "High Gain Up", "Low Gain Down",
			"High Gain Down");
		$Contrast = array("Normal", "Soft", "Hard");
		$Saturation = array("Normal", "Low Saturation", "High Saturation");
		$Sharpness = array("Normal", "Soft", "Hard");
		$SubjectDistanceRange = array("Unknown", "Macro", "Close View", "Distant View");
		$FocalPlaneResUnit = array("", "inches", "inches", "cm", "mm", "um");
		$SensingMethod = array("", "Not Defined", "One-chip Colour Area Sensor",
			"Two-chip Colour Area Sensor", "Three-chip Colour Area Sensor",
			"Colour Sequential Area Sensor", "Trilinear Sensor",
			"Colour Sequential Linear Sensor");
		if (isset($$tname)) 
		{
			$tmp = $$tname;
			return $tmp[$tvalue];
		} 
		else 
		{
			return $tvalue;
		}
	}

	/**
	 * Takes the USHORT of the flash value, splits it up into itc component bits and returns the string it represents
	 *
	 * @since Version 2.0 (Alpha 1)
	 *
	 * @param binary $bin
	 *
	 * @return string
	 *
	 */
	protected function flashvalue($bin)
	{
		$self = self::getInstance();
		$retval = "";
		$bin = $self->pad(decbin($bin), 8, "0");
		$flashfired = substr($bin, 7, 1);
		$returnd = substr($bin, 5, 2);
		$flashmode = substr($bin, 3, 2);
		$redeye = substr($bin, 1, 1);
		if ($flashfired == "1") 
		{
			$reval = "Fired";
		} 
		else 
		{
			if ($flashfired == "0") 
			{
				$retval = "Did not fire";
			}
		}
		if ($returnd == "10") 
		{
			$retval .= ", Strobe return light not detected";
		} 
		else 
		{
			if ($returnd == "11") 
				$retval .= ", Strobe return light detected";
		}
		if ($flashmode == "01" || $flashmode == "10") 
		{
			$retval .= ", Compulsory mode";
		} 
		else 
		{
			if ($flashmode == "11")
				$retval .= ", Auto mode";
		}
		if ($redeye) 
		{
			$retval .= ", Red eye reduction";
		} 
		else 
		{
			$retval .= ", No red eye reduction";
		}
		return $retval;
	}

	/**
	 * Takes a tag id along with the format, data and length of the data and deals with it appropriatly
	 *
	 * @since Version 2.0 (Alpha 1)
	 *
	 * @param string $tag
	 * @param string $format
	 * @param string $data
	 * @param integer $length
	 * @param integer $align
	 *
	 * @return updates GLOBAL.
	 *
	 */
	protected function dealwithtag($tag, $format, $data, $length, $align)
	{
		$self = self::getInstance();
		$format_type = array("", "BYTE", "STRING", "USHORT", "ULONG", "URATIONAL",
			"SBYTE", "UNDEFINED", "SSHORT", "SLONG", "SRATIONAL", "SINGLE", "DOUBLE");
		$w = false;
		$val = "";
		switch ($format_type[$format]) 
		{
			case "STRING":
				$val = trim(substr($data, 0, $length));
				$w = true;
				break;
			case "ULONG":
			case "SLONG":
				$val = $self->enumvalue($self->tagid2name($tag), $self->getnumber($data, 0, 4, $align));
				$w = true;
				break;
			case "USHORT":
			case "SSHORT":
				switch ($tag) 
				{
					case 0x9209:
						$val = array($self->getnumber($data, 0, 2, $align), $self->flashvalue($self->
							getnumber($data, 0, 2, $align)));
						$w = true;
						break;
					case 0x9214:
						break;
					case 0xa001:
						$tmp = $self->getnumber($data, 0, 2, $align);
						if ($tmp == 1) {
							$val = "sRGB";
							$w = true;
						} else {
							$val = "Uncalibrated";
							$w = true;
						}
						break;
					default:
						$val = $self->enumvalue($self->tagid2name($tag), $self->getnumber($data, 0, 2, $align));
						$w = true;
						break;
				}
				break;
			case "URATIONAL":
				switch ($tag) 
				{
					case 0x0002:
						if ($self->getrational(substr($data, 0, 8), $align, "U") == $self->getrational(substr($data, 0, 24), $align, "U")) 
						{
							$val = $self->getrational(substr($data, 0, 8), $align, "U") . ", " . $self->getrational(substr($data, 8, 16), $align, "U") . ", " . $self->getrational(substr($data, 16, 24), $align, "U");
						} 
						else 
						{
							$val = $self->getrational(substr($data, 0, 8), $align, "U") . ", " . $self->getrational(substr($data, 0, 16), $align, "U") . ", " . $self->getrational(substr($data, 0, 24), $align, "U");
						}
						$w = true;
						break;
					case 0x0004:
						if ($self->getrational(substr($data, 0, 8), $align, "U") == $self->getrational(substr($data, 0, 24), $align, "U")) 
						{
							$val = $self->getrational(substr($data, 0, 8), $align, "U") . ", " . $self->getrational(substr($data, 8, 16), $align, "U") . ", " . $self->getrational(substr($data, 16, 24), $align, "U");
						} 
						else 
						{
							$val = $self->getrational(substr($data, 0, 8), $align, "U") . ", " . $self->getrational(substr($data, 0, 16), $align, "U") . ", " . $self->getrational(substr($data, 0, 24), $align, "U");
						}
						$w = true;
						break;
					default;
						$val = $self->getrational(substr($data, 0, 8), $align, "U");
						$w = true;
						break;
				}
				break;
			case "SRATIONAL":
				$val = $self->getrational(substr($data, 0, 8), $align, "S");
				$w = true;
				break;
			case "UNDEFINED":
				switch ($tag) 
				{
					case 0xa300:
						$tmp = $self->getnumber($data, 0, 2, $align);
						if ($tmp == 3) 
						{
							$val = "Digital Camera";
							$w = true;
						} 
						else 
						{
							$val = "Unknown";
							$w = true;
						}
						break;
					case 0xa301:
						$tmp = $self->getnumber($data, 0, 2, $align);
						if ($tmp == 3) 
						{
							$val = "Directly Photographed";
							$w = true;
						} 
						else 
						{
							$val = "Unknown";
							$w = true;
						}
						break;
				}
				break;
		}
		if ($w) 
		{
			$self->tmpEXIFdata[$self->tagid2name($tag)] = $val;
		}
	}

	/**
	 * Reads the tags from and EXIF IFD and if correct deals with the data
	 *
	 * @since Version 2.0 (Alpha 1)
	 *
	 * @param string $data
	 * @param integer $offset_base
	 * @param integer $exif_length
	 *
	 * @return updates GLOBAL.
	 *
	 */

	protected function readEXIFDir($data, $offset_base, $exif_length, $align)
	{
		$self = self::getInstance();
		$format_length = array(0, 1, 1, 2, 4, 8, 1, 1, 2, 4, 8, 4, 8);
		$value_ptr = 0;
		$sofar = 2;
		$data_in = "";
		$number_dir_entries = $self->getnumber($data, 0, 2, $self->EXIFalign);
		for ($i = 0; $i < $number_dir_entries; $i++) 
		{
			$sofar += 12;
			$dir_entry = substr($data, 2 + 12 * $i);
			$tag = $self->getnumber($dir_entry, 0, 2, $self->EXIFalign);
			$format = $self->getnumber($dir_entry, 2, 2, $self->EXIFalign);
			$components = $self->getnumber($dir_entry, 4, 4, $self->EXIFalign);
			if (($format - 1) >= 12)
				return false;

			$byte_count = $components * $format_length[$format];
			if ($byte_count > 4) 
			{
				$offset_val = ($self->getnumber($dir_entry, 8, 4, $self->EXIFalign)) - $offset_base;
				if (($offset_val + $byte_count) > $exif_length) 
					return false;

				$data_in = substr($data, $offset_val);
			} 
			else 
			{
				$data_in = substr($dir_entry, 8);
			}
			if ($tag == 0x8769) //pointer to the Exif IFD
			{
				$tmp = ($self->getnumber($data_in, 0, 4, $self->EXIFalign)) - 8;
				$self->readEXIFDir(substr($data, $tmp), $tmp + 8, $exif_length, $self->EXIFalign);
			} 
			elseif ($tag == 0x8825) //pointer to GPS IFD
			{
				$tmp = ($self->getnumber($data_in, 0, 4, $self->EXIFalign)) - 8;
				$self->readEXIFDir(substr($data, $tmp), $tmp + 8, $exif_length, $self->EXIFalign);
			} 
			else 
			{
				$self->dealwithtag($tag, $format, $data_in, $byte_count, $self->EXIFalign);
			}
		}
	}
}