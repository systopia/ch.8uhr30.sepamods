{*-------------------------------------------------------+
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
+-------------------------------------------------------*}

<h3>Gruppe "{$group_info.reference}" wird auf 'Received' gesetzt, <br/>&nbsp;und {$group_info.in_progress_count} Zuwendungen im Status 'In Progress' werden wie folgt geschlossen:</h3>

<div class="crm-section">
  <div class="label">{$form.status_id.label}</div>
  <div class="content">{$form.status_id.html}</div>
  <div class="clear"></div>
</div>

<div class="crm-section">
  <div class="label">{$form.cancel_reason.label}</div>
  <div class="content">{$form.cancel_reason.html}</div>
  <div class="clear"></div>
</div>

{$form.group_id.label}

<div class="crm-submit-buttons">
  {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
