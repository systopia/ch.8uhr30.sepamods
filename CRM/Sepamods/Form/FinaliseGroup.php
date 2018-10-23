<?php
/*-------------------------------------------------------+
| CiviSEPA Modifications for CH                          |
| Copyright (C) 2018 SYSTOPIA                            |
| Author: B. Endres (endres@systopia.de)                 |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+--------------------------------------------------------*/

use CRM_Sepamods_ExtensionUtil as E;

/**
 * This form lets you close SEPA Groups, while
 *  changing the status of any remaining 'In Progress' Contributions
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Sepamods_Form_FinaliseGroup extends CRM_Core_Form {

  // will be set by buildQuickForm
  protected $group_id = NULL;


  public function buildQuickForm() {

    // get group ID
    $this->group_id = CRM_Utils_Request::retrieve('gid', 'Integer');
    if (empty($this->group_id)) {
      throw new Exception(E::ts("No Group ID given"));
    }

    // get some group data
    $group_info = $this->getGroupInfo();
    $this->assign('group_info', $group_info);

    if ($group_info['sdd_creditor_id'] != 3) {
      CRM_Core_Session::setStatus("Dies ist keine LSV+ Gruppe!", "Achtung", 'warning');
    }

    // build form
    $this->add(
      'select',
      'status_id',
      'Status',
      array(
          3 => 'Cancelled',
          4 => 'Failed',
          7 => 'Chargeback',
      ),
      TRUE
    );

    $this->add(
        'text',
        'cancel_reason',
        'Stornogrund',
        [],
        TRUE
    );

    // misc stuff
    $this->add('hidden', 'gid', $this->group_id);

    $this->addButtons(array(
      array(
          'type' => 'back',
          'name' => 'Abbruch',
          'isDefault' => FALSE,
      ),
      array(
          'type' => 'submit',
          'name' => 'Ausführen',
          'isDefault' => TRUE,
      ),
    ));

    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();

    // execute
    $group_info = $this->getGroupInfo();

    // finalise contributions
    $now = date('YmdHis');
    foreach ($group_info['in_progress_ids'] as $contribution_id) {
      civicrm_api3('Contribution', 'create', [
          'id'                     => $contribution_id,
          'contribution_status_id' => $values['status_id'],
          'cancel_reason'          => $values['cancel_reason'],
          'cancel_date'            => $now]);
    }

    // finally: close the group itself
    civicrm_api3('SepaTransactionGroup', 'create', [
        'id'        => $this->group_id,
        'status_id' => 6]);

    CRM_Core_Session::setStatus("Zuwendungen angepasst, Gruppe wurde als 'Received' markiert.", "Gruppe geschlossen", 'info');
    CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/sepa/dashboard', 'status=closed'));
  }

  /**
   * Get some information on the group
   */
  protected function getGroupInfo() {
    $group_info = civicrm_api3('SepaTransactionGroup', 'getsingle', ['id' => $this->group_id]);

    // load contribution IDs
    $contribution_ids = [];
    $in_progress_status_id = 5;
    $contribution_query = CRM_Core_DAO::executeQuery("
        SELECT contribution_id AS cid 
        FROM civicrm_contribution contribution
        LEFT JOIN civicrm_sdd_contribution_txgroup ctxg ON ctxg.contribution_id = contribution.id
        LEFT JOIN civicrm_sdd_txgroup              txg  ON txg.id = ctxg.txgroup_id
        WHERE ctxg.txgroup_id = {$this->group_id}
          AND contribution.contribution_status_id = {$in_progress_status_id}");
    while ($contribution_query->fetch()) {
      $contribution_ids[] = $contribution_query->cid;
    }

    // add to data
    $group_info['in_progress_count'] = count($contribution_ids);
    $group_info['in_progress_ids']   = $contribution_ids;

    return $group_info;
  }
}
