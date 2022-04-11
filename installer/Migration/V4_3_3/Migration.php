<?php

namespace OrangeHRM\Installer\Migration\V4_3_3;

use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;

class Migration extends \OrangeHRM\Installer\Util\V1\AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
//        $this->getSchemaHelper()->changeColumn('hs_hr_employee', 'emp_number', [
//            'Type'=>Type::getType(Types::INTEGER),'Length'=> 7, 'Notnull' => true,'Autoincrement'=>true
//        ]);
        $this->getSchemaHelper()->changeColumn('ohrm_timesheet', 'timesheet_id', [
            'Type'=>Type::getType(Types::BIGINT),'Length'=> 20, 'Notnull' => true,'Autoincrement'=>true
        ]);
        $this->getSchemaHelper()->changeColumn('ohrm_timesheet_item', 'timesheet_item_id', [
            'Type'=>Type::getType(Types::BIGINT),'Length'=> 20, 'Notnull' => true,'Autoincrement'=>true
        ]);
        $this->getSchemaHelper()->changeColumn('ohrm_timesheet_action_log', 'timesheet_action_log_id', [
            'Type'=>Type::getType(Types::BIGINT),'Length'=> 20, 'Notnull' => true,'Autoincrement'=>true
        ]);
        $this->getSchemaHelper()->changeColumn('ohrm_attendance_record', 'id', [
            'Type'=>Type::getType(Types::BIGINT),'Length'=> 20, 'Notnull' => true,'Autoincrement'=>true
        ]);

        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_candidate_attachment', ['ohrm_job_candidate_attachment_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_candidate_history', ['ohrm_job_candidate_history_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_candidate_vacancy', ['ohrm_job_candidate_vacancy_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_interview', ['ohrm_job_interview_ibfk_2']);

        $this->getSchemaHelper()->changeColumn('ohrm_job_candidate', 'id', [
            'Type'=>Type::getType(Types::INTEGER),'Length'=> 13, 'Notnull' => true,'Autoincrement'=>true
        ]);

        $jobCandidateAttachmentFK = new ForeignKeyConstraint(
            ['candidate_id'],
            'ohrm_job_candidate',
            ['id'],
            'ohrm_job_candidate_attachment_ibfk_1',
            ['onDelete' => 'CASCADE']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_job_candidate_attachment', $jobCandidateAttachmentFK);

        $jobCandidateHistoryFK1 = new ForeignKeyConstraint(
            ['candidate_id'],
            'ohrm_job_candidate',
            ['id'],
            'ohrm_job_candidate_history_ibfk_1',
            ['onDelete' => 'CASCADE']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_job_candidate_history', $jobCandidateHistoryFK1);

        $jobCandidateVacancyFK1 = new ForeignKeyConstraint(
            ['candidate_id'],
            'ohrm_job_candidate',
            ['id'],
            'ohrm_job_candidate_vacancy_ibfk_1',
            ['onDelete' => 'CASCADE']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_job_candidate_vacancy', $jobCandidateVacancyFK1);

        $jobInterviewFK2 = new ForeignKeyConstraint(
            ['candidate_id'],
            'ohrm_job_candidate',
            ['id'],
            'ohrm_job_interview_ibfk_2',
            ['onDelete' => 'CASCADE']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_job_interview', $jobInterviewFK2);

        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_interview', ['ohrm_job_interview_ibfk_1']);

        $this->getSchemaHelper()->changeColumn('ohrm_job_candidate_vacancy', 'id', [
            'Type'=>Type::getType(Types::INTEGER),'Length'=> 13, 'Notnull' => true,'Autoincrement'=>true
        ]);

        $jobInterviewFK1 = new ForeignKeyConstraint(
            ['candidate_vacancy_id'],
            'ohrm_job_candidate_vacancy',
            ['id'],
            'ohrm_job_interview_ibfk_1',
            ['onDelete' => 'SET NULL']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_job_interview', $jobInterviewFK1);

        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_candidate_history', ['ohrm_job_candidate_history_ibfk_2']);

        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_candidate_vacancy', ['ohrm_job_candidate_vacancy_ibfk_2']);

        $this->getSchemaHelper()->dropForeignKeys('ohrm_job_vacancy_attachment', ['ohrm_job_vacancy_attachment_ibfk_1']);

        $this->getSchemaHelper()->changeColumn('ohrm_job_vacancy', 'id', [
            'Type'=>Type::getType(Types::INTEGER),'Length'=> 13, 'Notnull' => true,'Autoincrement'=>true
        ]);

        $jobCandidateHistoryFK2 = new ForeignKeyConstraint(
            ['vacancy_id'],
            'ohrm_job_vacancy',
            ['id'],
            'ohrm_job_candidate_history_ibfk_2',
            ['onDelete' => 'SET NULL']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_job_candidate_history', $jobCandidateHistoryFK2);

        $jobCandidateVacancyFK2 = new ForeignKeyConstraint(
            ['vacancy_id'],
            'ohrm_job_vacancy',
            ['id'],
            'ohrm_job_candidate_vacancy_ibfk_2',
            ['onDelete' => 'CASCADE']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_job_candidate_vacancy', $jobCandidateVacancyFK2);

        $jobVacancyAttachmentFK1 = new ForeignKeyConstraint(
            ['vacancy_id'],
            'ohrm_job_vacancy',
            ['id'],
            'ohrm_job_vacancy_attachment_ibfk_1',
            ['onDelete' => 'CASCADE']
        );
        $this->getSchemaHelper()->addForeignKey('ohrm_job_vacancy_attachment', $jobVacancyAttachmentFK1);




    }
}
