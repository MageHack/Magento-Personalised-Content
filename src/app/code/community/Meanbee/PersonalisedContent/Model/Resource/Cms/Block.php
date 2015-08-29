<?php
 
class Meanbee_PersonalisedContent_Model_Resource_Cms_Block extends Mage_Cms_Model_Resource_Block {

    /**
     * Save personalisation tags after block has been saved.
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Cms_Model_Resource_Block
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $old_tags = $this->lookupPersonalisationTags($object->getId());
        $new_tags = (array)$object->getData('personalisation_tags');

        $table  = $this->getTable('meanbee_personalisedcontent/cms_block_personalisation');
        $insert = array_diff($new_tags, $old_tags);
        $delete = array_diff($old_tags, $new_tags);

        if ($delete) {
            $where = array(
                'block_id = ?'     => (int) $object->getId(),
                'category_id IN (?)' => $delete
            );

            $this->_getWriteAdapter()->delete($table, $where);
        }

        if ($insert) {
            $data = array();

            foreach ($insert as $category_id) {
                $data[] = array(
                    'block_id'  => (int) $object->getId(),
                    'category_id' => (int) $category_id
                );
            }

            $this->_getWriteAdapter()->insertMultiple($table, $data);
        }
        return parent::_afterSave($object);
    }

    /**
     * Clean up personalisation tags after block is deleted
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Cms_Model_Resource_Page
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        $condition = array(
            'block_id = ?' => (int) $object->getId(),
        );

        $this->_getWriteAdapter()->delete($this->getTable('meanbee_personalisedcontent/cms_block_personalisation'), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * Set personalisation tags on the object.
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Cms_Model_Resource_Block
     */
    protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
        if ($object->getId()) {
            $object->setData('personalisation_tags', $this->lookupPersonalisationTags($object->getId()));
        }
        return parent::_afterLoad($object);
    }

    /**
     * Look up Personalisation tags
     *
     * @param $block_id
     * @return array
     */
    public function lookupPersonalisationTags($block_id)
    {
        $adapter = $this->_getReadAdapter();

        $select  = $adapter->select()
            ->from($this->getTable('meanbee_personalisedcontent/cms_block_personalisation'), 'category_id')
            ->where('block_id = :block_id');

        $binds = array(
            ':block_id' => (int) $block_id
        );

        return $adapter->fetchCol($select, $binds);

    }
}
