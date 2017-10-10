<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: _CHANGELOG.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			  Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;
?>

CHANGELOG:

Legend:
* -> Security Fix
# -> Bug Fix
$ -> Language fix or change
+ -> Addition
^ -> Change
- -> Removed
! -> Note

-------------------- 3.15.0 Upgrade Release [9-Oct-2017] ------------

^ JCckContent class updated, and greatly improved! :)
  >> Method Chaining capabilities added, use ->setOptions( array( 'chain_methods'=>0 ) ) for previous behaviour of create/load.
  >> "__construct" methods' arguments changed: "$identifier" removed, use getInstance or new+load (cf 3.11.0 changelog)
  >> $identifier is now "pk" when the object is already known instead of the unique seblod id.
  >> "count", "find", "getResults" methods added.
  >> "clear" method added.
  >> "batch", "batchResults" methods added.
  >> missing permissions check added to "addToGroup", "removeFromGroup" on JCckContentJoomla_User class.
  >> "remove" method declaration has changed (from public to protected)!, use delete.
  >> "bind" return changed.

+ Support for Table name (or Aka) added on Search query parts >> Search Join plug-in required.
+ "Translated Text" mode added for Title on Form & List Menu Items.
+ Variables can be sent alongside Processing AJAX task.

^ "PrepareExport" applied on JForm Calendar plug-in.

- Temporary fixes for Joomla! 3.8 removed.

# ->values added in PrepareContent on JForm User Groups plug-in.
# "Default/Static" Live value fixed for fields within Group.
# Issue while saving Integration Options on PHP 7.1 fixed.
# Various improvements or issues fixed.

-------------------- 3.14.1 Upgrade Release [20-Sept-2017] ----------

+ JText support added on Date Typo plug-in.

# create() method fixed on JCckContent class (regression since SEBLOD 3.14.0).
# Dynamic Select lookup fixed within Generic Search field on Multilingual site.

-------------------- 3.14.0 Upgrade Release [19-Sept-2017] ----------

+ Add "raw.php" file in admin/site default templates (install and update).
+ "Edit Property" permission added.

+ "data-cck-boxchecked-multiple" attribute implemented for item selection.
+ "data-cck-boxchecked-single" attribute implemented for item selection.

+ "Auto Redirection (Vars)" now handles multiple variables.
+ Links support added in "Activation", "Block" behaviours (JGrid).
+ Support for multiple Ajax tasks added in Submit Button plug-in.

^ JCckContent class updated.
  >> "Edit Own (Related Content)" permission support added.
  >> "getData" method refactored, use "$content->getDataObject()" for previous behaviour.
  >> "getProperty", "setProperty", "updateProperty" methods added.
  >> "get" method refactored, use "$content->get( [instance_name], [property], [default_value] );" or "$content->getProperty( [property], [default_value] );".
  >> "__construct", "getInstance", "load" methods' arguments changed: "$data = true" removed.

! JCckContentArticle class deprecated, use JCckContentJoomla_Article class.
! JCckContentCategory class deprecated, use JCckContentJoomla_Category class.

# "Disabled" Variation fixed on JForm Calendar, JForm User plug-ins.
# Include inline scripts on JForm Calendar when tmpl=raw.
# Include inline scripts on Field/Group X when tmpl=raw.
# Various improvements or issues fixed.

-------------------- 3.13.0 Upgrade Release [30-Aug-2017] -----------

! Joomla! 3.8 ready.
  >> other minor fixes may be needed for final 3.8 release.

+ Menu Presets added for "mod_menu"
  >> SEBLOD, SEBLOD Add-ons, SEBLOD Apps, SEBLOD Ecommerce
+ Native redirections added for Lists using "Fragment as Resource" capability.
  >> from tmpl=raw to #fragment

+ Keyboard Shortcut added in Content/Search Types UI to close/cancel with (q).
+ Keyboard Shortcuts added to add a new Content/Search Type or Field/Site/Template with (n).
+ "Only Fragment" paths mode added on Content Link plug-in.

+ "addToGroup", "removeFromGroup" methods added on JCckContentJoomla_User class.
  >> use JCckContent->call( 'addToGroup', 00 );
  >> use JCckContent->call( 'removeFromGroup', 00 );

^ Back-end UI improved: Fields' Storage.
^ Various INT(11) updated to UNSIGNED INT(10).
^ Mobile Detect updated to 2.8.26.

! "data-cck-modal-hash" on any HTML element (Content View)
  >> to change the #fragment of the URL
! "data-cck-modal-title" on any HTML element (Content View)
  >> to set the title of the modal
! SEBLOD Admin Menu module (mod_cck_menu) deprecated, use "mod_menu";

- #__cck_core_preferences removed from install.sql

# "Featured" behaviour (JGrid) fixed.
# "Fragment as Resource" fixed on Safari (macOS).
# "From Name" fixed on Email Field plug-in.
# initialize() fixed in JCckContentJoomla_User for CRONs.
# JText not applied on Multilingual ("Yes but English") fixed (regression).
# Missing "return" added on "Export", "Process" tasks from Submit Button plug-in.
# Various improvements or issues fixed.

-------------------- 3.12.2 Upgrade Release [31-Jul-2017] -----------

# "data-cck-boxchecked" JS issue fixed.

-------------------- 3.12.1 Upgrade Release [31-Jul-2017] -----------

# Regression (objects with bridge capability) fixed.

-------------------- 3.12.0 Upgrade Release [28-Jul-2017] -----------

* Security Release:
  >> Missing escaping on 2 queries.

+ "Optimize (Memory)" process reworked (for both post/pre PHP 7).
+ Search Generic now gives the ability to target a specific column.
  >> [aka]:[value] instead of [value] while searching

+ "Clear" Access Level added (on Fields) from Content/Search Types UI.
+ "Edit Own Related Content" permission improved.
  >> any object suppport added >> [column]@[object]
+ Keyboard Shortcuts added in Fields/Sites/Templates UI (List View) to search (@).
+ Multiple items supported in "Dropdown Menu" behaviour (JGrid).
+ "Language" parameter added on Date Typo plug-in.
+ Links support added in "Featured", "Status" behaviours (JGrid).
+ "Property" picker added on Joomla! User Live plug-in.
+ "Title" added on Content Link plug-in.
+ "Title / Tooltip" (Self) parameter/process added on JGrid Typo plug-in.
+ Translated Title added on "Delete", "SEBLOD Form" Link plug-ins.

+ "data-cck-boxchecked" attribute implemented for item selection.
+ "is-filter" class implemented for Filter variation.
  >> useful to enable/disable "change" event

^ Apply Group's own restriction before fields (so they don't end up in $config['fields'])
^ JCckContent class updated.
  >> "delete" method refactored.
  >> Events triggering added.
  >> Permissions check added.
  >> "setOptions" method added.
  >> some method declarations have changed (from public to protected)!

! Priority added on Process stack.
  >> "beforeRenderForm" support added.

# Alternative Language constant "..._1" applied when no result as well.
# Bridge author issue fixed.
# Calendar fixed for PHP 5.4
# Code Mirror support fixed.
# "Completed" text added on Submit Button.
# Delete link issue fixed on Free Button field plug-in.
# Delete support added Batch/List (i.e Search Form).
# Development Prefix applied on Group creation.
# Integration issues fixed.
# Issues fixed on "JForm Associations" Field plug-in.
# Menu Tree (Add-ons for SEBLOD) issue fixed.
# Missing Development Prefix added on Group creation.
# Missing translation fixed for optgroups on Static Options of Dynamic Select plug-in.
# Missing "return" added on "Export", "Process" tasks from Submit Button plug-in.
# Multilanguage Integration issue fixed.
# Multiselect added on back-end managers.
# Object Plug-ins now loaded from CLI.
# "onAfterInitialise" processing calls move below Core/Multi-site code.
# "onAfterDelete" fixed for base table.
# Template positions toggle issue fixed on back-end.
# Validation Rules fixed on JForm Calendar plug-in.
# Various improvements or issues fixed.

-------------------- 3.11.3 Upgrade Release [09-Jun-2017] -----------

+ Alternative Language constant "..._1" for unique search result added.

! Various cleaning performed.

# Missing "Debug" fixed on Lists.
# Trigger "change" issue fixed on Calendar (regression since SEBLOD 3.11.0).
# Variations fixed on Calendar within Group X (regression since SEBLOD 3.11.2).
# Various minor issues fixed.

-------------------- 3.11.2 Upgrade Release [22-May-2017] -----------

+ "onUserLogin" event support added.
+ "Prepare Output" parameter added on Textarea, Wysiwyg Editor plug-ins.

# "/api..." URLs fixed.
# Calendar issues fixed (regression since SEBLOD 3.11.0).
# "Guest Only - [SITE]" group now assigned to "Guest" Access Level.

-------------------- 3.11.1 Upgrade Release [17-May-2017] -----------

# Live/Default value fixed on Calendar plug-in.
# Timezone issue fixed on Calendar plug-in.
# Timezone issue fixed on "JForm Calendar" plug-in.

-------------------- 3.11.0 Upgrade Release [10-May-2017] -----------

* Security Release:
  >> Making sure unauthorized values (plug-ins with "options") are properly filtered.
  >> Unauthorized values wrongly forced to "0" in some use cases.

+ "Edition as Copy" behaviour added on SEBLOD Form Link plug-in.
+ "Fragment as Resource" capability added.
  >> opens a Content view in Modal Box from any List.
+ "JForm Calendar" plug-in added.
+ Modal Box (Bootstrap-based) added.
+ Multi-sites system improved with context/subfolder capability.
+ Persistent Search behaviour added.

+ Ability to use "Admin Form" on Front-end only on Edit.
+ "Match Occurences Offset" parameter added for "Each Word Exact" match mode.
+ Placeholder tables added for Free storage.
  >> useful for AKA/Join queries (Search Join Plug-in).

+ "Append Context to URLs" parameter added on SEBLOD Options.
+ Country List updated in install.sql to include Ukrainian translation.
+ Development User Group added on SEBLOD Options.
  >> useful for having a specific TinyMCE profile on Construction (i.e. SEBLOD back-end).
+ History/Log support added for templates.
+ Keyboard Shortcuts added in Content/Search Types UI (List View) to search (@), and open a specific view (1-4).
+ Loading Overlay added on Content/Search Types UI (back-end).
+ "Message", "Message Style" (No Search) added on Search Types.
+ "Pagination (Text)" parameter added on SEBLOD List for Infinite pagination.
+ SEF (Root for Ajax) parameter added on SEBLOD Options.
+ Template Style support added on Versions.
+ Title (Class) parameter added on SEBLOD Options.

+ 2 new computation formats added on SEBLOD Options.
+ Custom class allowed in "Modal" position of One (seb_one) template.
+ "Description Tag" (div|p) added on Form & List Menu Items.
+ "Language Constant" mode added for Title on Form & List Menu Items.
  >> APP_CCK_FORM_[CONTENT_TYPE_NAME]_TITLE_ADD
  >> APP_CCK_FORM_[CONTENT_TYPE_NAME]_TITLE_EDIT
  >> APP_CCK_LIST_[SEARCH_TYPE_NAME]_TITLE

+ "Export (Ajax)", "Process (Ajax)" tasks added in Submit Button plug-in.
+ Modal Box parameter added on SEBLOD Options.
+ Modal Target added on Content, List Link plug-ins >> only the new modal is supported.
+ Responsive Layout mode added in Table (seb_table) template.

+ Column suggestion now supports #__cck_store_form... tables.
+ "Dropdown Menu" behaviour added on JGrid Typography plug-in.
+ "Invert" parameter added on Ajax Availability Validation plug-in.
+ "Is Submitted" added on Conditional States triggers.
+ "Show Buttons" parameter added on Wysiwyg Editor plug-in.
+ "Site" parameter added on Content Link plug-in.
+ "Start/Finish Publishing" for "Status" behaviour added on JGrid Typography plug-in.

+ "data-cck-remove-before-search" added.
  >> remove inputs before Search for cleaner/shorter URLs.

! List/Items not wrapped in <form> tag anymore even with Search Form set to "show"
  >> some plug-ins may use formWrapper=true to force items to be wrapped into <form>, ex: JGrid
     it is required when form elements are included in the list output, but it is now managed dynamically by the system.
