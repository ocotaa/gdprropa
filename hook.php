<?php
/*
 -------------------------------------------------------------------------
 GDPR Records of Processing Activities plugin for GLPI
 Copyright (C) 2020 by Yild.

 https://github.com/yild/gdprropa
 -------------------------------------------------------------------------

 LICENSE

 This file is part of GDPR Records of Processing Activities.

 GDPR Records of Processing Activities is free software; you can
 redistribute it and/or modify it under the terms of the
 GNU General Public License as published by the Free Software
 Foundation; either version 3 of the License, or (at your option)
 any later version.

 GDPR Records of Processing Activities is distributed in the hope that
 it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 See the GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with GDPR Records of Processing Activities.
 If not, see <http://www.gnu.org/licenses/>.

 Based on DPO Register plugin, by Karhel Tmarr.

 --------------------------------------------------------------------------

  @package   gdprropa
  @author    Yild
  @copyright Copyright (c) 2020 by Yild
  @license   GPLv3+
             http://www.gnu.org/licenses/gpl.txt
  @link      https://github.com/yild/gdprropa
  @since     2020
 --------------------------------------------------------------------------
 */

function plugin_gdprropa_install() {

   global $DB;

   $install = false;
   if (!$DB->tableExists('glpi_plugin_gdprropa_records')) {
      $install = true;
   }

   if ($install) {

      if ($DB->tableExists('glpi_states')){

         $query = "SELECT * FROM `glpi_states` WHERE `glpi_states`.`level` = 1 AND `glpi_states`.`name` = 'Traitement RGPD' AND `glpi_states`.`comment` = 'Créé via plugin GDPRRoPA';";
         $result = $DB->queryOrDie($query, $DB->error());

         if(!empty($result)){

            $parent = [
               'name' => 'Traitement RGPD',
               'comment' => 'Créé via plugin GDPRRoPA',
               'states_id' => 0,
               'completename' => 'Traitement RGPD',
               'level' => 1,
               'ancestors_cache' => '[]',
               'is_visible_computer' => 0,
               'is_visible_monitor' => 0,
               'is_visible_networkequipment' => 0,
               'is_visible_peripheral' => 0,
               'is_visible_phone' => 0,
               'is_visible_printer' => 0,
               'is_visible_softwareversion' => 0,
               'is_visible_softwarelicense' => 0,
               'is_visible_line' => 0,
               'is_visible_certificate' => 0,
               'is_visible_rack' => 0,
               'is_visible_passivedcequipment' => 0,
               'is_visible_enclosure' => 0,
               'is_visible_pdu' => 0,
               'is_visible_cluster' => 0,
               'is_visible_contract' => 0,
               'is_visible_appliance' => 0,
               'is_visible_databaseinstance' => 0,
               'is_visible_cable' => 0,
               'date_creation' => date("Y-m-d H:i:s"),
               'date_mod' => date("Y-m-d H:i:s"),
            ];
            $DB->insertOrDie('glpi_states', $parent, $DB->error());

            $parent_id = $DB->insertId();


            //Not mandatory part (If there is an error you can delete this part) ---
            $i = 0;
            $sons = [];
            //---

            $childnames = [
               'Nouveau',
               'En cours de traitement',
               'Non-conformité mineure',
               'Non-conformité majeure',
               'Conforme',
               'Retiré',
            ];

            foreach($childnames as $childname){
               $childs = [
                  'name' => $childname,
                  'comment' => 'Créé via plugin GDPRRoPA',
                  'states_id' => $parent_id,
                  'completename' => 'Traitement RGPD > '. $childname,
                  'level' => 2,
                  'ancestors_cache' => $parent_id,
                  'is_visible_computer' => 0,
                  'is_visible_monitor' => 0,
                  'is_visible_networkequipment' => 0,
                  'is_visible_peripheral' => 0,
                  'is_visible_phone' => 0,
                  'is_visible_printer' => 0,
                  'is_visible_softwareversion' => 0,
                  'is_visible_softwarelicense' => 0,
                  'is_visible_line' => 0,
                  'is_visible_certificate' => 0,
                  'is_visible_rack' => 0,
                  'is_visible_passivedcequipment' => 0,
                  'is_visible_enclosure' => 0,
                  'is_visible_pdu' => 0,
                  'is_visible_cluster' => 0,
                  'is_visible_contract' => 0,
                  'is_visible_appliance' => 0,
                  'is_visible_databaseinstance' => 0,
                  'is_visible_cable' => 0,
                  'date_creation' => date("Y-m-d H:i:s"),
                  'date_mod' => date("Y-m-d H:i:s"),
               ];
               $DB->insertOrDie('glpi_states', $childs, $DB->error(), );

               //Not mandatory part (If there is an error you can delete this part) ---
               $sons[$i] = $DB->insertId();
               $DB->queryOrDie("UPDATE `glpi_states` SET `glpi_states`.`sons_cache` = '{\"$sons[$i]\":$sons[$i]}' WHERE `glpi_states`.`id` = $sons[$i];", $DB->error());
               $i++;
               //---
            }

            //Not mandatory part (If there is an error you can delete this part) ---
            $parent_sons = "{\"$parent_id\":$parent_id";
            foreach ($sons as $son){
               $parent_sons .= ",\"$son\":$son";
            }
            $parent_sons .= "}";
            $DB->queryOrDie("UPDATE `glpi_states` SET `glpi_states`.`sons_cache` = '$parent_sons' WHERE `glpi_states`.`id` = $parent_id ;", $DB->error());
            //---
         }
      }

      if (!$DB->tableExists('glpi_plugin_gdprropa_configs')) {
         $query = "CREATE TABLE `glpi_plugin_gdprropa_configs` (
                     `id` int(11) NOT NULL auto_increment,
                     `entities_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_entities (id)',
                     `config` TEXT NOT NULL default '{}',

                     `date_creation` timestamp,
                     `users_id_creator` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',
                     `date_mod` timestamp,
                     `users_id_lastupdater` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',

                     PRIMARY KEY  (`id`)
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());
      }

      if (!$DB->tableExists('glpi_plugin_gdprropa_controllerinfos')) {
         $query = "CREATE TABLE `glpi_plugin_gdprropa_controllerinfos` (
                     `id` int(11) NOT NULL auto_increment,
                     `entities_id` int(11) COMMENT 'RELATION to glpi_entities (id)',
                     `is_recursive` tinyint(1) NOT NULL default '1',
                     `users_id_representative` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',
                     `users_id_dpo` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',
                     `contracttypes_id_jointcontroller` int(11) default NULL COMMENT 'RELATION to glpi_contracttypes (id)',
                     `contracttypes_id_processor` int(11) default NULL COMMENT 'RELATION to glpi_contracttypes (id)',
                     `contracttypes_id_thirdparty` int(11) default NULL COMMENT 'RELATION to glpi_contracttypes (id)',
                     `contracttypes_id_internal` int(11) default NULL COMMENT 'RELATION to glpi_contracttypes (id)',
                     `contracttypes_id_other` int(11) default NULL COMMENT 'RELATION to glpi_contracttypes (id)',
                     `controllername` varchar(250) default NULL,
                     `date_creation` timestamp,
                     `users_id_creator` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',
                     `date_mod` timestamp,
                     `users_id_lastupdater` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',
                     PRIMARY KEY  (`id`),
                     UNIQUE `entities_id` (`entities_id`)
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());

      }

      if (!$DB->tableExists('glpi_plugin_gdprropa_datasubjectscategories')) {
         $query = "CREATE TABLE `glpi_plugin_gdprropa_datasubjectscategories` (
                     `id` int(11) NOT NULL auto_increment,
                     `name` varchar(255) collate utf8_unicode_ci default NULL,
                     `comment` text collate utf8_unicode_ci,
                     `entities_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_entities (id)',
                     `is_recursive` tinyint(1) NOT NULL default '1',

                     `date_creation` timestamp,
                     `users_id_creator` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',
                     `date_mod` timestamp,
                     `users_id_lastupdater` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',

                     PRIMARY KEY  (`id`),
                     KEY `name` (`name`),
                     UNIQUE `un_per_record` (`name`, `entities_id`)
                  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());

         $dp = new DisplayPreference();
         $dp->add([
            'itemtype' => 'PluginGdprropaDataSubjectsCategory',
            'num' => 3,
            'rank' => 1,
            'users_id' => 0
         ]);
      }

      if (!$DB->tableExists('glpi_plugin_gdprropa_legalbasisacts')) {
         $query = "CREATE TABLE `glpi_plugin_gdprropa_legalbasisacts` (
                     `id` int(11) NOT NULL auto_increment,
                     `name` varchar(255) collate utf8_unicode_ci default NULL,
                     `type` tinyint(1) NOT NULL default '0',
                     `description` varchar(1024) collate utf8_unicode_ci default NULL,
                     `comment` text collate utf8_unicode_ci,
                     `injected` tinyint(1) NOT NULL default '0' COMMENT 'is record injected ad plugin install',
                     `entities_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_entities (id)',
                     `is_recursive` tinyint(1) NOT NULL default '1',
                     `date_creation` timestamp,
                     `users_id_creator` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',
                     `date_mod` timestamp,
                     `users_id_lastupdater` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',

                     PRIMARY KEY  (`id`),
                     KEY `name` (`name`),
                     UNIQUE `un_per_record` (`name`, `type`, `entities_id`)
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());

         $dp = new DisplayPreference();
         $dp->add([
            'itemtype' => 'PluginGdprropaLegalBasisAct',
            'num' => 3,
            'rank' => 1,
            'users_id' => 0
         ]);
         $dp->add([
            'itemtype' => 'PluginGdprropaLegalBasisAct',
            'num' => 4,
            'rank' => 2,
            'users_id' => 0
         ]);
      }

      if (!$DB->tableExists('glpi_plugin_gdprropa_datavisibilities')) {
         $query = "CREATE TABLE `glpi_plugin_gdprropa_datavisibilities` (
                     `id` int(11) NOT NULL auto_increment,
                     `name` varchar(255) collate utf8_unicode_ci default NULL,
                     `firstname` varchar(255) collate utf8_unicode_ci default NULL,
                     `type` tinyint(1) NOT NULL default '0',
                     `accessed_data` varchar(1024) collate utf8_unicode_ci default NULL,
                     `comment` text collate utf8_unicode_ci,
                     `entities_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_entities (id)',
                     `is_recursive` tinyint(1) NOT NULL default '1',
                     `date_creation` timestamp,
                     `users_id_creator` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',
                     `date_mod` timestamp,
                     `users_id_lastupdater` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',

                     PRIMARY KEY  (`id`),
                     KEY `name` (`name`),
                     UNIQUE `un_per_record` (`name`, `type`, `entities_id`)
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());

         $dp = new DisplayPreference();
         $dp->add([
            'itemtype' => 'PluginGdprropaDataVisibility',
            'num' => 3,
            'rank' => 1,
            'users_id' => 0
         ]);
         $dp->add([
            'itemtype' => 'PluginGdprropaDataVisibility',
            'num' => 4,
            'rank' => 2,
            'users_id' => 0
         ]);
      }
      
      if (!$DB->tableExists('glpi_plugin_gdprropa_personaldatacategories')) {
         $query = "CREATE TABLE `glpi_plugin_gdprropa_personaldatacategories` (
                     `id` int(11) NOT NULL auto_increment,
                     `name` varchar(255) collate utf8_unicode_ci default NULL,
                     `completename` text collate utf8_unicode_ci default NULL,
                     `level` int(11) NOT NULL default '0',
                     `comment` text collate utf8_unicode_ci,
                     `plugin_gdprropa_personaldatacategories_id` int(11) NOT NULL default '0',
                     `entities_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_entities (id)',
                     `is_recursive` tinyint(1) NOT NULL default '1',
                     `is_special_category` tinyint(1) default '0',

                     `date_creation` timestamp,
                     `users_id_creator` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',
                     `date_mod` timestamp,
                     `users_id_lastupdater` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',

                     PRIMARY KEY  (`id`),
                     KEY `name` (`name`),
                     UNIQUE `un_per_record` (`name`, `entities_id`)
                  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());

         $dp = new DisplayPreference();
         $dp->add([
            'itemtype' => 'PluginGdprropaPersonalDataCategory',
            'num' => 3,
            'rank' => 1,
            'users_id' => 0
         ]);
         $dp->add([
            'itemtype' => 'PluginGdprropaPersonalDataCategory',
            'num' => 4,
            'rank' => 2,
            'users_id' => 0
         ]);
      }

      if (!$DB->tableExists('glpi_plugin_gdprropa_securitymeasures')) {
         $query = "CREATE TABLE `glpi_plugin_gdprropa_securitymeasures` (
                     `id` int(11) NOT NULL auto_increment,
                     `name` varchar(255) collate utf8_unicode_ci default NULL,
                     `type` tinyint(1) NOT NULL default '0',
                     `description` varchar(1000) collate utf8_unicode_ci default NULL,
                     `comment` text collate utf8_unicode_ci,
                     `entities_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_entities (id)',
                     `is_recursive` tinyint(1) NOT NULL default '1',

                     `date_creation` timestamp,
                     `users_id_creator` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',
                     `date_mod` timestamp,
                     `users_id_lastupdater` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',

                     PRIMARY KEY  (`id`),
                     KEY `name` (`name`),
                     UNIQUE `un_per_record` (`name`, `type`, `entities_id`)
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());

         $dp = new DisplayPreference();
         $dp->add([
            'itemtype' => 'PluginGdprropaSecurityMeasure',
            'num' => 3,
            'rank' => 1,
            'users_id' => 0
         ]);
         $dp->add([
            'itemtype' => 'PluginGdprropaSecurityMeasure',
            'num' => 4,
            'rank' => 2,
            'users_id' => 0
         ]);
      }

      if (!$DB->tableExists('glpi_plugin_gdprropa_records')) {
         $query = "CREATE TABLE `glpi_plugin_gdprropa_records` (
                     `id` int(11) NOT NULL auto_increment,
                     `entities_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_entities (id)',
                     `is_recursive` tinyint(1) NOT NULL default '1',
                     `is_deleted` tinyint(1) NOT NULL default '0',
                     `name` varchar(255) collate utf8_unicode_ci default NULL,
                     `content` varchar(1000) collate utf8_unicode_ci default NULL,
                     `additional_info` varchar(1000) collate utf8_unicode_ci default NULL,
                     `states_id` int(11) NOT NULL default '1' COMMENT 'RELATION to glpi_states (id)',
                     `storage_medium` int(11) NOT NULL default '0'  COMMENT 'Default status to UNDEFINED',
                     `pia_required` tinyint(1) NOT NULL default '0',
                     `pia_status` int(11) NOT NULL default '0' COMMENT 'Default status to UNDEFINED',
                     `first_entry_date` timestamp,
                     `consent_required` tinyint(1) NOT NULL default '0',
                     `consent_storage` varchar(1000) collate utf8_unicode_ci default NULL,

                     `date_creation` timestamp,
                     `users_id_creator` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',
                     `date_mod` timestamp,
                     `users_id_lastupdater` int(11) default NULL COMMENT 'RELATION to glpi_users (id)',

                     PRIMARY KEY  (`id`),
                     KEY `name` (`name`),
                     UNIQUE `un_per_record` (`name`, `entities_id`)
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());

         $dp = new DisplayPreference();
         $dp->add([
            'itemtype' => 'PluginGdprropaRecord',
            'num' => 2,
            'rank' => 1,
            'users_id' => 0
         ]);
         $dp->add([
            'itemtype' => 'PluginGdprropaRecord',
            'num' => 5,
            'rank' => 2,
            'users_id' => 0
         ]);
         $dp->add([
            'itemtype' => 'PluginGdprropaRecord',
            'num' => 3,
            'rank' => 3,
            'users_id' => 0
         ]);
         $dp->add([
            'itemtype' => 'PluginGdprropaRecord',
            'num' => 6,
            'rank' => 4,
            'users_id' => 0
         ]);
         $dp->add([
            'itemtype' => 'PluginGdprropaRecord',
            'num' => 9,
            'rank' => 5,
            'users_id' => 0
         ]);
         $dp->add([
            'itemtype' => 'PluginGdprropaRecord',
            'num' => 10,
            'rank' => 6,
            'users_id' => 0
         ]);
         $dp->add([
            'itemtype' => 'PluginGdprropaRecord',
            'num' => 12,
            'rank' => 7,
            'users_id' => 0
         ]);
      }

      if (!$DB->tableExists('glpi_plugin_gdprropa_records_contracts')) {
         $query = "CREATE TABLE `glpi_plugin_gdprropa_records_contracts` (
                     `id` int(11) NOT NULL auto_increment,
                     `plugin_gdprropa_records_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugins_gdprropa_records (id)',
                     `contracts_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_contracts (id)',

                     PRIMARY KEY  (`id`),
                     UNIQUE `un_per_record` (`plugin_gdprropa_records_id`, `contracts_id`)
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());
      }
      
      if (!$DB->tableExists('glpi_plugin_gdprropa_records_datasubjectscategories')) {
         $query = "CREATE TABLE `glpi_plugin_gdprropa_records_datasubjectscategories` (
                     `id` int(11) NOT NULL auto_increment,
                     `plugin_gdprropa_records_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugins_gdprropa_records (id)',
                     `plugin_gdprropa_datasubjectscategories_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugins_gdprropa_datasubjectscategories (id)',

                     PRIMARY KEY  (`id`),
                     UNIQUE `un_per_record` (`plugin_gdprropa_records_id`, `plugin_gdprropa_datasubjectscategories_id`)
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());
      }

      if (!$DB->tableExists('glpi_plugin_gdprropa_records_legalbasisacts')) {
         $query = "CREATE TABLE `glpi_plugin_gdprropa_records_legalbasisacts` (
                     `id` int(11) NOT NULL auto_increment,
                     `plugin_gdprropa_records_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugins_gdprropa_records (id)',
                     `plugin_gdprropa_legalbasisacts_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugins_gdprropa_legalbasisacts (id)',

                     PRIMARY KEY  (`id`),
                     UNIQUE `un_per_record` (`plugin_gdprropa_records_id`, `plugin_gdprropa_legalbasisacts_id`)
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());
      }

      if (!$DB->tableExists('glpi_plugin_gdprropa_records_datavisibilities')) {
         $query = "CREATE TABLE `glpi_plugin_gdprropa_records_datavisibilities` (
                     `id` int(11) NOT NULL auto_increment,
                     `plugin_gdprropa_records_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugins_gdprropa_records (id)',
                     `plugin_gdprropa_datavisibilities_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugins_gdprropa_datavisibilities (id)',

                     PRIMARY KEY  (`id`),
                     UNIQUE `un_per_record` (`plugin_gdprropa_records_id`, `plugin_gdprropa_datavisibilities_id`)
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());
      }

      if (!$DB->tableExists('glpi_plugin_gdprropa_records_personaldatacategories')) {
         $query = "CREATE TABLE `glpi_plugin_gdprropa_records_personaldatacategories` (
                     `id` int(11) NOT NULL auto_increment,
                     `plugin_gdprropa_records_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugins_gdprropa_records (id)',
                     `plugin_gdprropa_personaldatacategories_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugin_gdprropa_personaldatacategories (id)',

                     PRIMARY KEY  (`id`)
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());
      }

      if (!$DB->tableExists('glpi_plugin_gdprropa_records_retentions')) {
         $query = "CREATE TABLE `glpi_plugin_gdprropa_records_retentions` (
                     `id` int(11) NOT NULL auto_increment,
                     `plugin_gdprropa_records_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugins_gdprropa_records (id)',
                     `type` int(11) NOT NULL default '0'  COMMENT 'Default status to UNDEFINED',
                     `contracts_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_contracts (id)',
                     `contract_until_is_valid` tinyint(1) NOT NULL default '0',
                     `contract_after_end_of` tinyint(1) NOT NULL default '0',
                     `contract_retention_value` int(11) NOT NULL default '0',
                     `contract_retention_scale` char(1) NOT NULL default 'y',
                     `plugin_gdprropa_legalbasisacts_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugins_gdprropa_legalbasisacts (id)',
                     `additional_info`  varchar(1000) collate utf8_unicode_ci default NULL,

                     PRIMARY KEY  (`id`),
                     UNIQUE `un_per_record` (`plugin_gdprropa_records_id`)
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());
      }

      if (!$DB->tableExists('glpi_plugin_gdprropa_records_securitymeasures')) {
         $query = "CREATE TABLE `glpi_plugin_gdprropa_records_securitymeasures` (
                     `id` int(11) NOT NULL auto_increment,
                     `plugin_gdprropa_records_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugins_gdprropa_records (id)',
                     `plugin_gdprropa_securitymeasures_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugin_gdprropa_securitymeasures (id)',

                     PRIMARY KEY  (`id`),
                     UNIQUE `un_per_record` (`plugin_gdprropa_records_id`, `plugin_gdprropa_securitymeasures_id`)
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());
      }

      if (!$DB->tableExists('glpi_plugin_gdprropa_records_softwares')) {
         $query = "CREATE TABLE `glpi_plugin_gdprropa_records_softwares` (
                     `id` int(11) NOT NULL auto_increment,
                     `plugin_gdprropa_records_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_plugins_gdprropa_records (id)',
                     `softwares_id` int(11) NOT NULL default '0' COMMENT 'RELATION to glpi_softwares (id)',

                     PRIMARY KEY  (`id`),
                     UNIQUE `un_per_record` (`plugin_gdprropa_records_id`, `softwares_id`)
                   ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
         $DB->queryOrDie($query, $DB->error());
      }

   }

   PluginGdprropaProfile::initProfile();
   PluginGdprropaProfile::createFirstAccess($_SESSION['glpiactiveprofile']['id']);

   return true;
}

function plugin_gdprropa_uninstall() {

   global $DB;

   $tables = [
      'glpi_plugin_gdprropa_configs',
      'glpi_plugin_gdprropa_controllerinfos',
      'glpi_plugin_gdprropa_datasubjectscategories',
      'glpi_plugin_gdprropa_legalbasisacts',
      'glpi_plugin_gdprropa_datavisibilities',
      'glpi_plugin_gdprropa_personaldatacategories',
      'glpi_plugin_gdprropa_securitymeasures',
      'glpi_plugin_gdprropa_records',
      'glpi_plugin_gdprropa_records_contracts',
      'glpi_plugin_gdprropa_records_retentions',
      'glpi_plugin_gdprropa_records_datasubjectscategories',
      'glpi_plugin_gdprropa_records_legalbasisacts',
      'glpi_plugin_gdprropa_records_datavisibilities',
      'glpi_plugin_gdprropa_records_personaldatacategories',
      'glpi_plugin_gdprropa_records_securitymeasures',
      'glpi_plugin_gdprropa_records_softwares',
   ];

   foreach ($tables as $table) {
      $DB->query("DROP TABLE IF EXISTS `$table`;");
   }


   $contracttypes = [
      'Contrat de contrôleur commun',
      'Contrat du processeur',
      'Contrat avec un tiers',
      'Contrat interne',
      'Autre contrat',
   ];

   foreach ($contracttypes as $contracttype) {
      $DB->queryOrDie("DELETE FROM `glpi_contracttypes` WHERE `glpi_contracttypes`.`comment` LIKE '%(Créé via plugin GDPRRoPA)%' AND `glpi_contracttypes`.`name` = '$contracttype';");
   }

   $statecnames = [
      'Traitement RGPD > Nouveau',
      'Traitement RGPD > En cours de traitement',
      'Traitement RGPD > Non-conformité mineure',
      'Traitement RGPD > Non-conformité majeure',
      'Traitement RGPD > Conforme',
      'Traitement RGPD > Retiré',
      'Traitement RGPD',
   ];

   foreach ($statecnames as $statecname) {
      $DB->queryOrDie("DELETE FROM `glpi_states` WHERE `glpi_states`.`comment` LIKE 'Créé via plugin GDPRRoPA' AND `glpi_states`.`completename` = '$statecname';", $DB->error());
   }


   $query = "DELETE FROM `glpi_logs`
               WHERE
                     `itemtype` LIKE 'PluginGdprropa%'
                  OR `itemtype_link` LIKE 'PluginGdprropa%'";
   $DB->queryOrDie($query, $DB->error());

   $dp = new DisplayPreference();
   $dp->deleteByCriteria(['itemtype' => [
      'PluginGdprropaRecord',
      'PluginGdprropaLegalBasisAct',
      'PluginGdprropaDataVisibility',
      'PluginGdprropaSecurityMeasure',
      'PluginGdprropaDataSubjectsCategory',
      'PluginGdprropaPersonalDataCategory'
      ]
   ]);

   $profileRight = new ProfileRight();
   foreach (PluginGdprropaProfile::getAllRights() as $right) {
      $profileRight->deleteByCriteria(['name' => $right['field']]);
   }

   PluginGdprropaMenu::removeRightsFromSession();
   PluginGdprropaProfile::removeRightsFromSession();

   return true;
}

function plugin_gdprropa_getDropdown() {

   return [
      PluginGdprropaLegalBasisAct::class => PluginGdprropaLegalBasisAct::getTypeName(2),
      PluginGdprropaDataVisibility::class => PluginGdprropaDataVisibility::getTypeName(2),
      PluginGdprropaSecurityMeasure::class => PluginGdprropaSecurityMeasure::getTypeName(2),
      PluginGdprropaDataSubjectsCategory::class => PluginGdprropaDataSubjectsCategory::getTypeName(2),
      PluginGdprropaPersonalDataCategory::class => PluginGdprropaPersonalDataCategory::getTypeName(2),
   ];
}

function plugin_gdprropa_getAddSearchOptions($itemtype) {

   $options = [];

   if ($itemtype == 'Entity') {
      $options = PluginGdprropaControllerInfo::getSearchOptionsControllerInfo();
   }

   return $options;
}

function plugin_gdprropa_getDatabaseRelations() {

   $plugin = new Plugin();

   if ($plugin->isActivated('gdprropa')) {

      return [
         'glpi_entites' => [
            'glpi_plugin_gdprropa_configs' => 'entities_id',
            'glpi_plugin_gdprropa_records' => 'entities_id',
            'glpi_plugin_gdprropa_controllerinfos' => 'entities_id',
            'glpi_plugin_gdprropa_datasubjectscategories' => 'entities_id',
            'glpi_plugin_gdprropa_legalbasisacts' => 'entities_id',
            'glpi_plugin_gdprropa_datavisibilities' => 'entities_id',
            'glpi_plugin_gdprropa_personaldatacategories' => 'entities_id',
            'glpi_plugin_gdprropa_securitymeasures' => 'entities_id',
            ],

         'glpi_users' => [
            'glpi_plugin_gdprropa_controllerinfos' => [
               'users_id_representative',
               'users_id_dpo',
            ],
            'glpi_plugin_gdprropa_configs' => [
               'users_id_creator',
               'users_id_lastupdater',
            ],
            'glpi_plugin_gdprropa_datasubjectscategories' => [
               'users_id_creator',
               'users_id_lastupdater',
            ],
            'glpi_plugin_gdprropa_legalbasisacts' => [
               'users_id_creator',
               'users_id_lastupdater',
            ],
            'glpi_plugin_gdprropa_datavisibilities' => [
               'users_id_creator',
               'users_id_lastupdater',
            ],
            'glpi_plugin_gdprropa_personaldatacategories' => [
               'users_id_creator',
               'users_id_lastupdater',
            ],
            'glpi_plugin_gdprropa_records' => [
               'users_id_creator',
               'users_id_lastupdater',
               'users_id_owner',
            ],
            'glpi_plugin_gdprropa_securitymeasures' => [
               'users_id_creator',
               'users_id_lastupdater',
            ],
         ],

         'glpi_contracts' => [
            'glpi_plugin_gdprropa_records_contracts' => 'contracts_id',
            'glpi_plugin_gdprropa_controllerinfos' => [
               'contracttypes_id_jointcontroller',
               'contracttypes_id_processor',
               'contracttypes_id_thirdparty',
               'contracttypes_id_internal',
               'contracttypes_id_other',
            ],
            'glpi_plugin_gdprropa_records_retentions' => 'contracts_id',
         ],
         'glpi_plugin_gdprropa_records' => [
            'glpi_plugin_gdprropa_records_contracts' => 'plugin_gdprropa_records_id',
            'glpi_plugin_gdprropa_records_datasubjectscategories' => 'plugin_gdprropa_records_id',
            'glpi_plugin_gdprropa_records_legalbasisacts' => 'plugin_gdprropa_records_id',
            'glpi_plugin_gdprropa_records_datavisibilities' => 'plugin_gdprropa_records_id',
            'glpi_plugin_gdprropa_records_personaldatacategories' => 'plugin_gdprropa_records_id',
            'glpi_plugin_gdprropa_records_retentions' => 'plugin_gdprropa_records_id',
            'glpi_plugin_gdprropa_records_securitymeasures' => 'plugin_gdprropa_records_id',
            'glpi_plugin_gdprropa_records_softwares' => 'plugin_gdprropa_records_id',
         ],

         'glpi_plugin_gdprropa_datasubjectscategories' => [
            'glpi_plugin_gdprropa_records_datasubjectscategories' => 'plugin_gdprropa_datasubjectscategories_id',
         ],

         'glpi_plugin_gdprropa_legalbasisacts' => [
            'glpi_plugin_gdprropa_records_legalbasisacts' => 'plugin_gdprropa_legalbasisacts_id',
            'glpi_plugin_gdprropa_records_retentions' => 'plugin_gdprropa_legalbasisacts_id',
         ],

         'glpi_plugin_gdprropa_datavisibilities' => [
            'glpi_plugin_gdprropa_records_datavisibilities' => 'plugin_gdprropa_datavisibilities_id',
            'glpi_plugin_gdprropa_records_retentions' => 'plugin_gdprropa_datavisibilities_id',
         ],

         'glpi_plugin_gdprropa_personaldatacategories' => [
            'glpi_plugin_gdprropa_records_personaldatacategories' => 'plugin_gdprropa_personaldatacategories_id',
         ],

         'glpi_plugin_gdprropa_securitymeasures' => [
            'glpi_plugin_gdprropa_records_securitymeasures' => 'plugin_gdprropa_securitymeasures_id',
         ],

         'glpi_softwares' => [
            'glpi_plugin_gdprropa_records_softwares' => 'softwares_id',
         ],

      ];

   }

   return [];
}

function plugin_gdprropa_postinit() {

   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['item_purge']['gdprropa'] = [];
   $PLUGIN_HOOKS['item_purge']['gdprropa']['Contract'] = ['PluginGdprropaRecord_Contract', 'cleanForItem'];
   $PLUGIN_HOOKS['item_purge']['gdprropa']['Software'] = ['PluginGdprropaRecord_Software', 'cleanForItem'];

}
