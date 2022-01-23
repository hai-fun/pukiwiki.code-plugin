<?php
/**
 * Time-stamp: <05/10/02 22:21:18 sasaki>
 * 
 * GPL
 *
 * r 0.6.0_pr2
 */
define('SOURCE_ENCODING', 'euc-jp');
define('LANG', 'ja');


define('PKWK_HOME', 'pukiwiki/');
define('PLUGIN_CODE_HEADER', 'code_');
define('PLUGIN_DIR', PKWK_HOME.'plugin/');

// �ơ��֥��Ȥ����ݤ�(FALSE��CSS��div�ˤ��ʬ��)
//define('PLUGIN_CODE_TABLE',     false);

// TAB��
define('PLUGIN_CODE_WIDTHOFTAB', '    ');

// defined image files by PukiWiki
define('IMAGE_DIR', PKWK_HOME.'image/');
define('PLUGIN_CODE_IMAGE_FILE', IMAGE_DIR.'code_dot.png');
define('PLUGIN_CODE_OUTLINE_OPEN_FILE',  IMAGE_DIR.'code_outline_open.png');
define('PLUGIN_CODE_OUTLINE_CLOSE_FILE', IMAGE_DIR.'code_outline_close.png');

header('Cache-control: no-cache');
header('Pragma: no-cache');
header('Content-Type: text/html; charset='.SOURCE_ENCODING);

$header = '
<?xml version="1.0" encoding="'.SOURCE_ENCODING.'"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="'.LANG.'" lang="'.LANG.'">
<head>
  <meta http-equiv="content-type" content="application/xhtml+xml; charset='.SOURCE_ENCODING.'" />
  <meta http-equiv="content-style-type" content="text/css" />
  <link rel="stylesheet" type="text/css" href="'.PKWK_HOME.'skin/code.css" />
  <meta http-equiv="Content-Script-Type" content="text/javascript" />
  <script type="text/javascript" src="'.PKWK_HOME.'skin/code.js"></script>
  <title>Code highlight</title>
</head>
<body>';

$tabwidth = $_POST['tabs'];
if (is_numeric($tabwidth)) {
	$tabs = str_repeat(' ', (int)$tabwidth);
} else {
	$tabs = "\t";
}
$lang = htmlspecialchars($_POST['lang']);
$data = mb_convert_encoding($_POST['text'], SOURCE_ENCODING, 'auto')."\n";
$option = array(
				'number'      => FALSE,  // ���ֹ��ɽ������
				'nonumber'    => FALSE,  // ���ֹ��ɽ�����ʤ�
				'outline'     => FALSE,  // �����ȥ饤�� �⡼��
				'nooutline'   => FALSE,  // �����ȥ饤�� ̵��
				'comment'     => FALSE,  // �����ȳ��� ͭ��
				'nocomment'   => TRUE,   // �����ȳ��� ̵��
				'menu'        => FALSE,  // ��˥塼��ɽ������
				'nomenu'      => FALSE,  // ��˥塼��ɽ�����ʤ�
				'icon'        => FALSE,  // ���������ɽ������
				'noicon'      => FALSE,  // ���������ɽ�����ʤ�
				'link'        => FALSE,  // �����ȥ�� ͭ��
				'nolink'      => FALSE,  // �����ȥ�� ̵��
				);

require_once(PLUGIN_DIR.'code/codehighlight.php');
$highlight = new CodeHighlight;
$lines = $highlight->highlight($lang, $data, $option);
$lines = '<div class="'.$lang.'">'.$lines.'</div>';

echo $header;
if ($_POST) {
	echo $lines;
} else {
	echo '
<div>
<form action="./code.php" method="post">
<div><input type="hidden" name="encode_hint" value="��" /></div>
<div>
Language:
<select name="lang" id="lang">
    <option value="c" selected="">C/C++</option>
    <option value="csharp">C#</option>
    <option value="java">Java</option>
    <option value="pascal">Pascal</option>
    <option value="perl">Perl</option>
    <option value="php">PHP</option>
    <option value="pli">PL/I</option>
    <option value="python">Python</option>
    <option value="ruby">Ruby</option>
    <option value="scheme">Scheme</option>
    <option value="sql">SQL</option>
    <option value="vb">Visual Basic</option>
    <option value="xml">XML</option>
    <option value="pre">Plain Text</option>
</select>
</div>
<div>Convert tabs:
<select name="tabs" id="cvt_tabs">
        <option value="No" selected="">No</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="8">8</option>
</select>
</div>

<div>
<textarea name="text" cols="80" rows="20"></textarea>
</div>

<div><input type="submit" value="Paste" />
</div>
</form>
</div>';
}

?>
</body>
</html>

<?php

/**
 * ���ֹ���������
 * �����Ϲ��ֹ���ϰ�
 * �������줿���ֹ���֤�
 */
function _plugin_code_makeNumber($end, $begin=1)
{
	$number='';
	$str_len=max(3,strlen(''.$end));
	for($i=$begin; $i<=$end; ++$i) {
		$number.= sprintf('%'.$str_len.'d',($i))."\n";
	}
	return $number;
}
/**
 * ���Ȥߤ��ƽ��Ϥ���
 * 
 * ����HTML���֤�
 */
function _plugin_code_column(& $text, $number=null, $outline=null)
{
	if ($number === null && $outline === null)
		return $text;
	
	if (PLUGIN_CODE_TABLE) {
		$html .= '<table class="'.PLUGIN_CODE_HEADER
			.'table" border="0" cellpadding="0" cellspacing="0"><tr>';
		if ($number !== null)
			$html .= '<td>'.$number.'</td>';
		if ($outline !== null)
			$html .= '<td>'.$outline.'</td>';
		$html .= '<td>'.$text.'</td></tr></table>';
	} else {
		if ($number !== null)
			$html .= '<div class="'.PLUGIN_CODE_HEADER.'number">'.$number.'</div>';
		if ($outline !== null)
			$html .= '<div class="'.PLUGIN_CODE_HEADER.'outline">'.$outline.'</div>';
		$html .= '<div class="'.PLUGIN_CODE_HEADER.'src">'.$text.'</div>'
			. '<div style="clear:both;"><br style="display:none;" /></div>';
	}

	return $html;
}

?>
