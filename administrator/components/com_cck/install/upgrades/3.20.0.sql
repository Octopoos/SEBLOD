
UPDATE `#__cck_core_fields` SET `selectlabel` = 'Auto' WHERE `name` = "core_orientation_vertical";

UPDATE `#__cck_core_search_field` SET `link_options` = REPLACE( `link_options`, '"form_edition":"1"', '"form_edition":"0"' ) WHERE `link` = 'cck_form' AND `link_options` LIKE '{"form":%' AND `link_options` NOT LIKE '{"form":""%';

UPDATE `#__cck_core_type_field` SET `link_options` = REPLACE( `link_options`, '"form_edition":"1"', '"form_edition":"0"' ) WHERE `link` = 'cck_form' AND `link_options` LIKE '{"form":%' AND `link_options` NOT LIKE '{"form":""%';
UPDATE `#__cck_core_fields` SET `options2` = REPLACE( `options2`, '\\\"form_edition\\\":\\\"1\\\"', '\\\"form_edition\\\":\\\"0\\\"' ) WHERE `type` = "button_free" AND `options2` LIKE '%\\\"button_link\\\":\\\"cck_form\\\"%' AND `options2` NOT LIKE '\\\"form\\\":\\\"\\\"%';