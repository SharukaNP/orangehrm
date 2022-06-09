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

namespace OrangeHRM\Core\Authorization\UserRole;

use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Performance\Traits\Service\PerformanceReviewServiceTrait;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Entity\PerformanceTracker;
use OrangeHRM\Entity\PerformanceTrackerLog;
use OrangeHRM\Performance\Traits\Service\PerformanceTrackerLogServiceTrait;
use OrangeHRM\Performance\Traits\Service\PerformanceTrackerServiceTrait;

class EssUserRole extends AbstractUserRole
{
    use AuthUserTrait;
    use PerformanceTrackerServiceTrait;
    use PerformanceTrackerLogServiceTrait;
    use PerformanceReviewServiceTrait;

    /**
     * @inheritDoc
     */
    protected function getAccessibleIdsForEntity(string $entityType, array $requiredPermissions = []): array
    {
        switch ($entityType) {
            case PerformanceReview::class:
                return $this->getAccessibleReviewIds();
            case PerformanceTracker::class:
                return $this->getAccessiblePerformanceTrackerIdsForESS($requiredPermissions);
            case PerformanceTrackerLog::class:
                return $this->getAccessiblePerformanceTrackerLogIdsForESS($requiredPermissions);
            default:
                return [];
        }
    }

    /**
     * @return int[]
     */
    protected function getAccessibleReviewIds(): array
    {
        $empNumber = $this->getEmployeeNumber();
        return $this->getPerformanceReviewService()->getPerformanceReviewDao()->getSelfReviewIds($empNumber);
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    protected function getAccessiblePerformanceTrackerIdsForESS(array $requiredPermissions = []): array
    {
        return $this->getPerformanceTrackerService()
            ->getPerformanceTrackerDao()
            ->getTrackerIdsByEmpNumber($this->getAuthUser()->getEmpNumber());
    }

    /**
     * @param array $requiredPermissions
     * @return int[]
     */
    protected function getAccessiblePerformanceTrackerLogIdsForESS(array $requiredPermissions = []): array
    {
        return $this->getPerformanceTrackerLogService()
            ->getPerformanceTrackerLogDao()
            ->getPerformanceTrackerLogIdsByUserId($this->getAuthUser()->getUserId());
    }
}
