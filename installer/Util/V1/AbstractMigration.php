<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Installer\Util\V1;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;

abstract class AbstractMigration
{
    // TODO:: remove and create new connection for installer
    use EntityManagerHelperTrait;

    private ?SchemaHelper $schemaHelper = null;
    private ?DataGroupHelper $dataGroupHelper = null;
    private ?LanguageHelper $languageHelper = null;

    /**
     * @return AbstractSchemaManager
     * @throws Exception
     */
    protected function getSchemaManager(): AbstractSchemaManager
    {
        return $this->getEntityManager()->getConnection()->createSchemaManager();
    }

    /**
     * @return Connection
     */
    protected function getConnection(): Connection
    {
        return $this->getEntityManager()->getConnection();
    }

    /**
     * @return QueryBuilder
     */
    protected function createQueryBuilder(): QueryBuilder
    {
        return $this->getEntityManager()->getConnection()->createQueryBuilder();
    }

    /**
     * @return SchemaHelper
     */
    protected function getSchemaHelper(): SchemaHelper
    {
        if (!$this->schemaHelper instanceof SchemaHelper) {
            $this->schemaHelper = new SchemaHelper($this->getSchemaManager());
        }
        return $this->schemaHelper;
    }

    /**
     * @return DataGroupHelper
     */
    public function getDataGroupHelper(): DataGroupHelper
    {
        if (!$this->dataGroupHelper instanceof DataGroupHelper) {
            $this->dataGroupHelper = new DataGroupHelper($this->getConnection());
        }
        return $this->dataGroupHelper;
    }

    /**
     * @return LanguageHelper
     */
    public function getLangHelper(): LanguageHelper
    {
        if (!$this->languageHelper instanceof LanguageHelper) {
            $this->languageHelper = new LanguageHelper();
        }
        return $this->languageHelper;
    }

    /**
     * Define schema increment or data insertion
     */
    abstract public function up(): void;
}
