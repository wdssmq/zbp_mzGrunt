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
  if ($blogtheme == $theme->id) {
    $selected = 1;
    $theme->name .= $arrAid[0];
  } else {
    $selected = "";
  }
  $dir = mzGenPath($theme->id);
  if (is_dir($dir)) {
    $theme->name .= $arrAid[1];
    // $arrForJS[$theme->id] = ;
  }
  $options .= mzGenOption($theme->id, $theme->name, $selected);
  // echo __LINE__ . ":<br>\n";
  // var_dump($theme);
  // echo "<br><br>\n\n";
  // die();
}
function mzBuilPath($id)
{
  $uFile = "";
  $dir = mzGenPath(trim($id), $uFile);
  if (!is_dir($dir)) {
    @mkdir($dir, 0755);
    FileUtil::copyDir(mzGrunt_Path("var"), $dir);
  }
  $vJson = file_get_contents(mzGrunt_Path("v-json"));
  $uJson = strtr($vJson, $_POST);
  // echo __LINE__ . ":<br>\n";
  // var_dump($uFile);
  // echo "<br><br>\n\n";
  // echo __LINE__ . ":<br>\n";
  // var_dump($uJson);
  // echo "<br><br>\n\n";
  // die();
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
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle; ?></div>
  <div class="SubMenu">
  </div>
  <div id="divMain2">
  <form method="post" action="<?php echo BuildSafeURL("main.php?act=save"); ?>">
    <input type="hidden" id="zb_path" name="zb_path" value="<?php echo $blogpath ?>">
    <table width="100%" cellspacing="0" cellpadding="0" class="tableBorder tableBorder-thcenter table_striped table_hover">
    <tbody>
      <tr>
        <th width="17%">&nbsp;</th>
        <th>&nbsp;</th>
      </tr>
      <tr>
        <td><b>· 选择主题</b></td>
        <td>
          <select name="app_id" id="app_id"><?php echo ($options); ?></select>
        </td>
      </tr>
      <tr>
        <td><b>· 主题简介</b></td>
        <td>
          <input id="app_note" name="app_note" style="width:550px;" type="text" value="">
        </td>
      </tr>
      <tr>
        <td><b>· 主题发布页面</b></td>
        <td>
          <input id="app_url" name="app_url" style="width:550px;" type="text" value="">
        </td>
      </tr>
      <tr>
        <td><b>· 作者</b></td>
        <td>
          <input id="app_author_name" name="author_name" style="width:550px;" type="text" value="">
        </td>
      </tr>
      <tr>
        <td><b>· 版本</b></td>
        <td>
          <input id="app_version" name="app_version" style="width:550px;" type="text" value="">
        </td>
      </tr>
      <tr>
        <td><b>· 日期</b></td>
        <td>
          <input id="app_modified" name="app_modified" style="width:550px;" type="text" value="">
        </td>
      </tr>
    </tbody>
    </table>
    <input type="submit">
  </form>
  </div><!-- end div#divMain -->
</div>

<script>
  const obj = <?php echo json_encode($arrForJS); ?>;
  console.log(obj);

  function fill(id) {
    let app = obj[id];
    for (const key in app) {
      if (app.hasOwnProperty(key)) {
        const el = app[key];
        console.log(key,el);
        $("#app_"+key).val(el);
        // $("#app_"+key).val(el);
      }
    }
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
