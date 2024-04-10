<?php
class Customer extends CustomerCore
{
    /*
    * module: developers
    * date: 2024-04-10 17:59:26
    * version: 0.0.1
    */
    public $developer_status;
    /*
    * module: developers
    * date: 2024-04-10 17:59:26
    * version: 0.0.1
    */
    public function __construct($id = null)
    {
        self::setupDefinition();
        parent::__construct($id);
    }
    /*
    * module: developers
    * date: 2024-04-10 17:59:26
    * version: 0.0.1
    */
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
    /*
    * module: developers
    * date: 2024-04-10 17:59:26
    * version: 0.0.1
    */
    protected static function setupDefinition()
    {
        self::$definition['fields']['developer_status'] = [
            'type' => self::TYPE_STRING
        ];
    }
}