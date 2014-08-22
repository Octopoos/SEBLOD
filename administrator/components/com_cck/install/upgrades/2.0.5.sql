
ALTER TABLE `#__cck_core_fields` CHANGE `maxlength` `maxlength` INT( 11 ) NOT NULL DEFAULT '255';

INSERT INTO `#__cck_core_fields` (`id`, `title`, `name`, `folder`, `type`, `description`, `published`, `label`, `selectlabel`, `display`, `required`, `validation`, `defaultvalue`, `options`, `options2`, `minlength`, `maxlength`, `size`, `cols`, `rows`, `ordering`, `sorting`, `divider`, `bool`, `location`, `extended`, `style`, `script`, `bool2`, `bool3`, `bool4`, `bool5`, `bool6`, `bool7`, `bool8`, `css`, `attributes`, `storage`, `storage_location`, `storage_table`, `storage_field`, `storage_field2`, `storage_params`, `storages`, `checked_out`, `checked_out_time`) VALUES
(229, 'Core Class Pagination', 'core_class_pagination', 3, 'text', '', 0, 'Class', ' ', 3, '', '', 'pagination', '', '', 0, 255, 12, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'dev', '', '', 'class_pagination', '', '', '', 0, '0000-00-00 00:00:00'),
(230, 'Core Tmpl', 'core_tmpl', 3, 'select_simple', '', 0, 'Tmpl', 'Default', 3, '', '', '', 'Component=component', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'dev', '', '', 'tmpl', '', '', '', 0, '0000-00-00 00:00:00'),
(231, 'Core Position Sidebody', 'core_position_sidebody', 3, 'select_simple', '', 0, '', ' ', 3, '', '', '0', 'Left=1||Right=0', '', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'dev', '', '', 'position_sidebody', '', '', '', 0, '0000-00-00 00:00:00'),
(232, 'Core Plugins', 'core_plugins', 3, '42', '', 0, 'Plugin', ' ', 3, '', '', '', '', '{"preparecontent":"","prepareform":"require_once JPATH_ADMINISTRATOR.DS. ''components'' .DS. CCK_COM .DS. ''models'' .DS. ''fields'' .DS. ''core.php'';\\r\\n$type = ( $field->location ) ? $field->location : ''field'';\\r\\n$opts = array();\\r\\nif ( trim( $field->selectlabel ) ) {\\r\\n  $selectlabel = ''COM_CCK_'' . str_replace( '' '', ''_'', trim( $field->selectlabel ) );\\r\\n  $opts[] = JHtml::_( ''select.option'','''',''- ''.JText::_( $selectlabel ).'' -'',''value'',''text'' );\\r\\n}\\r\\n$opts = array_merge( $opts, JFormFieldCore::getPluginTypes( $type, ''cck_'', false, false, true ) );\\r\\n$css  = ( $field->required == ''required'' ) ? '' validate[required]'' : '''';\\r\\n$form = JHtml::_( ''select.genericlist'', $opts, $name, ''class=\\"inputbox select''.$css.''\\" size=\\"1\\" ''.$field->attributes, ''value'', ''text'', $value );","preparestore":""}', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'dev', '', '', 'plugins', '', '', '', 0, '0000-00-00 00:00:00'),
(233, 'Core Dev Select Numeric', 'core_dev_select_numeric', 3, 'select_numeric', '', 0, '', ' ', 3, '', '', '1', '', '{"math":"0","start":"1","first":"","step":"1","last":"","end":"5","force_digits":"0"}', 0, 255, 32, 0, 0, 0, 0, '', 0, '', '', '', '', 0, 0, 0, 0, 0, 0, 0, '', '', 'dev', '', '', 'select_numeric', '', '', '', 0, '0000-00-00 00:00:00');

