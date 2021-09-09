<?php
#注册插件
RegisterPlugin("mzGrunt", "ActivePlugin_mzGrunt");

function ActivePlugin_mzGrunt()
{
  Add_Filter_Plugin('Filter_Plugin_Index_End', 'mzGrunt_Debug');
  Add_Filter_Plugin('Filter_Plugin_Zbp_BuildTemplate', 'mzGrunt_GenTpl');
  // Add_Filter_Plugin('Filter_Plugin_Admin_Footer', 'mzGrunt_AdminFooter');
}
function mzGrunt_AdminFooter()
{
  global $zbp;
  if (stripos($zbp->currenturl, "AppCentre") !== false) {
    return;
  }
  echo mzGrunt_ReloadJS();
}
function mzGrunt_Debug()
{
  global $zbp;
  $dev = GetVars("dev", "GET");
  if ($dev !== null) {
    mzGrunt_CheckLiveReload(1);
    $zbp->BuildTemplate();
    if ($zbp->CheckPlugin('FileIgnore')) {
      FileIgnore_BuildModule();
    }
    Redirect($zbp->fullcurrenturl);
  }
}
function mzGrunt_GenTpl(&$templates)
{
  global $zbp;
  if (!$zbp->option['ZC_DEBUG_MODE']) {
    return;
  }
  $templates['header'] = str_replace('{$style}.css', '{$style}.css?x=' . time(), $templates['header']);
  $liveReload = mzGrunt_ReloadJS();
  $templates['footer'] = str_replace('{$footer}', '{$footer}' . $liveReload, $templates['footer']);
}
function mzGrunt_ReloadJS()
{
  global $zbp;
  $reloadType = $zbp->Config('mzGrunt')->reloadType;
  $script = "\n<!--↓livereload↓-->\n<script>console.log($(window).width());</script>\n";
  switch ($reloadType) {
    case '1':
      $script .= '<script src="' . mzGrunt_Path("script/livereload.js", "host") . '"></script>';
      break;
    case '2':
      $script .= '<script src="http://127.0.0.1:35729/livereload.js?snipver=1"></script>';
      break;
    default:
      $script .= '<script src="' . mzGrunt_CheckLiveReload() . '"></script>';;
      break;
  }
  return $script . "\n<!--↑livereload {$reloadType}↑-->\n";
}
function mzGrunt_CheckLiveReload($force = false)
{
  global $zbp;
  $liveReload = $zbp->Config('mzGrunt')->liveReload;
  $lstTime = $zbp->Config('mzGrunt')->lstTime;
  $curTime = ceil(time() / 300);
  if ($curTime == $lstTime && !$force) {
    return $liveReload;
  }
  $liveReload = mzGrunt_Path("script/livereload.js", "host");
  $urlList = array("http://127.0.0.1:3000/browser-sync/browser-sync-client.js?v=2.18.11", "http://127.0.0.1:35729/livereload.js?snipver=1");
  foreach ($urlList as $url) {
    if (mzGrunt_Network($url)) {
      $liveReload = $url;
      break;
    }
  }
  $zbp->Config('mzGrunt')->liveReload = $liveReload;
  $zbp->Config('mzGrunt')->lstTime = $curTime;
  $zbp->SaveConfig('mzGrunt');
  $zbp->BuildTemplate();
  return $liveReload;
}
function mzGrunt_Network($url)
{
  $http = Network::Create();
  $http->open('GET', $url);
  // $http->setTimeOuts(10, 10, 0, 0);
  $http->send();
  if ($http->status == 200) {
    return true;
  }
  return false;
}
function mzGrunt_Path($file, $t = 'path')
{
  global $zbp;
  $result = $zbp->$t . 'zb_users/plugin/mzGrunt/';
  switch ($file) {
    case 'v-grunt':
      return $result . 'var/Gruntfile.js';
      break;
    case 'v-json':
      return $result . 'var/package.json';
      break;
    case 'console':
      return $result . 'var_cfg/console.txt';
      break;
    case 'cfg':
      return $result . 'var_cfg/';
      break;
    case 'zblogphp.js':
      return "{$zbp->path}zb_system/script/zblogphp.js";
      break;
    case 'zblogphp.orig.js':
      return "{$zbp->path}zb_system/script/zblogphp.orig.js";
      break;
    case 'usr':
      return $result . 'usr/';
      break;
    case 'var':
      return $result . 'var/';
      break;
    case 'main':
      return $result . 'main.php';
      break;
    default:
      return $result . $file;
  }
}
function InstallPlugin_mzGrunt()
{
}
function UninstallPlugin_mzGrunt()
{
}
