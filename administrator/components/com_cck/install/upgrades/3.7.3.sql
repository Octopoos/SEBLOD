
UPDATE `#__cck_core_fields` SET `options2` = '{"preparecontent":"","prepareform":"require_once JPATH_ADMINISTRATOR.''\\/components\\/com_cck\\/helpers\\/helper_workshop.php'';\\r\\n$opts = array();\\r\\nif ( trim( $field->selectlabel ) ) {\\r\\n  $opts[] = JHtml::_( ''select.option'','''',''- ''.$field->selectlabel.'' -'',''value'',''text'' );\\r\\n}\\r\\n$opts = array_merge( $opts, Helper_Workshop::getPositionVariations( @$config[''item'']->template, false ) );\\r\\n$attr = ''class=\\"inputbox\\"'';\\r\\n$form = JHtml::_( ''select.genericlist'', $opts, $name, $attr, ''value'', ''text'', $value, $id );","preparestore":""}' WHERE `id` = 107;

-- --------

UPDATE `#__cck_core_fields` SET `options` = 'Joomla=optgroup||Checkbox=selection||Checkbox Label For=selection_label||Featured=featured||Increment=increment||Reordering=sort||Status=state||Blocked=blocked||SEBLOD=optgroup||Form=form||Hidden=form_hidden||Form Disabled=form_disabled' WHERE `id` = 271;

-- --------