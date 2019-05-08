<?php

/**
 * The home manager controller for siteStatistics.
 *
 */
class siteStatisticsHomeManagerController extends modExtraManagerController
{
    /* @var siteStatistics $siteStatistics */
    public $siteStatistics;


    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return ['sitestatistics:default'];
    }

    /**
     * @return null|string
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('sitestatistics');
    }

    /**
     * @return void
     */
    public function initialize()
    {
        $corePath = $this->modx->getOption('sitestatistics_core_path', null, $this->modx->getOption('core_path') . 'components/sitestatistics/');
        require_once $corePath . 'services/sitestatistics.class.php';

        $this->siteStatistics = new siteStatistics($this->modx);

        parent::initialize();
    }

    /**
     * @return void
     */
    public function loadCustomCssJs()
    {
        $this->siteStatistics->initializeMgr();
    }


    /**
     * @return string
     */
    public function getTemplateFile()
    {
        return $this->siteStatistics->config('templatesPath') . 'home.tpl';
    }
}