! JCck::isSite() method improved.
  >> can identify if site is "master" or from aliases.
! JCckTable doesn't have support for Assets (and issues are now prevented!).
! JCckTable shouldn't be used with tables having an Object plug-in >> use JCckContent.
! "No Access" message more explicit when Debug is enabled, on Content/Search Types.
! Object plug-ins (exporter.php) updated for Exporter Add-on.
! Object plug-ins (importer.php) updated for Importer Add-on.
  >> suffix forced for existing/identical "alias" for new items.
! Priority added on Process stack.
  >> "beforeRenderContent" support added.

^ JCckContent class updated.
  >> "getInstanceBase" method added.
  >> "save" method refactored.
  >> support of "parent" Content Type added.
^ JCckContent classes added on each Object plug-in.
  >> use "$content = new JCckContentJoomla_Article;" in order to create new Articles.
  >> use "$content = new JCckContentJoomla_Category;" in order to create new Categories.
  >> use "$content = new JCckContentJoomla_User;" in order to create new Users.
  >> use "$content = new JCckContent[OBJECT_NAME];" in order to create new Items from a specific Object.
  >> use "$content = JCckContent::getInstance(...); in order to load/update any kind of Item."
^ Mobile Detect updated to 2.8.24.
^ Plug-ins translations updated.
^ Reset Button now clears hidden inputs as well.
  >> use data-cck-keep-for-search="" to keep them.
^ Search Ordering mode is now "Text" by default.
^ "translate_id" variable renamed to "copyfrom_id".

! "Helper_Include::addValidation()" back-end function deprecated, use JCckDev::addValidation();
! "Helper_Include::addScriptDeclaration()" front-end function deprecated.
! "Helper_Include::addValidation()" front-end function deprecated, use JCckDev::addValidation();
! "JROOT_CCK", "JROOT_MEDIA_CCK", "JPATH_LIBRARIES_CCK" defines deprecated.
! "JCck::getUser_Value()" function deprecated.
! <i> replaced with <span>

- "Box" (task=box.) removed on front-end.
- "Helper_Include::addColorbox()" front-end function removed.
- "JCck::setUser_Preference()" function removed.
- "JCck::setUser_Preferences()" function removed.
- "JCck::googleAnalytics()" function removed.
- Limit of characters removed in dropdowns (Integration).
- "Modal Box" location removed from "JForm Rules" plug-in.
- "Modal Box" location removed from "Wysiwyg Editor" plug-in.

# "Admin Form" permission added at component-level.
# Bad URLs fixed when SEF is deactivated.
# "Cancel" task doesn't trigger validation anymore.
# Code Mirror support added on Wysiwyg Editor plug-in.
# Custom Attributes support added for List (Field) Variations.
# "has-value" class fixed Select Dynamic/Multiple.
# Form / List & Search issues fixed, when Joomla! caching is on.
# Height issue fixed on Wysiwyg Editor plug-in. (it may prevent manually resizing)
# Issue fixed on Windows Edge, where form was submitted twice.
# Live value fixed for access levels in Multi-sites.
# Legal Extensions (Presets) issue fixed (right) after SEBLOD installation.
# Merging process (for scripts) fixed for sites in subdirectories of a domain name.
# Message/Redirection issues fixed on Multi-pages forms.
# Missing back-end methods for reorder by drag-and-drop capability added.
# Missing context added on Joomla! Category Object for "onContentBeforeDelete".
# Missing language string methods for reorder by drag-and-drop capability added.
# Missing parts added for "onUserAfterDelete" event.
# Numeric Select plug-in improved.
# One issue fixed for PHP 7.1 support.
# Preview (Field X, Group X) fixed in Upload File plug-in (regression).
# "Required" validation issue fixed on Upload File/Image plug-ins.
# "Return" URL fixed on User Profile Edit redirection.
# Site's public access level now stored separately.
# Table headings now translated in Table (seb_table) template.
# Translation issue (static options) fixed on Select Dynamic plug-in.
# User export (columns) issues fixed.
# Validation can now be applied on Calendar plug-in.
# Various issues fixed on Calendar plug-in.
# Various improvements applied on Group plug-in.
# Various improvements or issues fixed.

-------------------- 3.10.0 Upgrade Release [30-Sep-2016] -----------

+ Ability to use "Admin Form" on Front-end.
+ Automatic removal of oldest versions added.
+ Keep-alive behaviour forced on Admin/Site Form views.

+ Allow Options to be defined at Template level.
+ Load More (Infinite Pagination) support added on Blog template.

+ Above/Below locations added for "Add" button on Group X plug-in.
+ "->values" added (onCCK_FieldPrepareStore) on Field X and Group X plug-ins.

^ Various SEBLOD.com links changed to HTTPs.

# Google API load from modules issue fixed.
# Merging process (for scripts) reworked.
# Missing Group X icons (in Table orientation) added.
# Missing $config properties added on "Store".
  >> allows Permissions check on PrepareStore (Joomla! ACL pack)
# One more fix applied to the router.
# Parent (Content Type) Table entries are now removed "onContentAfterDelete".
# Sortable JS not included anymore when disabled with restrictions.
# Timezone issue fixed on Calendar plug-in (regression).
# Various improvements or issues fixed.

-------------------- 3.9.1 Upgrade Release [12-Sep-2016] -----------

# Broken Conditional States fixed on Group when tmpl=raw.
# Download task updated on back-end.
# Markup fixed on Group plug-in.
# Minor issues fixed on back-end.

-------------------- 3.9.0 Upgrade Release [1-Sep-2016] ------------

* Security Release:
  >> Status (+ Publication dates) applied on Download task.

+ Extensive cleaning performed.
  >> Deprecated Jquery stuff removed.
  >> Deprecated SEBLOD stuff removed.
  >> Joomla! 2.5 not supported anymore. :)
  >> PHP 5.2 not supported anymore. :)

! Memory & Performance optimizations on List rendering (for modules).

+ Keyboard Shortcut added in Content/Search Types UI to add a new field (n).
+ Match mode automation (multiple vs single selection) added.
+ Suffix forced for existing/identical "alias" on submission.
  >> available on JCckContent class
  >> support added on Article/Category Object plug-ins

+ $cck->retrieveValue(...); implemented in Template Framework.
  >> alternative to getValue with restriction applied.
+ "Limit" parameter added on List module.
+ "[pk]" syntax allowed in Bridge Title parameter.
+ "User Grp Password" group (used from User Content Type) added in install.sql

^ "Exact" match mode + "Unquoted" forced for Int/Tinyint while creating new fields.
^ "Match Occurences" parameter added for "Each Word Exact" match mode.
^ "PrepareExport" applied on Link, Textarea, Wysiwyg Editor plug-ins.
^ Primary Key for "More" tables now created as UNSIGNED INT(10).
  >> "#__cck_store_form_..."
  >> "#__cck_store_item_..."

^ jQuery UI updated to 1.12.0
^ jQuery UI Effect updated to 1.12.0

^ "/alias (Safe)" is now deprecated.
  >> "SEF Content Type(s)" now triggers "Safe" mode on its own.

- Deprecated "CCK" class removed.
- Deprecated "CCK_Database" class removed.
- Deprecated "CCK_Dev" class removed.
- Deprecated "CCK_Field" class removed.
- Deprecated "CCK_Language" class removed.

- Deprecated use of jQuery.fn.attr('checked') removed.
- Deprecated use of jQuery.fn.attr('disabled') removed.
- Deprecated use of jQuery.fn.die() removed.
- Deprecated use of jQuery.fn.live() removed.
- Deprecated use of jQuery.fn.removeAttr('checked') removed.
- Deprecated use of jQuery.fn.removeAttr('disabled') removed.
- Deprecated use of jQuery.fn.size() removed.

- jquery-1.8.3.min.js removed.
- jquery.jstree.min.js script removed.
- jquery.lavalamp.min.js script removed.
- jquery-noconflict.js script removed.
- jquery.qtip.min.js script removed.

- Permanent "limit" variable removed on Lists.

# A few "ALTER IGNORE TABLE" replaced by "ALTER TABLE" for MySQL 5.7.4+ support.
# "Auto Redirection (Vars)" now applied on Content redirection as well.
# Download Link/Task now applied on Upload File plug-in.
# "Form Action" Fields can now be included in a "Group" Field.
# Guest issue on Multi-sites fixed (regression since Joomla! 3.6.0)
# Green validation popup style fixed.
# Issues fixed on Group plug-in.
# JSON issue (PrepareDownload) fixed.
# Missing required "star" added in Group output.
# Missing Parent's fields while editing a Child Content Type issue fixed.
# Offline mode fixed in Multi-sites. (regression)
# Path issue fixed for "Modal Box Style" parameter on SEBLOD Options.
# User activation issue fixed on Multi-sites.
# Various improvements or issues fixed.

-------------------- 3.8.4 Upgrade Release [27-Jun-2016] ------------

+ Country List updated in install.sql to include Italian translation.

+ Allow syntaxes to retrieve Group X values in HTML Typography plug-in.
+ "Description Tag" (div|p) added on List/Search modules.

# Missing "TagsHelperRoute" class (in back-end) issue fixed.
# Page Title (override) issue fixed for view=category.
# Minor issues fixed.

-------------------- 3.8.3 Upgrade Release [10-Jun-2016] ------------

+ Country List updated in install.sql to include Spanish translation.
+ Position Overrides' path displayed in Content/Search Types UI.

+ "Integers" mode added in URL Variable Live plug-in.

# Back-end UI Drag and Drop responsiveness fixed (regression since SEBLOD 3.8.2).
# Email Validation plug-in altered for new TLDs
# "JForm User" issues fixed.
# One more fix to clearForm().
# Router now handles the "Intranet App for SEBLOD" correclty.

-------------------- 3.8.2 Upgrade Release [2-Jun-2016] -------------

+ Country List updated in install.sql to include Russian translation.
+ Keyboard Shortcuts added in Content/Search Types UI for various parameters (1-7).
+ Router improved
  >> 3 segments URLs (i.e. /parent/parent/...).
  >> support added on back-end via AJAX.

+ "Action" parameter added ("No Search") on Search Types.
+ "Both" behaviour added in Div plug-in.
+ getPk() method added to JCckContent class.
+ URL Query (Vars) added on Form menu item.

^ Colorbox updated to 1.6.4.

# A few more fixes applied to the router.
# getConfig_Param() fixed for CLI (regression since SEBLOD 3.8.0).
# Init "author" in Form Edition (pre-onCCK_Storage_LocationPrepareForm).
# User Export ("All Fields") issue fixed.
# User Import issue fixed.
# Various improvements or issues fixed.

-------------------- 3.8.1 Upgrade Release [8-May-2016] --------------

+ Country List updated in install.sql to include German translation.

^ Mobile Detect updated to 2.8.22.

# clearForm() fixed for multiple select in Firefox.
# Filter Variation fixed in Calendar plug-in.
# Menu Items in Multi-sites (on first load) issue fixed (regression since Joomla! 3.5.x).
# Missing translations added.
# Multi-sites no more automatically activated after first creation (regression since SEBLOD 3.8.0).
# Pagination on Lists (on back-end) fixed (regression since SEBLOD 3.8.0).
# Remaining Mac OS files (.DS_Store) removed from com_cck.zip
# "User Activation State" storage is now "none".
# Various issues related to the router fixed.

-------------------- 3.8.0 Upgrade Release [30-Apr-2016] -------------

* Security Release:
  >> Text Filter Settings (Configuration) applied on Textarea, Wysiwyg Editor.
  >> Variables (that were not protected) now escaped/secured.

! Memory & Performance optimizations on List rendering.
! Performance improvements on Search system >> SQL queries refactored.
^ Publishing comparison based on the minute (instead of second) for SQL optimizations.

+ "Article Manager", "Category Manager", "User Manager" included in install.sql
+ "Button Free", "Search Generic", "Search Ordering" Field Plug-ins added. (included in Core for Free now, yeah!)
+ "Joomla! JGrid" Typography Plug-in added.
+ "URL Variable" Restriction Plug-in added.

