<?php
/**
 * High-level interface for email templates data that hides format from the client code
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Email\Model\Template;

class Config
{
    /**
     * @var \Magento\Email\Model\Template\Config\Data
     */
    protected $_dataStorage;

    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $_moduleReader;


    /**
     * @var \Magento\Email\Model\Template|FileSystem
     */
    protected $_fileSystem;

    /**
     * @param \Magento\Email\Model\Template\Config\Data $dataStorage
     * @param \Magento\Email\Model\Template|FileSystem $fileSystem
     */
    public function __construct(
        \Magento\Email\Model\Template\Config\Data $dataStorage,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Email\Model\Template\FileSystem $fileSystem
    ) {
        $this->_dataStorage = $dataStorage;
        $this->_moduleReader = $moduleReader;
        $this->_fileSystem = $fileSystem;
    }

    /**
     * Retrieve unique identifiers of all available email templates
     *
     * @return string[]
     */
    public function getAvailableTemplates()
    {
        return array_keys($this->_dataStorage->get());
    }

    /**
     * Retrieve translated label of an email template
     *
     * @param string $templateId
     * @return \Magento\Framework\Phrase
     */
    public function getTemplateLabel($templateId)
    {
        return __($this->_getInfo($templateId, 'label'));
    }

    /**
     * Retrieve type of an email template
     *
     * @param string $templateId
     * @return string
     */
    public function getTemplateType($templateId)
    {
        return $this->_getInfo($templateId, 'type');
    }

    /**
     * Retrieve fully-qualified name of a module an email template belongs to
     *
     * @param string $templateId
     * @return string
     */
    public function getTemplateModule($templateId)
    {
        return $this->_getInfo($templateId, 'module');
    }

    /**
     * Retrieve full path to an email template file
     *
     * @param string $templateId
     * @return string
     */
    public function getTemplateFilename($templateId)
    {
        $module = $this->getTemplateModule($templateId);
        $file = $this->_getInfo($templateId, 'file');

        $result = $this->_fileSystem->getEmailTemplateFileName($file,$module);
        return $result;
        //TODO - remove this line, just here so I can compare the old behavior to the new
        //return $this->_moduleReader->getModuleDir('view', $module) . '/frontend/email/' . $file;
    }

    /**
     * Retrieve value of a field of an email template
     *
     * @param string $templateId Name of an email template
     * @param string $fieldName Name of a field value of which to return
     * @return string
     * @throws \UnexpectedValueException
     */
    protected function _getInfo($templateId, $fieldName)
    {
        $data = $this->_dataStorage->get();
        if (!isset($data[$templateId])) {
            throw new \UnexpectedValueException("Email template '{$templateId}' is not defined.");
        }
        if (!isset($data[$templateId][$fieldName])) {
            throw new \UnexpectedValueException(
                "Field '{$fieldName}' is not defined for email template '{$templateId}'."
            );
        }
        return $data[$templateId][$fieldName];
    }
}
