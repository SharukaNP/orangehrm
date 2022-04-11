<?php

namespace OrangeHRM\Installer\Migration\V4_1_1;

use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;

class Migration extends \OrangeHRM\Installer\Util\V1\AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(): void
    {
        $this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_member_detail', ['hs_hr_emp_member_detail_ibfk_1']);
        $this->getSchemaHelper()->dropForeignKeys('hs_hr_emp_member_detail', ['hs_hr_emp_member_detail_ibfk_2']);

        $this->getSchemaHelper()->dropPrimaryKey('hs_hr_emp_member_detail');

        $this->getSchemaHelper()->addColumn('hs_hr_emp_member_detail','id',Types::INTEGER,['Length'=>'6','Notnull'=>false]);
        $pk = new Index('PRIMARY',['id'],true,true);
        $this->getSchemaManager()->createIndex($pk,'hs_hr_emp_member_detail');
        $this->getSchemaHelper()->changeColumn('hs_hr_emp_member_detail','id',['Autoincrement' => true]);

    }
}
