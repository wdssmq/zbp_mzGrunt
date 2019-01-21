<?php
#注册插件
RegisterPlugin("mzGrunt", "ActivePlugin_mzGrunt");

function ActivePlugin_mzGrunt()
{
}
function mzGrunt_Path($file, $t = 'path')
{
  global $zbp;
  $result = $zbp->$t . 'zb_users/plugin/mzGrunt/';
  switch ($file) {
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
{
}
function UninstallPlugin_mzGrunt()
{
}
