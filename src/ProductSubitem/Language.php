<?php

namespace PONIpar\ProductSubitem;

use PONIpar\ProductSubitem\Subitem;
use PONIpar\Exceptions\ONIXException;


/**
 * <Language> code list
 */
class Language extends Subitem
{

    const ROLE_TEXT = "01";

    /**
     * LanguageRole
     *
     * codelist 22
     *
     * @var string
     */
    protected $role = null;

    /**
     * Language Code (ISO 639-2/B)
     *
     * codelist 74
     *
     * @var string
     */
    protected $code = null;

    public function __construct($input)
    {
        parent::__construct($input);


        $role = $this->_getSingleChildElementText("LanguageRole");
        if($role && !preg_match('/^[0-9]{2}$/', $role)) {
            throw new ONIXException('wrong format of LanguageRole');
        }
        $this->role = $role;
        $this->code = $this->_getSingleChildElementText("LanguageCode");

        $this->_forgetSource();
    }

    public function getRole()
    {
        return $this->role;
    }

    public function getCode()
    {
        return $this->code;
    }

}
