<?php
/**
 * Copyright (c) 2019 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv3, see docs/LICENSE
 */

define('PCSAGE_BACKSTEPS', '../../../../../../../');
chdir(PCSAGE_BACKSTEPS);

require_once("Services/Init/classes/class.ilInitialisation.php");
ilInitialisation::initILIAS();

require_once __DIR__ . "/classes/class.ilPCSageCellContentGUI.php";
$gui = new ilPCSageCellContentGUI();
$gui->show();
?>