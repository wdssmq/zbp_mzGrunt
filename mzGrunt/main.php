<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
require 'class/FileUtil.php';
$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
  $zbp->ShowError(6);
  die();
}
if (!$zbp->CheckPlugin('mzGrunt')) {
  $zbp->ShowError(48);
  die();
}

$act = GetVars('act', 'GET');
$suc = GetVars('suc', 'GET');
if (GetVars('act', 'GET') == 'save') {
  CheckIsRefererValid();
  mzBuilPath(GetVars("app_id", "POST"));
  $zbp->BuildTemplate();
  $zbp->SetHint('good');
  Redirect('./main.php' . ($suc == null ? '' : '?act=$suc'));
}

$options = "";
$arrAid = array("（当前）", "（已生成）");
$allthemes = $zbp->LoadThemes();
$WhiteList = array(
  'id' => null,
  'name' => null,
  'version' => null,
  'modified' => null,
  'url' => null,
  'author_name' => null,
  'note' => null
);
$arrForJS = array();
foreach ($allthemes as $theme) {
  $arrForJS[$theme->id] = array_intersect_key((array)$theme, $WhiteList);
  $arrForJS[$theme->id]["style"] = "";
  if ($blogtheme == $theme->id) {
    $selected = 1;
    $theme->name .= $arrAid[0];
  } else {
    $selected = "";
  }
  $uFile = "";
  $dir = mzGenPath(trim($theme->id), $uFile);
  if (is_dir($dir)) {
    $theme->name .= $arrAid[1];
  }
  if (is_file($uFile)) {
    $uJson = json_decode(file_get_contents($uFile));
    $arrForJS[$theme->id]["style"] = $uJson->appStyle;
  }
  $options .= mzGenOption($theme->id, $theme->name, $selected);
}
function mzBuilPath($id)
{
  $uFile = "";
  $dir = mzGenPath(trim($id), $uFile);
  if (!is_dir($dir) || (bool)GetVars("over", "POST")) {
    @mkdir($dir, 0755);
    FileUtil::copyDir(mzGrunt_Path("var"), $dir, true);
  }
  $vJson = file_get_contents(mzGrunt_Path("v-json"));
  $uJson = strtr($vJson, $_POST);
  if ($uCSS = GetVars("app_style", "POST")) {
    $arrCSS = explode(".", $uCSS);
    $ex = array_pop($arrCSS);
    $dirAG = "{$dir}{$ex}";
    if (!is_dir($dirAG)) {
      @mkdir($dirAG, 0755);
      #code
    }
  }
  $uJson = str_replace("-$ex-", $uCSS, $uJson);
  file_put_contents($uFile, $uJson);
}
function mzGenPath($id, &$file = "")
{
  $dir = $GLOBALS['usersdir'] . 'theme/' . $id . "/compile/";
  $file = "{$dir}package.json";
  return $dir;
}
function mzGenOption($v, $t = null, $s = "")
{
  if (empty($t)) {
    $t = $v;
  }
  if (!empty($s)) {
    $s = "selected";
  }
  $arr = array('-t-' => $t, '-v-' => $v, "data-selected" => $s);
  return strtr("<option value=\"-v-\" data-selected>-t-</option>", $arr);
}

$blogtitle = 'Grunt';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<style>
  .hidden{
    display:none;
  }
