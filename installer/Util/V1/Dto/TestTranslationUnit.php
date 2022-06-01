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

namespace OrangeHRM\Installer\Util\V1\Dto;

class TestTranslationUnit
{
    /**
     * @var string|null
     */
    private ?string $target;

    /**
     * @var string|null
     */
    private ?string $unitId;

    /**
     * @var string|null
     */
    private ?string $source;

    /**
     * @param string|null $target
     * @param string|null $unitId
     */
    public function __construct(?string $target, ?string $unitId, ?string $source)
    {
        $this->target = $target;
        $this->unitId = $unitId;
        $this->source = $source;
    }


    /**
     * @return string|null
     */
    public function getUnitId(): ?string
    {
        return $this->unitId;
    }

    /**
     * @param string|null $unitId
     */
    public function setUnitId(?string $unitId): void
    {
        $this->unitId = $unitId;
    }

    /**
     * @return string|null
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * @param string|null $source
     */
    public function setSource(?string $source): void
    {
        $this->source = $source;
    }

    /**
     * @return string|null
     */
    public function getTarget(): ?string
    {
        return $this->target;
    }

    /**
     * @param string|null $target
     */
    public function setTarget(?string $target): void
    {
        $this->target = $target;
    }
}
