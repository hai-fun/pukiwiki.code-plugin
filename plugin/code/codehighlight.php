<?php
/**
 * �����ɥϥ��饤�ȵ�ǽ
 * Time-stamp: <07/11/20 09:34:09 sky>
 * Modified:   <09/11/13 ruche>
 * 
 * GPL
 *
 * code.inc.php r 0.6.0_pr3
 */

class CodeHighlight {
    var $id_number;
    var $blockno;
    var $outline;
    var $nestlevel;
    function CodeHighlight()
    {
        if(!defined('CODE_HIGHLIGHT_DEFINE_DONE')){
            define('CODE_HIGHLIGHT_DEFINE_DONE', true); //���٤����̤�ʤ��褦�ˡ�
            // common
            define('PLUGIN_CODE_CODE_CANCEL',          0); // �����̵��������
            define('PLUGIN_CODE_IDENTIFIRE',           2); 
            define('PLUGIN_CODE_SPECIAL_IDENTIFIRE',   3); 
            define('PLUGIN_CODE_STRING_LITERAL',       5); 
            define('PLUGIN_CODE_NONESCAPE_LITERAL',    7); 
            define('PLUGIN_CODE_PAIR_LITERAL',         8); 
            define('PLUGIN_CODE_ESCAPE',              10);
            define('PLUGIN_CODE_COMMENT',             11);
            define('PLUGIN_CODE_COMMENT_WORD',        12); // �����Ȥ�ʸ����ǻϤޤ���
            define('PLUGIN_CODE_FORMULA',             14); 
            // outline
            define('PLUGIN_CODE_BLOCK_START',         20);
            define('PLUGIN_CODE_BLOCK_END',           21);
            define('PLUGIN_CODE_STRING_CONCAT',       24);
            // �Իظ���
            define('PLUGIN_CODE_COMMENT_CHAR',        50); // 1ʸ���ǥ����Ȥȷ���Ǥ�����
            define('PLUGIN_CODE_HEAD_COMMENT',        52); // �����Ȥ���Ƭ�����Τ�� (1ʸ��)  // fortran
            define('PLUGIN_CODE_HEADW_COMMENT',       53); // �����Ȥ���Ƭ�����Τ��   // pukiwiki
            define('PLUGIN_CODE_CHAR_COMMENT',        54); // �����Ȥ���Ƭ�������ıѻ��Ǥ���Τ�� (1ʸ��) // fortran
            define('PLUGIN_CODE_IDENTIFIRE_CHAR',     60); // 1ʸ����̿�᤬���ꤹ����
            define('PLUGIN_CODE_IDENTIFIRE_WORD',     61); // ̿�᤬ʸ����Ƿ��ꤹ����
            define('PLUGIN_CODE_MULTILINE',           62); // ʣ��ʸ����ؤ�̿��

            define('PLUGIN_CODE_CARRIAGERETURN',      70); // ����
            define('PLUGIN_CODE_POST_IDENTIFIRE',     75); // ʸ���θ��äƷ�ޤ�롼��
        }
        $this->blockno = 0;
        $this->outline = Array();// $outline[lineno][nest] $outline[lineno][blockno]�����롣
    }
    //include_once(PLUGIN_DIR.'code/line.php');

    function highlight(& $lang, & $src, & $option, $end = null, $begin = 1) {
        static $id = 0; // �ץ饰���󤬸ƤФ줿���(ID������)
        $this->id_number = ++$id;

        if (strlen($lang) > 16)
            $lang = '';
        
        $option['number']  = (PLUGIN_CODE_NUMBER      && ! $option['nonumber']  || $option['number']);
        $option['outline'] = (PLUGIN_CODE_FOLD        && ! $option['nooutline'] || $option['outline']);
        $option['block']   = (PLUGIN_CODE_FOLDBLOCK   && ! $option['noblock']   || $option['block']);
        $option['literal'] = (PLUGIN_CODE_FOLDLITERAL && ! $option['noliteral'] || $option['literal']);
        $option['comment'] = (PLUGIN_CODE_FOLDCOMMENT && ! $option['nocomment'] || $option['comment']);
        $option['link']    = (PLUGIN_CODE_LINK        && ! $option['nolink']    || $option['link']);

        // mozilla�ζ�����к�
        if($option['number'] || $option['outline']) {
            // �饤��ɽ��������
            $src = preg_replace('/^$/m',' ',$src);
        }
        if (file_exists(PLUGIN_DIR.'code/keyword.'.$lang.'.php')) {
            // ��������ե����뤬ͭ�����
            $data = $this->srcToHTML($src, $lang, $option, $end, $begin);
            $src = '<pre class="code"><code class="'.$lang.'">'.$data['src'].'</code></pre>';
        } else if (file_exists(PLUGIN_DIR.'code/line.'.$lang.'.php')) {
            // �Իظ���������ե����뤬ͭ�����
            $data = $this->lineToHTML($src, $lang, $option, $end, $begin);
            $src = '<pre class="code"><code class="'.$lang.'">'.$data['src'].'</code></pre>';
        } else {
            // PHP �� ̤�������
            $option['outline'] = 0;

            // �Ǹ��;ʬ�ʲ��Ԥ���
            if ($src[strlen($src)-2] == ' ')
                $src = substr($src, 0, -2);
            else
                $src= substr($src, 0, -1);

            if ($option['number']) {
                if ($src[strlen($src)-1] == "\n")
                    $src = substr($src,0,-1);
                if ($end === null || $end < $begin) // �Կ�������
                    $end = substr_count($src, "\n") + $begin;
                $data = array('number' => '');
                $data['number'] = _plugin_code_makeNumber($end, $begin);
            }
            if ('php' == $lang) // PHP��ɸ�ൡǽ��Ȥ�
                $src =  '<pre class="code">'.$this->highlightPHP($src). '</pre>';
            else // ̤�������
                $src =  '<pre class="code"><code class="unknown">' .htmlspecialchars($src). '</code></pre>';
        }
        $option['menu']  = (PLUGIN_CODE_MENU  && ! $option['nomenu']  || $option['menu']);
        $option['menu']  = ($option['menu'] && $option['outline']);

        $menu = '';
        if ($option['menu']) {
            // �������������
            $menu .= '<div class="'.PLUGIN_CODE_HEADER.'menu">';
            if ($option['outline']) {
                // �����ȥ饤��Υ�˥塼
                if ($option['block']) {
                    $_code_expand = _('���٤Ƴ���');
                    $_code_short = _('���٤��Ĥ���');
                    $menu .= '<img src="'.PLUGIN_CODE_OUTLINE_OPEN_FILE.'" style="cursor: hand" alt="'.$_code_expand.'" title="'.$_code_expand.'" '
                        .'onclick="javascript:code_classname(\''.PLUGIN_CODE_HEADER.$this->id_number.'\','.$data['blocknum'].',\'\',\'code_block\',\''.IMAGE_DIR.'\')" '
                        .'onkeypress="javascript:code_classname(\''.PLUGIN_CODE_HEADER.$this->id_number.'\','.$data['blocknum'].',\'\',\'code_block\',\''.IMAGE_DIR.'\')" />'
                        .'<img src="'.PLUGIN_CODE_OUTLINE_CLOSE_FILE.'" style="cursor: hand" alt="'.$_code_short.'" title="'.$_code_short.'" '
                        .'onclick="javascript:code_classname(\''.PLUGIN_CODE_HEADER.$this->id_number.'\','.$data['blocknum'].',\'none\',\'code_block\',\''.IMAGE_DIR.'\')" '
                        .'onkeypress="javascript:code_classname(\''.PLUGIN_CODE_HEADER.$this->id_number.'\','.$data['blocknum'].',\'none\',\'code_block\',\''.IMAGE_DIR.'\')" />';
                }
                /*
                if ($option['comment']) {
                    $menu .=  '<img src="'.PLUGIN_CODE_OUTLINE_CLOSE_FILE.'" style="cursor: hand" alt="'.$_code_short.'" title="'.$_code_short.'" '
                        .'onclick="javascript:code_classname(\''.PLUGIN_CODE_HEADER.$this->id_number.'\','.$data['blocknum'].',\'none\',\'code_comment\',\''.IMAGE_DIR.'\')" '
                        .'onkeypress="javascript:code_classname(\''.PLUGIN_CODE_HEADER.$this->id_number.'\','.$data['blocknum'].',\'none\',\'code_comment\',\''.IMAGE_DIR.'\')" />';
                }
                if ($option['literal']) {
                    $menu .=  '<img src="'.PLUGIN_CODE_OUTLINE_CLOSE_FILE.'" style="cursor: hand" alt="'.$_code_short.'" title="'.$_code_short.'" '
                        .'onclick="javascript:code_classname(\''.PLUGIN_CODE_HEADER.$this->id_number.'\','.$data['blocknum'].',\'none\',\'code_string\',\''.IMAGE_DIR.'\')" '
                        .'onkeypress="javascript:code_classname(\''.PLUGIN_CODE_HEADER.$this->id_number.'\','.$data['blocknum'].',\'none\',\'code_string\',\''.IMAGE_DIR.'\')" />';
                }
                */
            }
            $menu .= '</div>';
        }

        if ($option['number'])
            $data['number'] = '<pre class="'.PLUGIN_CODE_HEADER.'number">'.$data['number'].'</pre>';
        else
            $data['number'] = null;

        if ($option['outline'])
            $data['outline'] = '<pre class="'.PLUGIN_CODE_HEADER.'outline">'.$data['outline'].'</pre>';
        else
            $data['outine'] = null;

        $html = '<div id="'.PLUGIN_CODE_HEADER.$this->id_number.'" class="'.PLUGIN_CODE_HEADER.'table">'
            . $menu
            . _plugin_code_column($src, $data['number'], $data['outline'])
            . '</div>';

        return $html;
    }

