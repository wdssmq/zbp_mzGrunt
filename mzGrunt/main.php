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

// 生成处理
$act = GetVars('act', 'GET');
$suc = GetVars('suc', 'GET');
if (GetVars('act', 'GET') == 'save') {
  CheckIsRefererValid();
  $zbp->config('mzGrunt')->reloadType = GetVars("reloadType", "POST");
  $zbp->config('mzGrunt')->usersdir = trim(GetVars("usersdir", "POST"));
  $zbp->config('mzGrunt')->usersdir = rtrim($zbp->config('mzGrunt')->usersdir, '/') . '/';
  $zbp->config('mzGrunt')->Save();
  mzBuilPath(GetVars("app_id", "POST"));
  $zbp->BuildTemplate();
  $zbp->SetHint('good');
  Redirect('./main.php' . ($suc == null ? '' : '?act=$suc'));
}

// 表单输出预处理
$options = "";
$arrAid = array("（当前）", "（已生成）");
$allthemes = $zbp->LoadThemes();
$WhiteList = array(
  'id' => null,
  'name' => null,
  'version' => null,
  'pubdate' => null,
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
  $gruntPath = "";
  $pgkFile = "";
  $AppDir = mzGenPath($theme->id, $gruntPath, $pgkFile);
  if (is_dir($gruntPath)) {
    $theme->name .= $arrAid[1];
  }
  if (is_file($pgkFile)) {
    $uJson = json_decode(file_get_contents($pgkFile));
    $arrForJS[$theme->id]["style"] = $uJson->appStyle;
  }
  $options .= mzGenOption($theme->id, $theme->name, $selected);
}

// 强制检查 LiveReload
mzGrunt_CheckLiveReload(true);
$reloadType = $zbp->Config('mzGrunt')->reloadType;
if ("3" == $reloadType) {
  $liveReload = $zbp->Config('mzGrunt')->liveReload;
  $liveReload = str_replace("http://127.0.0.1:", "", $liveReload);
  $liveReload = str_replace("{$zbp->host}zb_users/", "", $liveReload);
  $liveReload = "<br>当前：{$liveReload}";
} else {
  $liveReload = "";
}

// 生成配置文件到主题目录
function mzBuilPath($id)
{
  global $zbp, $blogtheme;
  $over = (bool)GetVars("over", "POST");
  $gruntPath = "";
  $pgkFile = "";
  $AppDir = mzGenPath(trim($id), $gruntPath, $pgkFile);

  // 创建目录并写入配置文件
  if (!is_dir($gruntPath) || $over) {
    @mkdir($gruntPath, 0755);
    FileUtil::copyDir(mzGrunt_Path("var"), $gruntPath, true);
    if (is_dir(mzGrunt_Path("usr"))) {
      FileUtil::copyDir(mzGrunt_Path("usr"), $AppDir, true);
    }
    // fix style.css 2 themeName.css
    $sFile = "{$AppDir}style/style.css";
    $dFile = "{$AppDir}style/{$id}.css";
    if (is_file($sFile) && !is_file($dFile)) {
      FileUtil::moveFile($sFile, $dFile);
    }
    if ($zbp->option['ZC_BLOG_CSS'] == "style" && $blogtheme == $id) {
      SetTheme($id, $id);
    }
  }

  // 自动生存工作区文件
  $sFile = "{$AppDir}app.code-workspace";
  $dFile = "{$AppDir}{$id}.code-workspace";
  if (is_file($sFile) && !is_file($dFile)) {
    FileUtil::moveFile($sFile, $dFile);
  } elseif (is_file($sFile)) {
    FileUtil::unlinkFile($sFile);
  }

  // package.json 内容生成
  $vJson = file_get_contents(mzGrunt_Path("v-json"));
  $uJson = strtr($vJson, $_POST);

  // CSS 处理文件
  if ($uCSS = GetVars("app_style", "POST")) {
    $arrCSS = explode(".", $uCSS);
    $ex = array_pop($arrCSS);
    $dirAG = "{$gruntPath}{$ex}";
    if (!is_dir($dirAG)) {
      @mkdir($dirAG, 0755);
      #code
    }
    $uJson = str_replace("-$ex-", $uCSS, $uJson);
  }

  // 写入文件
  $uJson = mzReplaceUsrDir($uJson);
  file_put_contents($pgkFile, $uJson);
}

// 路径配置项不为空时替换
function mzReplaceUsrDir($str)
{
  global $zbp;
  $usersdir = $zbp->Config('mzGrunt')->usersdir;
  if (!empty($usersdir) && $usersdir !== "/") {
    $str = str_replace($zbp->usersdir, $usersdir, $str);
  }
  return $str;
}

// 获取所需要的文件路径
function mzGenPath($id, &$gruntPath = "", &$pgkFile = "")
{
  $dir = "{$GLOBALS['usersdir']}theme/{$id}/";
  $gruntPath = $dir . "compile/";
  $pgkFile = "{$gruntPath}package.json";
  return $dir;
}