UPDATE `#__cck_core_fields` SET `maxlength` = '255' WHERE `id` = 12;
UPDATE `#__cck_core_fields` SET `size` = '8' WHERE `id` = 19;
UPDATE `#__cck_core_fields` SET `script` = '$j("#title").live(''change'', function() { if ( !$j("#name").val() ) { var p = ""; if ($j("span.insidebox").length > 0) { var p = $j("span.insidebox").html()+"_"; } $j("#name").val( p+$j("#title").val().toLowerCase().replace(/^\\s+|\\s+$/g,"").replace(/\\s/g, "_").replace(/[^a-z0-9_]/gi, "") ) } }); if(!$j("#title").val()){ $j("#title").focus(); }' WHERE `id` = 27;
UPDATE `#__cck_core_fields` SET `script` = 'if(!$j("#title").val()){ $j("#title").focus(); }' WHERE `id` = 37;
UPDATE `#__cck_core_fields` SET `script` = '$j("#title").live(''change'', function() { if ( !$j("#name").val() ) { var p = ""; if ($j("span.insidebox").length > 0) { var p = $j("span.insidebox").html()+"_"; } $j("#name").val( p+$j("#title").val().toLowerCase().replace(/^\\s+|\\s+$/g,"").replace(/\\s/g, "_").replace(/[^a-z0-9_]/gi, "") ) } }); if(!$j("#title").val()){ $j("#title").focus(); }' WHERE `id` = 38;
UPDATE `#__cck_core_fields` SET `script` = 'if(!$j("#title").val()){ $j("#title").focus(); }' WHERE `id` = 39;
UPDATE `#__cck_core_fields` SET `script` = 'if(!$j("#title").val()){ $j("#title").focus(); }' WHERE `id` = 51;
UPDATE `#__cck_core_fields` SET `size` = '16' WHERE `id` IN (100,101,102,103,104,105);
UPDATE `#__cck_core_fields` SET `defaultvalue` = '1', `options2` = '{"preparecontent":"","prepareform":"$value = $field->defaultvalue;\\r\\nif ( $value == ''0'' ) {\\r\\n $c0 = ''checked=\\"checked\\"''; $c1 = ''''; $class = ''unlinked'';\\r\\n} else {\\r\\n $c0 = ''''; $c1 = ''checked=\\"checked\\"''; $class = ''linked'';\\r\\n}\\r\\n$desc = JText::_( ''COM_CCK_STORAGE_DESC_SHORT'' );\\r\\n$form = ''<input type=\\"radio\\" id=\\"''.$name.''0\\" name=\\"''.$name.''\\" value=\\"0\\" ''.$c0\\r\\n      . '' style=\\"display:none;\\" \\/>''\\r\\n      . ''<input type=\\"radio\\" id=\\"''.$name.''1\\" name=\\"''.$name.''\\" value=\\"1\\" ''.$c1\\r\\n      . '' style=\\"display:none;\\" \\/>''\\r\\n      . ''<a href=\\"javascript: void(0);\\" id=\\"''.$name.''\\" class=\\"switch qtip_cck\\" title=\\"''\\r\\n      . $desc.''\\">''\\r\\n      . ''<span class=\\"''.$name.'' ''.$class.''\\">''\\r\\n      . ''<\\/span>''\\r\\n      . ''<\\/a>'';"}' WHERE `id` = 106;
UPDATE `#__cck_core_fields` SET `options` = 'Default=1||Modal Box=0' WHERE `id` = 109;
UPDATE `#__cck_core_fields` SET `label` = 'Show Preview', `options` = 'Hide=-1||Show=optgroup||Icon=1||Image=2||Thumb1=3||Thumb2=4||Thumb3=5||Thumb4=6||Thumb5=7||Title=0' WHERE `id` = 113;
UPDATE `#__cck_core_fields` SET `options` = 'Hide=0||Show=1' WHERE `id` = 116;
UPDATE `#__cck_core_fields` SET `options` = 'No Process=||Resized=optgroup||Crop=crop||Max Fit=maxfit||Stretch=stretch' WHERE `id` = 117;
UPDATE `#__cck_core_fields` SET `options` = 'Resized=optgroup||Crop=crop||Max Fit=maxfit||Stretch=stretch', `storage_location` = '', `storage_table` = '' WHERE `id` = 120;
UPDATE `#__cck_core_fields` SET `label` = 'Show Preview', `options` = 'Hide=-1||Show=optgroup||Icon=1||Title=0' WHERE `id` = 132;
UPDATE `#__cck_core_fields` SET `label` = 'Show Preview', `options` = 'Hide=0||Show=1' WHERE `id` = 146;
UPDATE `#__cck_core_fields` SET `options` = 'Hide=0||Show=1' WHERE `id` = 153;
UPDATE `#__cck_core_fields` SET `options` = 'Hide=0||Show=optgroup||Below Field=1||Below FormValue=2||Below Label=3' WHERE `id` = 154;
UPDATE `#__cck_core_fields` SET `label` = 'Target', `display` = '3' WHERE `id` = 161;
UPDATE `#__cck_core_fields` SET `options` = 'Hide=0||Show=1' WHERE `id` = 167;
UPDATE `#__cck_core_fields` SET `options` = 'None=0||Page=-1||Joomla=optgroup||Error=error||Message=message||Notice=notice' WHERE `id` = 176;
UPDATE `#__cck_core_fields` SET `options` = 'No=0||Yes Multivalue Mode=1' WHERE `id` = 193;
UPDATE `#__cck_core_fields` SET `script` = 'if(!$j("#title").val()){ $j("#title").focus(); }' WHERE `id` = 201;
UPDATE `#__cck_core_fields` SET `label` = 'Content Type Form' WHERE `id` = 216;
UPDATE `#__cck_core_fields` SET `label` = 'Search Type List' WHERE `id` = 217;
UPDATE `#__cck_core_fields` SET `attributes` = 'style="max-width:200px;"' WHERE `id` = 218;


-- --------------------------------------------------------

UPDATE `#__extensions` SET `enabled` = '1' WHERE `folder` = 'cck_field' AND ( `element` = 'jform_componentlayout' );

UPDATE `#__cck_core_fields` SET `type` = 'jform_componentlayout', `selectlabel` = '', `options` = '', `options2` = '{"extension":"com_content","view":"article"}' WHERE `id` = 356;
UPDATE `#__cck_core_fields` SET `type` = 'jform_componentlayout', `selectlabel` = '', `options` = '', `options2` = '{"extension":"com_content","view":"category"}' WHERE `id` = 377;