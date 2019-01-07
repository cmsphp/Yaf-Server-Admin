<?php
/**
 * 日志封装。
 * @author fingerQin
 * @date 2018-06-27
 */

namespace Utils;

class YLog
{
    /**
     * 日志类别。
     */
    const LOG_TYPE_API           = 'call_api';              // 调用 API 接口日志。
    const LOG_TYPE_SERVICE_ERROR = 'service_error';         // 业务错误日志。
    const LOG_TYPE_SYSTEM_ERROR  = 'error';                 // 系统错误日志。
    const LOG_TYPE_SERVICE_LOG   = 'service_log';           // 业务操作日志。
    const LOG_TYPE_NONE          = 'none';                  // 不记录 MONGODB。

    /**
     * 日志队列 KEY。
     */
    const LOG_REDIS_QUEUE_KEY = 'Yaf-Server-log';

    /**
     * 写日志。
     * 
     * @param  string|array  $logContent    日志内容。
     * @param  string        $logDir        日志目录。如：bank
     * @param  string        $logFilename   日志文件名称。如：bind。生成文件的时候会在 bind 后面接上日期。如:bind-20171121.log
     * @param  string        $errLogType    日志类型。
     * @param  bool          $isForceWrite  是否强制写入硬盘。默认值：false。设置为 true 则日志立即写入硬盘而不是等待析构函数回收再执行。
     *
     * @return void
     */
    public static function log($logContent, $logDir = '', $logFilename = '', $errLogType = self::LOG_TYPE_SERVICE_LOG, $isForceWrite = false) 
    {
        $logContent = is_array($logContent) ? print_r($logContent, true) : $logContent;
        $time       = time();
        $logTime    = date('Y-m-d H:i:s', $time);
        $logfile    = date('Ymd', $time);
        if (strlen($logDir) > 0 && strlen($logFilename) > 0) {
            $logDir   = trim($logDir, '/');
            $logPath  = APP_PATH . '/logs/' . $logDir;
            \Utils\YDir::create($logPath);
            $logPath .= "/{$logFilename}-{$logfile}.log";
        } else {
            $logPath  = APP_PATH . '/logs/errors/';
            \Utils\YDir::create($logPath);
            $logPath  = $logPath . $logfile . '.log';
        }
        $serverIP = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
        $clientIP = YCore::ip();
        $logCtx   = "ErrorTime:{$logTime}\nServerIP:{$serverIP}\nClientIP:{$clientIP}\nErrorLog:{$logContent}\n\n";
        $logObj   = \finger\Log::getInstance();
        $logObj->write($logCtx, $logPath, $isForceWrite);
    }
}