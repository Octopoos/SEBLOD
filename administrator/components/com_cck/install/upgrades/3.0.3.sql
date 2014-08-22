
UPDATE `#__categories` SET `extension` = 'com_content'  WHERE `extension` = "extension";

UPDATE `#__cck_core_type_field` SET `live` = 'url_variable', `live_options` = '{"type":"string","multiple":"0","variables":"","variable":"extension","ignore_null":"0","return":"first"}', `live_value` = '' WHERE `typeid` = 5 AND `fieldid` = 402 AND `client` = 'admin';