+ "Count" (Auto / Estimate) parameter added on Search Types.
+ "Load" behaviour added to Infinite Pagination on Search Types.
  >> support added on the following templates: Chart, Map.
+ Reorder by drag-and-drop capabilities added.
  >> supported Object plug-ins: Joomla! Article, Joomla! Category
+ Router improved for 2 segments URLs (i.e. /author/...)
  >> supported Object plug-ins: Joomla! Article

+ Ability to apply INNER/LEFT/RIGHT joins >> Search Join plug-in required.
+ "Default Variation", "Default Variation (Form)" parameters added on SEBLOD Options (Site).
+ "Optimize (Memory)" parameter added on SEBLOD Options (Site).

+ "Alternative Search Type" parameter added on List module.
+ "Auto Redirection (Vars)", "SEF" parameters added on List Menu Items.
+ "Exclusions" (URLs) capability added on Sites >> force URLs with joomla's default language.
+ "Export", "Process" tasks (Submit Button) support added on Content views.
+ "Hidden & Anonymous" Field Variation added on Forms.
+ "Hidden when filled" Field Variation added on Forms.
+ "Final Ordering" (Random/Shuffle), "Limit", "Pagination" parameters added on List Menu Items.
+ "seb_css3b" variation added (cleaner version of seb_css3).
+ Title (Inherited / Menu Item / Custom) parameter added on Form & List Menu Items.
+ "URL" mode added to "Menu Item Target" on Search module.
+ "Validation" parameters added on Search Types.

+ Additionnal "Order By" can be prepended in Search Ordering Field plug-in.
+ "Alternative Format", "Unit", "Time Zone" parameters added on Date Typography plug-in.
+ "Author" condition added on Workflow Restriction plug-in.
+ "Auto" Tmpl parameter added on Content, "SEBLOD Form", "SEBLOD List" Link plug-ins.
+ "Behavior" parameter added on Clear Typo plug-in.
+ "Bridge Ordering" parameter added on Joomla! User, Joomla! Usergroup Object plug-ins.
+ Content Type (Form) added on Workflow Restriction plug-in.
+ "Default Value" added on Joomla! User Live plug-in.
+ "JForm Usergroup" plug-in improved >> Native Search behavior.
+ "JForm Tag" plug-in improved >> Native Search behavior, Static Variation (Rendering).
+ "Multiple", "Parent" parameters added on Joomla! Tag Field plug-in.
+ Params (JSON) can now be retrivied from Joomla! User Live plug-in.
+ "Redirection (Variables)" added on Delete, SEBLOD Form Link plug-ins.
+ "Rel" parameter added on Link plug-in.
+ "Remaining Characters" added on Textarea plug-in.
+ "Reset" tasks added on Submit Button plug-in.
+ "Routing context" parameter: "Best" mode added on Joomla! Category Object plug-in.
^ Script/Styles improved on Calendar, GroupX plug-ins when tmpl=raw.
+ $site->... syntaxes processes added on Freetext/Select Dynamic plug-ins.
+ Timeleft support added on Date Typography plug-in.

+ "#__cck_store_item_..." support added in JCckContent class.
+ js_replacehtml="" (jQuery from Xml) added.	[Dev. Framework]

+ "Add to Cart" permission added.
+ "Allow Login on all Sites" (Multi-sites) added on SEBLOD Options.
+ "Forbidden Extensions White List" parameter added on SEBLOD Options >> Must be allowed from Upload Files
+ "Registration Menu Item" parameter added on SEBLOD Options > Integration > Joomla! User.
+ "Yes, but English" (Language >> JText) added on SEBLOD Options.
+ Various Improvements for upcoming SEBLOD eCommerce add-on.

^ CHARSET updated to "utf8mb4" and DEFAULT COLLATE to "utf8mb4_unicode_ci".
^ Events changed in SEBLOD core for Content Type, Field, Folder, Search Type, Template:
	>> "onContentBeforeSave" replaced by "onCckConstructionBeforeSave"
	>> "onContentAfterSave" replaced by "onCckConstructionAfterSave"
	>> "onContentBeforeDelete" replaced by "onCckConstructionBeforeDelete"
	>> "onContentAfterDelete" replaced by "onCckConstructionAfterDelete"
- "onUserBeforeDeleteNote" event removed, use "onContentBeforeDelete"/"onContentAfterDelete".
- Various INT(11) updated to UNSIGNED INT(10).

- "Captcha Math", "Joomla Article (Related)", "Youtube" plug-ins removed from Core package.
	>> available for Free on SEBLOD.com/store

! Back-end UI reworked: performance/responsiveness improved greatly + look & feel is cleaner :)
! Copyright Updated.
! Force Random Password to have 20 characters.
! Object Plug-ins (exporter.php, importer.php) updated for Exporter/Importer Add-ons.
! Prepare "Jobs" for upcoming SEBLOD Toolbox Add-on.
! Prevent reset of auto_increment in InnoDB.
! SEBLOD.com Urls updated in Admin Menu Module.
! Sites generation refactored for greater flexibility.
! Various images added for all supported Object plug-ins.

^ AfterStore processing/events not executed anymore if an error is thrown while storing.
^ Colorbox updated to 1.6.3.
^ Custom Variables ($uri->get(...) in Links) removed when empty >> proper URLs.
^ Document title can now be altered in BeforeRender event/process.
^ "Exact" match mode + "Unquoted" applied to fields stored in Int/Tinyint formats.
^ Form/List titles, Page Heading forced to be hidden when tmpl=raw on Forms & Lists.
^ "Group X" refactored (cleaner JS, actions/buttons support added for Table mode).
^ jQuery Validation script updated for AJAX and Confirm validations.
^ Mobile Detect updated to 2.8.21.
^ One (seb_one) updated with "Inherited" variation parameter.
^ Table Template refactored (automatically ignore a column when there is no content in any of the rows).
	>> "Empty Columns" parameter added for the previous behaviour

# Add missing options pre/post before/after store processing.
# AJAX availability issue fixed >> no need to clic more than once now, yeah!
# AJAX availability issue fixed >> (POST) check using the "Fields" parameter.
# "Alternative Search" issue fixed for Infinite pagination.
# "allowUserRegistration" now supported.
# Checkbox (Delete File) "onchange" issue fixed on Upload File/Image plug-in.
# Cookie issue while creating a new Search Type (back-end) fixed.
# Custom Attributes issue fixed on Cotnent plug-in (beforeRender).
# Cyrillic issue fixed (while creating a field from a Content Type).
# < p > removed from Freetexts field in install.sql
# Empty message issue fixed after (submitting a Form).
# Execution time issue fixed for Bridge creation (when there are a lot of articles in category)
# Fields (id >= 533 AND id < 5000) patched for some websites (InnoDB issue).
# Filtering (form App Folder) issue fixed on back-end.
# Hardcoded condition (preventing required/validation) removed on Password field.
# HTML output improved on "Group" and "Group X" (Form views) Field plug-ins (markup=none or restrictions).
# Issues fixed on JCckContent class.
# Language assignement issue fixed in multi-sites system.
# Keep "return" in "user profile edit" redirection.
# Maxlength validation fixed on Messages (Configuration) on back-end.
# Menu ItemIds managed for App Export/Import (in Content, SEBLOD Form/List Link Plug-ins)
# Missing Language file issue fixed on the Calendar Plug-in.
# Missing Languages files (for Package Export) added.
# Missing ->values added in PrepareForm on Checkbox, Select Multiple plug-ins.
# Native Joomla edit button/link fixed for bridge items.
# Native Search behaviors fixed for Upload File/Image plug-ins.
# Nested Lists (using "Items" view) issue fixed.
# Permission issue fixed when Edit button/link is the 1st field assigned.
# "Profile Menu Item" (SEBLOD Options > Integration > Joomla! User) route fixed.
# "Prepare Content" issue fixed on "Joomla! Module" plug-in >> "PrepareContent" method.
# Processing Export issue fixed.
# Registration email/mode using COM_CCK_EMAIL_REGISTERED_BODY fixed.
# Router issue (related to 2 segments URLs) fixed (regression.. :/).
# SEF parameter properly propagated during List rendering.
# Sub-menu item issue fixed in App Export/Import.
# Styling issues fixed in Quick Add Modal (back-end) (regression since Joomla! 3.4.x)
# Syntax (replacement) issues fixed on "Email" plug-in.
# Time Zone issues fixed in Calendar Field plug-in.
# Time Zone now applied automatically by "Date" Typo plug-in.
# Typo issue (onBeforeRender) fixed on Conten Link plug-in, Date Typo plug-in.
# User's groups (erased after user profile edition on front-end) issue fixed. Good catch Lionel ! ;)
# Various improvements or issues fixed.

-------------------- 3.7.2 Upgrade Release [19-Sep-2015] -------------

+ "Class", "Custom Attributes" added on Image Typo plug-in.
+ "Comparison Rule" parameter added for "Exact", "Not Equal" for SQL optimizations.

^ Mobile Detect updated from 2.8.15 to 2.8.16.

# Custom Attributes issue fixed on SEBLOD Form Link plug-in (regression).
# Latest improvements in "Tabs" plug-in added on "PrepareForm", as well.
# Minor Javascript issues fixed (regression since Joomla! 3.4.4)

-------------------- 3.7.1 Upgrade Release [17-Aug-2014] -------------

! "Upgrade" process of Products improved >> SEBLOD Updater add-on required.
! XML scripts (of each SEBLOD product) updated.

! SQL Tables now backuped by default when uninstalling SEBLOD
>> Backup or Drop behavior can be selected in "SEBLOD 3.x > Options > Component".

! Post Install info updated.

+ "Item Identifier", "URL" params added on "Tabs" plug-in.

^ <!-- Begin: SEBLOD 3.x Document --> only displayed when debug is enabled.
^ <!-- End: SEBLOD 3.x Document --> only displayed when debug is enabled.

# "onBeforeRender" Restrictions support added on Tabs plug-in.

-------------------- 3.7.0 Upgrade Release [17-Jul-2015] -------------

+ Ability to create/link a new field to a specific Content Type... from Field Manager (or Search Type).
+ Ability to join the same table more than once added >> Search Join plug-in required.
+ Force Typography even if value is empty.
+ Session Manager added >> "Edit" capabilities for sessions created from Exporter/Importer add-ons.

+ Export of Processings implemented.
+ Export of Storage (Format) plug-ins implemented.
+ Import of Processings implemented.

+ "Custom Attribute" added on "Content", "SEBLOD Form" Link plug-ins.
+ "List" Variation added on "Select Dynamic/Simple"
+ "Prepare Content" parameter added on "Joomla! Module" plug-in.
+ "Static Options" placement added on "Select Dynamic".
+ "Use Value (Field)" added on "SEBLOD Form" Link plug-in.
+ Text input forced for Search Form, on "Wysiwyg Editor" plug-in.
+ ->values property available on "Checkbox" plug-in.

! Table (seb_table) updated to render <table></table> even if no items.

+ "JCck.Core.baseURI" javascript property added.

^ <form> ID suffixed on Form View when tmpl=component OR tmpl=raw.
^ JCckContent class updated >> "delete" method added and issues fixed.
^ Mobile Detect updated from 2.8.13 to 2.8.15.
^ Various Core improvements for upcoming Builder App.
^ Various Script/Styles improved when tmpl=raw.

- Limit of characters removed in "Admin Menu" module.

# "->get(...)" syntax fixed in "$cck->replaceLive()" method.
# A few PHP notices fixed/removed.
# Custom Attributes issue fixed on "Textarea" plug-in.
# Download issue fixed in Custom storage.
# Minimum number of rows forced on "Field X" plug-in.
# Minor CSS issues fixed.
# Minor issue (related to attachments) fixed on "Email" plug-in.
# Minor issues fixed on "Select Dynamic".
# Missing Script (JS) from Stuff applied on "Textarea" plug-in.
# Javascript issues fixed for Free Storage (back-end).
# Javascript issues fixed on "Group X" plug-in.
# Prevent default value to be removed in "Upload File/Image" plug-ins.
# Router issue (related to 2 segments URLs) fixed.
# Various improvements or issues fixed.

-------------------- 3.6.2 Upgrade Release [21-May-2015] -------------

