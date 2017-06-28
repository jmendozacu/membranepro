<?php

class Magestore_Dailydeal_Block_Adminhtml_Randomdeal_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct(){
		parent::__construct();
		$this->setId('randomdealGrid');
		$this->setDefaultSort('randomdeal_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection(){
		$collection = Mage::getModel('dailydeal/dailydeal')->getCollection()
                        ->addFieldToFilter('is_random','1');
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns(){
		$this->addColumn('id', array(
			'header'	=> Mage::helper('dailydeal')->__('ID'),
			'align'	 =>'right',
			'width'	 => '30px',
			'index'	 => 'id',
		));

		$this->addColumn('title', array(
			'header'	=> Mage::helper('dailydeal')->__('Title'),
			'align'	 =>'left',
                        'width'     => '150px',
			'index'	 => 'title',
		));

                $this->addColumn('products', array(
			'header'	=> Mage::helper('dailydeal')->__('Products in deal generator'),
			'filter'	=> false,
                        'renderer'  => 'dailydeal/adminhtml_randomdeal_renderer_product',
			'index'	 => 'product_name',
		));
                
                $this->addColumn('product_id', array(
			'header'	=> Mage::helper('dailydeal')->__('Daily deal available'),
			'width'	 => '150px',
                        'filter'	=> false,
                        'renderer'  => 'dailydeal/adminhtml_randomdeal_renderer_dailydealavailable',
			'index'	 => 'product_id',
		));
                $this->addColumn('save', array(
			'header'	=> Mage::helper('dailydeal')->__('Save'),
			'width'	 => '50px',
			'index'	 => 'save',
                        'type'	 =>'text',
                        'renderer'  => 'dailydeal/adminhtml_randomdeal_renderer_save',
		));

                $this->addColumn('quantity', array(
			'header'	=> Mage::helper('dailydeal')->__('Quantity'),
			'width'	 => '50px',
			'index'	 => 'quantity',
                        'type'	 =>'text',
		));
                $this->addColumn('sold', array(
			'header'	=> Mage::helper('dailydeal')->__('Sold'),
			'width'	 => '50px',
			'index'	 => 'sold',
                        'type'	 =>'number',
		));

               $this->addColumn('start_time', array(
			'header'	=> Mage::helper('dailydeal')->__('Start time'),
			'width'	 => '50px',
			'index'	 => 'start_time',
                        'type'	 =>'datetime',
		));
               $this->addColumn('close_time', array(
			'header'	=> Mage::helper('dailydeal')->__('Close time'),
			'width'	 => '50px',
			'index'	 => 'close_time',
                        'type'	 =>'datetime',
		));


		$this->addColumn('status', array(
			'header'	=> Mage::helper('dailydeal')->__('Status'),
			'align'	 => 'left',
			'width'	 => '100px',
			'index'	 => 'status',
			'type'		=> 'options',
			'options'	 => array(
				1 => 'Coming',
				3 => 'Active',
                                4 => 'Expired',
                                2 => 'Disable',
			),
		));

		$this->addColumn('action',
			array(
				'header'	=>	Mage::helper('dailydeal')->__('Action'),
				'width'		=> '100px',
				'type'		=> 'action',
				'getter'	=> 'getId',
				'actions'	=> array(
					array(
						'caption'	=> Mage::helper('dailydeal')->__('Edit'),
						'url'		=> array('base'=> '*/*/edit'),
						'field'		=> 'id'
					)),
				'filter'	=> false,
				'sortable'	=> false,
				'index'		=> 'stores',
				'is_system'	=> true,
		));

		$this->addExportType('*/*/exportCsv', Mage::helper('dailydeal')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('dailydeal')->__('XML'));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction(){
		$this->setMassactionIdField('id');
		$this->getMassactionBlock()->setFormFieldName('randomdeal');

		$this->getMassactionBlock()->addItem('delete', array(
			'label'		=> Mage::helper('dailydeal')->__('Delete'),
			'url'		=> $this->getUrl('*/*/massDelete'),
			'confirm'	=> Mage::helper('dailydeal')->__('Are you sure?')
		));

		$statuses = Mage::getSingleton('dailydeal/status')->getOptionArray();

		array_unshift($statuses, array('label'=>'', 'value'=>''));
		$this->getMassactionBlock()->addItem('status', array(
			'label'=> Mage::helper('dailydeal')->__('Change status'),
			'url'	=> $this->getUrl('*/*/massStatus', array('_current'=>true)),
			'additional' => array(
				'visibility' => array(
					'name'	=> 'status',
					'type'	=> 'select',
					'class'	=> 'required-entry',
					'label'	=> Mage::helper('dailydeal')->__('Status'),
					'values'=> $statuses
				))
		));
		return $this;
	}

	public function getRowUrl($row){
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
}