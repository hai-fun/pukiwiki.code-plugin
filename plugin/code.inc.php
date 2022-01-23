<?php
/**
 * �����ɥϥ��饤�ȵ�ǽ��PukiWiki���ɲä���
 * Time-stamp: <07/11/20 01:31:12 sky>
 * 
 * GPL
 *
 * r 0.6.0_pr3
 */

define('PLUGIN_CODE_LANGUAGE', 'c');  // ɸ����� ���ƾ�ʸ���ǻ���
// ɸ������
define('PLUGIN_CODE_NUMBER',        1);  // ���ֹ�
define('PLUGIN_CODE_FOLD',          1);  // �����ȥ饤��
define('PLUGIN_CODE_FOLDBLOCK',     1);  // �֥�å�����
define('PLUGIN_CODE_FOLDLITERAL',   1);  // ʸ������
define('PLUGIN_CODE_FOLDCOMMENT',   1);  // �����ȳ���
define('PLUGIN_CODE_MENU',          1);  // ��˥塼��ɽ��/��ɽ��;
define('PLUGIN_CODE_FILE_ICON',     1);  // ź�եե�����˥�������ɥ���������դ���
define('PLUGIN_CODE_LINK',          1);  // �����ȥ��
define('PLUGIN_CODE_CACHE',         0);  // ����å����Ȥ�


// URL�ǻ��ꤷ���ե�������ɤ߹��फ�ݤ�
define('PLUGIN_CODE_READ_URL',  0);

// �ơ��֥��Ȥ����ݤ�(0��CSS��div�ˤ��ʬ��)
define('PLUGIN_CODE_TABLE',     1);

// TAB��
define('PLUGIN_CODE_WIDTHOFTAB', '    ');
// �����ե����������
define('PLUGIN_CODE_IMAGE_FILE', IMAGE_DIR.'code_dot.png');

define('PLUGIN_CODE_OUTLINE_OPEN_FILE',  IMAGE_DIR.'code_outline_open.png');
define('PLUGIN_CODE_OUTLINE_CLOSE_FILE', IMAGE_DIR.'code_outline_close.png');

// plugin pre
define('PLUGIN_PRE_NUMBER',        0);  // ���ֹ�
define('PLUGIN_PRE_VERVATIM_HARD', 1);  // ����饤��Ÿ���򤷤ʤ�
define('PLUGIN_PRE_FILE_ICON',     1);  // ź�եե�����˥�������ɥ���������դ���


define('PLUGIN_CODE_HEADER', 'code_');
define('PLUGIN_PRE_HEADER', 'pre_');
define('PLUGIN_PRE_COLOR_REGEX', '/^(#[0-9a-f]{3}|#[0-9a-f]{6}|[a-z-]+)$/i');

if (! defined('FILE_ICON')) {
    define('FILE_ICON',
    '<img src="' . IMAGE_DIR . 'file.png" width="20" height="20"' .
    ' alt="file" style="border-width:0px" />');
}

define('PLUGIN_CODE_USAGE', 
       '<p class="error">Plugin code: Usage:<br />#code[(Lang)]{{<br />src<br />}}</p>');

define('PLUGIN_CODE_CONFIG_PAGE_MIME', 'plugin/code/mimetype');
define('PLUGIN_CODE_CONFIG_PAGE_EXTENSION', 'plugin/code/extension');

function pluing_code_init()
{
    global $javascript; $javascript = 1;
}
function plugin_code_action()
{
    global $vars;
    global $_source_messages;
    
    if (PKWK_SAFE_MODE) die_message('PKWK_SAFE_MODE prohibits this');

    $vars['refer'] = $vars['page'];

    if (! is_page($vars['page']) || ! check_readable($vars['page'],0,0)) {
        return array( 'msg' => $_source_messages['msg_notfound'],
                      'body' => $_source_messages['err_notfound'] );
    }
    return array( 'msg' => $_source_messages['msg_title'],
                  'body' => plugin_code_convert('pukiwiki',
                                                join('',get_source($vars['page']))."\n"));
}

