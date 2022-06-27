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

namespace OrangeHRM\Recruitment\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Recruitment\Service\RecruitmentAttachmentService;

class SaveCandidateController extends AbstractVueController
{
    protected ?ConfigService $configService = null;

    public function getConfigService(): ConfigService
    {
        if (!$this->configService instanceof ConfigService) {
            $this->configService = new ConfigService();
        }
        return $this->configService;
    }

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        if ($request->attributes->has('id')) {
            $component = new Component('view-candidate-profile');
            $component->addProp(new Prop('candidate-id', Prop::TYPE_NUMBER, $request->attributes->getInt('id')));
        } else {
            $component = new Component('save-candidate');
        }

        $component->addProp(new Prop('max-file-size', Prop::TYPE_NUMBER, 1024 * 1024));
        $component->addProp(
            new Prop(
                'allowed-file-types',
                Prop::TYPE_ARRAY,
                RecruitmentAttachmentService::ALLOWED_CANDIDATE_ATTACHMENT_FILE_TYPES
            )
        );
        $this->setComponent($component);
    }
}
