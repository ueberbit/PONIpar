<?php

namespace PONIpar\ProductSubitem;

use PONIpar\Exceptions\ONIXException;

class PublisherIdentifier extends Subitem
{

    const TYPE_CB_RELATIE_ID = "10";

    /**
     * PublisherIDType codelist 44
     *
     * @var string
     */
    protected $type = null;

    /**
     * Publisher
     *
     * @var string
     */
    protected $value = null;


    public function __construct($input)
    {
        parent::__construct($input);

        $type = $this->_getSingleChildElementText('PublisherIDType');
        if (!preg_match('/^[0-9]{2}$/', $type)) {
            throw new ONIXException('wrong format of PublisherIDType');
        }
        $this->type = $type;
        $this->value = $this->_getSingleChildElementText("IDValue");

        $this->_forgetSource();
    }

    public function getType()
    {
        return $this->type;
    }

    public function getValue()
    {
        return $this->value;
    }

}