</style>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle; ?></div>
  <div class="SubMenu">
  </div>
  <div id="divMain2">
  <form method="post" action="<?php echo BuildSafeURL("main.php?act=save"); ?>">
    <input type="hidden" id="zb_path" name="zb_path" value="<?php echo $usersdir . "theme/"; ?>">
    <input type="hidden" id="cfg_path" name="cfg_path" value="<?php echo mzGrunt_Path("cfg"); ?>">
    <table width="100%" cellspacing="0" cellpadding="0" class="tableBorder tableBorder-thcenter table_striped table_hover">
    <tbody>
      <tr>
        <th width="15%">&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
      </tr>
      <tr>
        <td><b>· 选择主题</b></td>
        <td>
          <select name="app_id" id="app_id"><?php echo ($options); ?></select>
        </td>
        <td></td>
      </tr>
      <tr>
        <td><b>· CSS预处理入口文件</b></td>
        <td>
          <input id="app_style" name="app_style" style="width:450px;" type="text" value="">
        </td>
        <td>
        例如：bulma.sass 对应路径为 <span class="js-id"></span>/compile/sass/bulma.sass<br>
        例如：bootstrap.less 对应路径为 <span class="js-id"></span>/compile/less/bootstrap.less
        </td>
      </tr>
      <tr>
        <td colspan="3" style="text-align: center;">
        以下内容来自主题的theme.xml,如需修改建议点击<a target="_blank" title="编辑主题" href="" class="js-editBTN">[编辑]</a>
        <p class="js-editURL hidden"><?php echo $bloghost; ?>zb_users/plugin/AppCentre/theme_edit.php?id=<span class="js-id"></span>&token=<?php echo $zbp->GetToken('AppCentre'); ?></p>
      </td>
      </tr>
      <tr>
        <td><b>· 主题简介</b></td>
        <td>
          <input id="app_note" name="app_note" style="width:450px;" type="text" value="">
        </td>
      </tr>
      <tr>
        <td><b>· 主题发布页面</b></td>
        <td>
          <input id="app_url" name="app_url" style="width:450px;" type="text" value="">
        </td>
        <td></td>
      </tr>
      <tr>
        <td><b>· 作者</b></td>
        <td>
          <input id="app_author_name" name="app_author_name" style="width:450px;" type="text" value="">
        </td>
        <td></td>
      </tr>
      <tr>
        <td><b>· 版本</b></td>
        <td>
          <input id="app_version" name="app_version" style="width:450px;" type="text" value="">
        </td>
        <td></td>
      </tr>
      <tr>
        <td><b>· 日期</b></td>
        <td>
          <input id="app_modified" name="app_modified" style="width:450px;" type="text" value="">
        </td>
        <td></td>
      </tr>
    </tbody>
    </table>
    <input type="submit"> <label for="over"><input type="text" class="checkbox" name="over" id="over">覆盖已有文件</label>
  </form>
  <?php
  $console = file_get_contents(mzGrunt_Path("console"));
  // $console = str_replace("\n", "<br>", $console);
  $arrLog = array();
  $arrLog["pAth"] = $usersdir . "theme/";
  $arrLog["fIle"] = mzGrunt_Path("v-json");
  ?>
  <h4>简要：</h4>
  <p>选择主题并提交会在主题目录下创建一个compile文件夹并放入Grunt所需文件，在命令行下进入该路径即可使用Grunt构建主题的CSS和JS，包括：</p>
  <p>·LESS、SASS编译生成CSS，CSS属性前缀添加，CSS属性排序……;</p>
  <p>·JS文件合并，压缩，语法检测……;</p>
  <h4>预置命令（需要先安装Git，Node，Grunt及相关的依赖）</h4>
  <p><pre>
grunt dist-css

#—— 编译并压缩CSS

#grunt dist-js

#—— 合并并压缩js

#grunt dist

#—— 调用dist-css、dist-js然后开启监听（自动编译Less或者刷新网页）

grunt test-css

#—— 检查并提示CSS语法错误，同样有test-js
  </pre></p>
  <h4>依赖安装</h4>
  <p>Git - Downloading Package：<a href="https://git-scm.com/download/win" title="Git - Downloading Package" target="_blank">https://git-scm.com/download/win</a></p>
  <p>Node.js：<a href="https://nodejs.org/zh-cn/" title="Node.js" target="_blank">https://nodejs.org/zh-cn/</a></p>
  <p>Ruby：<a href="https://rubyinstaller.org/downloads/" title="Ruby" target="_blank">https://rubyinstaller.org/downloads/</a>（SASS需要,安装Ruby后运行<code>gem install sass</code>）</p>
  <h4>在Git Bash中运行如下命令（只需运行一次）：</h4>
  <p><pre><?php echo strtr($console, $arrLog); ?></pre></p>
  <p><br></p>
  </div><!-- end div#divMain2 -->
</div>

<script>
  const obj = <?php echo json_encode($arrForJS); ?>;
  console.log(obj);
  function fill(id) {
    let app = obj[id];
    for (const key in app) {
      if (app.hasOwnProperty(key)) {
        const el = app[key];
        // console.log(key,el);
        $("#app_"+key).val(el);
        // $("#app_"+key).val(el);
      }
    }
    $(".js-id").text(app["id"]);
    $(".js-editBTN").attr("href",$(".js-editURL").text());
  }
  $("#app_id").change(function () {
    fill($(this).val());
    // console.log($(this).val());
  });
  fill($("#app_id").val());
</script>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>
