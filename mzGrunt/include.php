<?php
#注册插件
RegisterPlugin("mzGrunt", "ActivePlugin_mzGrunt");

function ActivePlugin_mzGrunt()
{
  Add_Filter_Plugin('Filter_Plugin_Index_End', 'mzGrunt_Debug');
  Add_Filter_Plugin('Filter_Plugin_Zbp_BuildTemplate', 'mzGrunt_GenTpl');
}

function mzGrunt_Debug()
{
  global $zbp;
  $dev = GetVars("dev", "GET");
  if ($dev !== null) {
    // 废弃 2023-09-13
    // mzGrunt_CheckLiveReload(1);
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
  $templates['footer'] = str_replace('script.min.js', 'script.js', $templates['footer']);
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
