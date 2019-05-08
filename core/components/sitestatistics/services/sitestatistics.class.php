<?php

/**
 * Service class for siteStatistics extra.
 */
class siteStatistics
{
    /* @var modX $modx */
    protected $modx;
    protected $config = [];
    public $need2ClearCache = false;
    protected $initialized = false;
    protected $ip_list = [];  //not allowed ip


    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct(modX $modx, array $config = [])
    {
        $this->modx = $modx;
        $corePath = $this->modx->getOption('sitestatistics_core_path', $config, $this->modx->getOption('core_path') . 'components/sitestatistics/');
        $assetsUrl = $this->modx->getOption('sitestatistics_assets_url', $config, $this->modx->getOption('assets_url') . 'components/sitestatistics/');
        $connectorUrl = $assetsUrl . 'connector.php';

        $this->config = array_merge([
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

            'countby' => 'day',
        ], $config);
        $ip_list = $this->modx->getOption('stat.not_allowed_ip');
        if (!empty($ip_list)) {
            $this->ip_list = explode(',', $ip_list);
            $this->ip_list = array_map('trim', $this->ip_list);
        }
        $this->modx->addPackage('sitestatistics', $this->config['modelPath']);
        $this->modx->lexicon->load('sitestatistics:default');
    }

    /**
     * @param string|array $key
     * @return $this|mixed
     */
    public function config($key = null)
    {
        if (is_null($key)) {
            return $this->config;
        } elseif (is_array($key)) {
            $this->config = array_merge($this->config, $key);
            return $this;
        } elseif (is_string($key)) {
            return @$this->config[$key];
        }
    }
    /**
     * @param array $sp
     */
    public function initialize($sp = [])
    {
        if (!$this->initialized) {
            $style = $this->modx->getOption('stat.frontend_css', null, '');
            if ($style) {
                $this->modx->regClientCSS($style);
            }
            $this->initialized = true;
        }
        if (isset($sp['count'])) {
            if (!isset($sp['countby'])) {
                $sp['countby'] = str_replace(['byday', 'bymonth', 'byyear'], ['day', 'month', 'year'], $sp['count']);
            }
            unset($sp['count']);
        }
        $this->config = array_merge($this->config, $sp);
    }

