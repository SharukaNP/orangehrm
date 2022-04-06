<?php

namespace OrangeHRM\Installer\Migration\V4_6_0;

use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\UniqueConstraint;
use Doctrine\DBAL\Types\Types;
use OrangeHRM\Installer\Util\V1\AbstractMigration;
use Symfony\Component\Yaml\Yaml;

class Migration extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        if (!$this->getSchemaHelper()->tableExists('ohrm_i18n_group')) {
            $this->getSchemaHelper()->createTable('ohrm_i18n_group')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
                ->addColumn('name', Types::STRING, ['Length' => 255])
                ->addColumn('title', Types::STRING, ['Length' => 255, 'Default' => null])
                ->setPrimaryKey(['id'])
                ->create();
        }

        if (!$this->getSchemaHelper()->tableExists('ohrm_i18n_language')) {
            $this->getSchemaHelper()->createTable('ohrm_i18n_language')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
                ->addColumn('name', Types::STRING, ['Length' => 255, 'Default' => null])
                ->addColumn('code', Types::STRING, ['Length' => 100, 'Notnull' => false])
                ->addColumn('enabled', Types::SMALLINT, ['Unsigned' => true, 'Notnull' => false, 'Default' => 1])
                ->addColumn('added', Types::SMALLINT, ['Unsigned' => true, 'Notnull' => false, 'Default' => 0])
                ->addColumn('modified_at', Types::DATETIME_MUTABLE, ['Default' => null])
                ->setPrimaryKey(['id'])
                ->addUniqueConstraint(['code'])
                ->create();
        }

        if (!$this->getSchemaHelper()->tableExists('ohrm_i18n_lang_string')) {
            $this->getSchemaHelper()->createTable('ohrm_i18n_lang_string')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
                ->addColumn('unit_id', Types::INTEGER, ['Notnull' => true])
                ->addColumn('source_id', Types::INTEGER)
                ->addColumn('group_id', Types::INTEGER, ['Default' => null])
                ->addColumn('value', Types::TEXT, ['Notnull' => true, 'CustomSchemaOptions' => ['collation' => 'utf8mb4_bin']])
                ->addColumn('note', Types::TEXT)
                ->addColumn('version', Types::STRING, ['Length' => 20, 'Default' => null])
                ->setPrimaryKey(['id'])
                ->create();
        }

        if (!$this->getSchemaHelper()->tableExists('ohrm_i18n_translate')) {
            $this->getSchemaHelper()->createTable('ohrm_i18n_translate')
                ->addColumn('id', Types::INTEGER, ['Autoincrement' => true])
                ->addColumn('lang_string_id', Types::INTEGER, ['Notnull' => false])
                ->addColumn('language_id', Types::INTEGER, ['Notnull' => false])
                ->addColumn('value', Types::TEXT)
                ->addColumn('translated', Types::SMALLINT, ['Unsigned' => true, 'Default' => 1])
                ->addColumn('customized', Types::SMALLINT, ['Unsigned' => true, 'Default' => 0])
                ->addColumn('version', Types::STRING, ['Length' => 20, 'Default' => null])
                ->addColumn('modified_at', Types::DATETIMETZ_MUTABLE, ['Notnull' => false, 'Default' => 'CURRENT_TIMESTAMP'])
                ->setPrimaryKey(['id'])
                ->create();
        }

        $foreignKeyConstraint = new ForeignKeyConstraint(
            ['group_id'],
            'ohrm_i18n_group',
            ['id'],
            'groupId',
            ['onDelete' => 'SET NULL']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_i18n_lang_string', $foreignKeyConstraint);

        $foreignKeyConstraint = new ForeignKeyConstraint(
            ['language_id'],
            'ohrm_i18n_language',
            ['id'],
            'languageId',
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_i18n_translate', $foreignKeyConstraint);

        $foreignKeyConstraint = new ForeignKeyConstraint(
            ['lang_string_id'],
            'ohrm_i18n_lang_string',
            ['id'],
            'langStringId',
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_i18n_translate', $foreignKeyConstraint);

        $foreignKeyConstraint = new ForeignKeyConstraint(
            ['source_id'],
            'ohrm_i18n_source',
            ['id'],
            'sourceId',
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_i18n_lang_string', $foreignKeyConstraint);

        $uniqueConstraint = new UniqueConstraint(
            'translateUniqueId',
            ['lang_string_id', 'language_id']
        );
        $this->getSchemaHelper()->getSchemaManager()->createUniqueConstraint($uniqueConstraint, 'ohrm_i18n_translate');
        $this->getDataGroupHelper()->insertScreenPermissions(__DIR__ . '/permission/screen.yaml');
        $languages = $this->readlanguageYaml(__DIR__ . '/language/languages.yaml');
        $this->insertLanguages($languages);
        $groups = $this->readlanguageYaml(__DIR__ . '/language/groups.yaml');
        $this->insertGroups($groups);
    }

    /**
     * @param string $filepath
     * @return array
     */
    private function readlanguageYaml(string $filepath): array
    {
        $apiPermissions = [];
        $yaml = Yaml::parseFile($filepath);
        $array = array_shift($yaml);
        return $array;
    }

    /**
     * @param array $languages
     * @return void
     */
    private function insertLanguages(array $languages): void
    {
        foreach ($languages as $language) {
            $this->createQueryBuilder()
                ->insert('ohrm_i18n_language')
                ->values(
                    [
                        'name' => ':name',
                        'code' => ':code',
                        'enabled' => ':enabled',
                        'added' => ':added'
                    ]
                )
                ->setParameter('name', $language['name'])
                ->setParameter('code', $language['code'])
                ->setParameter('enabled', $language['enabled'])
                ->setParameter('added', $language['added'])
                ->executeQuery();
        }
    }

    /**
     * @param array $groups
     * @return void
     */
    private function insertGroups(array $groups): void
    {
        foreach ($groups as $group) {
            $this->createQueryBuilder()
                ->insert('ohrm_i18n_group')
                ->values(
                    [
                        'name' => ':name',
                        'title' => ':title'
                    ]
                )
                ->setParameter('name', $group['name'])
                ->setParameter('title', $group['title'])
                ->executeQuery();
        }
    }
}
