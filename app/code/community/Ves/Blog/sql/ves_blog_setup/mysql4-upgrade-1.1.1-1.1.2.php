<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
	ALTER TABLE {$this->getTable('ves_blog/post')} ADD COLUMN `id_video` varchar(100);
	ALTER TABLE {$this->getTable('ves_blog/post')} ADD COLUMN `type_video` varchar(100);
");