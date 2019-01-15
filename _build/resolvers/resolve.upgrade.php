<?php

/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
            break;

        case xPDOTransport::ACTION_UPGRADE:
            /** @var modAction $action */
            if ($action = $modx->getObject('modAction', array('namespace' => 'sitestatistics'))) {
                $action->remove();
                /** @var modMenu $menu */
                if ($menu = $modx->getObject('modMenu', array('text' => 'sitestatistics'))) {
                    $menu->remove();
                }
                @unlink(MODX_CORE_PATH . 'components/sitestatistics/index.class.php');
            }
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            break;
    }
}
return true;