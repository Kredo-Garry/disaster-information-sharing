<?php

return [
    // ✅ WordPress REST API（Earthquakeページ本文をJSONで取得）
    'earthquake_url' => env('PHIVOLCS_EARTHQUAKE_URL', 'https://www.phivolcs.dost.gov.ph/wp-json/wp/v2/pages/40517'),

    // 津波（通常のWeb）
    'tsunami_url' => env('PHIVOLCS_TSUNAMI_URL', 'https://tsunami.phivolcs.dost.gov.ph/'),

    // 火山（LAVAのBulletin一覧）
    'volcano_list_url' => env('PHIVOLCS_VOLCANO_LIST_URL', 'https://wovodat.phivolcs.dost.gov.ph/bulletin/list-of-bulletin'),
];
