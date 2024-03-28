<?php
class Customer extends CustomerCore
{
    /*
    * module: developers
    * date: 2024-03-28 14:23:57
    * version: 0.0.1
    */
    public $developer_status;
    /*
    * module: developers
    * date: 2024-03-28 14:23:57
    * version: 0.0.1
    */
    public function __construct($id = null)
    {
        self::$definition['fields']['developer_status'] = [
            'type' => self::TYPE_STRING
        ];
        parent::__construct($id);
    }
    /*
    * module: developers
    * date: 2024-03-28 14:23:57
    * version: 0.0.1
    */
    public static function getDevelopers($active = true)
    {
        $collection = (new PrestaShopCollection('Customer'))
            ->sqlWhere('`deveveloper_status` IS NOT NULL');
        if ($active) {
            $collection->where('developer_status', '=', Developers::STATUS_APPROVED);
        }
        return $collection->getResults();
    }
}