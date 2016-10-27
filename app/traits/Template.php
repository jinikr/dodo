<?php
namespace App\Traits;

trait Template
{
    /**
     * @param  array|string $define
     * @param  array        $assign
     * @return string
     */
    public function show($define, $assign = [])
    {
        if (true === is_string($define)) {
            $define = ['layout' => $define];
        }

        $template = $this->template;
        $template->define($define);
        $template->assign($assign);

        return $template->show('layout');
    }
}
