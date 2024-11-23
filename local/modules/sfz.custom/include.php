<?php
$date = new DateTime();

\CJSCore::RegisterExt('general_change_thema',
[
    'js' => '/local/js/sfz.custom/general_change_thema.js?'.$date->getTimestamp()
]
);
\CJSCore::RegisterExt('type_requests_filtercontract',
[
    'js' => '/local/js/sfz.custom/type_requests_filtercontract.js?'.$date->getTimestamp()
]
);
\CJSCore::RegisterExt('type_throughcomp_hidemanager',
[
    'js' => '/local/js/sfz.custom/type_throughcomp_hidemanager.js?'.$date->getTimestamp()
]
);
\CJSCore::RegisterExt('calendar_hidebooking',
[
    'js' => '/local/js/sfz.custom/calendar_hidebooking.js?'.$date->getTimestamp()
]
);
\CJSCore::RegisterExt('crm_kanban',
[
    'js' => '/local/js/sfz.custom/crm_kanban.js?'.$date->getTimestamp()
]
);
\CJSCore::RegisterExt('group_interface',
[
    'js' => '/local/js/sfz.custom/group_interface.js?'.$date->getTimestamp()
]
);
\CJSCore::RegisterExt('workflow_features',
[
    'js' => '/local/js/sfz.custom/workflow_features.js?'.$date->getTimestamp()
]
);