# Remove temporary comments.

-------------------- 3.6.1 Upgrade Release [21-May-2015] -------------

+ J(...) support added for Custom Attributes on Textarea Field.

^ Mobile Detect updated from 2.8.12 to 2.8.13.

# Notice removed on "Joomla Article" Object plug-in.
# Routing issue fixed (regression).

-------------------- 3.6.0 Upgrade Release [8-May-2015] -------------

! Joomla! 3.4 ready.
* Security Release: 2 missing JEXEC security checks added.

+ "Alternative Search Type" added for Lists/Items rendering on Menu Items.
+ Content Type inheritance (Parent/Childs) implemented. (Thanks to pulsarinformatique!)
+ "Leave nothing behind" crowdfunding project implemented. (Thanks to the SEBLOD community!)
+ Load More (Infinite Pagination) added on Search Types.
  >> supported templates: Accordion, List, Masonry, Table, Tabs.
+ "save2copy" Task added on Forms.
+ Router improved for 2 segments URLs (i.e. /parent/...)

^ SQL table storage engine switched from MyISAM to InnoDB in install.sql (!)
^ SQL table storage engine switched from MyISAM to InnoDB for newly created tables. (!)

+ "Change Column" (Alter Storage Field) added.
+ "Comparison Mode" parameter added for "Any Exact" match >> multiple vs multiple.
+ Custom Attributes applied on Content view (Root).
+ Custom Attributes applied on List views' items.
+ Multilanguage Associations available for Joomla! Category object.

+ "More" Link handled more intelligently >> "only if results", "only if more results".
+ "More" Link Text parameter added on List module.
+ "Show List View" parameter added on List View.

+ "Auto Selection" added on Submit Button plug-in.
+ "CheckAll" toggle added on Checkbox plug-in.
+ Custom Attributes applied on Links plug-ins.
+ Default/Live values used to set the "Active" tab on Tabs plug-in.
+ "Delete" methods added to "Joomla Article", Upload File/Image Field plug-ins.
+ "PrepareDownload", "PrepareResource" methods added to "Joomla Article", Upload File/Image Field plug-ins.
+ Dynamic Itemid mode based on fields' mapping added on Content Link plug-in.
+ "Group Required" (at least one field of..) capability added.
+ "has-value" class added on Select Dynamic/Numeric/Simple, Text.. (when value != '')
+ "Image Alt Fieldname" added on Image Typography plug-in.
+ "Image Title" added on Image Typography plug-in.
+ Srcset (2x, 3x) added on Image Typography plug-in.
+ Ukrainian language file added to Calendar plug-in.
+ $user->... $uri->... syntaxes processed added on Freetext plug-in.

+ "Delete" method added (to Field plug-ins). (!)
+ "PrepareDelete" method added (to Object plug-ins). (!)
+ "PrepareDownload" method added (to Field & Object plug-ins). (!)
+ "PrepareExport" method added >> SEBLOD Export Add-on required. (!)
+ "PrepareResource" method added >> SEBLOD WebServices Add-on required. (!)

+ Ability to override HTTP Header fields.
+ Ability to append "Group By" clauses and "User-defined Variables" to Search system.

! One (seb_one) updated with "Custom Attributes".
! Table (seb_table) updated with "Custom Attributes" & "Load More" capabilities.

^ Default behaviour of "Show Value" changed in Form & Delete links >> now hidden by default. (!)
^ Default User Group set as Registered (2) on User (Admin Form). (!)
^ "Download" task refactored. (!)
- "Group By" clause removed in Search system (!)
^ Mootools not included anymore in each Form or List views (>= Joomla 3.4). (!)

^ A few more properties available from Object plug-ins.
^ Chosen script loaded on List views (back-end).
^ Include inline scripts for Tabs when tmpl=raw
^ jQuery Validation script and style updated.
^ Mobile Detect updated from 2.8.3 to 2.8.12.
^ "template_preview.png" updated for One (seb_one) template.

- "defines.php" file (from back-end component) removed.
- "size="1" attribute removed on JForm Category/MenuItem, Select Dynamic/Numeric/Simple.

# Broken rendering issue fixed for nested lists.
# "Create" permission check now applied on SEBLOD Form Link.
# Check state of Email field before sending (disabled = no email).
# Custom Redirection issue fixed Search Form (Submit Button).
# Default set up of "Export" & "Process" permissions forced after installation.
# Duplicated path segments issue fixed on router.
# "Edit Own" issue fixed on Free Object.
# HTML output fixed on "Group" & "Group X" Field plug-ins when markup=none.
# Inherit Object automatically when creating a new Search Type based on a Content Type.
# Inherit Search Type issue fixed on Search module.
# Minor CSS issues fixed.
+ Missing "Delete" methods added to Free, User, User Group Object plug-ins.
# Missing icons added on Form & List views (back-end).
# Missing computation rules now applied on "Group" Field plug-in.
# Multilanguage issue fixed on Joomla! Article object (regression).
# Retriving "Bridge" Author SQL query fixed.
# Routing issue (Auto Redirection: Content) fixed.
# Routing issue (when SEF is OFF) fixed on Search Module.
# Safe string issue (multiple allowed characters) fixed on Upload File/Image plug-ins.
# "sendpassword" parameter supported on Joomla! User registration.
# "undefined" javascript issue fixed on various plug-ins configuration.
# Unsafe Characters issues fixed on Links' Custom Variables.
# Validation rules fixed for fields inside Tabs.
# Various issues fixed on App Export.
# Various issues fixed, and code refactored on JCckContent class.
# Various SQL queries fixed.
# Wrong params for "Include File action" issue fixed.

-------------------- 3.5.0 Upgrade Release [5-Dec-2014] -------------

+ XML format added on List View.
+ Ordering By "Custom Values" added.
+ Ordering Modifiers (Length, Numeric) added >> natural sorting.

+ "Count" parameter added on List View.
+ "Empty", "Each Word Exact" Match mode added on Search Types.

+ "Show Title", "Class", "Tag" added to List module.
+ "Show Title", "Class", "Tag" added to Search module.
+ "Show Description" added to Search module.

+ JCckDevImage class added for Images processing.

! Complete Processing Task >> SEBLOD Toolbox Add-on required.
^ Image Quality (JPG, PNG) moved to SEBLOD Options (Media).
^ Include inline scripts for Validation when tmpl=raw
^ Implement JCckVersion class.
^ Move Processings from Toolbox Add-on to Core.
! Rename #__cck_more_toolbox_processings to #__cck_more_processings.

# Append/Prepend issue fixed on Submit Button plug-in.
# Export of Restriction plug-ins issue fixed on Search Types.
# Notices removed in Router.
# Redirection issue fixed on SEBLOD Form Link. (regression since SEBLOD 3.4.x)
# Remove Match modes on Div, Tabs plug-ins.
# Tooltip issue (Heading variation) fixed (Joomla 2.5).

-------------------- 3.4.3 Upgrade Release [12-Nov-2014] -------------

# "JHtml: :bootstrap not supported. File not found" on Joomla 2.5 issue fixed.
# Javascript issue fixed on Lists (regression)
# Markup fixed when "No Access" set to none on Forms.
# Missing "save2redirect", "save2skip" in SQL

-------------------- 3.4.2 Upgrade Release [27-Oct-2014] -------------

# Custom Attributes (>3) issue fixed on Select Dynamic plug-in.
# install.sql updated >> `location` column (#__cck_core_fields) now VARCHAR(1024)
# Wrong template path (for overrides on back-end) issue fixed.

-------------------- 3.4.1 Upgrade Release [20-Oct-2014] -------------

+ "Index, Follow, No archive", "Index, No follow, No archive" added to "Robots" fields.
+ "Index, Follow, No ODP", "Index, No follow, No ODP" added to "Robots" fields.
+ "Index, Follow, No snippet", "Index, No follow, No snippet" added to "Robots" fields.

# Missing "Decimal (10,8)" Storage Type (for Latitude) added.
# Missing "Decimal (11,8)" Storage Type (for Longitude) added.

-------------------- 3.4.0 Upgrade Release [17-Oct-2014] -------------

+ Geo Distance ("Radius Higher", "Radius Lower") Match modes added on Search Types.
+ Ordering on Table (seb_table) Columns >> Search Ordering plug-in required.

+ "onCckPreBeforeStore", "onCckPostBeforeStore" events added.
+ "onCckPreAfterStore", "onCckPostAfterStore" events added.
+ "odt", "ods", "odp", "xlsm" included in download.php.
+ "save2redirect", "save2skip" Tasks added on Forms.

+ Allowed Case (Media) added to SEBLOD Options.
+ Custom Attributes (Component) added to SEBLOD Options.
+ "Featured" parameter added to Templates to set any Content/Form template as default.

+ "Custom variables", "Redirection" parameters added on Submit Button plug-in.
+ $cck->getSafe[...] syntax added on "HTML" Typography plug-in.
+ $user->... $uri->... syntaxes processed added on Select Dynamic plug-in.
+ ->values property available on Select Multiple plug-in.

+ Custom Attributes added to One (seb_one) template.
+ "column-q" to "column-z" positions adde to Table (seb_table) template.
+ Heading Variation added on Table (seb_table) template.
+ "Header", "Layout", "Margin" parameters added on Table (seb_table) template.
+ "no_result.php" updated on Blog (seb_blog), Table (seb_table) templates.

^ Mobile Detect updated from 2.7.8 to 2.8.3.

! "download.php" file (from back-end component) removed.
! No <form> tag anymore when Search Form option is set to hide.
! No JS included anymore when Search Form option is set to hide.
! Rendering improved >> nested lists properly rendered.
! Template Manager updated.

# Data integrity check issue fixed (allowing n submissions per Form).
# Hard coded live removed on "JForm User" plug-in (on Search Form).
# Message markup (when empty message) issue fixed.
# Minor issues fixed on Search system.
# Multi-value mode issue fixed on Upload File plug-in.
# "Static" Variation issue fixed on "JForm User" plug-in.
# Reinstalling (after uninstall) issue fixed >> we suggest to reinstall the same version, though!
# Router legacy mode issue fixed.
# Various improvements or issues fixed.

-------------------- 3.3.7 Upgrade Release [6-Aug-2014] -------------

+ Custom Attributes added on Heading Typography plug-in.

# Export issue (related to pagination options) fixed >> only current items exported.
# Live User properties issue fixed.
# Restoring Versions issues fixed. (regression since Joomla! 3.3.x)

-------------------- 3.3.6 Upgrade Release [06-Jul-2014] -------------

+ $cck->getSafe[...] syntax added for Custom Variables on Links plug-ins.
>> JCckDev::toSafeID function applied. ex: $cck->getSafeValue('fieldname')

# App Export issue (variation="none") fixed.
# App Upgrade issue fixed.
# Broken Search query fixed.
# Custom Variables issue fixed on Content Link plug-in.
# Front-end issue partially fixed on "JForm User" plug-in.
# install.sql updated >> `access` columns are now INT(11)
# Restrictions applied on Tabs Field plug-in.

-------------------- 3.3.5 Upgrade Release [22-May-2014] -------------

! Export from App Folder Manager improved.
+ "Append Date" added to App Folder Options, and SEBLOD Options.
+ "Append Version Number" added to App Folder Options, and SEBLOD Options.

+ J(...) support added for Custom Attributes on Text Field.

# MySQL Reserved Words issue fixed.
# Restriction plug-ins now exported in App.

-------------------- 3.3.4 Upgrade Release [30-Apr-2014] -------------

* Security Release: download task was too permissive.

+ Allowed Paths (Media) added to SEBLOD Options. (cf details there)
+ "At the right", "Popover" field description placements added.
+ "Auto Hidden" Field Variation added on Select Dynamic plug-in.

! "download.php" file (from back-end component) deprecated.
! Table Template (seb_table) updated >> Markup (none) is now applied.

# Conditional States fixed when markup=none.
# Height:auto added on Textarea (cck.site.css)
# JForm Tag issue fixed.
# Number/URL variation fixed on Text plug-in.
# Redirection (after search) issue fixed on Homepage.
# Search issue (Field X > 10) fixed.
# Template Style issue fixed on Multi-site.

