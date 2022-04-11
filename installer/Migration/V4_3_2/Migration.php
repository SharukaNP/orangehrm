<?php

namespace OrangeHRM\Installer\Migration\V4_3_2;

use OrangeHRM\Installer\Util\V1\AbstractMigration;

class Migration extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $q = $this->createQueryBuilder();
        $q->select('email_config.sendmail_path')
            ->from('ohrm_email_configuration', 'email_config')
            ->where('email_config.mail_type = :mailType')
            ->setParameter('mailType', 'sendmail')
            ->andWhere($q->expr()->isNotNull('email_config.sendmail_path'))
            ->andWhere('sendmail_path != :empty')
            ->setParameter('empty', '');
        $oldPath = $q->executeQuery()
            ->fetchOne();

        $this->createQueryBuilder()
            ->insert('hs_hr_config')
            ->values(
                [
                    '`key`' => ':key',
                    'value' => ':value'
                ]
            )
            ->setParameter('key', 'email_config.sendmail_path')
            ->setParameter('value', '/usr/sbin/sendmail -bs')
            ->executeQuery();

        if ($oldPath != "") {
            $this->createQueryBuilder()
                ->update('hs_hr_config', 'config')
                ->set('value', ':oldValue')
                ->setParameter('oldValue', $oldPath)
                ->where('key = :sendmailPath')
                ->setParameter('sendmailPath', 'email_config.sendmail_path')
                ->executeQuery();
        }

        $this->getSchemaHelper()->dropColumn('ohrm_email_configuration', 'sendmail_path');

        // add the final one for ohrm_marketplace_addon
    }
}
