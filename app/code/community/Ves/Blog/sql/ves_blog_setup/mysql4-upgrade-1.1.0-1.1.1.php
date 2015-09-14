<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
	ALTER TABLE {$this->getTable('ves_blog/post')} ADD COLUMN `embed_code` varchar(300);
");