function plugin_code_convert()
{
    if (file_exists(PLUGIN_DIR.'code/codehighlight.php'))
        require_once(PLUGIN_DIR.'code/codehighlight.php');
    else
        die_message('file '.PLUGIN_DIR.'code/codehighlight.php not exist or not readable.');

    static $plugin_code_jscript_flag = 1;
    
    $title = '';
    $lang = null;
    $option = array(
                    'number'      => 0,  // ���ֹ��ɽ������
                    'nonumber'    => 0,  // ���ֹ��ɽ�����ʤ�
                    'outline'     => 0,  // �����ȥ饤�� �⡼��
                    'nooutline'   => 0,  // �����ȥ饤�� ̵��
                    'block'       => 0,  // �����ȥ饤�� �֥�å�
                    'noblock'     => 0,  // �����ȥ饤�� �֥�å�
                    'literal'     => 0,  // �����ȥ饤�� ʸ����
                    'noliteral'   => 0,  // �����ȥ饤�� ʸ����
                    'comment'     => 0,  // �����ȥ饤�� ������
                    'nocomment'   => 0,  // �����ȥ饤�� ������
                    'menu'        => 0,  // ��˥塼��ɽ������
                    'nomenu'      => 0,  // ��˥塼��ɽ�����ʤ�
                    'icon'        => 0,  // ���������ɽ������
                    'noicon'      => 0,  // ���������ɽ�����ʤ�
                    'link'        => 0,  // �����ȥ�� ͭ��
                    'nolink'      => 0,  // �����ȥ�� ̵��
                    );
    
    $num_of_arg = func_num_args();
    $args = func_get_args();
    if ($num_of_arg < 1) {
        return PLUGIN_CODE_USAGE;
    }

    $arg = $args[$num_of_arg-1];
    if (strlen($arg) == 0) {
        return PLUGIN_CODE_USAGE;
    }
    if ($num_of_arg != 1 && ! _plugin_code_check_argment($args[0], $option)) 
        $lang = htmlspecialchars(strtolower($args[0])); // ����̾�����ץ�����Ƚ��

    $begin = 1;
    $end = null;
    // ���ץ�����Ĵ�٤�
    for ($i = 1;$i < $num_of_arg-1; ++$i) {
        if (! _plugin_code_check_argment($args[$i], $option))
            _plugin_code_get_region($args[$i], $end, $begin);
    }
    $multiline = _plugin_code_multiline_argment($arg, $data, $lang, $option, $end, $begin);

    if (PLUGIN_CODE_CACHE && ! $multiline) { 
        $html = _plugin_code_read_cache($arg);
        if ($html != '' or $html != null)
            return $html;
    }
    
    if (isset($data['_error']) && $data['_error'] != '') {
        return $data['_error'];
    }
    $lines = $data['data'];
    $title = $data['title'];

    $highlight = new CodeHighlight;
    $lines = $highlight->highlight($lang, $lines, $option, $end, $begin);
    $lines = '<div class="'.$lang.'">'.$lines.'</div>';
    
    if ($plugin_code_jscript_flag && $option['outline']) {
        $plugin_code_jscript_flag = 0;
        $title .= '<script type="text/javascript" src="'.SKIN_DIR.'code.js"></script>'."\n";
    }
    $html = $title.$lines;
    if (PLUGIN_CODE_CACHE && ! $multiline) {
        _plugin_code_write_cache($arg, $html);
    }
    return $html;
}

/**
 * ����å���˽񤭹���
 * ������ź�եե�����̾, HTML�Ѵ���Υե�����
 */
function _plugin_code_write_cache($fname, $html)
{
    global $vars;
    // ź�եե�����Τ���ڡ���: default�ϸ��ߤΥڡ���̾
    $page = isset($vars['page']) ? $vars['page'] : '';
    
    // �ե�����̾�˥ڡ���̾(�ڡ������ȥѥ�)����������Ƥ��뤫
    //   (Page_name/maybe-separated-with/slashes/ATTACHED_FILENAME)
    if (preg_match('#^(.+)/([^/]+)$#', $fname, $matches)) {
        if ($matches[1] == '.' || $matches[1] == '..')
            $matches[1] .= '/'; // Restore relative paths
            $fname = $matches[2];
            $page = get_fullname(strip_bracket($matches[1]), $page); // strip is a compat
            $file = encode($page) . '_' . encode($fname);
    } else {
        // Simple single argument
        $file =  encode($page) . '_' . encode($fname);
    }
    $fp = fopen(CACHE_DIR.'code/'.$file.'.html', 'w') or
        die_message('Cannot write cache file ' .
                    CACHE_DIR.'code/'. $file .'.html'.
                    '<br />Maybe permission is not writable or filename is too long');
    
    set_file_buffer($fp, 0);
    flock($fp, LOCK_EX);
    rewind($fp);
    fputs($fp, $html);
    flock($fp, LOCK_UN);
    fclose($fp);
}