    /**
     * ���δؿ���1���ڤ�Ф�
     * �귿�ե����ޥåȤ���ĸ�����
     */
    function getline(& $string){
        $line = '';
        if(! isset($string[0])) return false;
        $pos = strpos($string, "\n"); // ���Ԥޤ��ڤ�Ф�
        if ($pos === false) { // ���Ĥ���ʤ��Ȥ��Ͻ����ޤ�
            $line = $string;
            $string = '';
        } else {
            $line = substr($string, 0, $pos+1);
            $string = substr($string, $pos+1);
        }
        return $line;
    }

    /**
     * ���δؿ��Ϲ�Ƭ��ʸ����Ƚ�ꤷ�Ʋ��ϡ��Ѵ�����
     * �귿�ե����ޥåȤ���ĸ�����
     */
    function lineToHTML(& $string, & $lang, & $option, $end = null, $begin = 1) {

        // �ơ��֥른�����ѥϥå���
        $switchHash = Array();
        $capital = false; // ��ʸ����ʸ������̤��ʤ�

        // ����
        $switchHash["\n"] = PLUGIN_CODE_CARRIAGERETURN;
        // ����������ʸ��
        $switchHash['\\'] = PLUGIN_CODE_ESCAPE;
        // ���̻ҳ���ʸ��
        for ($i = ord('a'); $i <= ord('z'); ++$i)
            $switchHash[chr($i)] = PLUGIN_CODE_IDENTIFIRE;
        for ($i = ord('A'); $i <= ord('Z'); ++$i)
            $switchHash[chr($i)] = PLUGIN_CODE_IDENTIFIRE;
        $switchHash['_'] = PLUGIN_CODE_IDENTIFIRE;

        // ʸ���󳫻�ʸ��
        $switchHash['"'] = PLUGIN_CODE_STRING_LITERAL;
        $linemode = false; // �������Ϥ��뤫�ݤ�

        $str_len = strlen($string);
        // ʸ��->html�Ѵ��ѥϥå���
        $htmlHash = Array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;', '&' => '&amp;');
        $spaceHash = Array("\t" => PLUGIN_CODE_WIDTHOFTAB, ' ' => ' ');
 
        // ��������ե������ɤ߹���
        include(PLUGIN_DIR.'code/line.'.$lang.'.php');
        
        $html = '';   // ���Ϥ����HTML�������դ�������
        $num_of_line = $begin-1;  // �Կ��򥫥����
        $this->nestlevel = 1;// �ͥ���
        $this->blockno = 0;// �����ܤΥ֥�å�����ID���ˡ����ˤ��뤿����Ѥ���
        $terminate = array();  // �֥�å���üʸ��
        $str_continue = 0; // �֥�å��μ���

        $line = $this->getline($string);
        while($line !== false) {
            ++$num_of_line;
            while (isset($line[strlen($line)-2]) && $line[strlen($line)-2] == '\\') {
                // ����������������ʸ���ʤ鼡�ιԤ��ڤ�Ф�
                ++$num_of_line;
                $line .= $this->getline($string);
            }
            // ��Ƭʸ����Ƚ��
            $hash_tmp = isset($switchHash[$line[0]]) ? $switchHash[$line[0]] : '';
            switch ($hash_tmp) {

            case PLUGIN_CODE_CHAR_COMMENT:
            case PLUGIN_CODE_HEAD_COMMENT:
            case PLUGIN_CODE_COMMENT_CHAR:
                // ��Ƭ��1ʸ���ǥ����Ȥ�Ƚ�ǤǤ�����
                $line = htmlspecialchars(substr($line,0,-1), ENT_QUOTES);
                if ($option['link']) 
                    $line = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
                                         '<a href="$0">$0</a>',$line);
                
                if($str_continue != PLUGIN_CODE_COMMENT) {
                    if ($str_continue != 0) {
                        $this->endRegion($num_of_line);
                        $html .= '</span>';
                    }
                    if ($option['comment']) {
                        $this->beginRegion($num_of_line);
                        // �����ȥ饤���Ĥ�������ɽ�������������������
                        //$html .= '<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'_img" display="none"></span>';
                        $html .= '<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'" class="'.PLUGIN_CODE_HEADER.'comment">';
                        $str_continue = PLUGIN_CODE_COMMENT;
                    }
                }
                // html���ɲ�
                $html .= '<span class="'.PLUGIN_CODE_HEADER.'comment">'.$line.'</span>'."\n";

                $line = $this->getline($string); // next line
                continue 2;
                
            case PLUGIN_CODE_HEADW_COMMENT:
            case PLUGIN_CODE_COMMENT_WORD:
                // 2ʸ���ʾ�Υѥ����󤫤�Ϥޤ륳����
                if (strncmp($line, $commentpattern, strlen($commentpattern)) == 0) {
                    $line = htmlspecialchars(substr($line,0,-1), ENT_QUOTES);
                    if ($option['link']) 
                        $line = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
                                             '<a href="$0">$0</a>',$line);
                    if($str_continue != PLUGIN_CODE_COMMENT) {
                        if ($str_continue != 0) {
                            $this->endRegion($num_of_line-1);
                            $html .= '</span>';
                        }
                        if ($option['comment']) {
                            $this->beginRegion($num_of_line);
                            // �����ȥ饤���Ĥ�������ɽ�������������������
                            //$html .= '<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'_img" display="none"></span>';
                            $html .= '<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'" class="'.PLUGIN_CODE_HEADER.'comment">';
                            $str_continue = PLUGIN_CODE_COMMENT;
                        }
                    }
                    // html���ɲ�
                    $html .= '<span class="'.PLUGIN_CODE_HEADER.'comment">'.$line.'</span>'."\n";
                    
                    $line = $this->getline($string); // next line
                    continue 2;
                }
                // �����ȤǤϤʤ�
                break;

            case PLUGIN_CODE_IDENTIFIRE_CHAR:
                // ��Ƭ��1ʸ������̣����Ĥ��
                if ($str_continue != 0) {
                    $this->endRegion($num_of_line);
                    $html .= '</span>';
                    $str_continue = 0;
                }
                $index = $code_keyword[$line[0]];
                $line = htmlspecialchars($line, ENT_QUOTES);
                if ($option['link']) 
                    $line = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
                                         '<a href="$0">$0</a>',$line);
                if ($index != '')
                    $html .= '<span class="'.PLUGIN_CODE_HEADER.$code_css[$index-1].'">'.$line.'</span>';
                else
                    $html .= $line;

