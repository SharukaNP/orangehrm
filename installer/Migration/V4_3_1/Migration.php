<?php

namespace OrangeHRM\Installer\Migration\V4_3_1;

use Doctrine\DBAL\Types\Types;

class Migration extends \OrangeHRM\Installer\Util\V1\AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        if ($this->getSchemaHelper()->getSchemaManager()->tablesExist('ohrm_reset_password')) {
            $this->getSchemaManager()->dropTable('ohrm_reset_password');
        }
        $this->getSchemaHelper()->createTable('ohrm_reset_password')
            ->addColumn('id', Types::BIGINT, ['Unsigned' => true, 'Autoincrement' => true])
            ->addColumn('reset_email', Types::STRING, ['Length' => 60,'Notnull' => true])
            ->addColumn('reset_request_date', Types::DATETIMETZ_MUTABLE, ['Notnull' => true])
            ->addColumn('reset_code', Types::STRING, ['Length' => 200,'Notnull' => true])
            ->setPrimaryKey(['id'])
            ->create();
    }
}
