<?php
/**
 * Custom Options Templates
 *
 * @category:    Aitoc
 * @package:     Aitoc_Aitoptionstemplate
 * @version      3.2.2
 * @license:     FoqAPsDYiV5k0g2ao7Zx8pPoJiVNkUYZBpxixzEerG
 * @copyright:   Copyright (c) 2013 AITOC, Inc. (http://www.aitoc.com)
 */
/**
 * @copyright  Copyright (c) 2010 AITOC, Inc. 
 */


class Aitoc_Aitoptionstemplate_Model_Uploader extends Varien_File_Uploader
{
    public function getUploadFileName()
    {
        return $this->_file['name'];
    }
}