-------------------- 3.3.3 Upgrade Release [1-Apr-2014] -------------

! Access (ACL) applied on Lists.
+ Allowed Characters (Media) added to SEBLOD Options.
+ "Action", "Redirection URL" (No Access) added on Search Types.
+ "Message", "Message Style" (No Access) added on Search Types.

^ cck-pos-[POSITION] added to "seb_css3" variation.
^ Default storage type updated from VARCHAR(255) to VARCHAR(512) on Link plug-in.
^ Upload File/Image plug-ins updated. (with allowed characters)

# ALTER TABLE [TABLE] DROP [COLUMN] issue fixed.
# "Edit Own (Related Content)" issues fixed.
# Multilanguage Integration issues fixed. (regression since Joomla! 3.2.x)

-------------------- 3.3.2 Upgrade Release [10-Mar-2014] -------------

* Security Release: XSS Vulnerability fixed. (Field Variation)

# Download issue fixed. (regression since SEBLOD 3.3.x)

-------------------- 3.3.1 Upgrade Release [5-Mar-2014] -------------

+ "event" argument added to $cck->addScriptDeclaration()

# Array issue fixed on "HTML" Typography plug-in.
# Memory issue patched on SEBLOD back-end.

-------------------- 3.3.0 Upgrade Release [27-Feb-2014] -------------

+ "Article", "Category", "User" Content Types refactored (Site Forms).
+ "CCK Workflow" Restriction plug-in added.
+ Core Stylesheets can be included/excluded easily.
+ Markup ("default" or "none") added on Content/Search Types.
+ Mobile Detect Library added, use "new JCckDevice".
+ Plug-in type added => { CCK Field Restriction }.

+ Aliases (URL) added to Sites. (Multi-sites)
+ "Edit Own (Related Content)", "Process" permissions added.
+ "onCckDownloadSuccess" event added.
! Performance improvements and optimizations.
+ "process" Task added on Lists >> SEBLOD Toolbox Add-on required.

^ "Any Word Exact" match updated >> "IN" clause instead of OR statements.
+ "Comparison Rule" parameter added for "Any Word Exact" match.
+ "Default Value" parameter added on "Stage" Live.
+ "Inherit Search Type" parameter added on Search module.
+ renderPosition() available on List view.
+ Stylesheets parameter added to SEBLOD Options.
+ Stylesheets parameter added to Content/Search Types (sliding panel).
^ "Typography" moved to 2nd pane on Content/Search Types.
^ "Typo on Label" moved to Typography plug-ins parameters.

+ "Crop Dynamic" thumbnail process added on Upload Image plug-in.
+ "First/Last" option text added on Select Numeric plug-in.
+ "Icon" Field plug-in added.
+ "Path(s)" parameter added to Content Link plug-in.
+ "Status" parameter added to Link plug-ins.

- "Author" Field plug-in removed, but available on SEBLOD.com
^ "border: 1px solid #dddddd !important;" removed on .inputbox
^ "border: 1px solid #888888 !important;" removed on .inputbox:focus

# Auto Redirection (content) issue fixed for Joomla! User/Group Object plug-ins.
# ".cck-clrfix" added on Form (edit.php) and List (default.php) layouts.
# ".cck-w30" added in cck.responsive.css
# "Cancel" Task issue fixed on Submit Button plug-in.
# CSS issues fixed on Colorbox styles.
# Issue on + "Quick Icons" module fixed.
# Padding updated fo "div.cck_page_desc" >> "div.cck_page_desc{padding:0;}"
# Pagination issue (default values applied when values were empty) fixed.
# Useless CSS lines removed.
# Various minor issues fixed.

-------------------- 3.2.2 Upgrade Release [20-Jan-2014] -------------

# Broken SQL queries (Advanced Search) fixed.
# Notice removed on "JForm Rules", "Wysiwyg Editor" plug-ins.
# Pagination issue fixed. (regression only for Joomla! 2.5.x)
# Routing issue fixed on Ajax Availability Validation plug-in.
# Updating (from 2.x versions) issues fixed.

-------------------- 3.2.1 Upgrade Release [02-Jan-2014] -------------

# Joomla!/Native (multilingual) router issue fixed. (regression since Joomla! 3.2.1)
# Redirection issue on Search module fixed. (regression on SEBLOD 3.2.0)
# Search Task issues fixed. (regression on SEBLOD 3.2.0)
# UI issues fixed on back-end.

-------------------- 3.2.0 Upgrade Release [24-Dec-2013] -------------

! Joomla! 3.2 ready.
+ "Article", "Category", "User", "User Group" Content Types refactored.
+ "Articles", "Categories", "Users", Search Types added.
+ "apply", "save2new", "save2view" Tasks added on Forms.
+ "export" Task added on Lists >> SEBLOD Exporter Add-on required.
+ History/Log added for all Install/Update/Uninstall events. (Joomla! 3.x)
+ UI improvements & issues fixed on back-end. (Joomla! 3.x)

+ "Export" permissions added.
+ "Is Null", "Is Not Null" Match modes added on Search Types.
+ "Not Beginning with", "Not Ending with", "Not In" Match modes added on Search Types.
+ "Override Title" parameter is now multilingual, ex: {"en-GB":"...","fr-FR":"..."}
+ "triggers" state added on Conditional States & Triggers.

+ "Default Value", "Encryption" parameters added on "Url Variable" Live plug-in.
+ "Default Value", "Excluded Values" added (for access, groups) on "User" Live plug-in.
+ "Div" Field plug-in added.
+ Folder Permissions added on Upload Image plug-in.
+ HTML5 number, url Input Types added (as Field Variation) on Text plug-in.
+ "Hidden" position added to seb_table.
+ "Language" parameter added on "Content" Link plug-in.
+ "Raw Rendering" added on Menu Items / Modules.
+ "Redirection Url" parameter added on Delete Link plug-in.
+ Search Form can now be displayed below the List.. on List menu item.
+ Table Template (seb_table) updated.
+ "Task" parameter added on Submit Button plug-in.
+ Toolbar Button Variation added on Submit Button plug-in.
+ "View More", "Class", "Variables" parameters added on List module.

^ Form rendering is now done after Items rendering on Lists.
^ Items are now rendered inside the <form> tag of the List view.
- Live, Match, Validation parameters removed on Submit Button plug-in.
- Preferences (per User) removed on component backend, and library.
- Style removed on component backend.
! JCck::getUser() arguments changed, JCckLegacy::getUser() added if needed..
! "JCck::setUser_Preference()" function deprecated, use JCckLegacy::setUser_Preference();
! "JCck::setUser_Preferences()" function deprecated, use JCckLegacy::setUser_Preferences();
! "JCck::googleAnalytics()" function deprecated, use JCckLegacy::googleAnalytics();

# "access", and other extended profile properties available on "User" Live plug-in.
# Characters/Escaping issues fixed on Ajax Availability Validation plug-in.
# Fatal error issue fixed on Joomla! User Object plug-in.
# Filename forced as "safe string" on Upload File/Image plug-ins.
# Issues on "Versions" and Version Manager.. fixed.
# Language Files (for "lib_cck") are not duplicated anymore.
# Redirection issue (when link applied on Content View) fixed on Delete Link plug-in.
# Reordering issue fixed on Joomla! Category Object plug-in.
# Missing text (prepareContent) added on "JForm Usergroups" plug-in.
# urlencode/urldecode added for Live parameters on Menu Items / Modules.
# Various improvements or issues fixed.

-------------------- 3.1.5 Upgrade Release [05-Aug-2013] -------------

# Autocomplete issue (Chromium) fixed. >> autocomplete="off" added on the form itself.
# "cck-clrfix" added (div class="cck_page") on Form/List component views.
# Tags issues fixed + updates for Joomla 3.1.5	[storage must be: standard|..|tags]

^ jQuery Sly (scrolling) script updated.		[back-end: please empty your cache]

-------------------- 3.1.4 Upgrade Release [12-Jul-2013] -------------

+ Format (HTML/Plain Text) added on Email Field plug-in.

# Breadcrumbs issue fixed. (please use "Menu" as Pathway on Joomla! 3)
# Cropping issue fixed on Upload Image Field plug-in. (@adonay, @txusmi, @niels85.. thanks!)
# Redirection added for homepages on SEBLOD Multi-sites.

# "Alternative Layout" parameter added on "Form", "List", and "Search" modules.
# Calendar consistency issue (admin/site form vs search form) fixed.
# Line Height issues (Label vs Value) fixed.
# Missing html/link/text (prepareContent) added on "JForm Menuitem" plug-in.
# Missing floating widths (.cck-w33f, .cck-w16f, ..) added in cck.responsive.css
# Quotes now escaped (confirm box) on Delete Link.
# Remaining "form div { margin: 0em 0 0em 0 !important; }" (in cck.search.css) removed.
# Single quote issue fixed on Ajax Availability Validation plug-in.
# "Text Input" parameter (enabled/read-only) added on Calendar Field plug-in.

-------------------- 3.1.3 Upgrade Release [06-Jun-2013] -------------

# Custom Variables issue fixed on Content Link Plug-in.
# Delete (Article, Category) fixed. (regression since Joomla! 3.1)

-------------------- 3.1.2 Upgrade Release [31-May-2013] -------------

# Canonical Urls issue fixed on Joomla! 3. ("SEF Canonical" option added)
# Item Route issue (on the back-end) fixed.
# Temporary fix added for upcoming SEBLOD Exporter add-on.
# Translation toggle issue fixed on Checkbox, Freetext, Radio, Select(s) plug-ins.
# Translation (of Options) issue fixed on Checkbox, Radio, Select(s) plug-ins.
# UI improvements on Joomla! 3.x
# Useless CSS lines removed.
# "form div { margin: 0em 0 0em 0 !important; }" removed.
# "prepateTable()" notice (Strict Standards) fixed for both Joomla! 2.5 and 3.x
# Minor issues fixed.

-------------------- 3.1.1 Upgrade Release [25-Apr-2013] -------------

# Tags List output (using native layout) added on "JForm Tag" field.

-------------------- 3.1.0 Upgrade Release [25-Apr-2013] -------------

+ Friendly Multilanguage Management added on Article Manager.
+ Multilanguage Associations available for Joomla! Article object.
+ Native Tag system available for Joomla! Article & Category objects.

+ "Nested Exact" Match modes added on Search Types.

+ "Clear", "Modal" positions added to seb_one.
+ Folder Permissions added on "Upload File" Field Plug-in.
+ "JForm Associations" Field plug-in added.
! "JForm Tag", "Tabs" Field plug-ins updated.
+ "Use Value" added on "Content" Link Plug-in.

# Character escaping issue fixed in JSON storage.
# Data integrity check improved.
# Dynamic Legend (Position Variation) get Typography as well now.
# Minor issues fixed.

-------------------- 3.0.4 Upgrade Release [16-Apr-2013] -------------

# Issue (related to SEBLOD Importer) fixed.

-------------------- 3.0.3 Upgrade Release [14-Apr-2013] -------------

# Category Extension issue fixed. (@Eddy, thank you for the report)

-------------------- 3.0.2 Upgrade Release [03-Apr-2013] -------------

* Security Release: "User Password2" field storage issue fixed.
# Template grid (33.33%,..) issue fixed.

-------------------- 3.0.1 Upgrade Release [02-04-2013] -------------

# Broken SQL query (during upgrade process) fixed.

-------------------- 3.0.0 Major Release [29-Mar-2013] -------------

+ App Import / Export refactored & completed.
+ Conditional States & Triggers v2 added.
+ Computation Rules added on Content Types.
! Performance improvements and optimizations.
+ Stages added on Content Types >> Multi-page Forms.
+ Router refactored & improved >> /id-alias, /id, /alias .. your choice!

+ Bridge with Article (User, User Group) is now optional (disabled by default).
! Extensive work on compatibility & consistency with both Joomla! 2.5 and 3.0
+ Init. View (Fields) using another view added on Content Types.
+ Integration improved >> Modal Box (Icon, List) added, Options updated.

