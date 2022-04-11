<?php

namespace OrangeHRM\Installer\Migration\V4_1;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;

class Migration extends \OrangeHRM\Installer\Util\V1\AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $this->getSchemaHelper()->changeColumn('hs_hr_config', 'value', ['Type' => Type::getType(Types::TEXT), 'Notnull' => true]);
        $this->insertConfig('open_source_integrations', '<xml><integrations></integrations></xml>');
        $this->insertConfig('authentication.status', 'Enable');
        $this->insertConfig('authentication.enforce_password_strength', 'on');
        $this->insertConfig('authentication.default_required_password_strength', 'strong');
    }

    /**
     * @param string $key
     * @param string $value
     * @return void
     */
    private function insertConfig(string $key, string $value): void
    {
        $this->createQueryBuilder()
            ->insert('hs_hr_config')
            ->values(
                [
                    '`key`' => ':key',
                    'value' => ':value'
                ]
            )
            ->setParameter('key', $key)
            ->setParameter('value', $value)
            ->executeQuery();
    }
}
