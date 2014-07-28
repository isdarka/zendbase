<?php
namespace Isdarka\Validator;

use Model\Bean\AbstractBean;
use Zend\Mvc\I18n\Translator;
use Zend\Db\Adapter\Adapter;
use Zend\Validator\Date;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;
use Zend\Validator\Db\NoRecordExists;
/**
 *
 * @author isdarka
 *        
 */
abstract class AbstractValidator
{
    /** @var $messages array */
    protected $messages = array();
    
    /** @var $tranlator Translator */
    private $translator;
    
    /** @var $adapter Adapter */
    private $adapter;
    
    /** @var $bean AbstractBean */
    protected $bean;
    
    /**
     * 
     * @param AbstractBean $bean
     */
    public function __construct(AbstractBean $bean)
    {
        $this->bean = $bean;
    }
    
    /**
     * Validate Bean
     */
    abstract public function validate();
    
    /**
     * 
     * @param Translator $tranlator
     * @return \Core\Validator\AbstractValidator
     */
    public function setTranslator(Translator $translator)
    {
    	$this->translator = $translator;
    	return $this;
    }
    
    /**
     * 
     * @return Translator
     */
    public function getTranslator()
    {
    	return $this->translator;
    }
    
    /**
     * 
     * @param Adapter $adapter
     * @return \Core\Validator\AbstractValidator
     */
    public function setAdapter(Adapter $adapter)
    {
    	$this->adapter = $adapter;
    	return $this;
    }
    
    /**
     * 
     * @return Adapter
     */
    public function getAdapter()
    {
    	return $this->adapter;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isValid()
    {
        if(count($this->messages))
            return false;

        return true;
    }
    
    /**
     * 
     * @return multitype:
     */
    public function getErrorMessages()
    {
        return array_unique($this->messages);
    }

    /**
     * 
     * @return \Zend\Validator\Date
     */
	public function getDateValidator()
	{
	    $validator = new Date();
	    $validator->setTranslator($this->getTranslator());
	    
	    return $validator;
	}
	
	/**
	 * 
	 * @return \Zend\Validator\NotEmpty
	 */
	public function getNotEmptyValidator()
	{
	    $validator = new NotEmpty();
	    $validator->setTranslator($this->getTranslator());
	    
	    return $validator;
	}
	
	/**
	 * 
	 * @return \Zend\Validator\StringLength
	 */
	public function getStringLengthValidator()
	{
	    $validator = new StringLength();
	    $validator->setTranslator($this->getTranslator());
	    
	    return $validator;
	}
	
	/**
	 * 
	 * @param array $params
	 * @return \Zend\Validator\Db\NoRecordExists
	 */
	public function getNoRecordExistValidator(array $params)
	{
	    $validator = new NoRecordExists($params);
	    $validator->setTranslator($this->getTranslator());
	    $validator->setAdapter($this->getAdapter());
	    
	    return $validator;
	}
}