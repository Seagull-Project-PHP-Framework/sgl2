<?php

class SGL2_View_Renderer_Html_Flexy extends SGL2_View_Renderer_Abstract
{
    const FORCE_COMPILE = 0;
    const DEBUG = 0;
    const FILTERS = 'SimpleTags';
    const ALLOW_PHP = true;
    const LOCALE = 'en';
    const COMPILER = 'Flexy';
    const VALID_FNS = 'include';
    const GLOBAL_FNS = true;
    const IGNORE =  0; //  don't parse forms when set to true

    /**
     * Director for html Flexy renderer.
     *
     * @param SGL2_View $view
     * @return string   rendered html output
     */
    public function render(SGL2_View_Abstract $view)
    {
        //  suppress error notices in templates
#SGL2_ErrorHandler::setNoticeBehaviour(SGL2_NOTICES_DISABLED);

        //  prepare flexy object
        $flexy = $this->_initEngine($view->data);

#fixme:
        $layout = isset($view->data->layout)
            ? $view->data->layout
            : 'layout.html';
        $ok = $flexy->compile($layout);

        $html = $flexy->bufferedOutputObject($view->data, array());

#SGL2_ErrorHandler::setNoticeBehaviour(SGL2_NOTICES_ENABLED);
        return $html;
    }

    /**
     * Initialise Flexy options.
     *
     * @param SGL2_Response $response
     * @return boolean
     *
     * @todo move flexy constants to this class def
     */
    protected function _initEngine($response)
    {
        //  initialise template engine
        $aTemplateDirs = array(
            // the current module's templates dir from the custom theme
            SGL2_THEME_DIR . '/' . $response->theme . '/' . $response->moduleName,
            // the default template dir from the custom theme
            SGL2_THEME_DIR . '/' . $response->theme . '/default',
            // the configured default module's templates dir
#SGL2_MOD_DIR . '/'. SGL2_Config::get('site.defaultModule') . '/templates',
            // the default template dir from the default theme
			SGL2_MOD_DIR . '/Default/View'
            );
        $options = array(
            'templateDir'       => implode(PATH_SEPARATOR, array_unique($aTemplateDirs)),
            'templateDirOrder'  => 'reverse',
            'multiSource'       => true,
            'compileDir'        => SGL2_CACHE_DIR . '/tmpl/' . $response->theme,
            'forceCompile'      => self::FORCE_COMPILE,
            'debug'             => self::DEBUG,
            'allowPHP'          => self::ALLOW_PHP,
            'filters'           => self::FILTERS,
            'locale'            => self::LOCALE,
            'compiler'          => self::COMPILER,
            'valid_functions'   => self::VALID_FNS,
            'flexyIgnore'       => self::IGNORE,
            'globals'           => true,
            'globalfunctions'   => self::GLOBAL_FNS,
        );

#$ok = $this->_setupPlugins($response, $options);
        $flexy = new HTML_Template_Flexy($options);
        return $flexy;
    }

    /**
     * Setup Flexy plugins if specified.
     *
     * @param SGL2_Output $data
     * @param array $options
     * @return boolean
     */
    protected function _setupPlugins(SGL2_Response $data, array $options)
    {
        //  Configure Flexy to use SGL ModuleOutput Plugin
        //   If an Output.php file exists in module's dir
        $customOutput = SGL2_MOD_DIR . '/' . $data->moduleName . '/classes/Output.php';
        if (SGL2_File::exists($customOutput)) {
            $className = ucfirst($data->moduleName) . 'Output';
            if (isset($options['plugins'])) {
                $options['plugins'] = $options['plugins'] + array($className => $customOutput);
            } else {
                $options['plugins'] = array($className => $customOutput);
            }
        }
        return true;
    }
}
?>