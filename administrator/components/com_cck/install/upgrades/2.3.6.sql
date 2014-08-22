
DELETE IGNORE a.*, b.* FROM #__update_sites AS a LEFT JOIN #__update_sites_extensions AS b ON b.update_site_id = a.update_site_id WHERE a.location = "http://localhost/seblod2/updates/core/list.xml";