+ "App Root", "Icon" parameters added on App Folder.
+ Access added on Search Types.
+ "Auto Redirection" action (No Result) added on Search Types.
+ Cache (on Search) added on Search Types.
+ "Convertible" (Toggle) added for multiple selection (Conditionnal, Live Value).
+ "Content Creation" parameter added on Content Types.
+ "Content Object", "Alias" parameters added on Content/Search Types.
+ "Delete", "Delete Own" permissions added.
+ "Ending with", "Not Equal", "Not Like" Match modes added on Search Types.
+ "Exclude Fields" (Data Integrity) added on Content Types.
+ "Filter" Field Variation added on Search Types.
+ Language files, Media folder included in Apps.
+ Live plug-ins improved >> options added.
+ "Offline" parameter added to Sites. (Multi-sites)
+ Options (Fields) added to Sites. (Multi-sites)
^ Permissions moved to Modal Box on Content Types (sliding panel).
+ "Optional Stages" added on Search Types.
+ "Quick Menu" item creation added on Content/Search Types.
+ "Rebuild" button added on Folder Manager.
+ SEBLOD Workshop optimized.
+ Template selection (List view) added on "New" Search Type modal box. (J!3.0)

+ "Admin Menu" module updated.
+ "Quick Add" module added.
+ Media Extensions, "Prepare Content", SEF, Validation added on SEBLOD configuration.
+ "SEBLOD Breadcrumbs" module added.
+ URL Query (Vars) added on List menu item.
+ URL Assignment added on "Form", "List", "Search" modules.

+ "Ajax Availability" Validation plug-in added.
+ "Columns" (orientation = vertical) added on Checkbox, Radio plug-ins.
+ "Custom Attributes" added on Checkbox, Radio, Select(s) plug-ins.
+ "Delete Content", Link plug-in added.
+ "Default Title" (Auto/Custom) available on User, User Group Storage Location plug-ins.
+ IcoMoon support added on Submit Button plug-in. (requirement: Joomla 3.0)
+ "JForm Tag", "Tabs" Field plug-ins added. (requirement: Joomla! 3.1)
+ "Joomla User", "Url Variable" Live plug-ins added.
+ "Modal" position added to seb_one. (requirement: Joomla 3.0)
+ "Orientation" added on "Group X" Field plug-in.
+ Tranlation of options can be disabled on Checkbox, Radio, Select(s) plug-ins.
+ Tranlation of text can be disabled on Freetext plug-in.

^ jQuery updated from 1.7.2 to 1.8.3.
^ jQuery UI updated.
^ jQuery Validation script updated.
^ JS files moved & refactored. (cf /media/cck/ folder)
^ Use of jQuery Encapsulation & Javascript Namespaces added.

+ buildRoute() & parseRoute() added to Storage Location plug-ins. (!)
+ onCCK_Storage_LocationPrepareItems() added to Storage Location plug-ins. (!)

^ .conditionalState() renamed to .conditionalStateLegacy()
^ $cck->renderPositions(...); must be replaced by: echo $cck->renderPositions(...);
^ "CCK_Submit"... JS function removed, use "JCck.Core.submit"...
! "getLink" (Storage Location) function removed, use "getRoute".
! "getProperty" (Storage Location) function removed, use "getStaticProperties".
! "JCckDatabase::doQuery()" function deprecated, use JCckDatabase::execute();
- "Div Clear" Field plug-in removed, but available on SEBLOD.com
- "IFrame" Field plug-in removed, but available on SEBLOD.com
- "Joomla! Message" Content Object plug-in removed, but available on SEBLOD.com
- "Url Var Int", "Url Var String" Live plug-ins removed, use "Url Variable".
- "User Profile" Live plug-in removed, use "Joomla User".
- Upload Image plug-in improved.
- "rewritten_url.php" file removed.
- "route.php" file (class CCKHelperRoute) removed.
^ "Storage Location" renamed to "Object".

# A few issues related to Multi-sites fixed.
# "Access" + "Status" applied on Joomla! Article (Related) plug-in.
# "Content" Redirection (after Form submission) fixed/updated.
# "Custom" Storage field defined by Content Object. (introtext,description..)
# "Data Type" (Storage) fixed on Calendar. (datetime)
# Empty task on component (view=list) and mod_cck_search fixed.
# "Group" Field plug-in updated.
# "Jform Rules" Field plug-in updated. (+ Modal Box added)
# Form still filled after Captcha verification failed.
# Missing Development Prefix added in Duplicate Process.
# New Lines issues fixed on "Textarea" Field plug-in.
# Template Style not inherited from parent anymore after Duplicate process.
# Transliteration added on Content/Search Type, Field >> name.
# Relative URLs issue fixed on "Link" Field plug-in.
# Separator inherited (any word/exact) from multiple fields.
# Use of "DS" constant removed.
# "Wysiwyg Editor" Field compatibility improved with: JCK Editor.
# Numerous improvements or issues fixed.

-------------------- 2.3.9 Upgrade Release [20-Jul-2012] -------------

* Security Release
* Upload & Delete issues fixed on Upload File/Image plug-ins. @4664

# Description link (Wysiwyg) issue fixed on Menu Items / Modules.
# Missing jimport('joomla.filesystem.file') issue (on CloudAccess, or..) fixed.
# Wrong value (from Upload File/Image) on Email plug-in fixed. @4672

-------------------- 2.3.8 Upgrade Release [28-Jun-2012] -------------

# "Joomla User Group" plug-in fixed. (broken since 2.3.7).

-------------------- 2.3.7 Upgrade Release [25-Jun-2012] -------------

^ JControllerLegacy, JModelLegacy, JViewLegacy aliases used.
^ jQuery UI updated from 1.8.19 to 1.8.21.

# Missing escape on JSON storage format added.	@4316
# Options (component) available for "Administrator" group.
# Route issue (when SEF is off) fixed.
# Search issue (when SEF is off) fixed.		@4180
# "Wysiwyg Editor" improved >> Button Style, Default Value, Toggle Editor.
# "Wysiwyg Editor" issues fixed (asset, box)	@3149, @2898
# Minor improvements or issues fixed.

-------------------- 2.3.6 Upgrade Release [1-May-2012] -------------

! Updates (from Extension Manager) available.

^ jQuery UI updated.

# ACL (App Folder, Content Type) properly initialized.
# autocomplete="off" added on "Password".
# Bad offset fixed on "Field X", "Group X".
# Dummy update site (http://localhost..) removed.
# Live value selection fixed on "JForm User Groups".	@3824
# Upload File (in Field X) fixed.

-------------------- 2.3.5 Upgrade Release [16-Apr-2012] -------------

+ "Stretch Dynamic" thumbnail process added on "Upload Image".

^ jQuery updated from 1.7.1 to 1.7.2.
^ jQuery UI updated.

# Calendar (as template parameter) issue fixed. (use js_format="raw")
# Default storage (not applied after changing Field Type) fixed.
# Email (SMTP mailer) issue fixed.	@3534
# Fatal error on com_search fixed.	@3542
# JSON storage issue on PHP 5.2 fixed.	@3842
# Language Package type fixed ("language" >> "file").	@3789
# MIME types (docx, xlsx, pptx, ppsx) added.	@3790
# Missing style added on "Joomla module" field plug-in.
# Multi-site creation (accents,special chars..) fixed.	@3783

-------------------- 2.3.1 Upgrade Release [30-Mar-2012] -------------

# Data integrity issue (on edition) fixed.
# Email {del} syntax issue fixed.
# Version feature configuration (for search type) fixed.

-------------------- 2.3.0 Upgrade Release [14-Mar-2012] -------------

* Security Release - All websites should be updated -
* Validations of Data integrity (beforeStore) added.

+ Embedded Documentation ("How to setup this Field") added.
+ Multiple User profile (registration) available.
! Natively Compatible with "Smart Search" (Finder).
+ "Versions" feature added for Content/Search Types.

+ "Auto Redirection" added on List menu item.
+ "Default Storage", "Indexing" added on Content Type.
+ "Save", "Save & New" added on Field (from Workshop).
+ One (seb_one) template optimized.
+ seb_css3 position variation optimized.

+ "Calendar" field plug-in improved. (language)
+ "Email" field plug-in improved.
+ "Date" typography plug-in improved. (language)
+ Links plug-ins improved.

! "cck-deespest" class replaced by $cck->id."-deepest"

# author_id (#__cck_core) fixed.
# Blog (seb_blog) template markup issue fixed.
# Cookie (Views on Workshop) fixed.
# Multi-site Language (override) fixed.
# "Upload File", "Upload Image" issues fixed.
# Various minor issues fixed.

-------------------- 2.2.0 Upgrade Release [17-Feb-2012] -------------

! Suitable for Joomla! 2.5
! Library refactored. >> Autoloader, Class Names, Folder Tree

+ "CSS Definitions" (Site) added on Configuration.
+ "Exclude Components" (Integration) added on Configuration.
+ "Heading", "Image" Typography plug-ins improved.
+ "Markup (Class)" added on Content/Search types.
^ "joomla_17" variation renamed to "joomla".

! "CCK" class deprecated, use "JCck".
! "CCK_Database" class deprecated, use "JCckDatabase".
! "CCK_Dev" class deprecated, use "JCckDev".
! "CCK_Field" class deprecated, use "JCckDevField".
! "CCK_TableGeneric" class deprecated, use "JCckTable".
! "plgCCK_FieldGeneric" class deprecated, use "JCckPluginField".
! "plgCCK_Field_LinkGeneric" class deprecated, use "JCckPluginLink".
! "plgCCK_Field_LiveGeneric" class deprecated, use "JCckPluginLive".
! "plgCCK_Field_TypoGeneric" class deprecated, use "JCckPluginTypo".
! "plgCCK_Field_ValidationGeneric" class deprecated, use "JCckPluginValidation".
! "plgCCK_StorageGeneric" class deprecated, use "JCckPluginStorage".
! "plgCCK_Storage_LocationGeneric" class deprecated, use "JCckPluginLocation".

! "CCK_Database::loadResultArray()" function deprecated,
  use JCckDatabase::loadColumn();
! "plgCCK_Field_LinkGeneric::g_addScript_Construct()" function deprecated,
  use JCckDev::initScript( 'link', .. );
! "plgCCK_Field_TypoGeneric::g_addScript_Construct()" function deprecated,
  use JCckDev::initScript( 'typo', .. );
! "plgCCK_Field_ValidationGeneric::g_addScript_Construct()" function deprecated,
  use JCckDev::initScript( 'validation', .. );

# Bridge (storage) fixed.
# Blog (seb_blog) template issues fixed.
# Integration issue (id) fixed.
# Javascript issue on Filters (SEBLOD Workshop) fixed.
# Language package export fixed.
# Missing SQL updates (previous package) added.
# Page Titles (Site Name) fixed on Form/List.
# "Upload File", "Upload Image" issues fixed.

-------------------- 2.1.0 Upgrade Release [18-Jan-2012] -------------

! Core Document updated (lighter & proper).
! Full Translation completed.
! "Ready" for upcoming SEBLOD's Add-ons.
+ Templates updated >> Blog (seb_blog), Table (seb_table).
+ Updates (from Extension Manager) enabled.

+ "BeforeRenderContent", "BeforeRenderForm" processes added.
- "Clear" variation removed from core package.
+ CSS, JS, Modal Box Style added on Configuration.
+ "Global" configuration added on Content/Search types.
+ Position can be displayed without variation. ("None")

+ "Button Submit", "Group", "Wysiwyg Editor", ... improved.
^ "Related Article" removed >> "Joomla Article" added.
+ "Class", "Rel", "Target", "Tmpl" added on Link plug-ins.
+ "Clear" Typography plug-in added.
+ "Clear" variation added on Form/List menu items & modules.
+ "Heading", "Html" typo improved.  Html >> "$cck->getValue(...)", "J(...)".
+ "as Tag", "with Class" added on Form/List menu items. (Show Title)
+ Description override added on Form/List menu items. (Show Description)
+ "with Class" added on List menu item. (Items Number, Pagination)

