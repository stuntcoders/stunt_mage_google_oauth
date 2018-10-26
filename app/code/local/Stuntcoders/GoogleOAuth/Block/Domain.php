<?php

class Stuntcoders_GoogleOAuth_Block_Domain
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function _prepareToRender()
    {
        $this->addColumn('domain_name', array(
            'label' => Mage::helper('stuntcoders_googleoauth')->__('E-mail domain'),
            'style' => 'width:100px',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('stuntcoders_googleoauth')->__('Add');
    }
}