/**
 * ����å�����ɤ߽Ф�
 * ������ź�եե�����̾
 * �Ѵ����줿�ե�����ǡ������֤�
 */
function _plugin_code_read_cache($fname)
{
    global $vars;
    // ź�եե�����Τ���ڡ���: default�ϸ��ߤΥڡ���̾
    $page = isset($vars['page']) ? $vars['page'] : '';
    
    // �ե�����̾�˥ڡ���̾(�ڡ������ȥѥ�)����������Ƥ��뤫
    //   (Page_name/maybe-separated-with/slashes/ATTACHED_FILENAME)
    if (preg_match('#^(.+)/([^/]+)$#', $fname, $matches)) {
        if ($matches[1] == '.' || $matches[1] == '..')
            $matches[1] .= '/'; // Restore relative paths
        $fname = $matches[2];
        $page = get_fullname(strip_bracket($matches[1]), $page); // strip is a compat
        $file = encode($page) . '_' . encode($fname);
    } else {
        // Simple single argument
        $file =  encode($page) . '_' . encode($fname);
    }
    
    /* Read file data */
    $fdata = '';
    $filelines = file(CACHE_DIR.'code/'.$file.'.html');
    
    foreach ($filelines as $line)
        $fdata .= $line;
    
    return $fdata;
}


/**
 * �����ѽ���
 * @author sky
 *
 * Code.inc.php Ver. 0.5
 */
function plugin_pre_convert()
{
    static $id_number = 0; // �ץ饰���󤬸ƤФ줿���(ID������)
    $id_number++;

    $option = array(
                  'number'      => 0,  // ���ֹ��ɽ������
                  'nonumber'    => 0,  // ���ֹ��ɽ�����ʤ�
                  'hard'        => 0,  // ����饤��Ÿ�����ʤ�
                  'soft'        => 0,  // ����饤��Ÿ������
                  'icon'        => 0,  // ���������ɽ������
                  'noicon'      => 0,  // ���������ɽ�����ʤ�
                  'link'        => 0,  // �����ȥ�� ͭ��
                  'nolink'      => 0,  // �����ȥ�� ̵��
              );
    $num_of_arg = func_num_args();
    $args = func_get_args();

    $text = '';
    $number = '';

    $style = '';
    $stylecnt = 0;

    $begin = 1;
    $end = null;
    $lang = null;

    $a = array();
    
    // ���ץ�����Ĵ�٤�
    for ($i = 0;$i < $num_of_arg-1; ++$i) {
        if (! _plugin_code_check_argment($args[$i], $option)) {
            if (! _plugin_code_get_region($args[$i], $end, $begin)) {
                // style
                if ($stylecnt == 0) {
                    $color   = $args[$i];
                    ++$stylecnt;
                } else {
                    $bgcolor = $args[$i];
                }
            }
        }
    }
    if ($stylecnt) {
        // Invalid color
        foreach(array($color, $bgcolor) as $col){
            if ($col != '' && ! preg_match(PLUGIN_PRE_COLOR_REGEX, $col))
                return '<p class="error">#pre():Invalid color: '.htmlspecialchars($col).';</p>';
        }
        if ($color != '' ) {
            $style   = ' style="color:'.$color;
            if ($bgcolor != '') 
                $style .= ';background-color:'.$bgcolor.'"';
            else
                $style .= '"';
        } else {
            if ($bgcolor != '') 
                $style .= ' style="background-color:'.$bgcolor.'"';
            else 
                $style = '';
        }
    }

    _plugin_code_multiline_argment($args[$num_of_arg-1], $data, $lang, $option, $end, $begin);
    if (isset($data['_error']) && $data['_error'] != '') {
        return $data['_error'];
    }
    $text = $data['data'];
    $title = $data['title'];

    if ($end === null)
        $end = substr_count($text, "\n") + $begin -1;

    if (PLUGIN_PRE_VERVATIM_HARD  && ! $option['soft']  || $option['hard']) {
        $text = htmlspecialchars($text);
    } else {
        $text = make_link($text);
    }
    $html = '<pre class="'.PLUGIN_PRE_HEADER.'body" '.$style.'>'.$text.'</pre>';

    if (PLUGIN_PRE_NUMBER  && ! $option['nonumber']  || $option['number']) {
        $number = '<pre class="'.PLUGIN_PRE_HEADER.'number">'
            ._plugin_code_makeNumber($end, $begin).'</pre>';
        $html = '<div id="'.PLUGIN_PRE_HEADER.$id_number.'" class="'.PLUGIN_PRE_HEADER.'table">'
            ._plugin_code_column($html, $number, null). '</div>';
    }
    
    return $title.$html;
}