                $line = $this->getline($string); // next line
                continue 2;

            case PLUGIN_CODE_IDENTIFIRE_WORD:
                if ($str_continue != 0) {
                    $this->endRegion($num_of_line);
                    $html .= '</span>';
                    $str_continue = 0;
                }

                if (strlen($line) < 2 && $line[0] == ' ') break; // ����Ƚ��
                // ��Ƭ�Υѥ������Ĵ�٤�
                foreach ($code_identifire[$line[0]] as $pattern) {
                    if (strncmp($line, $pattern, strlen($pattern)) == 0) {
                        $index = $code_keyword[$pattern];
                        // html���ɲ�
                        $line = htmlspecialchars($line, ENT_QUOTES);
                        if ($option['link']) 
                            $line = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
                                                 '<a href="$0">$0</a>',$line);
                        if ($index != '')
                            $html .= '<span class="'.PLUGIN_CODE_HEADER.$code_css[$index-1].'">'.$line.'</span>';
                        else
                            $html .= $line;
                        
                        $line = $this->getline($string); // next line
                        continue 3;
                    }
                }
                // ��Ƭ��1ʸ������̣����Ĥ�Τ�Ƚ��
                $index = $code_keyword[$line[0]];
                if ($index != '') {
                    $line = htmlspecialchars($line, ENT_QUOTES);
                    $html .= '<span class="'.PLUGIN_CODE_HEADER.$code_css[$index-1].'">'.$line.'</span>';
                    $line = $this->getline($string); // next line
                    continue 2;
                }
                else // IDENTIFIRE�ǤϤʤ�
                    break;

            case PLUGIN_CODE_POST_IDENTIFIRE:
                if ($str_continue != 0) {
                    $this->endRegion($num_of_line);
                    $html .= '</span>';
                    $str_continue = 0;
                }
                // ���������Υѥ�����򸡺�����
                // make�Υ������å��� ���̻�(����ե��٥åȤ���ϤޤäƤ���)
                $str_pos = strpos($line, $post_identifire);
                if ($str_pos !== false) {
                    $result  = htmlspecialchars(substr($line, 0, $str_pos), ENT_QUOTES);
                    $result2 = htmlspecialchars(substr($line, $str_pos+1), ENT_QUOTES);
                    $html .= '<span class="'.PLUGIN_CODE_HEADER.'target">'.$result.$post_identifire.'</span>'
                        .'<span class="'.PLUGIN_CODE_HEADER.'src">'.$result2.'</span>';
                    $line = $this->getline($string); // next line
                    continue 2;
                }
                else // �������ʤ�
                    break;

            case PLUGIN_CODE_MULTILINE:
                // ʣ���Ԥ��ϤäƸ��̤���Ļ���
                if ($str_continue != 0) {
                    $this->endRegion($num_of_line);
                    $html .= '</span>';
                    $str_continue = 0;
                }

                $index = $code_keyword[$line[0]];
                $src = rtrim(htmlspecialchars($line, ENT_QUOTES));
                if ($option['link']) 
                    $src = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
                                        '<a href="$0">$0</a>',$src);
                if ($index != '')
                    $html .= '<span class="'.PLUGIN_CODE_HEADER.$code_css[$index-1].'">'.$src;
                else
                    $html .= $src;
                // outline
                ++$this->blockno;
                $html .= '<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'_img" display="none"></span>'
                    ."\n"
                    .'<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'">';

                $line = $this->getline($string);
                $multilines = 0;
                $result = '';
                while (in_array($line[0], $multilineEOL) === false && $line !== false) {
                    // ���̤��ϰ�����������
                    $src = htmlspecialchars($line, ENT_QUOTES);
                    if ($option['link']) 
                        $src = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
                                             '<a href="$0">$0</a>',$src);
                    $result .= $src;
                    ++$multilines;
                    $line = $this->getline($string);
                }
                if ($multilines >= 1) {
                    if(! isset($this->outline[$num_of_line])) {
                        $this->outline[$num_of_line]=Array();
                    }
                    array_push($this->outline[$num_of_line],Array('nest'=>($this->nestlevel+1), 'blockno'=>$this->blockno, 'state'=>1));
                    $num_of_line += $multilines;
                    $this->outline[$num_of_line] = Array();
                    array_push($this->outline[$num_of_line],Array('nest'=>$this->nestlevel,'blockno'=>0, 'state'=>1));
                }
                
                $html .= $result;
                if ($index != '')
                    $html .= '</span>';
                $html .= '</span>';
                continue 2;

            case PLUGIN_CODE_BLOCK_START:
                // �ü�ʸ������Ϥޤ뼱�̻�
                if ($str_continue != 0) {
                    $this->endRegion($num_of_line);
                    $html .= '</span>';
                    $str_continue = 0;
                }
                // ����ʸ�����ѻ���Ƚ��
                if (! ctype_alpha($line[1])) break;
                $result = substr($line, 1);
                preg_match('/[A-Za-z0-9_\-]+/', $result, $matches);
                $str_pos = strlen($matches[0]);
                $result = substr($line, 0, $str_pos+1);
                $r_result = rtrim(substr($line, $str_pos+1));
                // html���ɲ�
                if($capital)
                    $index = $code_keyword[strtolower($result)];// ��ʸ����ʸ������̤��ʤ�
                else
                    $index = $code_keyword[$result];
                $result = htmlspecialchars($result, ENT_QUOTES);
                 if ($index != '')
                    $html .= '<span class="'.PLUGIN_CODE_HEADER.$code_css[$index-1].'">'.$result.'</span>';
                else
                    $html .= $result;

                if ($option['block'] && $r_result[strlen($r_result)-1] == '{') {
                    $this->beginRegion($num_of_line, 1);
                    $terminate[$this->nestlevel] = strlen($r_result) - strpos($r_result,'{');
                    $html .= $r_result;
                    // �����ȥ饤���Ĥ�������ɽ�������������������
                    $html .= '<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'_img" display="none"></span>';
                    $html .= '<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'" style="display:'.$display.'" class="'.PLUGIN_CODE_HEADER.'block">'."\n";
                } else 
                    $html .= $r_result."\n";

                $line = $this->getline($string); // next line
                continue 2;

            case PLUGIN_CODE_BLOCK_END:
                // outline ɽ����λʸ�� for PukiWikis
                if ($option['block'] && $terminate[$this->nestlevel] == strlen(trim($line))) {
                    $this->endRegion($num_of_line);
                    $html .= '</span>';
                }
                $html .= $line;
                $line = $this->getline($string); // next line
                continue 2;

             default:
                // �������Ϥ�����HTML���ɲä��� (diff)
                if($linemode) {
                    $line = htmlspecialchars($line, ENT_QUOTES);
                    if ($option['link']) 
                        $html .= preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
                                              '<a href="$0">$0</a>',$line);
                    
                    $line = $this->getline($string); // next line
                    continue 2;
                }
            } //switch
                
            // ����β��� 1ʸ�����Ĳ��Ϥ���
            $str_len = strlen($line);
            $str_pos = 0;
            if ($str_len == $str_pos) $code = false; else $code = $line[$str_pos++];// getc
            while($code !== false) {
                switch ($switchHash[$code]) {
                case PLUGIN_CODE_CHAR_COMMENT: // ��Ƭ�ʳ��Ǥϥ����ȤˤϤʤ�ʤ� (fortran)
                case PLUGIN_CODE_IDENTIFIRE:
                    // ���̻�(����ե��٥åȤ���ϤޤäƤ���)
                    if ($str_continue != 0) {
                        $this->endRegion($num_of_line);
                        $html .= '</span>';
                        $str_continue = 0;
                    }
                    // �����¤�Ĺ�����̻Ҥ�����
                    --$str_pos; // ���顼�����������ʤ�����preg_match��ɬ�����Ĥ���褦�ˤ���
                    $result = substr($line, $str_pos); 
                    preg_match('/[A-Za-z0-9_\-]+/', $result, $matches);
                    $str_pos += strlen($matches[0]);
                    $result = $matches[0];
                    
                    if($capital)
                        $index = $code_keyword[strtolower($result)];// ��ʸ����ʸ������̤��ʤ�
                    else
                        $index = $code_keyword[$result];
                    $result = htmlspecialchars($result, ENT_QUOTES);

                    if ($index!='')
                        $html .= '<span class="'.PLUGIN_CODE_HEADER.$code_css[$index-1].'">'.$result.'</span>';
                    else
                        $html .= $result;
                    
                    // ���θ����Ѥ��ɤ߹���
                    if ($str_len == $str_pos) $code = false; else $code = $line[$str_pos++]; // getc
                    continue 2;
                    
                case PLUGIN_CODE_SPECIAL_IDENTIFIRE:
                    // �ü�ʸ������Ϥޤ뼱�̻�
                    // ����ʸ�����ѻ���Ƚ��
                    if ($str_continue != 0) {
                        $this->endRegion($num_of_line);
                        $html .= '</span>';
                        $str_continue = 0;
                    }
                    if (! ctype_alpha($line[$str_pos])) break;
                    $result = substr($line, $str_pos);
                    preg_match('/[A-Za-z0-9_\-]+/', $result, $matches);
                    $str_pos += strlen($matches[0]);
                    $result = $code.$matches[0];
                    // html���ɲ�
                    if($capital)
                        $index = $code_keyword[strtolower($result)];// ��ʸ����ʸ������̤��ʤ�
                    else
                        $index = $code_keyword[$result];
                    $result = htmlspecialchars($result, ENT_QUOTES);
                    if ($index != '')
                        $html .= '<span class="'.PLUGIN_CODE_HEADER.$code_css[$index-1].'">'.$result.'</span>';
                    else
                        $html .= $result;
                    
                    // ���θ����Ѥ��ɤ߹���
                    if ($str_len == $str_pos) $code = false; else $code = $line[$str_pos++]; // getc
                    continue 2;

                case PLUGIN_CODE_STRING_LITERAL:
                case PLUGIN_CODE_NONESCAPE_LITERAL:
                    if($str_continue != PLUGIN_CODE_STRING_LITERAL) {
                        if ($str_continue != 0) {
                            $this->endRegion($num_of_line);
                            $html .= '</span>';
                        }
                        if ($option['literal']) {
                            $this->beginRegion($num_of_line);
                            // �����ȥ饤���Ĥ�������ɽ�������������������
                            //$html .= '<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'_img" display="none"></span>';
                            $html .= '<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'" class="'.PLUGIN_CODE_HEADER.'string">';
                            $str_continue = PLUGIN_CODE_STRING_LITERAL;
                        }
                    }
                    // ʸ�����ƥ������� //���ߥ��������פ���ɬ�פ�̵��
                    $pos = $str_pos;
                    $result = substr($line, $str_pos);
                    $pos1 = strpos($result, $code); // ʸ����λʸ������
                    if ($pos1 === false) { // ���򸡺�����
                        $pos1 = strpos($string, $code); // ʸ����λʸ������
                        if ($pos1 === false) { // ʸ���󤬽����ʤ��ä��Τ�����ʸ����Ȥ���
                            $num_of_line += substr_count($string, "\n")+1; // �饤����������
                            // �Ǹ��;ʬ�ʲ��Ԥ���
                            if ($string[strlen($string)-2] == ' ')
                                $string = substr($string, 0, -2);
                            else
                                $string = substr($string, 0, -1);
                            $result = $code.$result.$string;
                            $str_len = 0;
                            $str_pos = 0;
                            $string = '';
                            $code = false;
                            $result = htmlspecialchars($result, ENT_QUOTES);
                            if ($option['link']) 
                                $result = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
                                                       '<a href="$0">$0</a>',$result);
                            $html .= '<span class="'.PLUGIN_CODE_HEADER.'string">'.$result.'</span>';
                            break 3;
                        } else {
                            $result = $code.$result.substr($string, 0, $pos1+2);
                            $num_of_line += substr_count($result, "\n")-1; // �饤����������
                            $string = substr($string, $pos1+2);
                            if ($string[$pos1+2] == "\n") {
                                $str_len = 0;
                                $str_pos = 0;
                                $code = false;
                            } else {
                                $code = $line[$str_pos++]; // getc
                                $line = $this->getline($string);
                                $str_len = strlen($line);
                                $str_pos = 0;
                            }
                        }
                    } else {
                        $str_pos += $pos1 + 1;
                        $result = $code.substr($line, $pos, $str_pos - $pos);
                    }
                    // html���ɲ�
                    $result = htmlspecialchars($result, ENT_QUOTES);
                    if ($option['link']) 
                        $result = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
                                               '<a href="$0">$0</a>',$result);
                    $html .= '<span class="'.PLUGIN_CODE_HEADER.'string">'.$result.'</span>';
                    
                    // ���θ����Ѥ��ɤ߹���
                    if ($str_len == $str_pos) $code = false; else $code = $line[$str_pos++]; // getc
                    continue 2;

                case PLUGIN_CODE_COMMENT_CHAR: // 1ʸ���Ƿ�ޤ륳����
                    $line = substr($line, $str_pos-1, $str_len-$str_pos);
                    $line = htmlspecialchars($line, ENT_QUOTES);
                    if ($option['link']) 
                        $line = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
                                             '<a href="$0">$0</a>',$line);
                    if ($str_continue != 0) {
                        $this->endRegion($num_of_line);
                        $html .= '</span>';
                    }
                    $html .= '<span class="'.PLUGIN_CODE_HEADER.'comment">'.$line.'</span>'."\n";
                    
                    $line = $this->getline($string); // next line
                    continue 3;
                } //switch
                // ����¾��ʸ��
                $result = $spaceHash[$code];
                if ($result) {
                    $html .= $result;
                } else {
                    if ($str_continue != 0) {
                        $this->endRegion($num_of_line);
                        $html .= '</span>';
                        $str_continue = 0;
                    }
                    $result = isset($htmlHash[$code]) ? $htmlHash[$code] : '';
                    if ($result) {
                        $html .= $result;
                    } else {
                        $html .= $code;
                    }
                }
                // ���θ����Ѥ��ɤ߹���
                if ($str_len == $str_pos) $code = false; else $code = $line[$str_pos++]; // getc

            }// char
            $line = $this->getline($string); // next line
        } // line
        
        // �Ǹ��;ʬ�ʲ��Ԥ���
        if ($html[strlen($html)-2] == ' ') {
            $html = substr($html, 0, -2);
            --$num_of_line;
        } else {
            $html = substr($html, 0, -1);
        }
        
        $html = array('src' => $html, 'number' => '', 'outline' => '', 'blocknum' => $this->blockno);
        if($option['outline']) 
            return $this->makeOutline($html, $option['number'],$num_of_line-1, $begin); // �Ǹ�˲��Ԥ����������� -1
        if($option['number']) $html['number'] = _plugin_code_makeNumber($num_of_line-1, $begin); 
        return $html;
    }
    /**
      * ����������HTML����
      */
    function srcToHTML(& $string, & $lang, & $option, $end = null, $begin = 1) {
        // �ơ��֥른�����ѥϥå���
        $switchHash = Array();
        $capital = 0; // ��ʸ����ʸ������̤��ʤ�
        $mkoutline = $option['outline'];
        $mknumber  = $option['number'];

        // ����
        $switchHash["\n"] = PLUGIN_CODE_CARRIAGERETURN;

        $switchHash['\\'] = PLUGIN_CODE_ESCAPE;
        // ���̻ҳ���ʸ��
        for ($i = ord('a'); $i <= ord('z'); ++$i)
            $switchHash[chr($i)] = PLUGIN_CODE_IDENTIFIRE;
        for ($i = ord('A'); $i <= ord('Z'); ++$i)
            $switchHash[chr($i)] = PLUGIN_CODE_IDENTIFIRE;
        $switchHash['_'] = PLUGIN_CODE_IDENTIFIRE;

        // ʸ���󳫻�ʸ��
        $switchHash['"'] = PLUGIN_CODE_STRING_LITERAL;

        // ��������ե������ɤ߹���
        $code_space_keyword = Array(); // HACK: ���ڡ����դ��������
        include(PLUGIN_DIR.'code/keyword.'.$lang.'.php');

        // HACK: ������ɹ���ʸ��(����ɽ��)
        $keyword_letters = 'A-Za-z0-9_';
        foreach ((array_keys($code_space_keyword) + array_keys($code_keyword)) as $kwd)
        {
            if (strpos($kwd, '-') !== false)
            {
                // �ϥ��ե󤬻Ȥ��Ƥ��륭����ɤ�����ʤ鹽��ʸ�����ɲ�
                $keyword_letters .= '\-';
                break;
            }
        }

        // ʸ��->html�Ѵ��ѥϥå���
        $htmlHash = Array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;', '&' => '&amp;');
        $spaceHash = Array("\t" => PLUGIN_CODE_WIDTHOFTAB, ' ' => ' ');

        $html = '';
        $str_len = strlen($string);
        $str_pos = 0;
        $num_of_line = $begin;  // �Կ��򥫥����
        $this->nestlevel = 1;// �ͥ���
        $this->blockno = 0;// �����ܤΥ֥�å�����ID���ˡ����ˤ��뤿����Ѥ���
        $terminate = array();  // �֥�å���üʸ��
        $str_continue = 0; // �֥�å��μ���
        $startline = 1; // ��ƬȽ��
        //$indentlevel = 0; // ����ǥ�Ȥο���

        // �ǽ�θ����Ѥ��ɤ߹���
        if ($str_len == $str_pos) $code = false; else $code = $string[$str_pos++];// getc
        while ($code !== false) {

            $hash_tmp = isset($switchHash[$code]) ? $switchHash[$code] : '';
            switch ($hash_tmp) {

            case PLUGIN_CODE_CARRIAGERETURN: // ����
                $startline = 1;
                if ($str_continue == PLUGIN_CODE_STRING_LITERAL) {
                    $result = ltrim(substr($string, $str_pos));
                    $code = $result[0];
                    switch ($switchHash[$code]) {
                    case PLUGIN_CODE_STRING_LITERAL:
                    case PLUGIN_CODE_NONESCAPE_LITERAL:
                    case PLUGIN_CODE_PAIR_LITERAL:
                    case PLUGIN_CODE_STRING_CONCAT:
                        break;
                    default:
                        $this->endRegion($num_of_line);
                        $html .= '</span>';
                        $str_continue = 0;
                    }
                } else if ($str_continue != 0) {
                    $this->endRegion($num_of_line);
                    $html .= '</span>';
                    $str_continue = 0;
                }
                ++$num_of_line;
                $html .="\n";

                // ���θ����Ѥ��ɤ߹���
                if ($str_len == $str_pos) $code = false; else $code = $string[$str_pos++]; // getc
                continue 2;

            case PLUGIN_CODE_ESCAPE:
                $startline = 0;
                if ($str_continue != 0) {
                    $this->endRegion($num_of_line);
                    $html .= '</span>';
                    $str_continue = 0;
                }
                // escape charactor
                $start = $code;
                // Ƚ���Ѥˤ⤦1ʸ���ɤ߹���
                if ($str_len == $str_pos)
                    $code = false;
                else
                    $code = $string[$str_pos++]; // getc
                if (ctype_alnum($code)) {
                    // ʸ��(�ѿ�)�ʤ齪ü�ޤǸ��դ���
                    --$str_pos; // ���顼�����������ʤ�����preg_match��ɬ�����Ĥ���褦�ˤ���
                    $result = substr($string, $str_pos);
                    preg_match('/[A-Za-z0-9_]+/', $result, $matches);
                    $str_pos += strlen($matches[0]);
                    $result = $matches[0];
                } else {
                    // ����ʤ�1ʸ�������ڤ�Ф�
                    $result = $code;
                    if ($code == "\n") ++$num_of_line;
                }
                // html���ɲ�
                $html .= htmlspecialchars($start.$result, ENT_QUOTES);
                
                // ���θ����Ѥ��ɤ߹���
                if ($str_len == $str_pos) $code = false; else $code = $string[$str_pos++]; // getc
                continue 2;
            case PLUGIN_CODE_COMMENT:
                // ������
                --$str_pos;
                $result = substr($string, $str_pos);
                foreach($code_comment[$code] as $pattern) {
                    if (preg_match($pattern[0], $result)) {
                        $pos = strpos($result, $pattern[1]);
                        if ($pos === false) { // ���Ĥ���ʤ��Ȥ��Ͻ����ޤ�
                            $str_pos = $str_len;
                            //$result = $result; �äƤ��Ȥǲ��⤷�ʤ�
                        } else {
                            $pos += $pattern[2];
                            $str_pos += $pos;
                            $result = substr($result, 0, $pos);
                        }
                        // �饤����������
                        $commentlines = substr_count($result,"\n");
                        
                        if ($pattern[1] == "\n") {
                            if($str_continue != PLUGIN_CODE_COMMENT) {
                                if ($str_continue != 0) {
                                    $this->endRegion($num_of_line-1);
                                    $html .= '</span>';
                                }
                                if ($option['comment'] && $startline) {
                                    $this->beginRegion($num_of_line);
                                    // �����ȥ饤���Ĥ�������ɽ�������������������
                                    //$html .= '<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'_img" display="none"></span>';
                                    $html .= '<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'" class="'.PLUGIN_CODE_HEADER.'comment">';
                                    $str_continue = PLUGIN_CODE_COMMENT;
                                }
                            }
                            ++$num_of_line;
                            $startline = 1;
                        } else {
                            if ($str_continue != 0) {
                                $this->endRegion($num_of_line);
                                $html .= '</span>';
                                $str_continue = 0;
                            }
                            if ($option['comment']) {
                                $is_comment = 0;
                                if ($commentlines >= 1) {
                                    if(! isset($this->outline[$num_of_line])) {
                                        $this->outline[$num_of_line]=Array();
                                    }
                                    $this->beginRegion($num_of_line);
                                    $num_of_line += $commentlines;
                                    $this->endRegion($num_of_line);
                                }
                            } 
                            else
                                $num_of_line += $commentlines;
                            $startline = 0;
                        }
                        
                        // html���ɲ�
                        $result = str_replace("\t", PLUGIN_CODE_WIDTHOFTAB, htmlspecialchars($result, ENT_QUOTES));
                        if ($option['link']) 
                            $result = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
                                                   '<a href="$0">$0</a>',$result);
                        // �����ȥ饤���Ĥ�������ɽ�������������������
                        //$html .= '<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'_img" display="none"></span>';
                        $html .= '<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'" class="'.PLUGIN_CODE_HEADER.'comment">'
                            .$result.'</span>';
                        // ���θ����Ѥ��ɤ߹���
                        if ($str_len == $str_pos) $code = false; else $code = $string[$str_pos++]; // getc
                        continue 3;
                    }
                }
                // �����ȤǤϤʤ�
                ++$str_pos;
                break;
                
            case PLUGIN_CODE_COMMENT_WORD:
                // ʸ���󤫤�Ϥޤ륳����
                
                // �����¤�Ĺ�����̻Ҥ�����
                --$str_pos;
                $result = substr($string, $str_pos);
                foreach($code_comment[$code] as $pattern) {
                    if (preg_match($pattern[0], $result)) {
                        $pos = strpos($result, $pattern[1]);
                        if ($pos === false) { // ���Ĥ���ʤ��Ȥ��Ͻ����ޤ�
                            $str_pos = $str_len;
                            //$result = $result; �äƤ��Ȥǲ��⤷�ʤ�
                        } else {
                            $pos += $pattern[2];
                            $str_pos += $pos;
                            $result = substr($result, 0, $pos);
                        }

                        if($str_continue != PLUGIN_CODE_COMMENT) {
                            if ($str_continue != 0) {
                                $this->endRegion($num_of_line);
                                $html .= '</span>';
                            }
                            if ($option['comment'] && $startline) {
                                $this->beginRegion($num_of_line);
                                // �����ȥ饤���Ĥ�������ɽ�������������������
                                //$html .= '<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'_img" display="none"></span>';
                                $html .= '<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'" class="'.PLUGIN_CODE_HEADER.'comment">';
                                $str_continue = PLUGIN_CODE_COMMENT;
                            }
                        }
                        ++$num_of_line;
                        $startline = 1;
                        // html���ɲ�
                        $result = str_replace("\t", PLUGIN_CODE_WIDTHOFTAB, htmlspecialchars($result, ENT_QUOTES));
                        if ($option['link']) 
                            $result = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
                                                   '<a href="$0">$0</a>',$result);
                        $html .= '<span class="'.PLUGIN_CODE_HEADER.'comment">'.$result.'</span>';
                        // ���θ����Ѥ��ɤ߹���
                        if ($str_len == $str_pos) $code = false; else $code = $string[$str_pos++]; // getc
                        continue 3;
                    }
                }
                ++$str_pos;
                // �����ȤǤʤ����ʸ���� (break ��Ȥ�ʤ�)
            case PLUGIN_CODE_IDENTIFIRE:
                // ���̻�(����ե��٥åȤ���ϤޤäƤ���)

                // HACK: ���ڡ����դ�������� ->
                $existSpaceKeyword = (count($code_space_keyword) > 0);
                $loopCount = $existSpaceKeyword ? 3 : 1;
                $str_pos_base = $str_pos - 1;
                $str_base = substr($string, $str_pos_base);

                for ($loopIdx = 0; $loopIdx < $loopCount; ++$loopIdx) {
                    // �����¤�Ĺ�����̻Ҥ�����
                    $str_pos = $str_pos_base;
                    $findSp = ($existSpaceKeyword && $loopIdx < 2);
                    if ($findSp) {
                        // �ޤ��ϥ��ڡ����դ�������ɤ�Ĵ�٤�
                        // $loopIdx == 0 �ʤ饹�ڡ���2�ġ� $loopIdx == 1 �ʤ饹�ڡ���1��
                        $regText = '^[' . $keyword_letters . ']+\s+[' . $keyword_letters . ']+';
                        if ($loopIdx == 0) { $regText .= '\s+[' . $keyword_letters . ']+'; }
                        if (!preg_match('/' . $regText . '/', $str_base, $matches)) { continue; }
                    }
                    else {
                        preg_match('/^[' . $keyword_letters . ']+/', $str_base, $matches);
                    }
                    $str_pos += strlen($matches[0]);
                    $result = $matches[0];

                    // ������ɺ���
                    $keyText = '';
                    $keywords = Array();
                    if ($findSp) {
                        // ʣ���Υ��ڡ�����1�ĤˤޤȤ�ƥ�����ɤȤ���
                        $keyText = preg_replace('/([' . $keyword_letters . ']+)\s+/', '\1 ', $result);
	                    $keywords = $code_space_keyword;
                    } else {
                        $keyText = $result;
	                    $keywords = $code_keyword;
                    }
                    if($capital) {
                        // ��ʸ������ʸ������̤��ʤ�
                        $keyText = strtolower($keyText);
                    }

                    // ������ɸ���
                    $index = isset($keywords[$keyText]) ? $keywords[$keyText] : '';

                    // �ե����ޥå�
                    $result = htmlspecialchars($result, ENT_QUOTES);

                    // ���Υ�����ɤ��Ĥ���
                    if ($str_continue != 0) {
                        $this->endRegion($num_of_line);
                        $html .= '</span>';
                        $str_continue = 0;
                    }

                    // begin outline
                    $htmlAdded = true;
                    if ($option['block'] && isset($outline_def[$keyText])) {
                        $status = $outline_def[$keyText];
                        if ($option['outline'] && ! $status[1])
                            $state = 0;
                        else
                            $state = 1;
                        $display = $state?'':'none';
                        if ($status[2] != 'startline' || $startline != 0) {
                            $this->beginRegion($num_of_line, $state);
                            $terminate[$this->nestlevel] = $status[0];
                            $html .= '<span class="'.PLUGIN_CODE_HEADER.$code_css[$index-1].'">'.$result.'</span>'
                                .'<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'_img" display="none"></span>'
                                .'<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'" class="'.PLUGIN_CODE_HEADER.'block" style="display:'.$diplay.'">';
                        } else {
                            $html .= '<span class="'.PLUGIN_CODE_HEADER.$code_css[$index-1].'">'.$result.'</span>';
                        }
                    } else if (isset($terminate[$this->nestlevel]) && $keyText == $terminate[$this->nestlevel]) {
                        $this->endRegion($num_of_line);
                        $html .= '</span>';
                        $html .= '<span class="'.PLUGIN_CODE_HEADER.$code_css[$index-1].'">'.$result.'</span>';
                        //$pos1 = strpos($result2, "\n");
                        // end outline
                    } else if ($index != '') {
                        $html .= '<span class="'.PLUGIN_CODE_HEADER.$code_css[$index-1].'">'.$result.'</span>';
                    } else if (!$findSp) {
                        $html .= $result;
                    } else {
                        $htmlAdded = false;
                    }

                    // HTML�ɲúѤߤʤ��ȴ����
                    if ($htmlAdded) { break; }
                }
                // <- ���ڡ����դ��������

                $startline = 0;
                // ���θ����Ѥ��ɤ߹���
                if ($str_len == $str_pos) $code = false; else $code = $string[$str_pos++]; // getc
                continue 2;
                
            case PLUGIN_CODE_SPECIAL_IDENTIFIRE:
                // �ü�ʸ������Ϥޤ뼱�̻�
                // ����ʸ�����ѻ���Ƚ��
                if (! ctype_alpha($string[$str_pos])) break;
                $result = substr($string, $str_pos);
                preg_match('/[A-Za-z0-9_\-]+/', $result, $matches);
                $str_pos += strlen($matches[0]);
                $result = $code.$matches[0];
                // html���ɲ�
                if($capital)
                    $index = $code_keyword[strtolower($result)];// ��ʸ����ʸ������̤��ʤ�
                else
                    $index = $code_keyword[$result];
                $result = htmlspecialchars($result, ENT_QUOTES);
                
                if ($str_continue != 0) {
                    $this->endRegion($num_of_line);
                    $html .= '</span>';
                    $str_continue = 0;
                }
                // begin outline
                if ($option['block'] && isset($outline_def[$result])) {
                    $status = $outline_def[$result];
                    if ($option['outline'] && ! $status[1])
                        $state = 0;
                    else
                        $state = 1;
                    $display = $state ? '' : 'none';

                    $this->beginRegion($num_of_line, $state);
                    $terminate[$this->nestlevel] = $status[0];

                    $html .= '<span class="'.PLUGIN_CODE_HEADER.$code_css[$index-1].'">'.$result.'</span>'
                        .'<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'_img" display="none"></span>'
                        .'<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'" class="'.PLUGIN_CODE_HEADER.'block" style="display:'.$display.'">';
                } else if ($result == $terminate[$this->nestlevel]) {
                    $result2 = substr($string, $str_pos);
                    $html .= '</span>';
                    $html .= '<span class="'.PLUGIN_CODE_HEADER.$code_css[$index-1].'">'.$result.'</span>';
                    //$pos1 = strpos($result2, "\n");
                    $this->endRegion($num_of_line);
                    // end outline
                } else if ($index!='')
                    $html .= '<span class="'.PLUGIN_CODE_HEADER.$code_css[$index-1].'">'.$result.'</span>';
                else
                    $html .= $result;

                $startline = 0;
                // ���θ����Ѥ��ɤ߹���
                if ($str_len == $str_pos) $code = false; else $code = $string[$str_pos++]; // getc
                continue 2;
            case PLUGIN_CODE_STRING_LITERAL:
                // ʸ�����ƥ�������
                $pos = $str_pos;
                do {
                    $result = substr($string, $str_pos);
                    $pos1 = strpos($result, $code); // ʸ����λʸ������
                    if ($pos1 === false) { // ʸ���󤬽����ʤ��ä��Τ�����ʸ����Ȥ���
                        $str_pos = $str_len-1;
                        break;
                    }
                    $str_pos += $pos1 + 1;
                } while ($string[$str_pos-2] == '\\'); // ����ʸ��������������ʸ���ʤ�³����
                $result = $code.substr($string, $pos, $str_pos - $pos);
                if($str_continue != PLUGIN_CODE_STRING_LITERAL) {
                    if ($str_continue != 0) {
                        $this->endRegion($num_of_line);
                        $html .= '</span>';
                    }
                    if ($option['literal'] && $startline) {
                        $this->beginRegion($num_of_line);
                        // �����ȥ饤���Ĥ�������ɽ�������������������
                        //$html .= '<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'_img" display="none"></span>';
                        $html .= '<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'" class="'.PLUGIN_CODE_HEADER.'string">';
                        $str_continue = PLUGIN_CODE_STRING_LITERAL;
                    }
                }
                // �饤����������
                $num_of_line += substr_count($result,"\n");
                $startline = 0;        
                // html���ɲ�
                $result = htmlspecialchars($result, ENT_QUOTES);
                if ($option['link']) 
                    $result = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
                                           '<a href="$0">$0</a>',$result);
                $html .= '<span class="'.PLUGIN_CODE_HEADER.'string">'.$result.'</span>';
                
                // ���θ����Ѥ��ɤ߹���
                if ($str_len == $str_pos) $code = false; else $code = $string[$str_pos++]; // getc
                continue 2;
                
            case PLUGIN_CODE_NONESCAPE_LITERAL:
                // ����������ʸ���ȼ�Ÿ����̵�뤷��ʸ����
                // ʸ�����ƥ�������

                $pos = $str_pos;
                $result = substr($string, $str_pos);
                $pos1 = strpos($result, $code); // ʸ����λʸ������
                if ($pos1 === false) { // ʸ���󤬽����ʤ��ä��Τ�����ʸ����Ȥ���
                    $str_pos = $str_len-1;
                } else {
                    $str_pos += $pos1 + 1;
                }
                $result = $code.substr($string, $pos, $str_pos - $pos);
                if($str_continue != PLUGIN_CODE_STRING_LITERAL) {
                    if ($str_continue != 0) {
                        $this->endRegion($num_of_line);
                        $html .= '</span>';
                    }
                    if ($option['literal'] && $startline) {
                        $this->beginRegion($num_of_line);
                        // �����ȥ饤���Ĥ�������ɽ�������������������
                        //$html .= '<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'_img" display="none"></span>';
                        $html .= '<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'" class="'.PLUGIN_CODE_HEADER.'string">';
                        $str_continue = PLUGIN_CODE_STRING_LITERAL;
                    }
                }
                // �饤����������
                $num_of_line+=substr_count($result,"\n");
                $startline = 0;
                // html���ɲ�
                $result = htmlspecialchars($result, ENT_QUOTES);
                if ($option['link']) 
                    $result = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
                                           '<a href="$0">$0</a>',$result);
                $html .= '<span class="'.PLUGIN_CODE_HEADER.'string">'.$result.'</span>';
                
                // ���θ����Ѥ��ɤ߹���
                if ($str_len == $str_pos) $code = false; else $code = $string[$str_pos++]; // getc
                continue 2;
                
            case PLUGIN_CODE_PAIR_LITERAL:
                $startline = 0;
                // �е���ǰϤޤ줿ʸ�����ƥ������� PostScript
                $pos = $str_pos;
                do {
                    $result = substr($string, $str_pos);
                    $pos1 = strpos($result, $literal_delimiter); // ʸ����λʸ������
                    if ($pos1 === false) { // ʸ���󤬽����ʤ��ä��Τ�����ʸ����Ȥ���
                        $str_pos = $str_len-1;
                        break;
                    }
                    $str_pos += $pos1 + 1;
                } while ($string[$str_pos-2] == '\\'); // ����ʸ��������������ʸ���ʤ�³����
                $result = $code.substr($string, $pos, $str_pos - $pos);
                
                if($str_continue != PLUGIN_CODE_STRING_LITERAL) {
                    if ($str_continue != 0) {
                        $this->endRegion($num_of_line);
                        $html .= '</span>';
                    }
                    if ($option['literal']) {
                        $this->beginRegion($num_of_line);
                        // �����ȥ饤���Ĥ�������ɽ�������������������
                        //$html .= '<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'_img" display="none"></span>';
                        $html .= '<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'" class="'.PLUGIN_CODE_HEADER.'string">';
                        $str_continue = PLUGIN_CODE_STRING_LITERAL;
                    }
                }
                // �饤����������
                $num_of_line+=substr_count($result,"\n");
                
                // html���ɲ�
                $result = htmlspecialchars($result, ENT_QUOTES);
                if ($option['link']) 
                    $result = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
                                           '<a href="$0">$0</a>',$result);
                $html .= '<span class="'.PLUGIN_CODE_HEADER.'string">'.$result.'</span>';
                
                // ���θ����Ѥ��ɤ߹���
                if ($str_len == $str_pos) $code = false; else $code = $string[$str_pos++]; // getc
                continue 2;
            case PLUGIN_CODE_STRING_CONCAT:
                $startline = 0;
                $result = isset($htmlHash[$code]) ? $htmlHash[$code] : '';
                if ($result) 
                    $html .= $result;
                else
                    $html .= $code;
                
                // ���θ����Ѥ��ɤ߹���
                if ($str_len == $str_pos) $code = false; else $code = $string[$str_pos++]; // getc
                continue 2;
            case PLUGIN_CODE_FORMULA:
                $startline = 0;
                if ($str_continue != 0) {
                    $this->endRegion($num_of_line);
                    $html .= '</span>';
                }
                // TeX�ο����˻��� ����Ū�ˤ���������������� 
                $pos = $str_pos;
                $result = substr($string, $str_pos);
                $pos1 = strpos($result, $code); // ʸ����λʸ������
                if ($pos1 === false) { // ʸ���󤬽����ʤ��ä��Τ�����ʸ����Ȥ���
                    $str_pos = $str_len-1;
                } else {
                    $str_pos += $pos1 + 1;
                }
                $result = $code.substr($string, $pos, $str_pos - $pos);
                
                // html���ɲ�
                $result = htmlspecialchars($result, ENT_QUOTES);
                if ($option['link']) 
                    $result = preg_replace('/(s?https?:\/\/|ftp:\/\/|mailto:)([-_.!~*()a-zA-Z0-9;\/:@?=+$,%#]|&amp;)+/',
                                           '<a href="$0">$0</a>',$result);
                $html .= '<span class="'.PLUGIN_CODE_HEADER.'formula">'.$result.'</span>';
                
                // ���θ����Ѥ��ɤ߹���
                if ($str_len == $str_pos) $code = false; else $code = $string[$str_pos++]; // getc
                continue 2;
                
            case PLUGIN_CODE_BLOCK_START:
                $startline = 0;
                if ($str_continue != 0) {
                    $this->endRegion($num_of_line);
                    $html .= '</span>';
                }
                $html .= $code;
                if ($option['block']) {
                    // outline ɽ���ѳ���ʸ�� {, (
                    $this->beginRegion($num_of_line);
                    // �����ȥ饤���Ĥ�������ɽ�������������������
                    $html .= '<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'_img" display="none"></span>'
                        .'<span id="'.PLUGIN_CODE_HEADER.$this->id_number.'_'.$this->blockno.'" class="'.PLUGIN_CODE_HEADER.'block">';
                }
                if ($str_len == $str_pos)
                    $code = false;
                else
                    $code = $string[$str_pos++]; // getc
                continue 2;
                
            case PLUGIN_CODE_BLOCK_END:
                $startline = 0;
                if ($str_continue != 0) {
                    $this->endRegion($num_of_line);
                    $html .= '</span>';
                }
                if ($option['block']) {
                    // outline ɽ����λʸ�� }, )
                    $this->endRegion($num_of_line);
                    $html .= '</span>';
                }
                $html .= $code;
                if ($str_len == $str_pos)
                    $code = false;
                else
                    $code = $string[$str_pos++]; // getc
                continue 2;
                
            }// switch
            // ����¾��ʸ��
            $result = isset($spaceHash[$code]) ? $spaceHash[$code] : '';
            if ($result) {
                  $html .= $result;
            } else {
                $startline = 0;
                if ($str_continue != 0) {
                    $this->endRegion($num_of_line);
                    $html .= '</span>';
                    $str_continue = 0;
                }
                $result = isset($htmlHash[$code]) ? $htmlHash[$code] : '';
                if ($result) {
                    $html .= $result;
                } else {
                    $html .= $code;
                }
            }
            // ���θ����Ѥ��ɤ߹���
            if ($str_len == $str_pos) $code = false; else $code = $string[$str_pos++]; // getc

        }// while

        // �Ǹ��;ʬ�ʲ��Ԥ���
        if ($html[strlen($html)-2] == ' ') {
            $html = substr($html, 0, -2);
            --$num_of_line;
        } else {
            $html = substr($html, 0, -1);
        }
        $html = array('src' => $html, 'number' => '', 'outline' => '', 'blocknum' => $this->blockno);
        if($option['outline']) 
            return $this->makeOutline($html, $option['number'],$num_of_line-1, $begin); // �Ǹ�˲��Ԥ����������� -1
        if($option['number']) $html['number'] = _plugin_code_makeNumber($num_of_line-1, $begin); 
        return $html;
    }

    /**
     * �����ȥ饤��γ�������
     */
    function beginRegion($line, $state=1) 
    {
        ++$this->blockno;
        ++$this->nestlevel;

        if(! isset($this->outline[$line])) {
            $this->outline[$line]=Array();
        }
        array_push($this->outline[$line],Array('nest'=>$this->nestlevel, 'blockno'=>$this->blockno, 'state'=>$state));
    }
    /**
     * �����ȥ饤��ν�λ����
     */
    function endRegion($line) 
    {
        --$this->nestlevel;
        if(! isset($this->outline[$line])) {
            $this->outline[$line]=Array();
            array_push($this->outline[$line],Array('nest'=>$this->nestlevel,'blockno'=>0, 'state'=>1));
        } else {
            $old = array_pop($this->outline[$line]);
            if($old['blockno']!=0 && ($this->nestlevel+1) == $old['nest']) {
            } else {
                if(! is_null($old))
                    array_push($this->outline[$line],$old);
                array_push($this->outline[$line],Array('nest'=>$this->nestlevel,'blockno'=>0, 'state'=>1));
            }
        }
    }

    /**
     * outline �η���
     */
    function makeOutline(& $html, $mknumber, $end, $begin = 1)
    {
        while($this->nestlevel>1) {// �ͥ��Ȥ������Ȥ��Ƥʤ��ä������к�
            $html['src'] .= '</span>';
            --$this->nestlevel;
        }
        $outline = '';
        $number = '';
        $this->nestlevel = 1;

        //$linelen=$line+1;
        $str_len = max(3, strlen(''.$end-1));

        for($i=$begin; $i<=$end; ++$i) {
            $plus = '';
            $plus1 = '';
            $plus2 = '';
            $minus = '';
            if(isset($this->outline[$i])) {
                while(1) {
                    $array = array_shift($this->outline[$i]);
                    if (is_null($array))
                        break;
                    if ($this->nestlevel <= $array['nest']) {
                        $id = $this->id_number.'_'.$array['blockno'];
                        $display = $array['state'] ? '' : 'none';
                        $letter = $array['state'] ? '-' : '+';
                        //$letter = $array['state'] ? '<img src="'.IMAGE_DIR.'treemenu_triangle_open.png"  width="9" height="9" alt="-" title="close"  class="treemenu" />' : '<img src="'.IMAGE_DIR.'treemenu_triangle_close.png" width="9" height="9" alt="+" title="open" class="treemenu" />';
                        if($plus == '')
                            $plus = '<a class="'.PLUGIN_CODE_HEADER.'outline" href="javascript:code_outline(\''
                                .PLUGIN_CODE_HEADER.$id.'\',\''.IMAGE_DIR.'\')" id="'.PLUGIN_CODE_HEADER.$id.'a">'.$letter.'</a>';
                        $plus1 .= '<span id="'.PLUGIN_CODE_HEADER.$id.'o" style="display:'.$display.'">';
                        $plus2 .= '<span id="'.PLUGIN_CODE_HEADER.$id.'n" style="display:'.$display.'">';
                        $this->nestlevel = $array['nest'];
                    } else {
                        $this->nestlevel = $array['nest'];
                        $minus .= '</span>';
                    }
                }
            }
            if($mknumber) {
                $number.= sprintf('%'.$str_len.'d',($i)).$minus.$plus2."\n";
            }
            if($plus == '' && $minus == '') {
                if($this->nestlevel == 1)
                    $outline.=' ';
                else
                    $outline .= '|';
            } else if($plus != '' && $minus == '') {
                $outline.= $plus.$plus1;
            } else if($plus == '' && $minus != '') {
                $outline .=  '!'.$minus;
            } else if($plus != '' && $minus != '') {
                $outline .= $plus.$minus.$plus1;
            }
            $outline.="\n";
        }
        while ($this->nestlevel>1) {// �ͥ��Ȥ������Ȥ��Ƥʤ��ä������к�
            $number .= '</span>';
            $outline .= '</span>';
            --$this->nestlevel;
        }
        $html['number'] = $number;
        $html['outline'] = $outline;
        return $html;
    }

    /**
     * PHP��ɸ��ؿ��ǥϥ��饤�Ȥ���
     */
    function highlightPHP($src) {
        // php������¸�ߤ��뤫��
        $phptagf = 0;

        // HACK: 2009-11-13 ruche -- strpos ���ѹ�
        // ʸ�����ƥ�����php����������ȸ��Ф��Ƥ��ޤ��ޤ��������ݤʤΤ�̵��ġ�
        //if(! strstr($src,'<'.'?php')) {
        if (strpos($src, '<'.'?php') === false) {
            $phptagf = 1;
            $src = '<'.'?php '.$src.' ?'.'>';
        }

        // HACK: 2009-11-13 ruche -- ���Ͻ����ν���(ob_start��ǻ��Ѥ��٤��Ǥʤ���)
        //ob_start(); //���ϤΥХåե���󥰤�ͭ����
        //highlight_string($src); //php��ɸ��ؿ��ǥϥ��饤��
        //$html = ob_get_contents(); //�Хåե������Ƥ�����
        //ob_end_clean(); //�Хåե����ꥢ?
        $html = highlight_string($src, true); // php��ɸ��ؿ��ǥϥ��饤��

        // HACK: 2009-11-13 ruche -- PHP4�Υե����ޥåȤ�PHP5�Υե����ޥåȤ��ִ���pre�����Ѥ��ִ�
        $before = array('<font color="'      , 'font>', "\n", '&nbsp;', '<br /></span>', '<br />');
        $after  = array('<span style="color:', 'span>', ''  , ' '     , "</span>\n"    , "\n"    );
        $html = str_replace($before, $after, $html);

        // �դ���php�������������
        if ($phptagf) {
            // HACK: 2009-11-13 ruche -- php����������˼������ʤ��Х�����
            //$html = preg_replace('/&lt;\?php (.*)?(<font[^>]*>\?&gt;<\/font>|\?&gt;)/m','$1',$html);

            // ľ��/ľ����PHP�����ɤȰ��� span �����ǰϤޤ����⤢�뤿�ᡢ
            // ñ��˰��ֺǽ�γ��ϥ����Ȱ��ֺǸ�ν�ü�������������
            // ���θ塢�֤˲������äƤ��ʤ� span ����������Ф�����������
            $before = array('{^(.*?)&lt;\?php([^ ]*) }', '{ ([^ ]*)\?&gt;(.*?)$}', '{<span[^>]*></span>}');
            $after  = array('$1$2'                     , '$1$2'                  , ''                    );
            $html = preg_replace($before, $after, $html);
        }

        // HACK: 2009-11-13 ruche -- ����ִ����Ƥ���Τ�����
        //$html = str_replace('&nbsp;', ' ', $html);
        //$html = str_replace("\n", '', $html); //$html���"\n"��''���֤�������
        //$html = str_replace('<br />', "\n", $html);
        ////Vaild XHTML 1.1 Patch (thanks miko)
        //$html = str_replace('<font color="', '<span style="color:', $html);
        //$html = str_replace('</font>', '</span>', $html);

        return $html;
    }
}

?>