+ ".isVisibleWhen()" (jQuery) added.	[Dev. Framework]
+ ".isDisabledWhen()" (jQuery) added.	[Dev. Framework]
! ".displayFor()" (jQuery) is deprecated.	[Dev. Framework]
+ js_appendto="" (jQuery from Xml) added.	[Dev. Framework]
+ js_disabledwhen="" (jQuery from Xml) added.	[Dev. Framework]
+ js_isvisiblewhen="" (jQuery from Xml) added.	[Dev. Framework]
^ jQuery updated from 1.7.0 to 1.7.1.
^ JS files refactored (jquery-dev.js, jquery-more.js, jquery-noconflict.js).
^ jquery.cookie.min.js renamed to jquery.biscuit.min.js (true story! ;).

# Bad Ordering, "Created By", "Publish Up" fixed (submission).
# Check In fixed (edition).
# "Upload File", "Upload Image" issues fixed.
# Various improvements or issues fixed.

-------------------- 2.0.0 GA Upgrade Release [11-Nov-2011] -------------

+ App Import & Export system added.
+ Conditional States & Triggers process added.
+ Interface & Usability improved.
+ Multi-sites system added.
+ Plug-in type added => { CCK Field Link }.
+ Search module added.

+ Language package export (from Options) added.
+ Required/Validations added on Search Type.
+ Variation export (from Template Manager) added.

+ "Deepest" added on Header/Top/Bottom/Footer lines (template).
+ "Freetext", "JForm Menu Item" Field plug-ins added.
+ "Email", "Link", "Group X", "Related Article", ... improved.
+ "Content", "CCK Form", "CCK List" Link plug-ins added.
+ "Date" Typo plug-in added.
# "seb_css3", "joomla_17" variations updated.
+ "Show Description" implemented on Form/List menu items.
+ jQuery updated from 1.6.2 to 1.7.0.

^ "Content Types" renamed to "Form & Content Types".
^ "Search Types" renamed to "List & Search Types".
^ "Folders" renamed to "App Folders".
^ "joomla_16" variation renamed to "joomla_17".

# "Calendar" plug-in issues fixed.
# "Free" Storage Location plug-in issues fixed.
# CheckIn applied on Cancel. (Frontend)
# Missing (FR) language files added.
# Router/SEF improved.
# Some PHP 5.2 issues fixed.
# Uninstall process fixed.
# Various improvements or issues fixed.

... and so much more!

-------------------- 2.0.0 RC3 Upgrade Release [22-Jul-2011] -------------

! Suitable for Joomla! 1.7 :)

+ Search Type improved >> Search & List ANY content.
+ "Order", "List" & "Item" clients added.
+ "seb_blog", "seb_table" templates added.

+ ACL improved on Form (Content Type) => inherited from Folders.
+ Batch process (Folder) added.
+ Duplicate process added on Content/Search Type.
+ Field Markup (override) process added.
+ Interface improved + "Indigo Dye" style added + Tooltip selection added.
+ "Match Collection" added on Search Type => Search into Field X & Group X.
+ Max submission (in Parent, per User, per Group) added on Form (Content Type).
+ Multilingual compatibility completed (JText on label, options, ..).
+ Order/Ordering added on List (Search Type).
+ Pagination added on List (Search Type).
+ Router (1st part.) added on List (Search Type).
+ Various options added on Content/Search Type.

+ "Count", "Random & Shuffle" added on List module.
+ "Live" + "Variation" added on Form/List menu items & modules.
+ "Title/Heading", "Metadata" implemented on Form/List menu items.

+ Calendar, Email, Select Dynamic, Upload File/Image, ... improved.
+ "Author", "Div Clear", "IFrame", "Youtube" Field plug-ins added.
+ "Html", "Image" Typo plug-ins added.
+ jQuery updated from 1.5.2 to 1.6.2 & all scripts updated.

# Content/Search Type interface fixed (FF5, IE9, Safari).
# Form Validation fixed. (Multiple Form per page).
# Search feature fixed. (Checkbox, Select Multiple).
# Various improvements or issues fixed.

-------------------- 2.0.0 RC2 Upgrade Release [9-May-2011] ------------

! Editor Compatibility => Artof Editor, CKEditor, JCE, JoomlaCK, TinyMCE ! :)

+ ACL added on Form (Content Type) => create,edit,edit.own
+ Article Edition & User Profile Edition redirections added.
+ Featured attribute added on Folder Manager for Skeleton selection.
+ Splash screen added on Content/Search Type Manager.
+ Storage process improved.

+ Export link added on Plug-in Manager (edit view).

# Calendar, Field X, Group X, Link, Wysiwyg Editor updated.
# Download (file) task fixed + Access applied on it.
# Delete process completed on Article/Category/User Manager.
# Preferences (interface/style) fixed.
# Various PHP 5.2 issues fixed.

-------------------- 2.0.0 RC Initial Release [19-Apr-2011] ------------

! Core refactored & Whole code rewritten.
+ Interface & Usability completely redesigned.
+ Library CCK added.	see libraries/cck/_README.php
+ Template Framework (including Positions & Variations) implemented.
+ { CCK Field } plug-ins added.
+ { CCK Field Live } plug-ins added.
+ { CCK Field Typo } plug-ins added.
+ { CCK Field Validation } plu-gins added.
+ { CCK Storage } plug-ins added.
+ { CCK Storage Location } plug-ins added.

+ Auto-save feature added on Form.
+ Categories renamed to Folders.. in a single Tree.
+ Interface configuration & preferences added:
	+	Choose between Compact or Full view.
    +	Select the Style you prefer.
