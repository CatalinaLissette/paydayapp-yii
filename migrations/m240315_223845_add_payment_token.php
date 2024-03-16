<?php

use yii\db\Migration;

/**
 * Class m240315_223845_add_payment_token
 */
class m240315_223845_add_payment_token extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('payment_info', 'request_token', $this->string(32)->defaultValue(''));
        $this->createIndex('request_token_idx', 'payment_info', 'request_token');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('request_token_idx', 'payment_info');
        $this->dropColumn('payment_info', 'request_token');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240315_223845_add_payment_token cannot be reverted.\n";

        return false;
    }
    */
}