// 表单 option 生成
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
  .hidden {
    display: none;
  }
</style>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle; ?></div>
  <div class="SubMenu">
    <a href="main.php" title="首页"><span class="m-left m-now">首页</span></a>
    <?php require "about.php"; ?>
  </div>
  <div id="divMain2">
    <form method="post" action="<?php echo BuildSafeURL("main.php?act=save"); ?>">
      <input type="hidden" id="zb_host" name="zb_host" value="<?php echo $bloghost; ?>">
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
            <td><b>· CSS 预处理入口文件</b></td>
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
              以下内容来自主题的 theme.xml，如需修改建议点击<a target="_blank" title="编辑主题" href="" class="js-editBTN">「编辑」</a>
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
              <input id="app_pubdate" name="app_pubdate" style="width:7em;" type="text" value=""> &gt;&gt; <input id="app_modified" name="app_modified" style="width:7em;" type="text" value="">
            </td>
            <td></td>
          </tr>
          <td colspan="3" style="text-align: center;">
            通用选项
          </td>
          <tr>
            <td><b>· 实际路径</b></td>
            <td><?php zbpform::text("usersdir", $zbp->Config("mzGrunt")->usersdir, "90%"); ?></td>
            <td>站点如果在 Docker 内需要手动指定，否则留空；<?php echo $zbp->usersdir ?></td>
          </tr>
          <tr>
            <td><b>· livereload</b></td>
            <td><?php echo zbpform::select('reloadType', array('1' => '网页内定时查询（仅针对 CSS）', '2' => '编辑器/构建工具通知更新', '3' => '自动检测'), $zbp->config("mzGrunt")->reloadType); ?><?php echo $liveReload; ?></td>
            <td>推送调用端口 + 文件名 :35729/livereload.js</td>
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
    $arrLog["pAth"] = mzReplaceUsrDir($arrLog["pAth"]);
    $arrLog["pAthThEme"] = $arrLog["pAth"] . $blogtheme . "/";
    $arrLog["fIle"] = mzGrunt_Path("v-json");
    $arrLog["fIle"] = mzReplaceUsrDir($arrLog["fIle"]);
    ?>
    <h4>简要：</h4>
    <p>选择主题并提交会在主题目录下创建一个 compile 文件夹并放入 Grunt 所需文件，在命令行下进入该路径即可使用 Grunt 构建主题的 CSS 和 JS，包括：</p>
    <p>· LESS、SASS 编译生成 CSS，CSS 属性前缀添加，CSS 属性排序……;</p>
    <p>· JS 文件合并，压缩，语法检测……;</p>
    <h4>推荐（适用于插件或不需要预处理 CSS/JS 的主题）：</h4>
    <p>「开发工具」BrowserSync - Z-Blog 应用中心</p>
    <p><a href="https://app.zblogcn.com/?id=27743" target="_blank" title="「开发工具」BrowserSync - Z-Blog 应用中心">https://app.zblogcn.com/?id=27743</a></p>
    <h4>预置命令（需要先安装 Git, Node, Grunt 及相关的依赖）</h4>
    <h5>下方命令会预置在主题 compile/package.json 内，可配合编辑器任务调用</h5>
    <p>
    <pre>
cd <?php echo $arrLog["pAthThEme"]; ?>compile
#—— 进入主题路径

npm run dist
#—— 调用 dist-css、dist-js 然后开启监听（自动编译 Less 或者刷新网页）

npm run dist-css
#—— 编译并压缩 CSS

npm run dist-js
#—— 合并并压缩 js

npm run test-css
#—— 检查并提示 CSS 语法错误，同样有 test-js
  </pre>
    </p>
    <h4>依赖安装</h4>
    <p>Git - Downloading Package：<a href="https://git-scm.com/download/win" title="Git - Downloading Package" target="_blank">https://git-scm.com/download/win</a></p>
    <p>Node.js：<a href="https://nodejs.org/zh-cn/" title="Node.js" target="_blank">https://nodejs.org/zh-cn/</a></p>
    <!-- <p>Ruby：<a href="https://rubyinstaller.org/downloads/" title="Ruby" target="_blank">https://rubyinstaller.org/downloads/</a>（SASS需要,安装Ruby后运行<code>gem install sass</code>）</p> -->
    <h4>在 Git Bash 中运行如下命令（只需运行一次）：</h4>
    <p>
    <pre><?php echo strtr($console, $arrLog); ?></pre>
    </p>
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
        $("#app_" + key).val(el);
        // $("#app_"+key).val(el);
      }
    }
    $(".js-id").text(app["id"]);
    $(".js-editBTN").attr("href", $(".js-editURL").text());
  }
  $("#app_id").change(function() {
    fill($(this).val());
    // console.log($(this).val());
  });
  fill($("#app_id").val());
  // usersdir 值为 / 时清空；
  if ($("#usersdir").val() == "/") {
    $("#usersdir").val("");
  }
</script>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>
