<?php
//	Copyright 2017 Itach-soft.
//
if (!defined('FREEPBX_IS_AUTH')) {
    die('No direct script access allowed');
}

echo '<h2 id="title">' . _('IVR Statistics') . '</h2>';


?>


<table id="ivrstat_log"
       data-url="ajax.php?module=ivrstat&view=stat&command=getJSON&jdata=statistic"
       data-toolbar="#toolbar-main"
       data-cache="false"
       data-toggle="table"
       data-search="true"
       data-pagination="true"
       data-show-export="true"
       data-show-refresh="true"
       data-page-list="[5, 10, 15, 20, 'All']"
       class="table table-striped">
    <thead>
    <tr>
        <th data-field="date" data-sortable="true"><?php echo _("Date") ?></th>
        <th data-field="time" data-sortable="true"><?php echo _("Time") ?></th>
        <th data-field="uniqueid" data-sortable="true"><?php echo _("Uniqueid") ?></th>
        <th data-field="calleridnum" data-sortable="true"><?php echo _("Client CallerID") ?></th>
        <th data-field="agent" data-sortable="true"><?php echo _("Agent") ?></th>
        <th data-field="ivrsel" data-sortable="true"><?php echo _("IVR selection") ?></th>
        <th data-field="ivr" data-sortable="true"><?php echo _("IVR menu") ?></th>
    </tr>
    </thead>
</table>
