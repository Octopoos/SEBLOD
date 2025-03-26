<?php

/**
 * @version 			SEBLOD 3.x Core ~ $Id: database.php sebastienheraud $
 * @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
 * @url				https://www.seblod.com
 * @editor			Octopoos - www.octopoos.com
 * @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
 * @license 			GNU General Public License version 2 or later; see _LICENSE.php
 * */
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Factory;

\defined('_JEXEC') or die;

// JCckDatabase
abstract class JCckDatabase {

    // -------- -------- -------- -------- -------- -------- -------- -------- // Queries
    // convertUtf8mb4QueryToUtf8
    public static function convertUtf8mb4QueryToUtf8($query) {
        if (JCck::on('3.5')) {

            return Factory::getContainer()->get(DatabaseInterface::class)->convertUtf8mb4QueryToUtf8($query);
        }

        $beginningOfQuery = substr($query, 0, 12);
        $beginningOfQuery = strtoupper($beginningOfQuery);

        if (!in_array($beginningOfQuery, array('ALTER TABLE ', 'CREATE TABLE'))) {
            return $query;
        }

        return str_replace('utf8mb4', 'utf8', $query);
    }

    // execute
    public static function execute($query) {
        $db      = Factory::getContainer()->get(DatabaseInterface::class);
        $utf8mb4 = false;
        if (JCck::on('3.5')) {
            $utf8mb4 = $db->hasUTF8mb4Support($query);
        }
        if (!$utf8mb4) {
            $query = self::convertUtf8mb4QueryToUtf8($query);
        }

        $db->setQuery($query);

        if (!$db->execute()) {
            return false;
        }

        return true;
    }

    // loadAssocList
    public static function loadAssocList($query, $key = null, $column = null) {
        $db = Factory::getContainer()->get(DatabaseInterface::class);

        $db->setQuery($query);
        $res = $db->loadAssocList($key, $column);

        return $res;
    }

    // loadColumn
    public static function loadColumn($query) {
        $db = Factory::getContainer()->get(DatabaseInterface::class);

        $db->setQuery($query);
        $res = $db->loadColumn();

        return $res;
    }

    // loadObject
    public static function loadObject($query) {
        $db = Factory::getContainer()->get(DatabaseInterface::class);

        $db->setQuery($query);
        $res = $db->loadObject();

        return $res;
    }

    // loadObjectList
    public static function loadObjectList($query, $key = null) {
        $db = Factory::getContainer()->get(DatabaseInterface::class);

        $db->setQuery($query);
        $res = $db->loadObjectList($key);

        return $res;
    }

    // loadObjectListArray
    public static function loadObjectListArray($query, $akey, $key = null) {
        $db = Factory::getContainer()->get(DatabaseInterface::class);

        $db->setQuery($query);

        $list = $db->loadObjectList();
        $res  = array();
        if (count($list)) {
            if ($key) {
                foreach ($list as $row) {
                    $res[$row->$akey][$row->$key] = $row;
                }
            } else {
                foreach ($list as $row) {
                    $res[$row->$akey][] = $row;
                }
            }
        }

        return $res;
    }

    // loadResult
    public static function loadResult($query) {
        $db = Factory::getContainer()->get(DatabaseInterface::class);

        $db->setQuery($query);
        $res = $db->loadResult();

        return $res;
    }

    // -------- -------- -------- -------- -------- -------- -------- -------- // Tables
    // getTableColumns
    public static function getTableColumns($table, $flip = false) {
        return $flip ? array_flip(array_keys(Factory::getContainer()->get(DatabaseInterface::class)->getTableColumns($table))) : array_keys(Factory::getContainer()->get(DatabaseInterface::class)->getTableColumns($table));
    }

    // getTableFullColumns
    public static function getTableFullColumns($table) {
        return Factory::getContainer()->get(DatabaseInterface::class)->getTableColumns($table, false);
    }

    // getTableCreate
    public static function getTableCreate($tables) {
        $res = Factory::getContainer()->get(DatabaseInterface::class)->getTableCreate($tables);

        $res = str_replace(JFactory::getConfig()->get('dbprefix'), '#__', $res);
        $res = str_replace('CREATE TABLE `#__', 'CREATE TABLE IF NOT EXISTS `#__', $res);

        return $res;
    }

    // getTableList
    public static function getTableList($flip = false) {
        return $flip ? array_flip(Factory::getContainer()->get(DatabaseInterface::class)->getTableList()) : Factory::getContainer()->get(DatabaseInterface::class)->getTableList();
    }

    // -------- -------- -------- -------- -------- -------- -------- -------- // Misc
    // clean
    public static function clean($text) {
        if (is_numeric($text)) {
            return (string) $text;
        } else {
            $len = strlen($text);

            if ($text[0] == "'" && $text[$len - 1] == "'") {
                $t = substr($text, 1, - 1);

                if (is_numeric($t)) {
                    return "'" . (string) $t . "'";
                }
            }
        }

        return JCckDatabase::quote(uniqid());
    }

    // escape
    public static function escape($text, $extra = false) {
        return Factory::getContainer()->get(DatabaseInterface::class)->escape($text, $extra);
    }

    // match
    public static function matchWithin($column, $text) {
        if ($text == '') {
            return $text;
        }

        $glue = ',';
        $sql  = $column . ' = ' . self::quote($text)
                . ' OR ' . $column . ' LIKE ' . self::quote(self::escape($text, true) . $glue . '%', false)
                . ' OR ' . $column . ' LIKE ' . self::quote('%' . $glue . self::escape($text, true) . $glue . '%', false)
                . ' OR ' . $column . ' LIKE ' . self::quote('%' . $glue . self::escape($text, true), false)
        ;

        return '(' . $sql . ')';
    }

    // null
    public static function null() {
        $db = Factory::getContainer()->get(DatabaseInterface::class);

        return JCck::on('4.0') ? 'IS NULL' : '= ' . $db->quote($db->getNullDate());
    }

    // quote
    public static function quote($text, $escape = true) {
        return Factory::getContainer()->get(DatabaseInterface::class)->quote($text, $escape);
    }

    // quoteName
    public static function quoteName($name, $as = null) {
        return Factory::getContainer()->get(DatabaseInterface::class)->quoteName($name, $as);
    }

    // -------- -------- -------- -------- -------- -------- -------- -------- // Deprecated
    // doQuery (deprecated)
    public static function doQuery($query) {
        return self::execute($query);
    }
}

?>
