<?php

namespace SRC\Controller;

class TBlockIp {

    const blockSeconds = 60;

    const intervalSeconds = 3;

    const intervalTimes = 3;

    const isAlwaysActive = true;

    const isAlwaysBlock = true;

    const pathActive = 'active';

    const pathBlock = 'block';

    const pathIsAbsolute = false;

    public static $alwaysActive = array(
    '172.16.1.1', 
    );


    public static $alwaysBlock = array(
    '172.16.1.1', 
    );

 
    public static function checkIp() {

        $ip_address = self::_getIp();

        if (in_array($ip_address, self::$alwaysActive) && self::isAlwaysActive) {
            return;
        }

        if (in_array($ip_address, self::$alwaysBlock) && self::isAlwaysBlock) {
            echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
            echo '<html xmlns="http://www.w3.org/1999/xhtml">';
            echo '<head>';
            echo '<title>Вы заблокированы</title>';
            echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
            echo '</head>';
            echo '<body>';
            echo '<p style="background:#ccc;border:solid 1px #aaa;margin:30px au-to;padding:20px;text-align:center;width:700px">';
            echo 'Вы заблокированы администрацией ресурса.<br />';
            echo '</p>';
            echo '</body>';
            echo '</html>';
            exit;
        }

        $path_active = self::pathActive;
        $path_block = self::pathBlock;

        if (!self::pathIsAbsolute) {
            $path_active = str_replace('\\' , '/', dirname(__FILE__) . '/' . $path_active . '/');
            $path_block = str_replace('\\' , '/', dirname(__FILE__) . '/' . $path_block . '/');
        }

        $is_active = false;
        if ($dir = opendir($path_active)) {
            while (false !== ($filename = readdir($dir))) {
                if (preg_match('#^(\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3})_(\d+)$#', $filename, $matches)) {
                    if ($matches[2] >= time() - self::intervalSeconds) {
                        if ($matches[1] == $ip_address) {
                            $times = intval(trim(file_get_contents($path_active . $filename)));
                            if ($times >= self::intervalTimes - 1) {
                                touch($path_block . $filename);
                                unlink($path_active . $filename);
                            } else {
                                file_put_contents($path_active . $filename, $times + 1);
                            }
                            $is_active = true;
                        }
                    } else {
                        unlink($path_active . $filename);
                    }
                }
            }
            closedir($dir);
        }

        $is_block = false;
        if ($dir = opendir($path_block)) {
            while (false !== ($filename = readdir($dir))) {
                if (preg_match('#^(\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3})_(\d+)$#', $filename, $matches)) {
                    if ($matches[2] >= time() - self::blockSeconds) {
                        if ($matches[1] == $ip_address) {
                            $is_block = true;
                            $time_block = $matches[2] - (time() - self::blockSeconds) + 1;
                        }
                    } else {
                        unlink($path_block . $filename);
                    }
                }
            }
            closedir($dir);
        }

        if ($is_block) {
            header('HTTP/1.0 502 Bad Gateway');
            echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
            echo '<html xmlns="http://www.w3.org/1999/xhtml">';
            echo '<head>';
            echo '<title>502 Bad Gateway</title>';
            echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
            echo '</head>';
            echo '<body>';
            echo '<h1 style="text-align:center">502 Bad Gateway</h1>';
            echo '<p style="background:#ccc;border:solid 1px #aaa;margin:30px au-to;padding:20px;text-align:center;width:700px">';
            echo 'К сожалению, Вы временно заблокированы, из-за частого запроса страниц сайта.<br />';
            echo 'Вам придется подождать. Через ' . $time_block . ' секунд(ы) Вы будете автоматически разблокированы.';
            echo '</p>';
            echo '</body>';
            echo '</html>';
            exit;
        }

        if (!$is_active) {
            touch($path_active . $ip_address . '_' . time());
        }
    }


    private static function _getIp() {

        $ip_address = '127.0.0.1';

        $addrs = array();

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            foreach (array_reverse(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])) as $value) {
                $value = trim($value);
                if (preg_match('#^\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3}$#', $value)) {
                    $addrs[] = $value;
                }
            }
        }
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $addrs[] = $_SERVER['HTTP_CLIENT_IP'];
        }
        if (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
            $addrs[] = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        }
        if (isset($_SERVER['HTTP_PROXY_USER'])) {
            $addrs[] = $_SERVER['HTTP_PROXY_USER'];
        }
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $addrs[] = $_SERVER['REMOTE_ADDR'];
        }

        foreach ($addrs as $value) {
            if (preg_match('#^(\d{1,3}).(\d{1,3}).(\d{1,3}).(\d{1,3})$#', $value, $matches)) {
                $value = $matches[1] . '.' . $matches[2] . '.' . $matches[3] . '.' . $matches[4];
                if ('...' != $value) {
                    $ip_address = $value;
                    break;
                }
            }
        }

        return $ip_address;
    }

}