/* pre.inc.php �ȶ��� */

 /**
 * �ǽ���������Ϥ���
 * �����ϥץ饰����ؤκǸ�ΰ���������
 * ʣ���԰����ξ��˿����֤�
 */
function _plugin_code_multiline_argment(& $arg, & $data, & $lang, & $option, $end = null, $begin = 1)
{
    // ���ԥ������Ѵ�
    $arg = str_replace("\r\n", "\n", $arg);
    $arg = strtr($arg,"\r", "\n");

    $data['title'] = '';
    // �Ǹ��ʸ�������ԤǤʤ����ϳ����ե�����
    if ($arg[strlen($arg)-1] != "\n") {
        // ���켫ưȽ��
        if ($lang === null)
            $lang = _plugin_code_extension($arg);
        if ($lang === null)
            if (_plugin_code_mimetype($arg)) {
                $data['_error'] = '<p class="error">Maybe file extension like binary. '.htmlspecialchars($arg).';</p>';
                return 0;
            } else {
                $lang = PLUGIN_CODE_LANGUAGE;
            }

        $params = _plugin_code_read_file_data($arg, $end, $begin);
        if (isset($params['_error']) && $params['_error'] != '') {
            $data['_error'] = '<p class="error">'.$params['_error'].';</p>';
            return 0;
        }
        $data['data'] = $params['data'];
        if ($data['data'] == "\n" || $data['data'] == '' || $data['data'] == null) {
            $data['_error'] ='<p class="error">file '.htmlspecialchars($params['title']).' is empty.</p>';
            return 0;
        }
        if (PLUGIN_CODE_FILE_ICON && !$option['noicon'] || $option['icon']) $icon = FILE_ICON;
        else                                                       $icon = '';

        $data['title'] = '<h5 class="'.PLUGIN_CODE_HEADER.'title">'.'<a href="'.$params['url'].'" title="'.$params['info'].'">'
            .$icon.$params['title'].'</a></h5>'."\n";
    }
    else {
        $data['data'] = $arg;
        return 1;
    }
    return 0;
}
/**
 * �Х��ʥ�ե�����γ�ĥ�Ҥ򸡺�����
 * 
 */
function _plugin_code_mimetype($name)
{
    $extension = strtolower(substr($name, strrpos($name, '.')+1));
    if (!$extension) return null;

    // mime-type����ɽ�����
    $config = new Config(PLUGIN_CODE_CONFIG_PAGE_MIME);
    $table = $config->read() ? $config->get('mime-type') : array();
    unset($config); // ��������

    foreach ($table as $row) {
        $_type = trim($row[0]);
        $exts = preg_split('/\s+|,/', trim($row[1]), -1, PREG_SPLIT_NO_EMPTY);
        foreach ($exts as $ext) {
            if ($extension == $ext) return 1;
        }
    }
    return 0;
}
/**
 * �ե�����̾�����б�����򸡺�����
 *
 */
function _plugin_code_extension($name)
{
    //$lang = PLUGIN_CODE_LANGUAGE; // default
    
    //if (! file_exists($filename)) return $lang;

    if (! strncasecmp($name, 'makefile', 8))
        return 'make';

    $extension = strtolower(substr($name, strrpos($name, '.')+1));
    if (!$extension) return null;

    // extension ����ɽ�����
    $config = new Config(PLUGIN_CODE_CONFIG_PAGE_EXTENSION);
    $table = $config->read() ? $config->get('lang') : array();
    unset($config); // ��������

    // search extension
    foreach ($table as $row) {
        $_lang = trim($row[0]);
        $exts = preg_split('/\s+|,/', trim($row[1]), -1, PREG_SPLIT_NO_EMPTY);
        foreach ($exts as $ext) {
            if ($extension == $ext) return $_lang;
        }
    }
    return null;
}
/**
 * ������Ϳ����줿�ե���������Ƥ�ʸ������Ѵ������֤�
 * ʸ�������ɤ� PukiWiki��Ʊ��, ���Ԥ� \n �Ǥ���
 */