    public function initializeMgr($resourceTab = false)
    {
        if ($resourceTab) {
            $this->modx->controller->addLexiconTopic('sitestatistics:default');
            $this->modx->controller->addCss($this->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
            $this->modx->controller->addJavascript($this->config('assetsUrl') . 'js/mgr/sitestatistics.js');
            $this->modx->controller->addJavascript($this->config['jsUrl'] . 'mgr/misc/utils.js');
            $this->modx->controller->addJavascript($this->config('assetsUrl') . 'js/mgr/widgets/resusers.grid.js');
            $output = '
<style>
    ul.sitestatistics-row-actions .btn {padding: 2px 7px;}
    .action-red {color: darkred !important;}
    .x-grid3-col-actions {padding: 3px 0 3px 5px;}
</style>
<script>
    siteStatistics.config = ' . $this->modx->toJSON($this->config) . ';
    siteStatistics.config.connector_url = "' . $this->config['connectorUrl'] . '";
    Ext.ComponentMgr.onAvailable("modx-resource-tabs", function() {
        this.on("beforerender", function() {
            this.add({
                title: _("stat_tab_title"),
                id: "modx-resource-tabs-statistics",
                border: false,
                items: [{
                    layout: "anchor",
                    border: false,
                    items: [{
                        xtype: "sitestatistics-grid-res-users",
                        anchor: "100%",
                        cls: "main-wrapper",
                        resource: MODx.request.id
                    }]
                }]
            });
        });
    });
</script>';
            $this->modx->controller->addHtml($output);
        } else {
            // CSS
            $this->modx->controller->addCss($this->config['cssUrl'] . 'mgr/main.css');
            $this->modx->controller->addCss($this->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
            // JS
            $this->modx->controller->addJavascript($this->config['jsUrl'] . 'mgr/sitestatistics.js');
            $this->modx->controller->addJavascript($this->config['jsUrl'] . 'mgr/misc/utils.js');
            $this->modx->controller->addJavascript($this->config['jsUrl'] . 'mgr/widgets/users.windows.js');
            $this->modx->controller->addJavascript($this->config['jsUrl'] . 'mgr/widgets/stats.windows.js');
            $this->modx->controller->addJavascript($this->config['jsUrl'] . 'mgr/widgets/stats.grid.js');
            $this->modx->controller->addJavascript($this->config['jsUrl'] . 'mgr/widgets/users.grid.js');
            $this->modx->controller->addJavascript($this->config['jsUrl'] . 'mgr/widgets/onlineusers.grid.js');
            $this->modx->controller->addJavascript($this->config['jsUrl'] . 'mgr/widgets/home.panel.js');
            $this->modx->controller->addJavascript($this->config['jsUrl'] . 'mgr/sections/home.js');
            $this->modx->controller->addHtml('<script>
    Ext.onReady(function() {
        MODx.load({ xtype: "sitestatistics-page-home"});
    });
</script>');
            // Make context combo
            $q = $this->modx->newQuery('modContext');
            $q->select('key');
            $q->where(['key:!=' => 'mgr']);
            $q->prepare();
            $q->stmt->execute();
            $ctx = '[';
            while ($row = $q->stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($ctx != '[') {
                    $ctx .= ',';
                }
                $ctx .= "['" . $row['key'] . "','" . $row['key'] . "']";
            }
            $ctx .= ']';

            $this->modx->controller->addHtml('
<script>
    siteStatistics.config = ' . $this->modx->toJSON($this->config) . ';
    siteStatistics.config.connector_url = "' . $this->config['connectorUrl'] . '";
    siteStatistics.config.periods = ' . "[['day','" . $this->modx->lexicon('day') . "'],['month','" . $this->modx->lexicon('month') . "'],['year','" . $this->modx->lexicon('year') . "']]" . ';
    siteStatistics.config.contexts = ' . $ctx . ';
</script>
');
        }
    }

    /**
     */
    public function defineUserKey()
    {
        $key = 'siteStatistics';
        if ($this->modx->user->id != 0) {
            $query = $this->modx->newQuery('UserStatistics');
            $query->select('user_key');
            $query->where([
                'uid' => $this->modx->user->id,
            ]);
            $user_key = $this->modx->getValue($query->prepare());
            if (!empty($user_key)) {
                $_SESSION[$key] = $user_key;
            }
        }
        if (empty($_COOKIE[$key])) {
            if (empty($_SESSION[$key])) {
                $_SESSION[$key] = md5(MODX_HTTP_HOST . time() . rand());
            }
            $cookieSecure = (boolean)$this->modx->getOption('session_cookie_secure', null, false);
            $cookieHttpOnly = (boolean)$this->modx->getOption('session_cookie_httponly', null, true);
            $cookieDomain = $this->modx->getOption('session_cookie_domain', null, '');
            $cookiePath = $this->modx->getOption('session_cookie_path', null, MODX_BASE_URL);
            setcookie($key, $_SESSION[$key], 0x7FFFFFFF, $cookieDomain, $cookiePath, $cookieSecure, $cookieHttpOnly);
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
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            return false;
        }
        $data = [
            'rid' => $this->modx->resource->get('id'),
            'date' => date('Y-m-d'),
            'user_key' => $_SESSION['siteStatistics'],
        ];

        /** @var PageStatistics $pageStat */
        if ($pageStat = $this->modx->getObject('PageStatistics', $data)) {
            $query = $this->modx->newQuery('PageStatistics');
            $query->command('update')
                  ->set(['views' => $pageStat->get('views') + 1])
                  ->where($data);
            $tstart = microtime(true);
            if ($query->prepare() && $query->stmt->execute()) {
                $this->modx->queryTime += microtime(true) - $tstart;
                $this->modx->executedQueries++;
            } else {
                $this->modx->log(modX::LOG_LEVEL_ERROR, '[siteStatistics] Could not update the page statistics.');
                return false;
            }

        } else {
            $pageStat = $this->modx->newObject('PageStatistics');
            $pageStat->set('rid', $data['rid']);
            $pageStat->set('date', $data['date']);
            $pageStat->set('user_key', $data['user_key']);
            $pageStat->set('month', date('Y-m'));
            $pageStat->set('year', date('Y'));
            $pageStat->set('views', 1);

            if (!$pageStat->save()) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, '[siteStatistics] Could not add page statistics.');
                return false;
            }
        }
        return true;
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
            $query->where([
                'rid' => $resource,
            ]);
        }
        switch ($this->config['countby']) {
            case 'day':
                $query->groupby('date');
                if (empty($this->config['date'])) {
                    $query->where([
                        'date' => date('Y-m-d'),
                    ]);
                } else {
                    $query->where([
                        'date' => date('Y-m-d', strtotime($this->config['date'])),
                    ]);
                }
                break;
            case 'month':
                $query->groupby('month');
                if (empty($this->config['date'])) {
                    $query->where([
                        'month' => date('Y-m'),
                    ]);
                } else {
                    $query->where([
                        'month' => date('Y-m', strtotime($this->config['date'])),
                    ]);
                }
                break;
            case 'year':
                $query->groupby('year');
                if (empty($this->config['date'])) {
                    $query->where([
                        'year' => date('Y'),
                    ]);
                } else {
                    $query->where([
                        'year' => (int)$this->config['date'],
                    ]);
                }
                break;
        }
        if (($this->config['toPlaceholders'] && $this->config['toPlaceholders'] != 'false') || $this->config['show'] == 'all') {
            $query->select('COUNT(DISTINCT user_key) as users, SUM(views) as views');
            $tstart = microtime(true);
            if ($query->prepare() && $query->stmt->execute()) {
                $this->modx->queryTime += microtime(true) - $tstart;
                $this->modx->executedQueries++;
                $res = $query->stmt->fetch(PDO::FETCH_ASSOC);
            }
            if (empty($res)) {
                $res = ['users' => 0, 'views' => 0];
            }
        } else {
            if (trim($this->config['show']) == 'users') {
                $query->select('COUNT(DISTINCT user_key)');
            } else {
                $query->select('SUM(views)');
            }
            $res = $this->modx->getValue($query->prepare());
            if (empty($res)) {
                $res = 0;
            }
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
        if ($this->modx->getCount('UserStatistics', ['user_key' => $user_key])) {
            $query = $this->modx->newQuery('UserStatistics');
            $query->command('update');
            $setData = [
                'date' => date('Y-m-d H:i:s'),
                'rid' => $this->modx->resource->id,
                'context' => $this->modx->context->get('key'),
                'ip' => $this->getUsetIP(),
            ];
            if ($this->modx->user->id != 0) {
                $setData['uid'] = $this->modx->user->id;
            }
            $query->set($setData);
            $query->where(['user_key' => $user_key]);
            $tstart = microtime(true);
            if ($query->prepare() && $query->stmt->execute()) {
                $this->modx->queryTime += microtime(true) - $tstart;
                $this->modx->executedQueries++;
            }
        } else {
            $meta = $this->modx->getFieldMeta('UserStatistics');
            /** @var UserStatistics $userStat */
            $userStat = $this->modx->newObject('UserStatistics');
            $userStat->fromArray([
                    'user_key' => $user_key,
                    'date' => date('Y-m-d H:i:s'),
                    'uid' => $this->modx->user->id,
                    'context' => $this->modx->context->get('key'),
                    'rid' => $this->modx->resource->id,
                    'ip' => $this->getUsetIP(),
                    'user_agent' => $this->limit(htmlspecialchars($_SERVER['HTTP_USER_AGENT'], ENT_QUOTES), $meta['user_agent']['precision']),
                    'referer' => $this->limit(htmlspecialchars($_SERVER['HTTP_REFERER'], ENT_QUOTES), $meta['referer']['precision']),
            ], '', true, true);
            if (!$userStat->save()) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, '[siteStatistics] Could not save online user data.');
            };
        }
    }

