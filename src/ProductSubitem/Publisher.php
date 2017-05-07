<?php

namespace PONIpar\ProductSubitem;

use PONIpar\ProductSubitem\Subitem;
use PONIpar\Exceptions\ONIXException;

class Publisher extends Subitem
{

    const ROLE_PUBLISHER = "01";
    const ROLE_COPUBLISHER = "02";

    /**
     * PublisherIdentifier
     *
     * @var PublisherIdentifier
     */
    protected $identifier = null;

    /**
     * PublisherName
     *
     * @var string|null
     */
    protected $name = null;

    /**
     * PublishingRole
     *
     * codelist 45
     *
     * @var string|null
     */
    protected $publishingRole = null;

    public function __construct($input)
    {
        parent::__construct($input);

        $this->identifier = new PublisherIdentifier($this->_getSingleChildElement('PublisherIdentifier'));

        $role = $this->_getSingleChildElementText("PublishingRole");
        if($role && !preg_match('/^[0-9]{2}$/', $role)) {
            throw new ONIXException('wrong format of PublishingRole');
        }
        $this->publishingRole = $role;

        $this->name = $this->_getSingleChildElementText("PublisherName");

        $this->_forgetSource();
    }

    public function getPublisherIdentifier()
    {
        return $this->identifier;
    }

    public function getRole()
    {
        return $this->publishingRole;
    }

    public function getName()
    {
        return $this->name;
    }


}
