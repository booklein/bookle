<?php 
class Ves_TabsHome_Model_Mage_Widget extends Mage_Widget_Model_Widget
{
    public function getWidgetDeclaration($type, $params = array(), $asIs = true)
    {

        if( preg_match('~(^ves_tabshome/widget_tab)~', $type) )
        {
            $params['pretext'] = base64_encode($params['pretext']);
        }

        return parent::getWidgetDeclaration($type, $params, $asIs);
    }
}