+ jQuery powered (for everyone's pleasure!).
+ New scripts (JS => Lightbox, Tooltip, Validation..) added.
+ Template Style instance (for Content/Search Type) automation added.

-------- OVERVIEW --------
! Content Type Manager: Create any Form (Content Type) to manage your submission/edition of content.
! Field Manager: Create any Custom Fields.
! Search Type Manager: Create any advanced List Views using multi-criteria Search features.
! Template Manager: Connect any suitable Template to define it as a CCK Template.
! Folder Manager: Classify any template/type/field/search.. to Organize your work.

Now, let's.. TAKE CONTROL OF JOOMLA, and.. ENJOY! ;)

------------------------------------------------------------------------
-------------------- 2.0.0 Beta Release [24-Feb-2011] ------------------

     The King (CCK 1.8.x) is dead. Long live the King (CCK 2.x) !
! jSeblod CCK becomes SEBLOD. Here is the v2.0 which is A-M-A-Z-I-N-G !

  #####################################################################

       ####  ##### ####  ####  ##### ##### ##### ##### ##### ####
 #     #   # #     #   # #   # #     #     #   #   #   #     #   #     #
#      #   # ###   ####  ####  ###   #     #####   #   ###   #   #      #
 #     #   # #     #     #   # #     #     #   #   #   #     #   #     #
       ####  ##### #     #   # ##### ##### #   #   #   ##### ####

------------------------------------------------------------------------
-------------------- 1.8.2 Upgrade Release [29-Nov-2010] ---------------

+ Calendar (Style Variation) added on Alias Custom.
+ Publish Up & Publish Down (Search In) added on Search Generic.
+ To (Field) improved on E-mail >> Grab multi-values (many e-mail address).
+ Bcc (Field) added on E-mail.
# Minor issues fixed.

-------------------- 1.8.1 Upgrade Release [5-Nov-2010] ---------------

+ "Off" State added on SEF Urls (Search Action).

# Upload Image (1folder/content) fixed on FieldX.
# Minor issues fixed on Search Type.
# SEO/Router issues fixed.

-------------------- 1.8.0 Upgrade Release [15-Oct-2010] ---------------

+ ACL added on Content Types >> Different Fields for different Users.
+ Router added. >> SEF Urls for Articles on List (Search Types).

! Performance Improvements on Search ( Cache + Indexes )
+ 2nd Caching Process added on Search Types (on Display).
+ "Exact Phrase Indexed", "Any Words Exact Indexed" Match added on Search Types.
+ "Indexed" added on Text, Select Simple, Radio. >> Content ID + Value joined in db.
+ Inherited User added (from Live) on Content & Search Types.
+ "Live Url = Var, Var(Int)" Live added on Content & Search Types.
+ Personal Content submission added on User Manager.
+ "Search Results = User" Live added on Search Types.

+ Cache available on List Module.
+ "Module Parameters" Live added on Search Types >> Live added on List Module.
+ Random & Shuffle added on List Module.

# Article Title & Stored Value ( Display on... ) added on External Article.
# Auto Redirection parameter added on Search & List Layout (Menu).
+ Article Meta process added on Import (CSV).
+ Cache (On Display) added on Search Action.
	+ CKEditor compatibility added. { TODO }
+ Created Date (Search In) added on Search Generic.
+ Defaultvalue attribute added + Live available on Save.
+ Number Incrementer added on Text.
+ Message (Edition) added on Form Action.
+ SEF Urls (SEO) added on Search Action.
+ To (Field) available on E-mail >> Grab value from Field to set as To.
+ Link & Typography (Html) available on GroupX.
+ Variables added on Free Code.

- Search into Uncategorized Articles removed.
* SQL Injections Vulnerabilities prevented on Search Types.
* XSS Vulnerabilities prevented on Search Types.
# Various issues fixed.

-------------------- 1.7.0.RC4 Upgrade Release [31-Aug-2010] ---------------

+ Display (Submission & Edition) added on Content Types.
+ Live & Value added on Content Types.

! Performance Improvements on Search ( Cache + Indexes )
+ Caching Process added on Search Types >> Speed Up Search & List.
+ "Indexed" added on External Article >> Content ID + External Value joined in database.
+ "Indexed (as Key)" added on Text >> Content ID + Value (Key,Sku..) joined in database.
+ "Menu Parameters" Live added on Search Types. >> Live added on Search & List Layout.
+ Search Operator Field Type added on Fields >> AND, OR, ...

+ CSV Import improved (Joomfish tranlations compatible).
+ CSV Import improved (FieldX & GroupX & Index compatible, UTF-8).

+ Cache attribute added on Search Action.
+ Debug attribute added on Search Action.
+ Deletable attribute added on FieldX & GroupX.
+ Language (JText) added on Typography (Html) >> J(...) for JText::_( '...' ).
+ Mode added on External Article & Save >> Index (as Key) compatible.
+ Orientation attribute added on GroupX.
+ State & Category Parent Id (Search In) added on Search Generic.
+ Target (_self,_blank) added on Form Action & Search Action.
+ Text attribute added on Checkbox & Select Multiple & (all) Select Dynamic.

^ "Required" column replaced by "Title || Index" filters on Field Manager.
$ "com_cckjseblod_more" language loaded.

+ List Module improved.
# Search module improved & issues fixed.
# Readmore issues fixed. (GroupX, index2.php)
# Various improvements added & issues fixed.

-------------------- 1.7.0.RC3 Upgrade Release [1-Jul-2010] ---------------

+ Group (Content Type) Field Type added on Fields.
+ Free Code (Php) Field Type added on Fields.

+ ACL added on Search Types >> Different Fields for different Users.
+ Live added on Search Types >> Get Live Values.
+ Stage added on Search Types >> Join/Multi Queries (External, ...).
+ Custom Sort Mode added on Search Types.

+ "Advanced List" template added.

+ Copy and Rename Prefix added on Batch Process (Fields).
+ CSV Import improved (FieldX & Readmore process added).

+ FieldX improved (Draggable).
+ Free Query added on Select Dynamic.
+ Save style variation added on Alias Custom.
+ Subcategories (In Categories attribute) added on Save.
+ Substitute attribute added on Checkbox & Radio & Select Multiple/Numeric/Simple & External Article & Save.
+ Text attribute added on Radio & Select Dynamic/Simple.
+ Thumb4 & Thumb5 added on Upload Image.

# Alias Custom improved (Search Generic).
# FieldX compatibility improved (External Article, Wysiwyg Editor).

# Various issues fixed.

-------------------- 1.6.2 Upgrade Release [4-May-2010] ---------------

+ Display (Content/List) attributes added on External Article.
+ Javascript Links added on Content dimension (Search Type).
+ Autogenerate Field name function added on Fields.

# Active Menu issue fixed.
# Search Form Submit (IE7) issue fixed.
# Some addtionnal issues fixed on Search/List.
# FieldX (IE7) issue fixed. (Default Form Template updated)
# Validation Alert (IE7) issue fixed.

# Description stored (when Copy) on Templates, Content Types, Fields, Search Types.
# Default Value stored (when Copy) on Fields (Wysiwyg Editor, Free Text).
# Message stored (when Copy) on Fields (Form Action, E-mail).

-------------------- 1.6.1 Upgrade Release [08-Apr-2010] ---------------

+ "1 Folder / Content" attribute added on Upload Image & Upload Simple.

# Bug fixed on Search Multiple.
# Bug fixed on New Template view. (issues since 1.6.0 stable)

-------------------- 1.6.0 Upgrade Release [31-Mar-2010] ---------------

+ Sort added on Search Types >> 4 succeeding levels to sort results.
+ Checkbox, Select (Dynamic, Multiple), Upload (Image, Simple), Wysiwyg searchable.
+ Target added on Search Types >> Use a portion of value.
+ Search Links added on Content dimension (Content & Search Type)
+ "Each Word" Match added on Search Types.
^ "All Words" Match renamed to "Default (Phrase)"
+ Auto Type improved on Search Types.

+ Access added on Content dimension (Search Type) >> Access field by Name or by Location.

+ List module included.
+ "Default Slideshow" template added.
+ "Default Blog" template added.

+ Search Multiple Field Type added on Fields.

+ Value Importer added on SiteForms module.

+ Columns & Table Style attributes added on Checkbox & Radio.
+ Default Value added on Upload Image.
+ Field replacement process in Message added on E-mail.
+ From (E-mail or Field) & To (Field) attributes added on E-mail.
+ Length & Message & Uncategorized attributes added on Search Action.
+ Panel Closer attribute added on Panel & Sub Panel.
+ "Content Hits", "Panel End", "Sub Panel End" field added on Fields.

+ Article Parameters process added on Import (CSV).
- Limit & Ordering filter (static) removed.

# "403 Access Forbidden" (when registration disabled) bug fixed on User Manager.
# "Fakepath" (IE or Opera 10.50) bug fixed on Template.
# Menu (5.0.41 or older database) bug fixed on Joomla Menu field.
# Some issues fixed on Select Dynamic.
# Some issues fixed on Site Form Module.
# Views fixed on Templates. (issues since 1.5.0 stable)

-------------------- 1.5.1 Upgrade Release [22-Feb-2010] ---------------

+ Pack Process updated on Content Types.
+ Language Localization added on Calendar.
# FieldX compatibility improved (Textarea).

-------------------- 1.5.0.STABLE Upgrade Release [22-Feb-2010] ---------------

+ Search Types added >> Create advanced & multi criteria List and/or Search Views.

+ CSV Import on Article Manager >> Import thousands of articles in a few clicks.
+ CSV Import on User Manager >> Import thousands of users in a few clicks.
+ HTML Export on Article Manager >> Export Content as HTML for Newsletters or a Static Html page.

+ Template Type ("Content", "Form", "List") added on Templates.
+ "Default List" template added.

+ New Custom Content Dimension (Link, Typo, Html)
+ Alias Custom Field Type added on Fields.
+ Search Action Field Type added on Fields.
+ Search Generic Field Type added on Fields.

+ Download task added on front-end (File & Upload Simple).
+ Delete Box attribute added on Upload Image & Upload Simple.
+ Filesize property added on File, Upload Image & Upload Simple.
+ Legal Extensions attribute added on Upload Image & Upload Simple.
+ Max File Size attribute added on Upload Image & Upload Simple.
+ Preview attribute added on File, Upload Image & Upload Simple.
+ Images now well supported in Checkbox & Radio options.
+ Separator & Columns attributes added on Field X.
+ User's categories attribute added on Save field.

+ Restriction Levels added on Configuration.

# Cache Issue fixed on Captcha.
# Issues (for Old Packs) fixed on Pack Export/Import
# "Modified by" value fixed.
# Required/Not Required fixed on Wysiwyg Editor (Box) & Plug-in Button.

! Performance Improvements

-------------------- 1.5.0.RC5 Upgrade Release [20-Jan-2010] ------------------

+ Joomla Plug-in (Content) Field Type added on Fields (Any Content Plug-in with Parameters!)
+ Batch Image Process Integration added on Media Manager
+ Watermark added on Upload Image Field Type
+ Update Title & Color added on Configuration/Operations

+ Hide Icon Edit parameter back on Configuration & process improved.

+ Remove value Icon added on Field X. (Available on Default Form Template)
^ Add value Link (on label) replaced by a Icon on Field X.
# FieldX compatibility improved (Folder/Upload Simple/Upload Image)

# Improvements & Some Bugs fixed on Pack Export/Import
# Captcha Field fixed on Form Edition
# Default Value added on Select Dynamic field
# Icon Edit redirection Fixed with SEF urls
# Readmore Field value fixed
# Subpanel Field fixed
# Template Manager fixed
# Upload Image fixed
# Where Clause Operator (null) added on Select Dynamic field

-------------------- 1.5.0.RC4-2 Upgrade Release [22-Dec-2009] ------------------

^ Site Form Creation/Edition Access updated on Form Action fields (new & existing fields).

+ "Published/Archived" parameter added on User's Articles List Layout (Menu Item Type).
+ "All Authors" parameter added on User's Articles List Layout (Menu Item Type).

+ Icon Edit link updated >> Redirect to suitable Content Type Site Form.
- Hide Icon Edit parameter removed on Configuration.

# Article edition fixed on Article/Category User's List (when Auto Redirection).
# "param.ini" file renamed to "params.ini" on Html Prepare (Template Generator).

-------------------- 1.5.0.RC4 Upgrade Release [14-Dec-2009] ------------------

^ jSeblod CCK Installer rewritten (part 2)

+ Amazing Joomfish Compatibility & Integration on Joomla Article Manager.
+ "Content & Form" Tab on Joomla Template Manager >> List jSeblod CCK templates.
+ "Save & New" + "Apply" buttons added on Content Manager

+ Prepare Html (Template) added on Content Type manager.
+ Php version check added on jSeblod CCK Administration (Control Panel).
+ Remove User's Content feature added on User Manager.
+ Remove User<->Article connection process added on Article Manager.

+ Advanced Content template updated.
+ Business Card template added.
+ Default Avatars added in User Profil template.

+ Joomla User's Content Submission Layout added on Menu Item Type

# Bad order after submission fixed on Article Manager & Category Manager.
# Some compatibility issues with components fixed into System Plug-in.
# Some compatibility issues with modules fixed into System Plug-in.
# CSS fixed in Portfolio template.
# Password input no more disabled on Add New User (Defaut View)
# PNG with alpha (transparency) fixed on Upload Image Field Type.
# Some issues fixed on Captcha Field Type.
# Some issues fixed on Site Form Module.
# Some improvements & issues fixed on Folder Field Type.
# Some subjects & messages added/updated on existing Email Fields
# "[sitename]","[siteurl]","[username]" values fixed on Email Field Type (Messages & Subjects)

-------------------- 1.5.0.RC3 Upgrade Release [21-Nov-2009] ------------------

Universal CCK for Joomla: Powerfull Templates, Content Types, Custom Fields (40 Field Types)
with Admin & Site Submission and/or Registration & Community Features.
Fully integrated to Joomla Content Manager, Category Manager, User Manager and all other components.
Suitable for Components & Modules using or not Plug-in Process.
Advanced Media Manager (soon), Export/Import using CCK Packs, Highly Integrated Content Manager,
Joomla Plug-in Button as Field, SubCategories (Catalog & List Layouts), True Preview,
Upload Image with Automatic Thumbs Creation, and much more...

^ jSeblod CCK Installer rewritten (part 1)

+ Joomla Categories Submission (admin/site) added
+ Joomla User Registration (admin/site) added
+ Joomla User's Content Submission (admin/site) added

+ SubCategories added (integrated to Joomla Category Manager)
+ Joomla Plug-in Buttons available as Content Field

+ "Content Edition Kit" renamed to "Content Manager"
+ Content Manager available as Box added
+ Content Manager available as Fullscreen added
+ Advanced Content Manager configuration added

+ True Preview added on Article Manager

+ Site Url Views added on Templates
+ Site Views view added from Content Templates >> List any Site Views
+ Multiple parameters + ColorPicker in Templates

+ "Advanced Content" template added.
+ "Default Content" template added.
^ "Default Submission" renamed to "Default Form" on Templates
+ "Simple Form" template added.

+ Content Fields creation/edition added on Content Types
+ Emails Fields assignment added on Content Types

+ Blog Content Type add.
+ Contact Content Type add.
+ User Profile Content Type add.

+ Alias Field Type added on Fields
+ Button Free Field Type added on Fields
+ Captcha Image Field Type added on Fields
+ External Article Field Type added on Fields
+ External Subcatagories Field Type added on Fields
+ Field X Field Type added on Fields >> Repeat X Time a Field, and add some "on the Fly" (Duplicate)
+ Folder Field Type added on Fields
+ Joomla Menu Field Type added on Fields
+ Joomla Plug-in Button Field Type added on Fields
+ Joomla User Field Type added on Fields
+ Password Field Type added on Fields
+ Save Field Type added on Fields
+ Select Numeric Field Type added on Fields
+ Upload Image Field Type added on Fields >> Automatic Thumbs Creation (Backgroud Color, Crop, Max Fit, Stretch)

+ Email Field Type improved >> SendEmail events, Include Fields’ value
^ "Image" Field Type renamed to "Media Field Type (Soon: Advanced Media Manager)
^ "Media" Field Type renamed to "File" Field Type
^ "Joomla Article" Field Type renamed to "Joomla Content" Field Type
^ Wysiwyg Editor improved >> Joomla Editor, on Form or on Box
# Multiples fix, improvements, or attributes added on any Field Types

+ User's Articles List Layout added on Menu Item Type >> List, Add, Publish, Unpublish, Delete
+ User's Categories List Layout added on Menu Item Type >> List, Add, Publish, Unpublish, Delete
+ Content Types List Layout added on Menu Item Type
+ Joomla Category Submission Layout added on Menu Item Type
+ Joomla User Registration Layout added on Menu Item Type
+ Users List Layout added on Menu Item Type >> List, Add, Publish, Unpublish, Delete
+ User's Form Layout added on Menu Item Type
+ User's Homepage Layout added on Menu Item Type (Soon)
+ User's Content List Layout added on Menu Item Type >> List, Add, Publish, Unpublish, Delete

+ Extended Toolbar admin module included
+ Extended Login site module included
+ Toggle CCK admin module included

-------------------- 1.2.5 Update Release [29-August-2009] ------------------

! New jSeblod CCK Club Subscription: Free Subscription
- Domain license check removed
# Joomla Article Fields edition fixed
# Errors if database prefix is not "jos_" fixed
# Automatic Content Type/Field creation fixed on Content Templates
# Other fixs

-------------------- 1.2.0 Upgrade Release [30-July-2009] ------------------

+ Site Forms module included
+ Joomla Module Field Type added on Content Fields
+ Joomla Readmore Field Type added on Content Fields
+ MediaBox added on Basic Site Display
+ Categories created in Tree during Content Pack import
+ Country List added into Database (#__cck_core_extra_country)
# Gold Calendar style ok

-------------------- 1.1.3 Update Release [23-July-2009] ------------------

+ Component Update process (source code)
+ How To? Documentation added
^ "Content Templates" renamed to "jSeblod Templates"
# Content Fields in correct order in Basic Site Display (defaut mode without Content Templates)

-------------------- 1.1.2 Update Release [14-July-2009] ------------------

+ Extended Admin Menu module included
^ "Content Items" renamed to "Content Fields"

-------------------- 1.1.1 Update Release [12-July-2009] ------------------

+ Plug-in process for Content Modules added

-------------------- 1.1.0 Upgrade Release [02-July-2009] ------------------

+ E-mail Field Type added on Content Fields
+ Content Pack added >> Import & Export of Content Pack (Content Types, Content Field(s), Content Template(s))
^ "Content Interface" renamed to "jSeblod CEK (Content Edition Kit)"

-------------------- 1.0.1 Update Release [22-June-2009] ------------------

+ Menu Item Views added on Content Templates

-------------------- 1.0.0 Initial Release [12-June-2009] ------------------

+ Initial Release
