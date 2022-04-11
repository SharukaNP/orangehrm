<?php

namespace OrangeHRM\Installer\Migration\V4_3;

use Doctrine\DBAL\Types\Types;

class Migration extends \OrangeHRM\Installer\Util\V1\AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $this->createQueryBuilder()
            ->insert('ohrm_module')
            ->values(
                [
                    'name' => ':name',
                    'status' => ':status'
                ]
            )
            ->setParameter('name', 'marketPlace')
            ->setParameter('status', 1)
            ->executeQuery();

        $this->getDataGroupHelper()->insertScreenPermissions(__DIR__ . '/permission/screen.yaml');

        $this->createQueryBuilder()
            ->insert('hs_hr_config')
            ->values(
                [
                    '`key`' => ':key',
                    'value' => ':value'
                ]
            )
            ->setParameter('key', 'base_url')
            ->setParameter('value', 'https://marketplace.orangehrm.com')
            ->executeQuery();

        $this->getDataGroupHelper()->insertDataGroupPermissions(__DIR__ . '/permission/data_group.yaml');
        
        $dataGroupId = $this->createQueryBuilder()
            ->select('data_group.id')
            ->from('ohrm_data_group', 'data_group')
            ->where('name = :name')
            ->setParameter('name', 'Marketplace')
            ->executeQuery()
            ->fetchOne();

        $homeScreenId = $this->createQueryBuilder()
            ->select('screen.id')
            ->from('ohrm_screen', 'screen')
            ->where('action_url =:actionUrl')
            ->setParameter('actionUrl', 'ohrmAddons')
            ->executeQuery()
            ->fetchOne();

        $this->createQueryBuilder()
            ->insert('ohrm_data_group_screen')
            ->values(
                [
                    'data_group_id' => ':dataGroupId',
                    'screen_id' => ':screenId',
                    'permission' => ':permission'
                ]
            )
            ->setParameter('dataGroupId', $dataGroupId)
            ->setParameter('screenId', $homeScreenId)
            ->setParameter('permission', 1)
            ->executeQuery();


        if ($this->getSchemaHelper()->getSchemaManager()->tablesExist('ohrm_reset_password')) {
            $this->getSchemaHelper()->createTable('ohrm_marketplace_addon')
                ->addColumn('addon_id', Types::INTEGER, ['Length' => 11, 'Autoincrement' => true])
                ->addColumn('title', Types::STRING, ['Length' => 100])
                ->addColumn('date', Types::DATETIMETZ_MUTABLE)
                ->addColumn('status', Types::STRING, ['Length' => 30])
                ->addColumn('version', Types::STRING, ['Length' => 100])
                ->addColumn('plugin_name', Types::STRING, ['Length' => 255])
                ->setPrimaryKey(['addon_id'])
                ->create();
        }
    }
}
