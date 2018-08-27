<?php

/**
 * The base class for siteStatistics.
 */
class siteStatistics {
    /* @var modX $modx */
    public $modx;
    public $config = array();
    public $need2ClearCache = false;
    public $initialized = false;
    protected $ip_list = array();  //not allowed ip


    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX $modx, array $config = array())
    {
        $this->modx = $modx;
        $corePath = $this->modx->getOption('sitestatistics_core_path', $config, $this->modx->getOption('core_path') . 'components/sitestatistics/');
        $assetsUrl = $this->modx->getOption('sitestatistics_assets_url', $config, $this->modx->getOption('assets_url') . 'components/sitestatistics/');
        $connectorUrl = $assetsUrl . 'connector.php';

        $this->config = array_merge(array(
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'connectorUrl' => $connectorUrl,

            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'templatesPath' => $corePath . 'elements/templates/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'processorsPath' => $corePath . 'processors/',

            'countby' => 'day'
        ), $config);
        $ip_list = $this->modx->getOption('stat.not_allowed_ip');
        if (!empty($ip_list)) {
            $this->ip_list = explode(',',$ip_list);
            $this->ip_list = array_map('trim',$this->ip_list);
        }
        $this->modx->addPackage('sitestatistics', $this->config['modelPath']);
        $this->modx->lexicon->load('sitestatistics:default');
    }

    /**
     * @param array $sp
     */
    public function initialize($sp = array())
    {
        if (!$this->initialized) {
            $style = $this->modx->getOption('stat.frontend_css',null,'');
            if ($style) $this->modx->regClientCSS($style);
            $this->initialized = true;
        }
        if (isset($sp['count'])) {
            if (!isset($sp['countby'])) {
                $sp['countby'] = str_replace(array('byday', 'bymonth', 'byyear'), array('day', 'month', 'year'), $sp['count']);
            }
            unset($sp['count']);
        }
        $this->config = array_merge($this->config, $sp);
    }

    /**
     */
    public function defineUserKey()
    {
        $key = 'siteStatistics';
        if ($this->modx->user->id != 0) {
            $query = $this->modx->newQuery('UserStatistics');
            $query->select('user_key');
            $query->where(array(
                'uid' => $this->modx->user->id,
            ));
            $user_key = $this->modx->getValue($query->prepare());
            if (!empty($user_key)) $_SESSION[$key] = $user_key;
        }
        if (empty($_COOKIE[$key])) {
            if (empty($_SESSION[$key])) {
                $_SESSION[$key] = md5('modzone.ru' . time() . rand());
            }
            setcookie($key, $_SESSION[$key], 0x7FFFFFFF, '/');
        } elseif (empty($_SESSION[$key])) {
            $_SESSION[$key] = $_COOKIE[$key];
        } elseif ($_SESSION[$key] != $_COOKIE[$key]) {
            $_COOKIE[$key] = $_SESSION[$key];
        }
        $this->modx->setPlaceholder('sitestatistics.userKey', $_SESSION[$key]);
    }

    /**
     * Set page statistics
     * @return boolean
     */
    public function setStatistics()
    {
        if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') return false;
        $data = array(
            'rid' => $this->modx->resource->get('id'),
            'date' => date('Y-m-d'),
            'user_key' => $_SESSION['siteStatistics']
        );
        if (!$pageStat = $this->modx->getObject('PageStatistics',$data)) {
            $pageStat = $this->modx->newObject('PageStatistics');
            $pageStat->set('rid',$data['rid']);
            $pageStat->set('date',$data['date']);
            $pageStat->set('user_key',$data['user_key']);
            $pageStat->set('month',date('Y-m'));
            $pageStat->set('year',date('Y'));
            $pageStat->set('views',0);
        }
        $count = $pageStat->get('views');
        $pageStat->set('views',$count+1);
        if ($pageStat->save()) {
            return true;
        }
        return false;
    }

    /**
     * @param $resource
     * @return int|void
     */
    public function getPageStatistics($resource = 0)
    {
        $query = $this->modx->newQuery('PageStatistics');
        //$query->setClassAlias('');
        if (!empty($resource) && is_numeric($resource)) {
            $query->where(array(
                'rid' => $resource,
            ));
        }
        switch ($this->config['countby']) {
            case 'day':
                $query->groupby('date');
                if (empty($this->config['date'])) {
                    $query->where(array(
                        'date' => date('Y-m-d'),
                    ));
                } else {
                    $query->where(array(
                        'date' => date('Y-m-d', strtotime($this->config['date'])),
                    ));
                }
                break;
            case 'month':
                $query->groupby('month');
                if (empty($this->config['date'])) {
                    $query->where(array(
                        'month' => date('Y-m'),
                    ));
                } else {
                    $query->where(array(
                        'month' => date('Y-m', strtotime($this->config['date'])),
                    ));
                }
                break;
            case 'year':
                $query->groupby('year');
                if (empty($this->config['date'])) {
                    $query->where(array(
                        'year' => date('Y'),
                    ));
                } else {
                    $query->where(array(
                        'year' => (int) $this->config['date'],
                    ));
                }
                break;
        }
        if (($this->config['toPlaceholders'] && $this->config['toPlaceholders'] !='false') || $this->config['show'] == 'all') {
            $query->select('COUNT(DISTINCT user_key) as users, SUM(views) as views');
            $tstart = microtime(true);
            if ($query->prepare() && $query->stmt->execute()) {
                $this->modx->queryTime += microtime(true) - $tstart;
                $this->modx->executedQueries++;
                $res = $query->stmt->fetch(PDO::FETCH_ASSOC);
            }
            if (empty($res)) $res = array('users'=>0,'views'=>0);
        } else {
            if (trim($this->config['show']) == 'users')
                $query->select('COUNT(DISTINCT user_key)');
            else
                $query->select('SUM(views)');
            $res = $this->modx->getValue($query->prepare());
            if (empty($res)) $res = 0;
        }
        return $res;
    }

    /**
     * @return array
     */
    public function getSiteStatistics()
    {
        $this->config['show'] = 'all';
        /** @var array $output */
        $output = $this->getPageStatistics();
        $output = $this->modx->getChunk($this->config['tpl'], $output);
        return $output;
    }

    /**
     * Set user statistics
     */
    public function setUserStatistics()
    {
        $user_key = $_SESSION['siteStatistics'];
        if ($this->modx->getCount('UserStatistics',$user_key)) {
            $query = $this->modx->newQuery('UserStatistics');
            $query->command('update');
            $setData = array(
                'date' => date('Y-m-d H:i:s'),
                'rid' => $this->modx->resource->id,
                'context' => $this->modx->context->get('key'),
                'ip' => $this->getUsetIP(),
            );
            if ($this->modx->user->id != 0) $setData['uid'] = $this->modx->user->id;
            $query->set($setData);
            $query->where(array('user_key' => $user_key));
            $tstart = microtime(true);
            if ($query->prepare() && $query->stmt->execute()) {
                $this->modx->queryTime += microtime(true) - $tstart;
                $this->modx->executedQueries++;
            }
        } else {
            $data = array(
                $this->modx->quote($user_key),
                $this->modx->quote(date('Y-m-d H:i:s')),
                $this->modx->user->id,
                "'".$this->modx->context->get('key')."'",
                $this->modx->resource->id,
                "'".$this->getUsetIP()."'",
                "'".$_SERVER['HTTP_USER_AGENT']."'",
                "'".$_SERVER['HTTP_REFERER']."'",
            );
            $sql = "INSERT INTO {$this->modx->getTableName('UserStatistics')} (`user_key`,`date`,`uid`,`context`,`rid`,`ip`,`user_agent`,`referer`) VALUES (" . implode(',',$data).")";
            $query = $this->modx->prepare($sql);
            if (!$query->execute()) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, '[siteStatistics] Could not save online user data. '.print_r($query->errorInfo(),1));
            }
        }
    }

    /**
     * @return array
     */
    public function getOnlineUsers()
    {
        $query = $this->modx->newQuery('UserStatistics');
        if ($this->config['fullMode']) {
            $query->leftJoin('modUserProfile','Profile');
            $query->leftJoin('modUser','User');
            $query->select("Profile.fullname, User.username");

        } else {
            $query->select('uid');
        }
        $time = $this->modx->getOption('stat.online_time',null,15);
        $query->where("date > NOW() -  INTERVAL '$time' MINUTE");
        if (!empty($this->config['ctx'])) $query->where(array('context'=>trim($this->config['ctx'])));
        $tstart = microtime(true);
        $res = array();
        if ($query->prepare() && $query->stmt->execute()) {
            $this->modx->queryTime += microtime(true) - $tstart;
            $this->modx->executedQueries++;
            $res = $query->stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        $output = '';
        if ($this->config['fullMode']) {
            $tplItem = $this->modx->getOption('tplItem', $this->config, '@INLINE <p>[[+stat.fullname]]</p>', true);
            if (strpos($tplItem, '@INLINE') === false) {
                if (!$content = $this->modx->getChunk($tplItem)) {
                    $content = '<p>[[+stat.fullname]]</p>';
                }
            } else {
                $content = substr($tplItem, 8);
            }
            $guests = $this->modx->lexicon('stat_online_guests');
            $guestCount = 0;
            foreach ($res as $user) {
                if (empty($user['fullname'])) {
                    $guestCount++;
                    continue;
                }
                $this->modx->setPlaceholders(array(
                    'fullname' => $user['fullname'],
                    'username' => $user['username'],
                    ),
                    'stat.'
                );
                $output .= $this->parseChunk($content);
            }
            if ($guestCount) {
                $this->modx->setPlaceholders(array(
                    'fullname' => $guests . ": " . $guestCount,
                    'username' => $guests . ": " . $guestCount,
                    ),
                    'stat.'
                );
                $output .= $this->parseChunk($content);
            }
            $this->modx->unsetPlaceholders('stat.fullname');
        } else {
            $users = $guests = 0;
            foreach ($res as $user) {
                if ($user['uid']) {
                    $users++;
                } else {
                    $guests++;
                }
            }
            $output = $this->modx->getChunk($this->config['tpl'], array('stat.online_users'=>$users,'stat.online_guests'=>$guests));
        }
        return $output;
    }

    /**
     * @param $chunk
     * @return string
     */
    public function parseChunk($chunk)
    {
        $this->modx->getParser()->processElementTags('', $chunk, false, false, '[[', ']]', array(), 10);
        $this->modx->getParser()->processElementTags('', $chunk, true, true, '[[', ']]', array(), 10);
        return $chunk;
    }
    /**
     * @return bool
     */
    public function getMessage()
    {
        if ($user = $this->modx->getObject('UserStatistics', array('user_key'=>$_SESSION['siteStatistics'], 'show_message'=>1)) ) {
            $message = $user->get('message');
            $user->set('show_message', 0);
            $user->set('message_showed', time());
            $user->save();
            if ($message) {
                $message = nl2br($message);
                $dlg = $this->modx->getChunk('tpl.siteStatistics.message', array('stat.message' => $message));
                if (strpos($dlg, '[[') !== false) {
                    $maxIterations = (integer)$this->modx->getOption('parser_max_iterations', null, 10);
                    $this->modx->getParser()->processElementTags('', $dlg, false, false, '[[', ']]', array(), $maxIterations);
                    $this->modx->getParser()->processElementTags('', $dlg, true, true, '[[', ']]', array(), $maxIterations);
                }
                $script = $dlg . "\n<script type=\"text/javascript\">
    function statDialogClose(){
        var statDialog = document.getElementById('sitestat-message-dlg');
        statDialog.firstElementChild.style.opacity=0;
        setTimeout(function(){statDialog.style.display = 'none';},500);
    }
    document.getElementById('message-dlg-close-btn').onclick = statDialogClose;
    setTimeout(function(){
        var statDialog = document.getElementById('sitestat-message-dlg');
        statDialog.style.display = 'block';
        setTimeout(function(){statDialog.firstElementChild.style.opacity=1;},500);
    },1000)</script>";
                $this->modx->regClientHTMLBlock($script);
                if (!$this->initialized) $this->modx->regClientCSS($this->config['cssUrl'] . 'web/style.css');
            }
            return true;
        }
        return false;
    }

    /**
     * Clear cache after the user got a message.
     */
    public function clearCache()
    {
        if ($this->need2ClearCache) {
            /** @var xPDOFileCache $cache */
            $cache = $this->modx->cacheManager->getCacheProvider($this->modx->getOption('cache_resource_key', null, 'resource'));
            $cacheKey = $this->modx->resource->getCacheKey($this->modx->context->key);
            $cache->delete($cacheKey, array('deleteTop' => true));
            $cache->delete($cacheKey);
            $this->need2ClearCache = false;
        }
    }

    /**
     * Get the user IP
     * @return string
     */
    function getUsetIP(){
        $ip = $_SERVER['REMOTE_ADDR'];
        return $ip;
    }

    /**
     * Check the user IP
     * @return boolean
     */
    function checkIP(){
        $user_ip = $this->getUsetIP();
        if (in_array($user_ip, $this->ip_list)) return false;
        return true;
    }
}