    /**
     * @return string
     */
    public function getOnlineUsers()
    {
        $query = $this->modx->newQuery('UserStatistics');
        if ($this->config['fullMode']) {
            $query->leftJoin('modUserProfile', 'Profile');
            $query->leftJoin('modUser', 'User');
            $query->select("Profile.fullname, User.username");

        } else {
            $query->select('uid');
        }
        $time = $this->modx->getOption('stat.online_time', null, 15);
        $query->where("date > NOW() -  INTERVAL '$time' MINUTE");
        if (!empty($this->config['ctx'])) {
            $query->where(['context' => trim($this->config['ctx'])]);
        }
        $tstart = microtime(true);
        $res = [];
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
                $this->modx->setPlaceholders([
                    'fullname' => $user['fullname'],
                    'username' => $user['username'],
                ],
                    'stat.'
                );
                $output .= $this->parseChunk($content);
            }
            if ($guestCount) {
                $this->modx->setPlaceholders([
                    'fullname' => $guests . ": " . $guestCount,
                    'username' => $guests . ": " . $guestCount,
                ],
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
            $output = $this->modx->getChunk($this->config['tpl'], ['stat.online_users' => $users, 'stat.online_guests' => $guests]);
        }
        return $output;
    }

    /**
     * @param $chunk
     * @return string
     */
    public function parseChunk($chunk)
    {
        $this->modx->getParser()->processElementTags('', $chunk, false, false, '[[', ']]', [], 10);
        $this->modx->getParser()->processElementTags('', $chunk, true, true, '[[', ']]', [], 10);
        return $chunk;
    }

