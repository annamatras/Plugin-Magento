<?php
class Synerise_Integration_Block_Opengraph_Default extends Synerise_Integration_Block_Opengraph_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        
        $this->setOgType('article');
    }
}