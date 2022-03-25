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

namespace OrangeHRM\Tools\Migrations\V5;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use Symfony\Component\Yaml\Yaml;

class TranslationTool
{
    use EntityManagerHelperTrait;

    protected ?LangStringHelper $langStringHelper = null;

    /**
     * @param string $groupName
     * @return void
     * @throws Exception
     */
    public function up(string $groupName)
    {
        $filename = 'installer/upgrader/Migrations/V5/messages.bg_BG.xml';
        $langCode = 'bg_BG';
        $this->readTranslations($filename, $langCode);
        $this->addTranslations($langCode, $groupName);
    }

    /**
     * @param string $filepath
     * @param string $language
     * @return void
     */
    private function readTranslations(string $filepath, string $language): void
    {
        $xml = simplexml_load_file($filepath);
        $transArray = ['translations' => []];
        foreach ($xml->file->body->children() as $string) {
            $translation = new TransUnit($string->source, $string->target);
            $transArray['translations'][] = ['source' => $translation->getSource(), 'target' => $translation->getTarget()];
        }
        $Yaml = Yaml::dump($transArray, 2, 4);
        $filename = 'installer/upgrader/Migrations/V5/messages.' . $language . '.yml';
        file_put_contents($filename, $Yaml);
    }

    /**
     * @param string $language
     * @param string $groupName
     * @return void
     * @throws Exception
     */
    private function addTranslations(string $language, string $groupName): void
    {
        $filepath = 'installer/upgrader/Migrations/V5/messages' . $language . '.yml';
        $yml = Yaml::parseFile($filepath);
        $translations = array_shift($yml);
        $filepath2 = 'installer/upgrader/Migrations/V5/' . $groupName . 'LangString.yaml';
        $yml2 = Yaml::parseFile($filepath2);
        $langStrings = array_shift($yml2);
        foreach ($langStrings as $langString) {
            foreach ($translations as $translation) {
                if ($translation['source'] === $langString['value']) {
                    $sourceObj = new TransUnit($langString['value'], $translation['target']);
                    $this->saveTranslationRecord($groupName, $sourceObj, $language);
                    break;
                }
            }
        }
    }

    /**
     * @param string $groupName
     * @param TransUnit $source
     * @param string $language
     * @return void
     * @throws Exception
     */
    private function saveTranslationRecord(string $groupName, TransUnit $source, string $language): void
    {
        $groupId = $this->getLangStringHelper()->getGroupId($groupName);
        $langStringId = $this->getLangStringHelper()->getLangStringRecord($source->getSource(), $groupId);
        if ($langStringId == null) {
            throw new Exception('Cannot add a translation to a non existent lang string: ' . $source->getSource());
        }
        $langId = $this->getLanguageId($language);
        $existTranslation = $this->getTranslationRecord($langStringId, $langId);
        if ($existTranslation != null) {
            // TODO hanldle customized translations
        } else {
            $insetQuery = $this->createQueryBuilder();
            $insetQuery->insert('ohrm_i18n_translate')->values(['lang_string_id' => ':langStringId', 'language_id' => ':langId', 'value' => ':target',])->setParameter('langStringId', array_column($langStringId, 'id'), Connection::PARAM_INT_ARRAY)->setParameter('langId', $langId)->setParameter('target', $source->getTarget())->executeQuery();
        }
    }

    /**
     * @return LangStringHelper|null
     */
    public function getLangStringHelper(): ?LangStringHelper
    {
        if (is_null($this->langStringHelper)) {
            $this->langStringHelper = new LangStringHelper();
        }
        return $this->langStringHelper;
    }

    /**
     * @param string $langCode
     * @return int
     * @throws Exception
     */
    private function getLanguageId(string $langCode): int
    {
        $searchQuery = $this->createQueryBuilder();
        $searchQuery->select('language.id')->from('ohrm_i18n_language', 'language')->where('language.code = :langCode')->setParameter('langCode', $langCode);
        return $searchQuery->executeQuery()->fetchOne();
    }

    /**
     * @return QueryBuilder
     */
    protected function createQueryBuilder(): QueryBuilder
    {
        return $this->getEntityManager()->getConnection()->createQueryBuilder();
    }

    /**
     * @param array $langStringId
     * @param int $langId
     * @return string
     * @throws Exception
     */
    private function getTranslationRecord(array $langStringId, int $langId): string
    {
        $searchQuery = $this->createQueryBuilder();
        $searchQuery->select('translate.id')->from('ohrm_i18n_translate', 'translate')->where('translate.language_id = :langCode')->andWhere('translate.lang_string_id = :langStringId')->setParameter('langCode', $langId)->setParameter('langStringId', array_column($langStringId, 'id'), Connection::PARAM_INT_ARRAY);
        return $searchQuery->executeQuery()->fetchOne();
    }

}
