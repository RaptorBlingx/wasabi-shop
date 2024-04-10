<?php

class Customer extends CustomerCore
{
    public $developer_status;

    public function __construct($id = null)
    {
        self::setupDefinition();
        parent::__construct($id);
    }

    public static function getDevelopers($active = true)
    {
        self::setupDefinition();
        $collection = (new PrestaShopCollection(self::class))
            ->sqlWhere('`developer_status` IS NOT NULL')
            ->where('developer_status', '!=', '');
        if ($active) {
            $collection->where('developer_status', '=', Developers::STATUS_APPROVED);
        }
        return $collection->getResults();
    }

    protected static function setupDefinition()
    {
        self::$definition['fields']['developer_status'] = [
            'type' => self::TYPE_STRING
        ];
    }

}