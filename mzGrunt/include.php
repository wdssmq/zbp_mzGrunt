<?php
 #注册插件
RegisterPlugin("mzGrunt", "ActivePlugin_mzGrunt");

function ActivePlugin_mzGrunt()
{
  Add_Filter_Plugin('Filter_Plugin_Zbp_BuildTemplate', 'mzGrunt_Copy');
}
function mzGrunt_Copy(&$templates)
{
  global $zbp;
  $templates['header'] = str_replace("-time-", time(), $templates['header']);
  $dir = $GLOBALS['usersdir'] . 'theme/' . $GLOBALS['blogtheme'] . "/compile/";
  if (is_dir($dir)) {
    copy(mzGrunt_Path("v-grunt"), "{$dir}Gruntfile.js");
  }
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
{ }
function UninstallPlugin_mzGrunt()
{ }