    /**
     * @return bool
     */
    public function getMessage()
    {
        if ($user = $this->modx->getObject('UserStatistics', ['user_key' => $_SESSION['siteStatistics'], 'show_message' => 1])) {
            $message = $user->get('message');
            $user->set('show_message', 0);
            $user->set('message_showed', time());
            $user->save();
            if ($message) {
                $message = nl2br($message);
                $dlg = $this->modx->getChunk('tpl.siteStatistics.message', ['stat.message' => $message]);
                if (strpos($dlg, '[[') !== false) {
                    $maxIterations = (integer)$this->modx->getOption('parser_max_iterations', null, 10);
                    $this->modx->getParser()->processElementTags('', $dlg, false, false, '[[', ']]', [], $maxIterations);
                    $this->modx->getParser()->processElementTags('', $dlg, true, true, '[[', ']]', [], $maxIterations);
                }
                $script = $dlg . "\n<script>
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
                if (!$this->initialized) {
                    $this->modx->regClientCSS($this->config['cssUrl'] . 'web/style.css');
                }
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
            $cache->delete($cacheKey, ['deleteTop' => true]);
            $cache->delete($cacheKey);
            $this->need2ClearCache = false;
        }
    }

    /**
     * Get the user IP
     * @return string
     */
    function getUsetIP()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        return $ip;
    }

    /**
     * Check the user IP
     * @return boolean
     */
    function checkIP()
    {
        $user_ip = $this->getUsetIP();
        if (in_array($user_ip, $this->ip_list)) {
            return false;
        }
        return true;
    }

    /**
     * Prepare string for STRICT MODE of MySql.
     * @param string $string
     * @param int $length
     * @return string
     */
    private function limit($string, $length = 250)
    {
        return strlen($string) > $length ? substr($string, 0, $length) : $string;
    }
}