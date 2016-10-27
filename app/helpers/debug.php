<?php

namespace debug;

register_shutdown_function(function () {
    pr(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']);;

    $di = \Phalcon\Di::getDefault();

    if (true === isset($di['profiler'])) {
        $profiler = $di['profiler'];

        if ($profiler->getProfiles()) {
            $table = [];

            foreach ($profiler->getProfiles() as $key => $profile) {
                $table[] = [
                    'time' => $profile->getTotalElapsedSeconds(),
                    'sql ' => $profile->getSQLStatement(),
                    'bind' => $profile->getSqlVariables()
                ];
            }

            pr($table);
        }
    }

    pr(get_required_files());

    /**
     * @param $bytes
     * @param $decimals
     */
    function filesize($bytes, $decimals = 2)
    {
        $size   = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)).@$size[$factor];
    }

    pr(filesize(memory_get_peak_usage(false)));
    pr($_SERVER);
});