function _plugin_code_read_file_data(& $name, $end = null, $begin = 1) {
    global $vars;
    global $script;
    // ź�եե�����Τ���ڡ���: default�ϸ��ߤΥڡ���̾
    $page = isset($vars['page']) ? $vars['page'] : '';

    // ź�եե�����ޤǤΥѥ������(�ºݤ�)�ե�����̾
    $file = '';
    $fname = $name;

    $is_url = is_url($fname);

    /* Check file location */
    if ($is_url) { // URL
        if (! PLUGIN_CODE_READ_URL || ini_get('allow_url_fopen') != 1) {
            $params['_error'] = 'Cannot assign URL';
            return $params;
        }
        $url = htmlspecialchars($fname);
        $params['title'] = htmlspecialchars(preg_match('/([^\/]+)$/', $fname, $matches) ? $matches[1] : $url);
    } else {  // ź�եե�����
        if (! is_dir(UPLOAD_DIR)) {
            $params['_error'] = 'No UPLOAD_DIR';
            return $params;
        }

        $matches = array();
        // �ե�����̾�˥ڡ���̾(�ڡ������ȥѥ�)����������Ƥ��뤫
        //   (Page_name/maybe-separated-with/slashes/ATTACHED_FILENAME)
        if (preg_match('#^(.+)/([^/]+)$#', $fname, $matches)) {
            if ($matches[1] == '.' || $matches[1] == '..')
                $matches[1] .= '/'; // Restore relative paths
            $fname = $matches[2];
            $page = get_fullname(strip_bracket($matches[1]), $page); // strip is a compat
            $file = UPLOAD_DIR . encode($page) . '_' . encode($fname);
            $is_file = is_file($file);
        } else {
            // Simple single argument
            $file = UPLOAD_DIR . encode($page) . '_' . encode($fname);
            $is_file = is_file($file);
        }

        if (! $is_file) {
            $params['_error'] = htmlspecialchars('File not found: "' .$fname . '" at page "' . $page . '"');
            return $params;
        }
        $params['title'] = htmlspecialchars($fname);
        $fname = $file;

        $url = $script . '?plugin=attach' . '&amp;refer=' . rawurlencode($page) .
            '&amp;openfile=' . rawurlencode($name); // Show its filename at the last
    }

    $params['url'] = $url;
    $params['info'] = get_date('Y/m/d H:i:s', filemtime($file) - LOCALZONE)
        . ' ' . sprintf('%01.1f', round(filesize($file)/1024, 1)) . 'KB';

    /* Read file data */
    $fdata = '';
    $filelines = file($fname);
    if ($end === null) 
        $end = count($filelines);
    
    for ($i=$begin-1; $i<$end; ++$i)
        $fdata .= str_replace("\r\n", "\n", $filelines[$i]);

    $fdata = strtr($fdata, "\r", "\n");
    $fdata = mb_convert_encoding($fdata, SOURCE_ENCODING, "auto");

    // �ե�����κǸ����Ԥˤ���
    if($fdata[strlen($fdata)-1] != "\n")
        $fdata .= "\n";

    $params['data'] = $fdata;

    return $params;
}
/**
 * ���ץ�������
 * �������б����륭����On�ˤ���
 * �����򥻥åȤ�����"1"���֤�
 */
function _plugin_code_check_argment(& $arg, & $option) {
    $arg = strtolower($arg);
    if (isset($option[$arg])) {
        $option[$arg] = 1;
        return 1;
    }
    return 0;
}
/**
 * �ϰϻ�������
 * �ƤӽФ�¦���ѿ������ꤹ��
 * �ϰϤ����ꤷ����"1"���֤�
 */
function _plugin_code_get_region(& $option, & $end, & $begin)
{
    if (false !== strpos($option, '-')) {
        $array = explode('-', $option);
    } else if (false !== strpos($option, '..')) {
        $array = explode('..', $option);
    } else {
        return 0;
    }

    if (is_numeric ($array[0]))
        if ($array[0] < 1)
            $begin = 1;
        else
            $begin = $array[0];
    else
        $begin = 1;
    if (is_numeric ($array[1]))
        $end = $array[1];
    else
        $end = null;

    return 1;
}

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

    $html = '';
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
