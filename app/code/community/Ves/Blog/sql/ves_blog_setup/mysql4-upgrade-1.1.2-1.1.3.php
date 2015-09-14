<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer->startSetup();

if ($installer->getConnection()->tableColumnExists($this->getTable('ves_blog/post'), "embed_code")) {
	$installer->run("
		ALTER TABLE `{$this->getTable('ves_blog/post')}` DROP COLUMN embed_code;
		");
}

if ($installer->getConnection()->tableColumnExists($this->getTable('ves_blog/post'), "id_video")) {
	$installer->run("
		ALTER TABLE `{$this->getTable('ves_blog/post')}` CHANGE `id_video` `video_id` varchar(100);
		");
}

if ($installer->getConnection()->tableColumnExists($this->getTable('ves_blog/post'), "type_video")) {
	$installer->run("
		ALTER TABLE `{$this->getTable('ves_blog/post')}` CHANGE `type_video` `video_type` varchar(100);
		");
}