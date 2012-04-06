<?php if (!defined('APPLICATION')) exit();

$PluginInfo['ErrorCatcher'] = array(
   'Name' => 'Error Catcher',
   'Description' => 'This plugin will catch all PHP errors and sends them to email',
   'Version' => '1.0',
   'Author' => "Ton Sharp",
   'AuthorEmail' => 'Forma-PRO@66ton99.org.ua',
   'AuthorUrl' => 'http://66ton99.org.ua',
   'PluginUrl' => 'https://github.com/66Ton99/Vanilla-ErrorCatcher-Plugin#readme',
   'MobileFriendly' => TRUE,
   'RequiredApplications' => FALSE,
   'RequiredTheme' => FALSE,
   'RequiredPlugins' => FALSE,
//    'SettingsUrl' => '/dashboard/settings/errorcatcher',
   'SettingsPermission' => 'Garden.Settings.Manage',
);
