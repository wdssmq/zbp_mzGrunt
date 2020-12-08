<?php
#注册插件
RegisterPlugin("mzGrunt", "ActivePlugin_mzGrunt");

function ActivePlugin_mzGrunt()
{
  Add_Filter_Plugin('Filter_Plugin_Zbp_Load', 'mzGrunt_BuildTemplate');
  Add_Filter_Plugin('Filter_Plugin_Zbp_BuildTemplate', 'mzGrunt_Copy');
}
function mzGrunt_BuildTemplate()
{
  global $zbp;
  $zbp->BuildTemplate();
}
function mzGrunt_Copy(&$templates)
{
  global $zbp;
  if (!$zbp->option['ZC_DEBUG_MODE']) {
    return;
  }
  $uFile = mzGrunt_Path("zblogphp.js");
  $vFile = mzGrunt_Path("zblogphp.orig.js");
  if (is_file($vFile)) {
    copy($vFile, $uFile);
  }
  $templates['header'] = str_replace('{$style}.css', '{$style}.css?x=' . time(), $templates['header']);

  // $codeFile = "{$curThemePath}{$curTheme}.code-workspace";
  if ($zbp->config('mzGrunt')->reloadType == "2") {
    $templates['footer'] .= <<<script
    <script>
    console.log($(window).width());
    document.write(
      '<script src="http://' +
        (location.host).split(':')[0] +
        ':35729/livereload.js?snipver=1"></' +
        'script>'
    );
  </script>
script;
  } else {
    $templates['footer'] = str_replace('{$footer}', '{$footer}' . "\n" . '<script src="' . $zbp->host . 'zb_users/plugin/mzGrunt/script/script.js"></script>', $templates['footer']);
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
