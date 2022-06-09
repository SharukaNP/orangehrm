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

namespace OrangeHRM\Performance\Service;

use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Entity\JobTitle;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\ReviewerRating;
use OrangeHRM\Performance\Dao\PerformanceReviewDao;
use OrangeHRM\Performance\Exception\ReviewServiceException;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class PerformanceReviewService
{
    use AuthUserTrait;
    use EmployeeServiceTrait;

    private ?PerformanceReviewDao $performanceReviewDao = null;

    /**
     * @return PerformanceReviewDao
     */
    public function getPerformanceReviewDao(): PerformanceReviewDao
    {
        if (!($this->performanceReviewDao instanceof PerformanceReviewDao)) {
            $this->performanceReviewDao = new PerformanceReviewDao();
        }
        return $this->performanceReviewDao;
    }

    /**
     * @param PerformanceReview $performanceReview
     * @param int $reviewerEmpNumber
     * @return PerformanceReview
     * @throws ReviewServiceException
     */
    public function activateReview(PerformanceReview $performanceReview, int $reviewerEmpNumber): PerformanceReview
    {
        $this->activateReviewCommonExceptions($performanceReview);
        return $this->getPerformanceReviewDao()->createReview($performanceReview, $reviewerEmpNumber);
    }

    /**
     * @param PerformanceReview $performanceReview
     * @return PerformanceReview
     * @throws ReviewServiceException
     */
    public function updateActivateReview(PerformanceReview $performanceReview, int $reviewerEmpNumber): PerformanceReview
    {
        $this->activateReviewCommonExceptions($performanceReview);
        if (!($this->getEmployeeService()->getEmployeeDao()->getEmployeeByEmpNumber($reviewerEmpNumber)->getEmployeeTerminationRecord()
            == null)) {
            throw ReviewServiceException::pastEmployeeForReviewer();
        }
        if ($this->getPerformanceReviewDao()->getSupervisorRecord(
            $performanceReview->getEmployee()->getEmpNumber(),
            $reviewerEmpNumber
        ) == null) {
            throw ReviewServiceException::invalidSupervisor();
        }

        return $this->getPerformanceReviewDao()->updateReview($performanceReview, $reviewerEmpNumber);
    }

    /**
     * @param PerformanceReview $performanceReview
     * @return void
     * @throws ReviewServiceException
     */
    private function activateReviewCommonExceptions(PerformanceReview $performanceReview): void
    {
        if (!$performanceReview->getEmployee()->getJobTitle() instanceof JobTitle) {
            throw ReviewServiceException::activateWithoutJobTitle();
        }
        if ($this->getPerformanceReviewDao()->getReviewKPI($performanceReview) == null) {
            throw ReviewServiceException::activateWithoutKPI();
        }
    }

    public function saveAndUpdateReviewRatings(PerformanceReview $review, array $ratings, array $kpisForReview): void
    {
        $reviewerRatings = $this->createRatingsFromRows($review, $ratings, $kpisForReview);
        $this->getPerformanceReviewDao()->saveAndUpdateReviewerRatings($reviewerRatings);
    }

    /**
     * @param PerformanceReview $review
     * @param array $rows
     * @return array
     */
    private function createRatingsFromRows(PerformanceReview $review, array $rows, array $kpisForReview): array
    {
        $ratings = [];
        $reviewer = $this->getPerformanceReviewDao()->getSupervisorReviewerForReview(
            $review->getId()
        );

        foreach ($rows as $row) {
            $itemKey = $this->getPerformanceReviewDao()->generateReviewReviewerRatingKey(
                $reviewer->getId(),
                $review->getId(),
                $row['kpiId'],
            );
            $reviewerRating = new ReviewerRating();
            $reviewerRating->setReviewer($reviewer);
            $reviewerRating->getDecorator()->setKpiByKpiId($row['kpiId']);
            $reviewerRating->setComment($row['comment']);
            $reviewerRating->setRating($row['rating']);
            $reviewerRating->setPerformanceReview($review);
            $ratings[$itemKey] = $reviewerRating;
        }
        return $ratings;
    }


}
