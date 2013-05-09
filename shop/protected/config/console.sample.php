<?php

return CMap::mergeArray(require(dirname(__FILE__) . '/base.php'), array(
            'name' => 'Console Application',
            'import' => array(
                'application.modules.user.*',
            ),
            'components' => array(
                'db' => array(
                    'password' => '',
                ),
            ),
));
