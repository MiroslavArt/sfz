<?php
$date = new DateTime();

\CJSCore::RegisterExt('general_change_thema',
[
    'js' => '/local/js/general_change_thema.js?'.$date->getTimestamp()
]
);
\CJSCore::RegisterExt('type_requests_filtercontract',
[
    'js' => '/local/js/type_requests_filtercontract.js?'.$date->getTimestamp()
]
);
\CJSCore::RegisterExt('type_throughcomp_hidemanager',
[
    'js' => '/local/js/type_throughcomp_hidemanager.js?'.$date->getTimestamp()
]
);
\CJSCore::RegisterExt('calendar_hidebooking',
[
    'js' => '/local/js/calendar_hidebooking.js?'.$date->getTimestamp()
]
);
