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


cj(document).ready(function() {
    // let group_id_regex = /civicrm\/sepa\/listgroup\/.group_id=/g;
    let group_id_regex = /listgroup.group_id=(\d+)$/;

    // add stornieren link
    cj("a[id^=mark_received_]").each(function() {
        // first: hide this button
        cj(this).hide();

        // then: add another button instead
        let setfailed_link = CRM.vars.sepamods.setfailed_url;
        let group_id = cj(this).attr('id').substr(14);
        setfailed_link = setfailed_link.replace('__GROUPID__', group_id);
        cj(this).parent().append("<a href=\"" + setfailed_link + "\" class=\"button button_setfailed\">Rest Stornieren</a>");
    });
});


