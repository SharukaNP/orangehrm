<?php

namespace OrangeHRM\Installer\Migration\V4_1_2;

class Migration extends \OrangeHRM\Installer\Util\V1\AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $adminUserRoleId = $this->createQueryBuilder()
            ->select('user_role.id')
            ->from('ohrm_user_role', 'user_role')
            ->where('user_role.name = :role')
            ->setParameter('role', 'Admin')
            ->executeQuery()
            ->fetchOne();

        $timeModuleId = $this->createQueryBuilder()
            ->select('module.id')
            ->from('ohrm_module', 'module')
            ->where('module.name = :module')
            ->setParameter('module', 'time')
            ->executeQuery()
            ->fetchOne();

        $this->createQueryBuilder()
            ->update('ohrm_module_default_page', 'module_default')
            ->set('module_default.priority', ':priority')
            ->setParameter('priority', 200)
            ->where('module_default.module_id = :moduleId')
            ->setParameter('moduleId', $timeModuleId)
            ->andWhere('module_default.user_role_id = :roleId')
            ->setParameter('roleId', $adminUserRoleId)
            ->andWhere('module_default.action = :action')
            ->setParameter('action', 'time/defineTimesheetPeriod')
            ->executeQuery();
    }
}
