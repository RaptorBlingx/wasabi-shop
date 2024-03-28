<?php

class Customer extends CustomerCore
{
    public $developer_status;

    public function __construct($id = null)
    {
        self::$definition['fields']['developer_status'] = [
            'type' => self::TYPE_STRING
        ];
        parent::__construct($id);
    }

    public static function getDevelopers($active = true)
    {
        $collection = (new PrestaShopCollection('Customer'))
            ->sqlWhere('`deveveloper_status` IS NOT NULL')
            ->where('developer_status', '!=', '');
        if ($active) {
            $collection->where('developer_status', '=', Developers::STATUS_APPROVED);
        }
        return $collection->getResults();
    }

}