<?php

/**
 * Basic localization class.
 *
 * @package SGL2
 * @author  Philipp Simon <psimon@solotics.com>
 * @version $Revision: 1.00 $
 */
class SGL2_Locale
{
	private static $_allLocales = array(
		'af',    'sq',    'ar_DZ', 'ar_BH', 'ar_EG', 'ar_IQ', 'ar_JO', 'ar_KW', 'ar_LB', 'ar_LY',
		'ar_MA', 'ar_OM', 'ar_QA', 'ar_SA', 'ar_SY', 'ar_TN', 'ar_AE', 'ar_YE', 'ar',    'hy',
		'as',    'az',    'az',    'eu',    'be',    'bn',    'bg',    'ca',    'zh_CN', 'zh_HK',
		'zh_MO', 'zh_SG', 'zh_TW', 'zh',    'hr',    'cs',    'da',    'div',   'nl_BE',  'nl',
		'en_AU', 'en_BZ', 'en_CA', 'en_IE', 'en_JM', 'en_NZ', 'en_PH', 'en_ZA', 'en_TT', 'en_GB',
		'en_US', 'en_ZW', 'en',    'et',    'fo',    'fa',    'fi',    'fr_BE', 'fr_CA', 'fr_LU',
		'fr_MC', 'fr_CH', 'fr',    'mk',    'gd',    'ka',    'de_AT', 'de_CH', 'de_LI', 'de_LU',
		'de',    'el',    'gu',    'he',    'hi',    'hu',    'is',    'id',    'it',    'it_CH',
		'ja',    'kn',    'kk',    'kok',   'ko',    'kz',    'lv',    'lt',    'ms',    'ml',
		'mt',    'mr',    'mn',    'ne',    'nb_NO', 'no',    'nn_NO', 'nn',    'or',    'pl',
		'pt_BR', 'pt',    'pa',    'rm',    'ro_MD', 'ro',    'ru_MD', 'ru',    'sa',    'sr',
		'sk',    'ls',    'sb',    'es_AR', 'es_BO', 'es_CL', 'es_CO', 'es_CR', 'es_DO', 'es_EC',
		'es_SV', 'es_GT', 'es_HN', 'es_MX', 'es_NI', 'es_PA', 'es_PY', 'es_PE', 'es_PR', 'es',
		'es_US', 'es_UY', 'es_VE', 'es',    'sx',    'sw',    'sv_FI', 'sv',    'syr',   'ta',
		'tt',    'te',    'th',    'ts',    'tn',    'tr',    'uk',    'ur',    'uz',    'uz',
		'vi',    'xh',    'yi',    'zu ',
	);
	
	protected $_locale = null;	
	protected $_defLocale = 'en';	
	protected $_validLocales = null;	
		
	public function __construct($locale = null, $defLocale = null, array $validLocales = null)
	{
		$this->_validLocales = $validLocales;
		
		$this->setDefaultLocale($defLocale);
		$this->setLocale($locale);				
	}

	public function getLocale()
	{
		if (!$this->_locale) {
			$this->_locale = $this->_detectLocale();
		}

		return $this->_locale;
	}
	
	public function setLocale($locale)
	{
		if (empty($locale)) {
			$this->_locale = $this->_detectLocale();
		} else if ($this->_isValidLocale((string) $locale)) {
			$this->_locale = (string) $locale;
		} else if (strpos((string) $locale, '_') !== false) {
			$language = explode('_', (string) $locale);
			$this->setLocale($language[0]);
		}
	}
	
	public function getDefaultLocale()
	{
		return $this->_defLocale;
	}
	
	public function setDefaultLocale($locale)
	{
		if ($this->_isValidLocale((string) $locale)) {
			$this->_defLocale = (string) $locale;
		} else if (!$this->_isValidLocale($this->_defLocale)) {
			$this->_defLocale = $this->_validLocales[0];
		}
	}	
		
	public function getLanguage()
	{
		if (!$this->_locale) {
			$this->_locale = $this->_detectLocale();
		}
		
		$locale = explode('_', $this->_locale);
		
		return $locale[0];
	}
	
	public function getRegion()
	{
		if (!$this->_locale) {
			$this->_locale = $this->_detectLocale();
		}
		
		$locale = explode('_', $this->_locale);
		if (!empty($locale[1])) {
			return $locale[1];
		}
				
		return false;
	}
	
	protected function _isValidLocale($locale)
	{
		if (empty($locale)) {
			return false;
		}
		
		if (!empty($this->_validLocales)) {
			return in_array((string) $locale, $this->_validLocales);
		}
		
		return in_array((string) $locale, self::$_allLocales);
	}
	
	protected function _detectLocale()
	{
		$env = getenv('HTTP_ACCEPT_LANGUAGE');
		$locales = preg_split(';[\s,]+;', substr($env, 0, strpos($env . ';', ';')), -1, PREG_SPLIT_NO_EMPTY);
		
		if (!empty($locales)) {
            foreach ($locales as $locale) {
            	$locale   = explode('-', $locale);
            	$language = strtolower($locale[0]);
            	$region   = !empty($locale[1]) ? strtolower($locale[1]) : false;
            	
            	$locale   = $language;
            	$locale  .= (!empty($region) && $language != $region) ? '_' . strtoupper($region) : '';
            	
            	if ($this->_isValidLocale($locale)) {
            		return $locale;
            	} else if ($this->_isValidLocale($language)) {
            		return $language;
            	}
            }		
		}
		
		return $this->_defLocale;
	}
	
	public function toString()
	{
		return $this->getLocale();
	}	
	
	public function __toString()
	{
		return $this->getLocale();
	}
};

?>