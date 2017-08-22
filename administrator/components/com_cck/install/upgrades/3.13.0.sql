
ALTER TABLE `#__cck_core` CHANGE `author_id` `author_id` INT(10) UNSIGNED NOT NULL;
ALTER TABLE `#__cck_core` CHANGE `store_id` `store_id` INT(10) UNSIGNED NOT NULL;

ALTER TABLE `#__cck_core_sites` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__cck_core_sites` CHANGE `guest` `guest` INT(10) UNSIGNED NOT NULL;
ALTER TABLE `#__cck_core_sites` CHANGE `guest_only_group` `guest_only_group` INT(10) UNSIGNED NOT NULL;
ALTER TABLE `#__cck_core_sites` CHANGE `guest_only_viewlevel` `guest_only_viewlevel` INT(10) UNSIGNED NOT NULL;
ALTER TABLE `#__cck_core_sites` CHANGE `public_viewlevel` `public_viewlevel` INT(10) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `#__cck_more_jobs` CHANGE `run_as` `run_as` INT(10) UNSIGNED NOT NULL DEFAULT '0';

ALTER TABLE `#__cck_store_item_users` CHANGE `id` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT;